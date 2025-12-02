@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <h2 class="text-xl font-semibold mb-4">My Cash Advance Requests</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Amount</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Reason</th>
                </tr>
            </thead>

            <tbody>
                @forelse($requests as $req)
                    <tr class="border-t">
                        <td class="p-3">{{ $req->request_date }}</td>
                        <td class="p-3">₱{{ number_format($req->amount,2) }}</td>
                        <td class="p-3">
                            @if($req->status === 'pending')
                                <span class="text-yellow-600 font-semibold">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="text-green-600 font-semibold">Approved</span>
                            @else
                                <span class="text-red-600 font-semibold">Rejected</span>
                            @endif
                        </td>
                        <td class="p-3">{{ $req->reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-3 text-center text-gray-500">
                            No requests submitted yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
