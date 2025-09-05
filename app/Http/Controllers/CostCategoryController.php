<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CostCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CostCategoryController extends Controller
{
    /**
     * Visualizza l'elenco delle categorie di costo per l'utente loggato.
     */
    public function index()
    {
        $user = Auth::user();

        // 1) Gruppo di utenti visibili
        if (is_null($user->created_by)) {
            // Utente root → te stesso + eventuali figli diretti
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            // Utente figlio → te stesso + il tuo creatore
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Carica categorie di questi utenti o globali (user_id NULL)
        $categories = CostCategory::with('user')
                         ->where(function($q) use ($visibleUserIds) {
                             $q->whereIn('user_id', $visibleUserIds)
                               ->orWhereNull('user_id');
                         })
                         ->orderBy('name')
                         ->get();

        return view('frontend.categories.index', compact('categories'));
    }

    /**
     * Il create reindirizza all'indice: il form è incluso in index.
     */
    public function create()
    {
        return redirect()->route('cost_categories.index');
    }

    /**
     * Mostra una singola categoria.
     */
    public function show(CostCategory $costCategory)
    {
        return view('frontend.categories.show', compact('costCategory'));
    }

    /**
     * Memorizza una nuova categoria per l'utente.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data['user_id'] = Auth::id();

        CostCategory::create($data);

        return redirect()
            ->route('cost_categories.index')
            ->with('success', 'Categoria aggiunta con successo!');
    }

    /**
     * Mostra il form di modifica (il form è incluso in index).
     */
    public function edit(CostCategory $cost_category)
    {
        if ($cost_category->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        $categories = CostCategory::where('user_id', Auth::id())
                                  ->latest()
                                  ->get();

        return view('frontend.categories.index', [
            'category'   => $cost_category,
            'categories' => $categories,
        ]);
    }

    /**
     * Aggiorna la categoria (solo se di proprietà dell'utente).
     */
    public function update(Request $request, CostCategory $cost_category)
    {
        if ($cost_category->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $cost_category->update($data);

        return redirect()
            ->route('cost_categories.index')
            ->with('success', 'Categoria aggiornata con successo!');
    }

    /**
     * Elimina la categoria (solo se di proprietà dell'utente).
     */
    public function destroy(CostCategory $cost_category)
    {
        if ($cost_category->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        $cost_category->delete();

        return redirect()
            ->route('cost_categories.index')
            ->with('success', 'Categoria eliminata con successo!');
    }
}
