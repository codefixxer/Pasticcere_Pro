<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RecipeCategory;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RecipeCategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1) Build the “group” of IDs you should see
        if (is_null($user->created_by)) {
            // You’re a root user: see yourself + anyone you created
            $visibleUserIds = \App\Models\User::where('created_by', $user->id)
                ->pluck('id')
                ->push($user->id)
                ->unique();
        } else {
            // You’re a child: see yourself + your creator
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Fetch categories in your group OR with status = 'Default'
        $categories = RecipeCategory::with('user')
            ->where(function ($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds)
                    ->orWhere('status', 'Default');
            })
            ->orderBy('name')
            ->get();

        return view('frontend.recipe_categories.index', compact('categories'));
    }

    /**
     * Store a new category for this user.
     */


public function store(Request $request)
{
    $user = Auth::user();

    if (is_null($user->created_by)) {
        $visibleUserIds = User::where('created_by', $user->id)
            ->pluck('id')
            ->push($user->id)
            ->unique();
    } else {
        $visibleUserIds = collect([$user->id, $user->created_by])->unique();
    }

    $data = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('recipe_categories')
                ->where(function ($query) use ($visibleUserIds, $request) {
                    return $query->whereIn('user_id', $visibleUserIds)
                                 ->where('name', $request->name);
                }),
        ],
    ]);

    $data['user_id'] = $user->id;

    RecipeCategory::create($data);

    return back()->with('success', 'Categoria aggiunta con successo!');
}

public function update(Request $request, RecipeCategory $recipeCategory)
{
    $user = Auth::user();

    // guard: only own records
    if ($recipeCategory->user_id !== $user->id) {
        abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata');
    }

    if (is_null($user->created_by)) {
        $visibleUserIds = User::where('created_by', $user->id)
            ->pluck('id')
            ->push($user->id)
            ->unique();
    } else {
        $visibleUserIds = collect([$user->id, $user->created_by])->unique();
    }

    $data = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('recipe_categories')
                ->where(function ($query) use ($visibleUserIds, $request, $recipeCategory) {
                    return $query->whereIn('user_id', $visibleUserIds)
                                 ->where('name', $request->name);
                })
                ->ignore($recipeCategory->id),
        ],
    ]);

    $recipeCategory->update([
        'name' => $data['name'],
        // user_id unchanged
    ]);

    return redirect()
        ->route('recipe-categories.index')
        ->with('success', 'Categoria aggiornata con successo!');
}


    /**
     * Show the edit form (same view, but with $category pre-filled).
     */
    public function edit(RecipeCategory $recipeCategory)
    {
        $userId = Auth::id();

        // guard: only own records
        if ($recipeCategory->user_id !== $userId) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata');
        }

        // only this user’s categories
        $categories = RecipeCategory::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        return view('frontend.recipe_categories.create', [
            'category'   => $recipeCategory,
            'categories' => $categories,
        ]);
    }

    /**
     * Update an existing category, scoped to the user.
     */

    /**
     * Display a single category.
     */
    public function show(RecipeCategory $recipeCategory)
    {
        return view('frontend.recipe_categories.show', compact('recipeCategory'));
    }

    /**
     * Delete a category—only if it belongs to the user.
     */
    public function destroy(RecipeCategory $recipeCategory)
    {
        $userId = Auth::id();

        // guard: only own records
        if ($recipeCategory->user_id !== $userId) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata');
        }

        $recipeCategory->delete();

        return back()->with('success', 'Categoria eliminata con successo!');
    }
}
