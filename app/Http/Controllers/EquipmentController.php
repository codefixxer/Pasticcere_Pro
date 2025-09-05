<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the logged-in user’s equipment.
     */
    public function index()
    {
        $user = Auth::user();

        // 1) Build your two-level group of user IDs
        if (is_null($user->created_by)) {
            // Root user: yourself + anyone you created
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            // Child user: yourself + your creator
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Fetch equipment: in-group OR status = 'Default'
        $equipments = Equipment::with('user')
                        ->where(function($q) use ($visibleUserIds) {
                            $q->whereIn('user_id', $visibleUserIds)
                              ->orWhere('status', 'Default');
                        })
                        ->latest()
                           ->get();

        return view('frontend.equipment.index', compact('equipments'));
    }

    /**
     * Show the form for creating a new piece of equipment.
     */
    public function create()
    {
        return view('frontend.equipment.create');
    }

    /**
     * Store a newly created piece of equipment for this user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = Auth::id();

        Equipment::create($data);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Attrezzatura aggiunta con successo!');
    }

    /**
     * Show the form for editing the specified equipment,
     * only if it belongs to the logged-in user.
     */
    public function edit(Equipment $equipment)
    {
        if ($equipment->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('frontend.equipment.create', compact('equipment'));
    }

    /**
     * Update the specified equipment in storage,
     * only if it belongs to the logged-in user.
     */
    public function update(Request $request, Equipment $equipment)
    {
        if ($equipment->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);

        $equipment->update($data);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Attrezzatura aggiornata con successo!');
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment)
    {
        return view('frontend.equipment.show', compact('equipment'));
    }

    /**
     * Remove the specified equipment from storage,
     * only if it belongs to the logged-in user.
     */
    public function destroy(Equipment $equipment)
    {
        if ($equipment->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $equipment->delete();

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Attrezzatura eliminata con successo!');
    }
}
