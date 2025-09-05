<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $categories = IncomeCategory::visibleTo(Auth::user())
            ->orderByRaw('user_id IS NULL DESC') // show global first (NULL comes first)
            ->orderBy('name')
            ->get();

        return view('frontend.income_categories.index', compact('categories'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $ownerId = IncomeCategory::ownerIdFor(Auth::user());

    // 🔹 Check for duplicate name for this owner or global
    $exists = IncomeCategory::where('name', $data['name'])
        ->where(function ($q) use ($ownerId) {
            $q->where('user_id', $ownerId)
              ->orWhereNull('user_id'); // if you also want to block same name as global
        })
        ->exists();

    if ($exists) {
        return back()
            ->withErrors(['name' => 'Questa categoria esiste già.'])
            ->withInput();
    }

    IncomeCategory::create([
        'name'    => $data['name'],
        'user_id' => $ownerId,
    ]);

    return back()->with('success', 'Categoria salvata.');
}

    public function edit(IncomeCategory $income_category)
    {
        // Only allow editing if it belongs to this tenant owner or it's global and you want to allow it.
        // Here: global rows (user_id NULL) are read-only for non-super users.
        if (is_null($income_category->user_id)) {
            abort(Response::HTTP_FORBIDDEN, 'Questa categoria globale non è modificabile.');
        }

        $ownerId = IncomeCategory::ownerIdFor(Auth::user());
        if ((int) $income_category->user_id !== $ownerId) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $categories = IncomeCategory::visibleTo(Auth::user())
            ->orderByRaw('user_id IS NULL DESC')
            ->orderBy('name')
            ->get();

        return view('frontend.income_categories.index', [
            'categories'       => $categories,
            'editingCategory'  => $income_category,
        ]);
    }

  public function update(Request $request, IncomeCategory $income_category)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $ownerId = IncomeCategory::ownerIdFor(Auth::user());

    // Only tenant owner can edit their own category
    if ((int) $income_category->user_id !== $ownerId) {
        abort(Response::HTTP_FORBIDDEN);
    }

    // 🔹 Check for duplicate name excluding this category
    $exists = IncomeCategory::where('name', $data['name'])
        ->where('id', '<>', $income_category->id)
        ->where(function ($q) use ($ownerId) {
            $q->where('user_id', $ownerId)
              ->orWhereNull('user_id');
        })
        ->exists();

    if ($exists) {
        return back()
            ->withErrors(['name' => 'Questa categoria esiste già.'])
            ->withInput();
    }

    $income_category->update(['name' => $data['name']]);

    return back()->with('success', 'Categoria aggiornata.');
}


    public function destroy(IncomeCategory $income_category)
    {
        // Block deleting global categories (user_id NULL)
        if (is_null($income_category->user_id)) {
            abort(Response::HTTP_FORBIDDEN, 'Non puoi eliminare categorie globali.');
        }

        $ownerId = IncomeCategory::ownerIdFor(Auth::user());
        if ((int) $income_category->user_id !== $ownerId) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $income_category->delete();
        return back()->with('success', 'Categoria rimossa.');
    }
}
