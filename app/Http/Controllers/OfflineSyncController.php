<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class OfflineSyncController extends Controller
{
    public function process(Request $request)
    {
        $item = $request->all();

        if (($item['action'] ?? null) === 'create_client') {

            $client = Client::create($item['data']);
            return response()->json([
                'status' => 'success',
                'id' => $client->id,
                'redirect' => route('clients.show', $client->id)
            ]);
        }

        return response()->json(['status' => 'ignored']);
    }
}
