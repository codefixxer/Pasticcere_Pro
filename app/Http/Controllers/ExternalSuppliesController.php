<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Recipe;
use App\Models\LaborCost;
use App\Models\ReturnedGood;
use App\Models\ExternalSupply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExternalSuppliesController extends Controller
{
    /* ---------------------------- LIST (INDEX) ---------------------------- */
    public function index()
    {
        $user = Auth::user();

        // two-level visibility group (root + children, or child + root)
        if (is_null($user->created_by)) {
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 🟢 show ONLY real records (hide templates)
        $supplies = ExternalSupply::with(['client','recipes.recipe','user'])
            ->whereIn('user_id', $visibleUserIds)
            ->where('save_template', false)
            ->latest()
            ->get();

        $returns  = ReturnedGood::with(['client','recipes.supplyLine.recipe','user'])
            ->whereIn('user_id', $visibleUserIds)
            ->get();

        $all = collect();

        foreach ($supplies as $supply) {
            $all->push([
                'type'               => 'supply',
                'client'             => $supply->client->name,
                'date'               => $supply->supply_date->toDateString(),
                'external_supply_id' => $supply->id,
                'lines'              => $supply->recipes,
                'revenue'            => $supply->total_amount,
                'created_by'         => $supply->user->name ?? '—',
            ]);
        }

        foreach ($returns as $return) {
            $returnedLines = $return->recipes->map(fn($r) => (object)[
                'recipe'       => $r->supplyLine->recipe ?? null,
                'qty'          => $r->qty,
                'total_amount' => $r->total_amount,
            ]);

            $all->push([
                'type'               => 'return',
                'client'             => $return->client->name,
                'date'               => $return->return_date->toDateString(),
                'external_supply_id' => $return->external_supply_id,
                'lines'              => $returnedLines,
                'revenue'            => -1 * $return->total_amount,
                'created_by'         => $return->user->name ?? '—',
            ]);
        }

        $grouped = $all
            ->groupBy('client')
            ->map(function ($byClient) {
                return $byClient
                    ->sortByDesc('date')
                    ->groupBy('date');
            });

        return view('frontend.external-supplies.index', ['all' => $grouped]);
    }

    /* ----------------------------- SHOW ---------------------------------- */
    public function show(ExternalSupply $externalSupply)
    {
        $externalSupply->load(['client', 'recipes.recipe', 'user']);
        return view('frontend.external-supplies.show', compact('externalSupply'));
    }

    /* ---------------------------- CREATE --------------------------------- */
    public function create()
    {
        $user = Auth::user();

        if (is_null($user->created_by)) {
            $groupUserIds = User::where('created_by', $user->id)
                                ->pluck('id')
                                ->push($user->id)
                                ->unique();
        } else {
            $groupUserIds = collect([$user->id, $user->created_by])->unique();
        }

        $groupRootId = $user->created_by ?? $user->id;
        $laborCost   = LaborCost::where('user_id', $groupRootId)->first();

        // clients in group (or global)
        $clients = Client::with('user')
            ->where(function($q) use ($groupUserIds) {
                $q->whereIn('user_id', $groupUserIds)
                  ->orWhereNull('user_id');
            })
            ->latest()
            ->get();

        // external labor-mode recipes within group
        $recipes   = Recipe::where('labor_cost_mode', 'external')
                        ->whereIn('user_id', $groupUserIds)
                        ->get();

        $templates = ExternalSupply::where('save_template', true)
                        ->whereIn('user_id', $groupUserIds)
                        ->pluck('supply_name', 'id');

        return view('frontend.external-supplies.create', compact(
            'laborCost', 'clients', 'recipes', 'templates'
        ));
    }

    /* ----------------------------- STORE --------------------------------- */
    public function store(Request $request)
    {
        $messages = [
            'supply_name.required_if'          => 'Il nome della fornitura è obbligatorio quando si salva come modello.',
            'client_id.required'               => 'Devi selezionare un cliente.',
            'client_id.exists'                 => 'Il cliente selezionato non esiste.',
            'supply_date.required'             => 'La data della fornitura è obbligatoria.',
            'supply_date.date'                 => 'Inserisci una data valida.',
            'recipes.required'                 => 'Devi aggiungere almeno una ricetta.',
            'recipes.*.id.required'            => 'Seleziona una ricetta per ogni riga.',
            'recipes.*.id.exists'              => 'La ricetta selezionata non esiste.',
            'recipes.*.price.required'         => 'Il prezzo è obbligatorio e deve essere un numero.',
            'recipes.*.qty.required'           => 'La quantità è obbligatoria e deve essere un numero intero.',
            'recipes.*.total_amount.required'  => 'L\'importo totale è obbligatorio e deve essere un numero.',
        ];

        $data = $request->validate([
            'supply_name'            => 'required_if:template_action,template,both|max:255',
            'template_action'        => 'nullable|in:none,template,both',
            'client_id'              => 'required|exists:clients,id',
            'supply_date'            => 'required|date',
            'recipes'                => 'required|array|min:1',
            'recipes.*.id'           => 'required|exists:recipes,id',
            'recipes.*.price'        => 'required|numeric|min:0',
            'recipes.*.qty'          => 'required|integer|min:0',
            'recipes.*.total_amount' => 'required|numeric|min:0',
        ], $messages);

        $userId      = Auth::id();
        $saveAsTpl   = in_array(($data['template_action'] ?? 'none'), ['template', 'both']);
        $totalAmount = array_sum(array_column($data['recipes'], 'total_amount'));

        return DB::transaction(function () use ($data, $userId, $totalAmount, $saveAsTpl) {

            // 🟢 1) ALWAYS create the RECORD (save_template = false)
            $record = ExternalSupply::create([
                'client_id'     => $data['client_id'],
                'supply_name'   => $data['supply_name'] ?? null,
                'supply_date'   => $data['supply_date'],
                'total_amount'  => $totalAmount,
                'save_template' => false,
                'user_id'       => $userId,
            ]);

            foreach ($data['recipes'] as $row) {
                $record->recipes()->create([
                    'recipe_id'    => $row['id'],
                    'category'     => $row['category'] ?? '',
                    'price'        => $row['price'],
                    'qty'          => $row['qty'],
                    'total_amount' => $row['total_amount'],
                    'user_id'      => $userId,
                ]);
            }

            // 🟡 2) Optionally create a TEMPLATE (separate row, save_template = true)
            if ($saveAsTpl) {
                $tpl = ExternalSupply::create([
                    'client_id'     => $data['client_id'],
                    'supply_name'   => $data['supply_name'],
                    'supply_date'   => $data['supply_date'],
                    'total_amount'  => $totalAmount, // keep precomputed totals
                    'save_template' => true,
                    'user_id'       => $userId,
                ]);

                foreach ($data['recipes'] as $row) {
                    // keep qty/price in template; totals precomputed as price*qty
                    $tpl->recipes()->create([
                        'recipe_id'    => $row['id'],
                        'category'     => $row['category'] ?? '',
                        'price'        => $row['price'],
                        'qty'          => $row['qty'],
                        'total_amount' => $row['total_amount'],
                        'user_id'      => $userId,
                    ]);
                }
            }

            return redirect()
                ->route('external-supplies.create')
                ->with('success', $saveAsTpl
                    ? 'Fornitura salvata + modello aggiornato.'
                    : 'Fornitura esterna salvata con successo!');
        });
    }

    /* --------------------------- LOAD TEMPLATE ---------------------------- */
    public function getTemplate($id)
    {
        $user = Auth::user();

        // allow loading any template owned by user or its group
        if (is_null($user->created_by)) {
            $groupUserIds = User::where('created_by', $user->id)
                                ->pluck('id')
                                ->push($user->id)
                                ->unique();
        } else {
            $groupUserIds = collect([$user->id, $user->created_by])->unique();
        }

        $supply = ExternalSupply::with('recipes')
            ->where('id', $id)
            ->where('save_template', true)
            ->whereIn('user_id', $groupUserIds)
            ->firstOrFail();

        $rows = $supply->recipes->map(fn($r) => [
            'recipe_id'    => $r->recipe_id,
            'price'        => $r->price,
            'qty'          => $r->qty,
            'total_amount' => $r->total_amount,
        ]);

        return response()->json([
            'client_id'       => $supply->client_id,
            'supply_name'     => $supply->supply_name,
            'supply_date'     => $supply->supply_date->format('Y-m-d'),
            'template_action' => 'template',
            'rows'            => $rows,
        ]);
    }

    /* ----------------------------- EDIT ---------------------------------- */
    public function edit(ExternalSupply $externalSupply)
    {
        $externalSupply->load('recipes.recipe');

        $user = Auth::user();

        if (is_null($user->created_by)) {
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        $clients = Client::with('user')
            ->where(function($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                  ->orWhereNull('user_id');
            })
            ->latest()
            ->get();

        $recipes   = Recipe::where('labor_cost_mode', 'external')
                        ->whereIn('user_id', $visibleUserIds)
                        ->get();

        $templates = ExternalSupply::where('save_template', true)
                        ->whereIn('user_id', $visibleUserIds)
                        ->pluck('supply_name', 'id');

        return view('frontend.external-supplies.create', compact(
            'externalSupply', 'clients', 'recipes', 'templates'
        ));
    }

    /* ---------------------------- UPDATE --------------------------------- */
    public function update(Request $request, ExternalSupply $externalSupply)
    {
        $messages = [
            'supply_name.required_if'          => 'Il nome della fornitura è obbligatorio quando si salva come modello.',
            'client_id.required'               => 'Devi selezionare un cliente.',
            'client_id.exists'                 => 'Il cliente selezionato non esiste.',
            'supply_date.required'             => 'La data della fornitura è obbligatoria.',
            'supply_date.date'                 => 'Inserisci una data valida.',
            'recipes.required'                 => 'Devi aggiungere almeno una ricetta.',
            'recipes.*.id.required'            => 'Seleziona una ricetta per ogni riga.',
            'recipes.*.id.exists'              => 'La ricetta selezionata non esiste.',
            'recipes.*.price.required'         => 'Il prezzo è obbligatorio e deve essere un numero.',
            'recipes.*.qty.required'           => 'La quantità è obbligatoria e deve essere un numero intero.',
            'recipes.*.total_amount.required'  => 'L\'importo totale è obbligatorio e deve essere un numero.',
        ];

        $data = $request->validate([
            'supply_name'            => 'required_if:template_action,template,both|max:255',
            'template_action'        => 'nullable|in:none,template,both',
            'client_id'              => 'required|exists:clients,id',
            'supply_date'            => 'required|date',
            'recipes'                => 'required|array|min:1',
            'recipes.*.id'           => 'required|exists:recipes,id',
            'recipes.*.price'        => 'required|numeric|min:0',
            'recipes.*.qty'          => 'required|integer|min:0',
            'recipes.*.total_amount' => 'required|numeric|min:0',
        ], $messages);

        $userId      = Auth::id();
        $saveAsTpl   = in_array(($data['template_action'] ?? 'none'), ['template', 'both']);
        $totalAmount  = array_sum(array_column($data['recipes'], 'total_amount'));

        return DB::transaction(function () use ($externalSupply, $data, $saveAsTpl, $userId, $totalAmount) {
            // 🟢 1) Update the RECORD (do NOT flip save_template)
            $externalSupply->update([
                'client_id'     => $data['client_id'],
                'supply_name'   => $data['supply_name'] ?? $externalSupply->supply_name,
                'supply_date'   => $data['supply_date'],
                'total_amount'  => $totalAmount,
                'save_template' => $externalSupply->save_template, // keep as-is (records stay false)
                'user_id'       => $userId,
            ]);

            $externalSupply->recipes()->delete();
            foreach ($data['recipes'] as $row) {
                $externalSupply->recipes()->create([
                    'recipe_id'    => $row['id'],
                    'category'     => $row['category'] ?? '',
                    'price'        => $row['price'],
                    'qty'          => $row['qty'],
                    'total_amount' => $row['total_amount'],
                    'user_id'      => $userId,
                ]);
            }

            // 🟡 2) Optionally UPSERT a TEMPLATE (by name for this user)
            if ($saveAsTpl) {
                $template = ExternalSupply::where('user_id', $userId)
                    ->where('save_template', true)
                    ->where('supply_name', $data['supply_name'])
                    ->first();

                if (!$template) {
                    $template = ExternalSupply::create([
                        'client_id'     => $data['client_id'],
                        'supply_name'   => $data['supply_name'],
                        'supply_date'   => $data['supply_date'],
                        'total_amount'  => $totalAmount,
                        'save_template' => true,
                        'user_id'       => $userId,
                    ]);
                } else {
                    $template->update([
                        'client_id'     => $data['client_id'],
                        'supply_date'   => $data['supply_date'],
                        'total_amount'  => $totalAmount,
                        'save_template' => true,
                        'user_id'       => $userId,
                    ]);
                    $template->recipes()->delete();
                }

                foreach ($data['recipes'] as $row) {
                    $template->recipes()->create([
                        'recipe_id'    => $row['id'],
                        'category'     => $row['category'] ?? '',
                        'price'        => $row['price'],
                        'qty'          => $row['qty'],
                        'total_amount' => $row['total_amount'],
                        'user_id'      => $userId,
                    ]);
                }
            }

            return redirect()
                ->route('external-supplies.index')
                ->with('success', $saveAsTpl
                    ? 'Fornitura aggiornata e modello sincronizzato.'
                    : 'Fornitura esterna aggiornata con successo!');
        });
    }

    /* ---------------------------- DESTROY -------------------------------- */
    public function destroy(ExternalSupply $externalSupply)
    {
        $externalSupply->recipes()->delete();
        $externalSupply->delete();

        return redirect()
            ->route('external-supplies.index')
            ->with('success', 'Fornitura esterna eliminata con successo!');
    }
}
