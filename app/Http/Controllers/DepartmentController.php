<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends Controller
{
    /**
     * Visualizza l'elenco dei reparti per l'utente loggato.
     */
    public function index()
    {
        $user = Auth::user();

        // 1) Gruppo di utenti visibili (te + figli o creatore)
        if (is_null($user->created_by)) {
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Carica reparti di quei user o globali (user_id NULL)
        $departments = Department::with('user')
                            ->where(function($q) use ($visibleUserIds) {
                                $q->whereIn('user_id', $visibleUserIds)
                                  ->orWhereNull('user_id');
                            })
                            ->latest()
                               ->get();

        return view('frontend.departments.index', compact('departments'));
    }

    /**
     * Mostra il form per creare un nuovo reparto.
     */
    public function create()
    {
        return view('frontend.departments.create');
    }

    /**
     * Memorizza un nuovo reparto per l'utente.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data['user_id'] = Auth::id();

        Department::create($data);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Reparto creato con successo!');
    }

    /**
     * Mostra il form per modificare un reparto esistente.
     */
    public function edit(Department $department)
    {
        $user = Auth::user();

        // Autorizzazione: proprietario o ruoli speciali
        if (
            $department->user_id !== $user->id &&
            !$user->hasRole('admin') &&
            !$user->hasRole('super') &&
            !$user->hasRole('master')
        ) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        return view('frontend.departments.create', compact('department'));
    }

    /**
     * Aggiorna il reparto (solo se di proprietà dell'utente).
     */
    public function update(Request $request, Department $department)
    {
        if ($department->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->update($data);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Reparto aggiornato con successo!');
    }

    /**
     * Visualizza i dettagli di un reparto.
     */
    public function show(Department $department)
    {
        return view('frontend.departments.show', compact('department'));
    }

    /**
     * Elimina un reparto (solo se di proprietà dell'utente).
     */
    public function destroy(Department $department)
    {
        if ($department->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Reparto eliminato con successo!');
    }
}
