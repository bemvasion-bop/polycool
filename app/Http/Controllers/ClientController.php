<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ALWAYS READ FROM LOCAL MYSQL DB
        $clients = \App\Models\Client::orderBy('id', 'desc')->get();

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('clients.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $client = Client::create([
            'name'          => $request->name,
            'contact_person'=> $request->contact_person,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'sync_status'   => 'pending', // 👈 IMPORTANT!
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client saved locally — pending sync.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
        return view('clients.edit', compact('client'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255'

        ]);

        $client->update($validated);

        return redirect()
            ->route('clients.show', $client)
            ->with('success','Client updated successfully. ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
        $client->delete();

        return redirect()
            ->route('clients.index', $client)
            ->with('success','Client deleted successfully. ');
    }

    public function syncClients() {
    $pending = Client::where('sync_status', 'pending')->get();

    if ($pending->count() == 0) {
        return back()->with('info', 'No pending sync data');
    }

    $syncedCount = 0;

    foreach ($pending as $client) {
        try {
            \DB::connection('cloud')->table('clients')->insert([
                'name' => $client->name,
                'contact_person' => $client->contact_person,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
            ]);

            $client->sync_status = 'synced';
            $client->save();
            $syncedCount++;

        } catch (\Exception $e) {
            \Log::error("SYNC FAILED: " . $e->getMessage());
        }
    }

    return back()->with('success', "Synced {$syncedCount} clients successfully!");
    }

}
