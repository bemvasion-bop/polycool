@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-3xl font-semibold mb-6">Payments</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Create Payment Button (Owner + Accounting Only) --}}
    @if(in_array(auth()->user()->system_role, ['owner','accounting']))
        <button onclick="document.getElementById('addPaymentModal').classList.remove('hidden')"
            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 mb-6 inline-block">
            + Add Payment
        </button>

    @endif

    <div class="bg-white p-6 shadow rounded">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border">Project</th>
                        <th class="p-3 border">Amount</th>
                        <th class="p-3 border">Method</th>
                        <th class="p-3 border">Date</th>
                        <th class="p-3 border">Status</th>
                        <th class="p-3 border">Added By</th>
                        <th class="p-3 border">Approved By</th>
                        <th class="p-3 border">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($payments as $payment)
                    <tr class="border-b">
                        <td class="p-3 border">
                            {{ $payment->project->project_name }}
                        </td>

                        <td class="p-3 border">
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>

                        <td class="p-3 border">
                            {{ ucfirst($payment->payment_method) }}
                        </td>

                        <td class="p-3 border">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                        </td>

                        <td class="p-3 border">
                            @if($payment->status === 'pending')
                                <span class="px-2 py-1 bg-yellow-400 text-black rounded text-sm">Pending</span>
                            @elseif($payment->status === 'approved')
                                <span class="px-2 py-1 bg-green-600 text-white rounded text-sm">Approved</span>
                            @elseif($payment->status === 'rejected')
                                <span class="px-2 py-1 bg-red-600 text-white rounded text-sm">Rejected</span>
                            @elseif($payment->status === 'reversed')
                                <span class="px-2 py-1 bg-gray-600 text-white rounded text-sm">Reversed</span>
                            @endif
                        </td>

                        <td class="p-3 border">
                            {{ optional($payment->addedBy)->full_name ?? '—' }}
                        </td>

                        <td class="p-3 border">
                            {{ optional($payment->approvedBy)->full_name ?? '—' }}
                        </td>

                        {{-- ONLY RIGHT SIDE ACTION BUTTON --}}
                        <td class="p-3 border">
                            <a href="{{ route('payments.show', $payment->id) }}"
                            class="text-purple-600 hover:underline">View</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>


        </table>
    </div>

</div>
@endsection
