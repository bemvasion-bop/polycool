<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search'));

        $clients = Client::orderBy('name')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->get();

        return view('clients.index', compact('clients', 'search'));
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
    public function store(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'project_name'   => 'required|string|max:255',
            'scope_of_work'  => 'nullable|string',
            'duration_days'  => 'nullable|numeric|min:1',
            'rate_per_bdft'  => 'required|numeric|min:0',
            'discount'       => 'required|numeric|min:0',
            'contract_price' => 'required|numeric|min:0',
            'down_payment'   => 'nullable|numeric|min:0',
            'balance'        => 'required|numeric|min:0',
        ]);

        $quotation = Quotation::create($validated + [
            'status' => 'pending',
            'sync_status' => 'pending',
        ]);

        return redirect()
            ->route('quotations.show', $quotation)
            ->with('success', 'Quotation created successfully.');
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
            'name' => 'required|string|max:255|unique:clients,name,' . ($client->id ?? 'NULL'),
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

    public function syncClients()
    {

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
