<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PastryChef;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PastryChefController extends Controller
{
    /**
     * Visualizza l’elenco di tutti i pasticceri visibili all’utente.
     */
    public function index()
    {
        $user = Auth::user();

        // 1) Costruisci il gruppo di user_id visibili
        if (is_null($user->created_by)) {
            // ROOT user: me + chi ho creato
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            // CHILD user: me + chi mi ha creato
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Prendi i pasticceri del gruppo o quelli di default globali
        $pastryChefs = PastryChef::with('user')
            ->where(function($q) use ($visibleUserIds) {
                $q->whereIn('user_id', $visibleUserIds);
            })
            ->orWhere(function($q) {
                $q->whereNull('user_id')
                  ->where('status', 'Default');
            })
            ->latest()
            ->get();

        return view('frontend.pastry-chefs.index', compact('pastryChefs'));
    }

    /**
     * Mostra il form per creare un nuovo pasticcere.
     */
    public function create()
    {
        return view('frontend.pastry-chefs.create');
    }

    /**
     * Salva un nuovo pasticcere.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Assegna l’utente corrente
        $data['user_id'] = Auth::id();

        PastryChef::create($data);

        return redirect()
            ->route('pastry-chefs.index')
            ->with('success', 'Pasticcere aggiunto con successo!');
    }

    /**
     * Mostra il form per modificare un pasticcere (solo se di sua proprietà).
     */
    public function edit(PastryChef $pastryChef)
    {
        $user = Auth::user();

        // Permetti al super admin o al creatore
        if (!$user->hasRole('super') && $pastryChef->user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('frontend.pastry-chefs.create', compact('pastryChef'));
    }

    /**
     * Aggiorna un pasticcere esistente.
     */
    public function update(Request $request, PastryChef $pastryChef)
    {
        if ($pastryChef->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $pastryChef->update($data);

        return redirect()
            ->route('pastry-chefs.index')
            ->with('success', 'Pasticcere aggiornato con successo!');
    }

    /**
     * Visualizza i dettagli di un pasticcere.
     */
    public function show(PastryChef $pastryChef)
    {
        return view('frontend.pastry-chefs.show', compact('pastryChef'));
    }

    /**
     * Elimina un pasticcere (solo se di sua proprietà).
     */
    public function destroy(PastryChef $pastryChef)
    {
        if ($pastryChef->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $pastryChef->delete();

        return redirect()
            ->route('pastry-chefs.index')
            ->with('success', 'Pasticcere eliminato con successo!');
    }
}
