<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;

class ManageRecipeController extends Controller
{
    /**
     * Display the manage recipe form.
     *
     * @param  int  $id  Recipe ID.
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        return view('frontend.managerecipes');
    }

    /**
     * Update a recipe with calculated totals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Recipe ID.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'display_quantity' => 'required|numeric',
            'sold_pieces'      => 'required|numeric',
            'sold_kg'          => 'required|numeric',
            'waste_pieces'     => 'required|numeric',
            'waste_kg'         => 'required|numeric',
        ]);

        $recipe = Recipe::findOrFail($id);

        // piece weight in grams → kg
        $pieceWeightKg = $recipe->piece_weight / 1000;

        // inputs
        $soldPieces    = $request->input('sold_pieces');
        $soldKgManual  = $request->input('sold_kg');
        $wastePieces   = $request->input('waste_pieces');
        $wasteKgManual = $request->input('waste_kg');

        // compute totals
        $computedSoldKg  = ($soldPieces * $pieceWeightKg) + $soldKgManual;
        $computedWasteKg = ($wastePieces * $pieceWeightKg) + $wasteKgManual;
        $totalUsedKg     = $computedSoldKg + $computedWasteKg;
        $reuseTotalKg    = $recipe->recipe_weight - $totalUsedKg;

        // apply updates
        $recipe->display_quantity  = $request->input('display_quantity');
        $recipe->sold_pieces       = $soldPieces;
        $recipe->sold_kg           = $soldKgManual;
        $recipe->total_sold_kg     = round($computedSoldKg, 2);
        $recipe->waste_pieces      = $wastePieces;
        $recipe->waste_kg          = $wasteKgManual;
        $recipe->total_waste_kg    = round($computedWasteKg, 2);
        $recipe->reuse_total_kg    = round($reuseTotalKg, 2);

        // stamp with the current user's ID
        $recipe->user_id = Auth::id();

        $recipe->save();

        return redirect()->back()->with('status', 'Recipe updated successfully!');
    }
}
