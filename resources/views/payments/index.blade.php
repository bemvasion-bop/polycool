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
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-3 border">Project</th>
                    <th class="p-3 border">Amount</th>
                    <th class="p-3 border">Method</th>
                    <th class="p-3 border">Date</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">Added By</th>
                    <th class="p-3 border">Approved By</th>
                    <th class="p-3 border w-48">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                <tr class="border-b">

                    <td class="p-3">{{ $payment->project->project_name ?? '—' }}</td>

                    <td class="p-3">₱{{ number_format($payment->amount, 2) }}</td>

                    <td class="p-3">{{ ucfirst($payment->payment_method) }}</td>

                    <td class="p-3">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>

                    {{-- Status Badge --}}
                    <td class="p-3">
                        @if($payment->status === 'pending')
                            <span class="px-3 py-1 bg-yellow-400 text-black rounded">Pending</span>
                        @elseif($payment->status === 'approved')
                            <span class="px-3 py-1 bg-green-600 text-white rounded">Approved</span>
                        @else
                            <span class="px-3 py-1 bg-red-500 text-white rounded">Rejected</span>
                        @endif
                    </td>

                    <td class="p-3">
                        {{ $payment->addedBy->full_name ?? '—' }}
                    </td>

                    <td class="p-3">
                        {{ $payment->approvedBy->full_name ?? '—' }}
                    </td>

                    {{-- Actions --}}
                    <td class="p-3 flex space-x-4">

                    {{-- VIEW --}}
                    <a href="{{ route('payments.show', $payment->id) }}" class="text-blue-600 hover:underline">
                        View
                    </a>

                    {{-- APPROVE / REJECT (Accounting Only) --}}
                    @if($payment->status === 'approved')
                        <span class="px-3 py-1 text-sm bg-green-500 text-white rounded">Approved</span>
                    @elseif($payment->status === 'cancelled')
                        <span class="px-3 py-1 text-sm bg-red-500 text-white rounded">Cancelled</span>
                    @else
                        <span class="px-3 py-1 text-sm bg-gray-300 rounded">Pending</span>
                    @endif

                    {{-- PRINT PDF (Owner/Accounting/Audit only)
                    @if(in_array(auth()->user()->system_role, ['owner','accounting','audit']))
                        <a href="{{ route('payments.pdf', $payment->id) }}" class="text-purple-700 ml-4">Print</a>
                    @endif
                     --}}


                    @if($payment->status === 'cancelled')
                        <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded">Cancelled</span>
                    @endif
                </td>


                </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 p-4">
                            No payments recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
@endsection
