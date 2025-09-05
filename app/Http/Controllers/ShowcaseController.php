<?php
// app/Http/Controllers/ShowcaseController.php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Models\ShowcaseRecipe;
use App\Models\Recipe;
use App\Models\LaborCost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowcaseController extends Controller
{
    /* ---------------------------- LIST (INDEX) ---------------------------- */
    public function index()
    {
        $user        = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;

        $groupUserIds = User::where('created_by', $groupRootId)
            ->pluck('id')
            ->push($groupRootId);

        // ⚠️ Show only real records (hide templates)
        $showcases = Showcase::with([
                'recipes.recipe.ingredients.ingredient',
                'recipes.recipe'
            ])
            ->whereIn('user_id', $groupUserIds)
            ->where('save_template', false)
            ->latest()
            ->get();

        return view('frontend.showcase.index', compact('showcases'));
    }

    /* ---------------------------- CREATE (FORM) --------------------------- */
    public function create()
    {
        $user        = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;

        $laborCost     = LaborCost::where('user_id', $groupRootId)->first();
        $laborCostRate = $laborCost;

        $groupUserIds = User::where('created_by', $groupRootId)
            ->pluck('id')
            ->push($groupRootId);

        // ✅ Only sellable recipes (priced per current sell_mode)
        $recipes = $this->getSellableRecipes($groupUserIds, withIngredients: true)->get();

        // Precompute costs for client-side math
        $recipes->each(function ($r) use ($laborCostRate) {
            $rate = $r->labor_cost_mode === 'external'
                ? ($laborCostRate->external_cost_per_min ?? 0)
                : ($laborCostRate->shop_cost_per_min     ?? 0);

            $r->batch_labor_cost = round($r->labour_time_min * $rate, 2);
            $r->batch_ing_cost   = $r->ingredients_cost_per_batch;
        });

        // Templates dropdown (group scope)
        $templates = Showcase::where('save_template', true)
            ->whereIn('user_id', $groupUserIds)
            ->pluck('showcase_name', 'id');

        $isEdit = false;

        return view('frontend.showcase.create', compact(
            'recipes',
            'laborCost',
            'laborCostRate',
            'templates',
            'isEdit'
        ));
    }

    /* --------------------------- STORE (CREATE) --------------------------- */
    public function store(Request $request)
    {
        $messages = [
            'showcase_name.required'              => 'Il nome della vetrina è obbligatorio.',
            'showcase_date.required'              => 'La data della vetrina è obbligatoria.',
            'template_action.required'            => 'Seleziona un\'azione valida per il salvataggio.',
            'items.required'                      => 'Devi aggiungere almeno un articolo alla vetrina.',
            'items.*.recipe_id.required'          => 'Seleziona una ricetta per ciascuna riga.',
            'items.*.price.required'              => 'Il prezzo è obbligatorio e deve essere un numero.',
            'items.*.quantity.required'           => 'La quantità è obbligatoria e deve essere un numero intero.',
            'items.*.sold.required'               => 'Il numero di venduti è obbligatorio e deve essere un numero intero.',
            'items.*.reuse.required'              => 'Il numero di riutilizzi è obbligatorio e deve essere un numero intero.',
            'items.*.waste.required'              => 'Il numero di scarti è obbligatorio e deve essere un numero intero.',
            'items.*.potential_income.required'   => 'Il potenziale è obbligatorio e deve essere un numero.',
            'items.*.actual_revenue.required'     => 'Il ricavo effettivo è obbligatorio e deve essere un numero.',
            'total_revenue.required'              => 'Il ricavo totale è obbligatorio e deve essere un numero.',
            'plus.required'                       => 'Il valore "Extra" è obbligatorio e deve essere un numero.',
            'real_margin.required'                => 'Il margine reale è obbligatorio e deve essere un numero.',
        ];

        $request->validate([
            'showcase_name'   => 'nullable|string|max:255',
            'showcase_date'   => 'required|date',
            'template_action' => 'required|in:none,template,both',
            'template_id'     => 'nullable|exists:showcases,id',
            'items'           => 'required|array|min:1',
            'items.*.recipe_id'        => 'required|exists:recipes,id',
            'items.*.price'            => 'required|numeric|min:0',
            'items.*.quantity'         => 'required|integer|min:0',
            'items.*.sold'             => 'required|integer|min:0',
            'items.*.reuse'            => 'required|integer|min:0',
            'items.*.waste'            => 'required|integer|min:0',
            'items.*.potential_income' => 'required|numeric|min:0',
            'items.*.actual_revenue'   => 'required|numeric|min:0',
            'total_revenue'            => 'required|numeric|min:0',
            'plus'                     => 'required|numeric',
            'real_margin'              => 'required|numeric',
        ], $messages);

        if (in_array($request->template_action, ['template', 'both'])) {
            $request->validate([
                'showcase_name' => 'required|string|max:255',
            ], $messages);
        }

        $data   = $request->all();
        $userId = Auth::id();

        $syncLinesForRecord = function (Showcase $sc, array $items) use ($userId) {
            $sc->recipes()->delete();
            foreach ($items as $row) {
                ShowcaseRecipe::create([
                    'showcase_id'      => $sc->id,
                    'recipe_id'        => $row['recipe_id'],
                    'price'            => $row['price'],
                    'quantity'         => $row['quantity'],
                    'sold'             => $row['sold'],
                    'reuse'            => $row['reuse'],
                    'waste'            => $row['waste'],
                    'potential_income' => $row['potential_income'],
                    'actual_revenue'   => $row['actual_revenue'],
                    'user_id'          => $userId,
                ]);
            }
        };

        $syncLinesForTemplate = function (Showcase $sc, array $items) use ($userId) {
            $sc->recipes()->delete();
            foreach ($items as $row) {
                $price = (float) $row['price'];
                $qty   = (float) $row['quantity'];
                ShowcaseRecipe::create([
                    'showcase_id'      => $sc->id,
                    'recipe_id'        => $row['recipe_id'],
                    'price'            => $price,
                    'quantity'         => $qty,
                    'sold'             => 0,
                    'reuse'            => 0,
                    'waste'            => 0,
                    'potential_income' => round($price * $qty, 2),
                    'actual_revenue'   => 0,
                    'user_id'          => $userId,
                ]);
            }
        };

        return DB::transaction(function () use ($data, $userId, $syncLinesForRecord, $syncLinesForTemplate) {
            $action = $data['template_action'];

            // 1) Always create the RECORD
            $record = Showcase::create([
                'showcase_name'   => $data['showcase_name'] ?? null,
                'showcase_date'   => $data['showcase_date'],
                'break_even'      => $data['break_even'],
                'template_action' => $action,
                'save_template'   => false,
                'total_revenue'   => $data['total_revenue'],
                'plus'            => $data['plus'],
                'real_margin'     => $data['real_margin'],
                'user_id'         => $userId,
            ]);
            $syncLinesForRecord($record, $data['items']);

            // 2) Optionally create a TEMPLATE
            if (in_array($action, ['template', 'both'])) {
                $template = Showcase::create([
                    'showcase_name'   => $data['showcase_name'],
                    'showcase_date'   => $data['showcase_date'],
                    'break_even'      => $data['break_even'],
                    'template_action' => 'template',
                    'save_template'   => true,
                    'total_revenue'   => 0,
                    'plus'            => 0,
                    'real_margin'     => 0,
                    'user_id'         => $userId,
                ]);
                $syncLinesForTemplate($template, $data['items']);
            }

            return redirect()
                ->route('showcase.index')
                ->with('success', in_array($action, ['template', 'both'])
                    ? 'Vetrina salvata + modello aggiornato.'
                    : 'Vetrina salvata con successo.');
        });
    }

    /* ---------------------------- UPDATE (EDIT) --------------------------- */
    public function update(Request $request, Showcase $showcase)
    {
        abort_if($showcase->user_id !== Auth::id(), 403);

        $messages = [
            'showcase_date.required'            => 'La data della vetrina è obbligatoria.',
            'items.required'                    => 'Devi aggiungere almeno un articolo alla vetrina.',
            'items.*.recipe_id.required'        => 'Seleziona una ricetta per ciascuna riga.',
            'items.*.price.required'            => 'Il prezzo è obbligatorio e deve essere un numero.',
            'items.*.quantity.required'         => 'La quantità è obbligatoria e deve essere un numero intero.',
            'items.*.sold.required'             => 'Il numero dei venduti è obbligatorio e deve essere un numero intero.',
            'items.*.reuse.required'            => 'Il numero di riutilizzi è obbligatorio e deve essere un numero intero.',
            'items.*.waste.required'            => 'Il numero di scarti è obbligatorio e deve essere un numero intero.',
            'items.*.potential_income.required' => 'Il potenziale è obbligatorio e deve essere un numero.',
            'items.*.actual_revenue.required'   => 'Il ricavo effettivo è obbligatorio e deve essere un numero.',
            'total_revenue.required'            => 'Il ricavo totale è obbligatorio e deve essere un numero.',
            'plus.required'                     => 'Il valore "Extra" è obbligatorio e deve essere un numero.',
            'real_margin.required'              => 'Il margine reale è obbligatorio e deve essere un numero.',
        ];

        $request->validate([
            'showcase_name'            => 'nullable|string|max:255',
            'showcase_date'            => 'required|date',
            'template_action'          => 'nullable|in:none,template,both',
            'items'                    => 'required|array|min:1',
            'items.*.recipe_id'        => 'required|exists:recipes,id',
            'items.*.price'            => 'required|numeric|min:0',
            'items.*.quantity'         => 'required|integer|min:0',
            'items.*.sold'             => 'required|integer|min:0',
            'items.*.reuse'            => 'required|integer|min:0',
            'items.*.waste'            => 'required|integer|min:0',
            'items.*.potential_income' => 'required|numeric|min:0',
            'items.*.actual_revenue'   => 'required|numeric|min:0',
            'total_revenue'            => 'required|numeric|min:0',
            'plus'                     => 'required|numeric',
            'real_margin'              => 'required|numeric',
        ], $messages);

        if (in_array($request->template_action, ['template', 'both'])) {
            $request->validate([
                'showcase_name' => 'required|string|max:255',
            ], [
                'showcase_name.required' => 'Il nome della vetrina è obbligatorio quando si salva come modello.',
            ]);
        }

        $data   = $request->all();
        $userId = Auth::id();

        $syncLinesForRecord = function (Showcase $sc, array $items) use ($userId) {
            $sc->recipes()->delete();
            foreach ($items as $row) {
                ShowcaseRecipe::create([
                    'showcase_id'      => $sc->id,
                    'recipe_id'        => $row['recipe_id'],
                    'price'            => $row['price'],
                    'quantity'         => $row['quantity'],
                    'sold'             => $row['sold'],
                    'reuse'            => $row['reuse'],
                    'waste'            => $row['waste'],
                    'potential_income' => $row['potential_income'],
                    'actual_revenue'   => $row['actual_revenue'],
                    'user_id'          => $userId,
                ]);
            }
        };

        $syncLinesForTemplate = function (Showcase $sc, array $items) use ($userId) {
            $sc->recipes()->delete();
            foreach ($items as $row) {
                $price = (float) $row['price'];
                $qty   = (float) $row['quantity'];
                ShowcaseRecipe::create([
                    'showcase_id'      => $sc->id,
                    'recipe_id'        => $row['recipe_id'],
                    'price'            => $price,
                    'quantity'         => $qty,
                    'sold'             => 0,
                    'reuse'            => 0,
                    'waste'            => 0,
                    'potential_income' => round($price * $qty, 2),
                    'actual_revenue'   => 0,
                    'user_id'          => $userId,
                ]);
            }
        };

        return DB::transaction(function () use ($showcase, $data, $syncLinesForRecord, $syncLinesForTemplate, $userId) {
            $action = $data['template_action'] ?? 'none';

            // 1) Update the RECORD (do not flip save_template flag)
            $showcase->update([
                'showcase_name'   => $data['showcase_name'] ?? $showcase->showcase_name,
                'showcase_date'   => $data['showcase_date'],
                'template_action' => $action,
                'save_template'   => $showcase->save_template,
                'break_even'      => $data['break_even'],
                'total_revenue'   => $data['total_revenue'],
                'plus'            => $data['plus'],
                'real_margin'     => $data['real_margin'],
                'user_id'         => $userId,
            ]);
            $syncLinesForRecord($showcase, $data['items']);

            // 2) Optionally upsert a TEMPLATE (by name for this user)
            if (in_array($action, ['template', 'both'])) {
                $template = Showcase::where('user_id', $userId)
                    ->where('save_template', true)
                    ->where('showcase_name', $data['showcase_name'])
                    ->first();

                if (!$template) {
                    $template = Showcase::create([
                        'showcase_name'   => $data['showcase_name'],
                        'showcase_date'   => $data['showcase_date'],
                        'break_even'      => $data['break_even'],
                        'template_action' => 'template',
                        'save_template'   => true,
                        'total_revenue'   => 0,
                        'plus'            => 0,
                        'real_margin'     => 0,
                        'user_id'         => $userId,
                    ]);
                } else {
                    $template->update([
                        'showcase_date'   => $data['showcase_date'],
                        'break_even'      => $data['break_even'],
                        'template_action' => 'template',
                        'save_template'   => true,
                        'total_revenue'   => 0,
                        'plus'            => 0,
                        'real_margin'     => 0,
                        'user_id'         => $userId,
                    ]);
                }

                $syncLinesForTemplate($template, $data['items']);
            }

            return redirect()
                ->route('showcase.index')
                ->with('success', in_array($action, ['template', 'both'])
                    ? 'Vetrina aggiornata e modello sincronizzato.'
                    : 'Vetrina aggiornata con successo.');
        });
    }

    /* ------------------------- LOAD TEMPLATE (AJAX) ----------------------- */
    public function getTemplate($id)
    {
        $user        = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;
        $groupUserIds = User::where('created_by', $groupRootId)
            ->pluck('id')
            ->push($groupRootId);

        // Allow loading any template that belongs to the user’s group
        $template = Showcase::with('recipes')
            ->where('id', $id)
            ->where('save_template', true)
            ->whereIn('user_id', $groupUserIds)
            ->firstOrFail();

        $details = $template->recipes->map(fn ($r) => [
            'recipe_id'        => $r->recipe_id,
            'price'            => $r->price,
            'quantity'         => $r->quantity,
            // transactional fields reset for template
            'sold'             => 0,
            'reuse'            => 0,
            'waste'            => 0,
            'potential_income' => $r->potential_income,
            'actual_revenue'   => 0,
        ]);

        return response()->json([
            'showcase_name'    => $template->showcase_name,
            'showcase_date'    => $template->showcase_date->format('Y-m-d'),
            'break_even'       => $template->break_even,
            'template_action'  => 'template',
            'details'          => $details,
        ]);
    }

    /* ----------------------------- EDIT (FORM) ---------------------------- */
    public function edit(Showcase $showcase)
    {
        abort_if($showcase->user_id !== Auth::id(), 403);

        $user        = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;

        $groupUserIds = User::where('created_by', $groupRootId)
            ->pluck('id')
            ->push($groupRootId);

        // ✅ Use the same "sellable" filter as create()
        $recipes = $this->getSellableRecipes($groupUserIds, withIngredients: true)->get();

        $laborCost = LaborCost::where('user_id', $groupRootId)->first();
        $recipes->each(function ($r) use ($laborCost) {
            $rate = $r->labor_cost_mode === 'external'
                ? ($laborCost->external_cost_per_min ?? 0)
                : ($laborCost->shop_cost_per_min     ?? 0);
            $r->batch_labor_cost = round($r->labour_time_min * $rate, 2);
            $r->batch_ing_cost   = $r->ingredients_cost_per_batch;
        });

        $templates = Showcase::where('save_template', true)
            ->whereIn('user_id', $groupUserIds)
            ->get();

        $isEdit = true;
        $showcase->load('recipes');

        return view('frontend.showcase.create', compact(
            'showcase',
            'recipes',
            'laborCost',
            'templates',
            'isEdit'
        ));
    }

    /* ----------------------------- DESTROY -------------------------------- */
    public function destroy(Showcase $showcase)
    {
        abort_if($showcase->user_id !== Auth::id(), 403);

        $showcase->recipes()->delete();
        $showcase->delete();

        return redirect()
            ->route('showcase.index')
            ->with('success', 'Vetrina eliminata con successo.');
    }

    /* -------------------------------- SHOW -------------------------------- */
    public function show(Showcase $showcase)
    {
        $showcase->load([
            'recipes.recipe',
            'recipes.recipe.department',
            'user'
        ]);

        return view('frontend.showcase.show', compact('showcase'));
    }

    /* ---------------------------- HELPERS --------------------------------- */

    /**
     * Build a base query for recipes that are "sellable":
     * - sell_mode = 'kg'    → selling_price_per_kg > 0
     * - sell_mode = 'piece' → selling_price_per_piece > 0
     *
     * @param \Illuminate\Support\Collection|array $groupUserIds
     * @param bool $withIngredients  Whether to eager-load ingredients
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getSellableRecipes($groupUserIds, bool $withIngredients = false)
    {
        $query = Recipe::query()
            ->whereIn('user_id', $groupUserIds)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('sell_mode', 'kg')
                       ->where('selling_price_per_kg', '>', 0);
                })->orWhere(function ($q2) {
                    $q2->where('sell_mode', 'piece')
                       ->where('selling_price_per_piece', '>', 0); // change to selling_price_per_pc if that's your column
                });
            });

        if ($withIngredients) {
            $query->with('ingredients');
        }

        return $query;
    }
}
