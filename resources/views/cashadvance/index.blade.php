@extends('layouts.app')

@section('content')
<div class="p-8">

    <h2 class="text-2xl font-semibold mb-6">Cash Advance Requests</h2>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">Employee</th>
                    <th class="p-3 border">Amount</th>
                    <th class="p-3 border">Reason</th>
                    <th class="p-3 border">Request Date</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($advances as $adv)
                    <tr class="border-b">
                        <td class="p-3">{{ $adv->employee->full_name }}</td>

                        <td class="p-3">₱{{ number_format($adv->amount, 2) }}</td>

                        <td class="p-3">{{ $adv->reason ?? '—' }}</td>

                        <td class="p-3">{{ $adv->request_date }}</td>

                        <td class="p-3">
                            @if($adv->status == 'pending')
                                <span class="px-3 py-1 bg-yellow-300 text-yellow-900 rounded">Pending</span>
                            @elseif($adv->status == 'approved')
                                <span class="px-3 py-1 bg-green-500 text-white rounded">Approved</span>
                            @else
                                <span class="px-3 py-1 bg-red-500 text-white rounded">Rejected</span>
                            @endif
                        </td>

                        <td class="p-3 text-center">

                            {{-- Only show approve/reject if pending --}}
                            @if($adv->status === 'pending')
                                <div class="flex items-center justify-center space-x-2">

                                    {{-- Approve --}}
                                    <form method="POST"
                                          action="{{ route('cashadvance.approve', $adv->id) }}">
                                        @csrf
                                        <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                            Approve
                                        </button>
                                    </form>

                                    {{-- Reject --}}
                                    <form method="POST"
                                          action="{{ route('cashadvance.reject', $adv->id) }}">
                                        @csrf
                                        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                            Reject
                                        </button>
                                    </form>

                                </div>
                            @else
                                <span class="text-gray-500 text-sm">No actions</span>
                            @endif

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">
                            No cash advance requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
