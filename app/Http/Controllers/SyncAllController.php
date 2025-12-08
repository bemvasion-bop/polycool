<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SyncAllController extends Controller
{
    public function syncAll(Request $request)
    {
        try {
            // Get unsynced data from LOCAL DB (mysql)
            $unsynced = Client::where('sync_status', 'pending')->get();

            if ($unsynced->isEmpty()) {
                return redirect()->back()->with('info', 'Nothing to sync');
            }

            // Start cloud connection
            $cloud = DB::connection('cloud');

            // Sync one by one
            foreach ($unsynced as $item) {

                // Insert to CLOUD DB
                $cloud->table('clients')->insert([
                    'name'          => $item->name,
                    'contact_person'=> $item->contact_person,
                    'email'         => $item->email,
                    'phone'         => $item->phone,
                    'address'       => $item->address,
                    'sync_status'   => 'synced',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                // Update local status
                $item->update([
                    'sync_status' => 'synced',
                ]);
            }

            return redirect()->back()->with('success', 'Successfully synced to Cloud! 🎉');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}
