<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Equipment;
use App\Models\PastryChef;
use App\Models\Production;
use Illuminate\Http\Request;
use App\Models\ProductionDetail;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Chef;
use Symfony\Component\HttpFoundation\Response;

class ProductionController extends Controller
{
    public function getTemplate($id)
    {
        $userId = Auth::id();

        $production = Production::with('details')
            ->where('user_id', $userId)
            ->findOrFail($id);

        $details = $production->details->map(function($d) {
            return [
                'recipe_id'         => $d->recipe_id,
                'chef_id'           => $d->pastry_chef_id,
                'quantity'          => $d->quantity,
                'execution_time'    => $d->execution_time,
                'equipment_ids'     => $d->equipment_ids ? explode(',', $d->equipment_ids) : [],
                'potential_revenue' => $d->potential_revenue,
            ];
        });

        return response()->json([
            'production_name' => $production->production_name,
            'details'         => $details
        ]);
    }

    public function show(Request $request, Production $production)
    {
        // 1) Equipment lookup
        $equipmentMap = Equipment::pluck('name', 'id')->toArray();

        // 2) Build dropdown of chefs used here
        $allChefs = PastryChef::whereIn(
            'id',
            $production->details()->pluck('pastry_chef_id')->unique()
        )
        ->orderBy('name')
        ->pluck('name', 'id')
        ->toArray();

        // 3) Start query
        $detailsQuery = $production
            ->details()
            ->with(['recipe', 'chef']); 
            // make sure your ProductionDetail model’s chef() relation uses pastry_chef_id

        // 4) Apply chef filter
        if ($request->filled('chef_id')) {
            $detailsQuery->where('pastry_chef_id', $request->chef_id);
        }

        // 5) Sort by chef if requested
        $sortDir = $request->input('direction', 'asc');
        if ($request->input('sort') === 'chef') {
            $detailsQuery
                ->join('pastry_chefs', 'production_details.pastry_chef_id', '=', 'pastry_chefs.id')
                ->orderBy('pastry_chefs.name', $sortDir)
                ->select('production_details.*');
        }

        // 6) Get the rows
        $details = $detailsQuery->get();

        // 7) Render
        return view('frontend.production.show', [
            'production'   => $production,
            'equipmentMap' => $equipmentMap,
            'allChefs'     => $allChefs,
            'details'      => $details,
            'selectedChef' => $request->chef_id,
            'sortDir'      => $sortDir,
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        // 1) Determine group: top-level users see themselves + their children; child users see themselves + their creator
        $groupRootId  = $user->created_by ?? $user->id;
        $groupUserIds = User::where('created_by', $groupRootId)
                            ->pluck('id')
                            ->push($groupRootId)
                            ->unique();

        // 2) Load all productions for that group
        $productions = Production::with(['details.recipe', 'details.chef', 'user'])
                                 ->whereIn('user_id', $groupUserIds)
                                 ->latest()
                                 ->get();

        // 3) Compute total potential revenue across all details
        $totalPotentialRevenue = $productions
            ->flatMap(fn($p) => $p->details)    // flatten all detail collections
            ->sum('potential_revenue');        // sum the potential_revenue field

        // 4) Equipment map for display & filtering
        $equipmentMap = Equipment::whereIn('user_id', $groupUserIds)
                                 ->pluck('name', 'id')
                                 ->toArray();

        return view('frontend.production.index', compact(
            'productions',
            'equipmentMap',
            'totalPotentialRevenue'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;
        $groupUserIds = \App\Models\User::where('created_by', $groupRootId)
                            ->pluck('id')
                            ->push($groupRootId)
                            ->unique();

        // 1) Recipes (shop labor mode in your group)
        $recipes = \App\Models\Recipe::where('labor_cost_mode', 'shop')
                        ->whereIn('user_id', $groupUserIds)
                        ->get();

        // 2) Build visible user IDs (for chefs & equipment)
        if (is_null($user->created_by)) {
            $visibleUserIds = \App\Models\User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 3) Chefs: in-group OR default
        $chefs = PastryChef::with('user')
            ->where(function($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds);
            })
            ->orWhere(function($q) {
                $q->whereNull('user_id')
                  ->where('status', 'Default');
            })
            ->orderBy('name')
            ->get();

        // 4) Equipment: in-group OR default
        $equipments = Equipment::with('user')
            ->where(function($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                  ->orWhere('status', 'Default');
            })
            ->orderBy('name')
               ->get();

        // 5) Templates (saved production templates in your group)
        $templates = \App\Models\Production::where('save_template', true)
                        ->whereIn('user_id', $groupUserIds)
                        ->pluck('production_name', 'id');

        return view('frontend.production.create', compact(
            'recipes',
            'chefs',
            'equipments',
            'templates'
        ));
    }

    public function store(Request $request)
    {
        $templateAction = $request->input('template_action');
        $isTemplate = in_array($templateAction, ['template', 'both']);

        $rules = [
            'production_name'   => $isTemplate ? 'required|string|max:255' : 'nullable|string|max:255',
            'production_date'   => 'required|date',
            'template_action'   => 'nullable|in:none,template,both',
            'recipe_id'         => 'required|array',
            'pastry_chef_id'    => 'required|array',
            'quantity'          => 'required|array',
            'execution_time'    => 'required|array',
            'equipment_ids'     => 'required|array',
            'potential_revenue' => 'required|array',
            'total_revenue'     => 'required|numeric|min:0',
        ];

        $data = $request->validate($rules);

        $saveTemplate = $isTemplate;

        $production = Production::create([
            'production_name'         => $data['production_name'],
            'production_date'         => $data['production_date'],
            'total_potential_revenue' => $data['total_revenue'],
            'save_template'           => $saveTemplate,
            'user_id'                 => Auth::id(),
        ]);

        foreach ($data['recipe_id'] as $i => $recipeId) {
            ProductionDetail::create([
                'production_id'     => $production->id,
                'recipe_id'         => $recipeId,
                'pastry_chef_id'    => $data['pastry_chef_id'][$i],
                'quantity'          => $data['quantity'][$i],
                'execution_time'    => $data['execution_time'][$i],
                'equipment_ids'     => implode(',', $data['equipment_ids'][$i] ?? []),
                'potential_revenue' => $data['potential_revenue'][$i],
                'user_id'           => Auth::id(),
            ]);
        }

        return redirect()->route('production.create')
            ->with('success', 'Produzione salvata con successo!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $userId = $user->id;

        // 1) Load the production (with details) for this user
        $production = Production::with('details')
            ->where('user_id', $userId)
            ->findOrFail($id);

        // 2) Recipes (shop labor mode owned by this user)
        $recipes = Recipe::where('labor_cost_mode', 'shop')
            ->where('user_id', $userId)
            ->get();

        // 3) Build visible user IDs (for chefs & equipment filters)
        if (is_null($user->created_by)) {
            $visibleUserIds = \App\Models\User::where('created_by', $user->id)
                ->pluck('id')
                ->push($user->id)
                ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 4) Chefs: in-group OR default
        $chefs = PastryChef::with('user')
            ->where(function($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds);
            })
            ->orWhere(function($q) {
                $q->whereNull('user_id')
                  ->where('status', 'Default');
            })
            ->orderBy('name')
            ->get();

        // 5) Equipment: in-group OR default
        $equipments = Equipment::with('user')
            ->where(function($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                  ->orWhere('status', 'Default');
            })
            ->orderBy('name')
               ->get();

        // 6) Templates (saved production templates by this user)
        $templates = Production::where('save_template', true)
            ->where('user_id', $userId)
            ->pluck('production_name', 'id');

        return view('frontend.production.create', compact(
            'production',
            'recipes',
            'chefs',
            'equipments',
            'templates'
        ));
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $production = Production::where('user_id', $userId)
            ->findOrFail($id);

        $templateAction = $request->input('template_action');
        $isTemplate = in_array($templateAction, ['template', 'both']);

        $rules = [
            'production_name'   => $isTemplate ? 'required|string|max:255' : 'nullable|string|max:255',
            'production_date'   => 'required|date',
            'template_action'   => 'required|in:none,template,both',
            'recipe_id'         => 'required|array',
            'pastry_chef_id'    => 'required|array',
            'quantity'          => 'required|array',
            'execution_time'    => 'required|array',
            'equipment_ids'     => 'required|array',
            'potential_revenue' => 'required|array',
            'total_revenue'     => 'required|numeric|min:0',
        ];

        $data = $request->validate($rules);

        $production->update([
            'production_name'         => $data['production_name'],
            'production_date'         => $data['production_date'],
            'total_potential_revenue' => $data['total_revenue'],
            'save_template'           => $isTemplate,
        ]);

        $production->details()->delete();

        foreach ($data['recipe_id'] as $i => $recipeId) {
            ProductionDetail::create([
                'production_id'     => $production->id,
                'recipe_id'         => $recipeId,
                'pastry_chef_id'    => $data['pastry_chef_id'][$i],
                'quantity'          => $data['quantity'][$i],
                'execution_time'    => $data['execution_time'][$i],
                'equipment_ids'     => implode(',', $data['equipment_ids'][$i] ?? []),
                'potential_revenue' => $data['potential_revenue'][$i],
                'user_id'           => Auth::id(),
            ]);
        }

        return redirect()->route('production.index')
            ->with('success', 'Produzione aggiornata con successo!');
    }

    public function destroy($id)
    {
        $production = Production::where('user_id', Auth::id())
            ->findOrFail($id);

        $production->delete();

        return redirect()->route('production.index')
            ->with('success', 'Produzione eliminata con successo!');
    }
}
