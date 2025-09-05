<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the logged-in user’s clients.
     */
    public function index()
    {
        $user = Auth::user();
    
        // 1) Build your two-level group of user IDs
        if (is_null($user->created_by)) {
            // Root user → see yourself + anyone you created
            $visibleUserIds = \App\Models\User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            // Child user → see yourself + your creator
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }
    
        // 2) Fetch clients owned by those users OR global clients (user_id IS NULL)
$clients = Client::with('user')
    ->where(function ($q) use ($visibleUserIds) {
        $q->whereIn('user_id', $visibleUserIds)
          ->orWhereNull('user_id');
    })
    ->latest()
    ->get(); // ✅ no more paginate()

    
        return view('frontend.clients.index', compact('clients'));
    }
    
    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('frontend.clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:50',
            'email'    => 'nullable|email|max:255',
            'notes'    => 'nullable|string',
        ]);

        // stamp with the current user's ID
        $data['user_id'] = Auth::id();

        Client::create($data);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente creato con successo.');
    }

    /**
     * Display the specified client (only if it belongs to the user).
     */
    public function show(Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        return view('frontend.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        return view('frontend.clients.create', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:50',
            'email'    => 'nullable|email|max:255',
            'notes'    => 'nullable|string',
        ]);

        $client->update($data);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente aggiornato con successo.');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Operazione non autorizzata.');
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente eliminato con successo.');
    }
}
