@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">My Cash Advance Requests</h2>

<a href="{{ route('cashadvance.my.create') }}"
   class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
   + Request Cash Advance
</a>

<div class="mt-6 bg-white p-6 rounded shadow">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Request Date</th>
                <th class="p-2">Amount</th>
                <th class="p-2">Status</th>
                <th class="p-2">Notes</th>
            </tr>
        </thead>

        <tbody>
            @forelse($requests as $req)
            <tr class="border-t">
                <td class="p-2">{{ $req->request_date }}</td>
                <td class="p-2">₱{{ number_format($req->amount, 2) }}</td>
                <td class="p-2 capitalize">{{ $req->status }}</td>
                <td class="p-2">{{ $req->notes ?? '--' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-gray-500">No requests yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
