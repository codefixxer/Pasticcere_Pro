<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Client;
use App\Models\Recipe;
use App\Models\ReturnedGood;
use Illuminate\Http\Request;
use App\Models\ExternalSupply;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ExternalSupplyRecipe;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReturnedGoodController extends Controller
{
    public function index(Request $request)
    {
        $user        = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;

        // 1) Visibility Group
        $groupUserIds = User::where('created_by', $groupRootId)
                            ->pluck('id')
                            ->push($groupRootId)
                            ->unique();

        // 2) Clients for dropdown
        $clients = Client::whereIn('user_id', $groupUserIds)
                         ->orderBy('name')
                         ->get();

        // 3) Base queries
        $suppliesQ = ExternalSupply::with('client')
                                   ->whereIn('user_id', $groupUserIds);
        $returnsQ  = ReturnedGood::with('client', 'recipes', 'externalSupply')
                                  ->whereIn('user_id', $groupUserIds);

        // 4) Apply filters
        if ($request->filled('client_id')) {
            $suppliesQ->where('client_id', $request->client_id);
            $returnsQ->where('client_id', $request->client_id);
        }
        if ($request->filled('start_date')) {
            $suppliesQ->where('supply_date', '>=', $request->start_date);
            $returnsQ->where('return_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $suppliesQ->where('supply_date', '<=', $request->end_date);
            $returnsQ->where('return_date', '<=', $request->end_date);
        }

        // 5) Fetch & sort
        $supplies      = $suppliesQ->orderBy('supply_date', 'desc')->get();
        $returnedGoods = $returnsQ->orderBy('return_date', 'desc')->get();

        // 6) Returns mapped by supply ID
        $returnsBySupply = $returnedGoods
            ->groupBy('external_supply_id')
            ->map(fn($grp) => $grp->sum('total_amount'));

        // 7) Correct Daily Summary (group by supply date, match returns to original supply date)
        $supsByDate = $supplies
            ->groupBy(fn($supply) => $supply->supply_date->toDateString())
            ->map(function($group, $date) use ($returnedGoods) {
                $supplyIdsOnDate = $group->pluck('id');

                $returnsForDate = $returnedGoods
                    ->whereIn('external_supply_id', $supplyIdsOnDate)
                    ->sum('total_amount');

                return (object)[
                    'date'         => $date,
                    'total_supply' => $group->sum('total_amount'),
                    'total_return' => $returnsForDate,
                ];
            })
            ->sortByDesc('date');

        // 8) Grand totals
        $grandSupply = $supplies->sum('total_amount');
        $grandReturn = $returnedGoods->sum('total_amount');
        $grandNet    = $grandSupply - $grandReturn;

        // 9) AJAX response
        if ($request->ajax()) {
            return view('frontend.returned-goods.index', compact(
                'clients', 'supplies', 'returnedGoods', 'returnsBySupply',
                'supsByDate', 'grandSupply', 'grandReturn', 'grandNet'
            ));
        }

        // 10) Full page load
        return view('frontend.returned-goods.index', compact(
            'clients', 'supplies', 'returnedGoods', 'returnsBySupply',
            'supsByDate', 'grandSupply', 'grandReturn', 'grandNet'
        ));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;

        $groupUserIds = User::where('created_by', $groupRootId)
            ->pluck('id')
            ->push($groupRootId);

        $externalSupplyId = $request->query('external_supply_id');

        if (! $externalSupplyId) {
            abort(Response::HTTP_BAD_REQUEST, 'Parametro external_supply_id mancante');
        }

        $externalSupply = ExternalSupply::with(['client', 'recipes.recipe', 'recipes.returns'])
            ->whereIn('user_id', $groupUserIds)
            ->findOrFail($externalSupplyId);

        return view('frontend.returned-goods.form', compact('externalSupply'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'           => 'required|exists:clients,id',
            'external_supply_id'  => 'required|exists:external_supplies,id',
            'return_date'         => 'required|date',
            'recipes'             => 'required|array|min:1',
            'recipes.*.qty'       => 'required|integer|min:0',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($data, $userId) {
            $rg = ReturnedGood::create([
                'client_id'           => $data['client_id'],
                'external_supply_id'  => $data['external_supply_id'],
                'return_date'         => $data['return_date'],
                'total_amount'        => 0,
                'user_id'             => $userId,
            ]);

            $grandTotal = 0;

            foreach ($data['recipes'] as $lineId => $row) {
                $line = ExternalSupplyRecipe::findOrFail($lineId);
                $toReturn = (int) $row['qty'];
                $remaining = $line->remaining_qty;

                if ($toReturn > $remaining) {
                    $recipeName = optional($line->recipe)->recipe_name ?? 'Ricetta sconosciuta';
                    abort(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        "Non è possibile restituire più di {$remaining} unità per {$recipeName}"
                    );
                }

                if ($toReturn <= 0) {
                    continue;
                }

                $lineTotal = round($line->price * $toReturn, 2);

                $rg->recipes()->create([
                    'external_supply_recipe_id' => $line->id,
                    'price'                     => $line->price,
                    'qty'                       => $toReturn,
                    'total_amount'              => $lineTotal,
                ]);

                $grandTotal += $lineTotal;
            }

            $rg->update(['total_amount' => $grandTotal]);
        });

        return redirect()
            ->route('returned-goods.index')
            ->with('success', 'Reso registrato con successo!');
    }

    public function edit(ReturnedGood $returnedGood)
    {
        if ($returnedGood->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $userId = Auth::id();

        $clients = Client::where('user_id', $userId)
                         ->orderBy('name')
                         ->get();

        $recipes = Recipe::where('user_id', $userId)
                         ->orderBy('recipe_name')
                         ->get();

        $externalSupply = $returnedGood->externalSupply;

        return view('frontend.returned-goods.form', compact(
            'returnedGood', 'clients', 'recipes', 'externalSupply'
        ));
    }

    public function update(Request $request, ReturnedGood $returnedGood)
    {
        if ($returnedGood->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'client_id'           => 'required|exists:clients,id',
            'return_date'         => 'required|date',
            'recipes'             => 'required|array|min:1',
            'recipes.*.qty'       => 'required|integer|min:1',
            'recipes.*.price'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $returnedGood) {
            $returnedGood->update([
                'client_id'    => $data['client_id'],
                'return_date'  => $data['return_date'],
                'total_amount' => 0,
            ]);

            $returnedGood->recipes()->delete();
            $grandTotal = 0;

            foreach ($data['recipes'] as $line) {
                $lineTotal = round($line['price'] * $line['qty'], 2);
                $returnedGood->recipes()->create([
                    'recipe_id'    => $line['id'],
                    'price'        => $line['price'],
                    'qty'          => $line['qty'],
                    'total_amount' => $lineTotal,
                ]);
                $grandTotal += $lineTotal;
            }

            $returnedGood->update(['total_amount' => $grandTotal]);
        });

        return redirect()
            ->route('returned-goods.index')
            ->with('success', 'Resi aggiornati con successo!');
    }

    public function destroy(ReturnedGood $returnedGood)
    {
        if ($returnedGood->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $returnedGood->delete();

        return redirect()
            ->route('returned-goods.index')
            ->with('success', 'Resi eliminati con successo!');
    }
}
