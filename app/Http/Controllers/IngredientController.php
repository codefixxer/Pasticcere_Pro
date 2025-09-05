<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;               // ← servono anche le ricette

class IngredientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1) Calcolo id utenti visibili
        if (is_null($user->created_by)) {
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Prendo ingredienti + eventuale relazione recipe
        $ingredients = Ingredient::with('recipe')
                                 ->whereIn('user_id', $visibleUserIds)
                                 ->get();

        return view('frontend.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('frontend.ingredients.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // validazione
        $data = $request->validate([
            'ingredient_name' => [
                'required','string','max:255',
                Rule::unique('ingredients')->where(fn($q) =>
                    $q->where('user_id', $user->id)
                      ->where('ingredient_name', $request->ingredient_name)
                ),
            ],
            'price_per_kg'    => 'required|numeric|min:0',
        ]);

        // aggiungo user_id
        $data['user_id'] = $user->id;

        // creo l'ingrediente
        $ingredient = Ingredient::create($data);

        // se è una chiamata AJAX (modal “aggiungi ingrediente”)
        if ($request->expectsJson()) {
            return response()->json($ingredient, 201);
        }

        return back()->with('success', 'Ingrediente salvato con successo.');
    }

    public function show(Ingredient $ingredient)
    {
        abort_unless($ingredient->user_id === Auth::id(), 403);
        return view('frontend.ingredients.show', compact('ingredient'));
    }

    public function edit(Ingredient $ingredient)
    {
        abort_unless($ingredient->user_id === Auth::id(), 403);
        return view('frontend.ingredients.create', compact('ingredient'));
    }

      // ... keep your other actions (index/create/show/edit/store/destroy) ...

    public function update(Request $request, Ingredient $ingredient)
    {
        $user = Auth::user();
        abort_unless($ingredient->user_id === $user->id, 403);

        $data = $request->validate([
            'ingredient_name' => [
                'required','string','max:255',
                Rule::unique('ingredients')
                    ->where(fn($q) => $q->where('user_id', $user->id))
                    ->ignore($ingredient->id),
            ],
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($ingredient, $data, $user) {
            // 1) Update base ingredient
            $ingredient->update($data);

            // 2) If this ingredient is a “recipe-as-ingredient”, sync the recipe’s base €/kg
            if ($ingredient->recipe_id) {
                Recipe::where('id', $ingredient->recipe_id)
                      ->update(['production_cost_per_kg' => $data['price_per_kg']]);
            }

            // 3) Cascade: any recipe that uses THIS ingredient gets recalculated.
            //    If that recipe is also saved as an ingredient, update that ingredient's price
            //    to the recipe's unit_ing_cost, then keep cascading upwards.
            $visited = []; // avoid cycles
            $this->cascadeFromIngredient($ingredient, $visited);
        });

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente aggiornato e costi ricalcolati in cascata!');
    }

    /**
     * Recalculate all recipes that use $ingredient; if any recipe has a linked ingredient row,
     * update that ingredient's price to the recipe's unit_ing_cost, and recurse upward.
     */
    private function cascadeFromIngredient(Ingredient $ingredient, array &$visited): void
    {
        if (isset($visited[$ingredient->id])) {
            return;
        }
        $visited[$ingredient->id] = true;

        // find recipes that include this ingredient
        $recipes = Recipe::whereHas('ingredients', function ($q) use ($ingredient) {
                $q->where('ingredient_id', $ingredient->id);
            })
            ->with(['ingredients.ingredient']) // need all ingredient prices to recalc
            ->get();

        foreach ($recipes as $recipe) {
            // Recalc unit_ing_cost (same logic as in your recipe list)
            $this->recalcAndPersistRecipeUnitIngCost($recipe);

            // If this recipe is also saved as an ingredient, update that ingredient’s price
            // to MATCH the recipe’s unit_ing_cost (your “Costo ingr.” shown in list)
            $linkedIng = Ingredient::where('recipe_id', $recipe->id)
                                   ->where('user_id', $recipe->user_id)
                                   ->first();

            if ($linkedIng) {
                // Important: set ingredient's price to the recipe's unit-ing-cost
                $linkedIng->update(['price_per_kg' => $recipe->unit_ing_cost]);

                // Recurse: recipes that use this newly-updated ingredient must be recalculated too
                $this->cascadeFromIngredient($linkedIng, $visited);
            }
        }
    }

    /**
     * Compute batch ingredient cost and unit_ing_cost exactly like recipe index and persist.
     * - batchIngCost = sum(quantity_g/1000 * ingredient.price_per_kg)
     * - unitIngCost depends on sell_mode:
     *     piece: batchIngCost / total_pieces (>=1)
     *     kg:    batchIngCost / (recipe_weight>0 ? recipe_weight/1000 : sumQty/1000)
     */
    private function recalcAndPersistRecipeUnitIngCost(Recipe $recipe): void
    {
        $recipe->loadMissing('ingredients.ingredient');

        $batchIngCost = 0.0;
        $sumWeightG   = 0.0;

        foreach ($recipe->ingredients as $line) {
            $qtyG    = (float) $line->quantity_g;
            $priceKg = (float) ($line->ingredient->price_per_kg ?? 0);
            $sumWeightG   += $qtyG;
            $batchIngCost += ($qtyG / 1000.0) * $priceKg;
        }
        $batchIngCost = round($batchIngCost, 2);

        if ($recipe->sell_mode === 'piece') {
            $pcs = $recipe->total_pieces > 0 ? $recipe->total_pieces : 1;
            $unitIngCost = round($batchIngCost / $pcs, 2);
        } else {
            $wLossG = (float) ($recipe->recipe_weight ?? 0);
            if ($wLossG <= 0) { $wLossG = $sumWeightG; }
            $kg = $wLossG > 0 ? ($wLossG / 1000.0) : 1;
            $unitIngCost = round($batchIngCost / $kg, 2);
        }

        // Persist both so list & ingredient sync are reliable
        $recipe->update([
            'unit_ing_cost'   => $unitIngCost,
            // keep this if you use it elsewhere:
            // 'batch_ing_cost'   => $batchIngCost,  // only if you have this column
        ]);
    }

    public function destroy(Ingredient $ingredient)
    {
        abort_unless($ingredient->user_id === Auth::id(), 403);

        // se era legato ad una recipe pivot, stacco
        $ingredient->recipes()->detach();
        $ingredient->delete();

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente eliminato con successo!');
    }
}
