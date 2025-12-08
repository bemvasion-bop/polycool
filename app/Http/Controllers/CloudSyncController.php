<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class CloudSyncController extends Controller
{
    public function syncAll()
    {
        try {
            // SELECT PENDING CLIENTS ONLY
            $pending = Client::where('sync_status', 'pending')->get();

            if ($pending->isEmpty()) {
                return back()->with('success', 'No pending clients to sync.');
            }

            foreach ($pending as $client) {
                DB::connection('mysql_cloud')
                    ->table('clients')
                    ->updateOrInsert(
                        ['id' => $client->id],
                        [
                            'name' => $client->name,
                            'contact_person' => $client->contact_person,
                            'email' => $client->email,
                            'created_at' => $client->created_at,
                            'updated_at' => now(),
                        ]
                    );

                // UPDATE LOCAL STATUS
                $client->update(['sync_status' => 'synced']);
            }

            return back()->with('success', 'Client Sync to Cloud Complete! 🎉');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
