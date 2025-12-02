@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow p-8 rounded">

    <h2 class="text-2xl font-semibold mb-6">Payment Details</h2>

    <div class="space-y-4">

        <div>
            <p class="text-gray-500">Project</p>
            <p class="text-xl font-semibold">{{ $payment->project->project_name }}</p>
        </div>

        <div>
            <p class="text-gray-500">Amount</p>
            <p class="text-xl font-semibold">₱{{ number_format($payment->amount, 2) }}</p>
        </div>

        <div>
            <p class="text-gray-500">Payment Method</p>
            <p>{{ ucfirst($payment->payment_method) }}</p>
        </div>

        <div>
            <p class="text-gray-500">Date</p>
            <p>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</p>
        </div>

        <div>
            <p class="text-gray-500">Status</p>
            <p class="font-semibold">
                @if($payment->status === 'pending')
                    <span class="px-3 py-1 bg-yellow-400 text-black rounded">Pending</span>
                @elseif($payment->status === 'approved')
                    <span class="px-3 py-1 bg-green-600 text-white rounded">Approved</span>
                @else
                    <span class="px-3 py-1 bg-red-600 text-white rounded">Rejected</span>
                @endif
            </p>
        </div>

        <div>
            <p class="text-gray-500">Added By</p>
            <p>{{ optional($payment->addedBy)->full_name ?? '—' }}</p>
        </div>

        <div>
            <p class="text-gray-500">Approved / Rejected By</p>
            <p>{{ optional($payment->approvedBy)->full_name ?? '—' }}</p>

        </div>

        <hr>

        <div class="flex justify-between mt-4">

            <a href="{{ route('payments.index') }}"
               class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
               Back
            </a>

            <a href="{{ route('payments.pdf', $payment->id) }}"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                Download PDF
            </a>

        </div>


        @if($payment->status !== 'cancelled')
            @can('cancel', $payment)
            <form action="{{ route('payments.cancel', $payment->id) }}" method="POST" class="mb-2">
                @csrf
                <input type="text" name="reason" placeholder="Reason for cancellation" required
                    class="border p-2 rounded">
                <button class="bg-red-500 text-white px-4 py-2 rounded">Cancel Payment</button>
            </form>
            @endcan
        @endif

        @can('reissue', App\Models\Payment::class)
            <form action="{{ route('payments.reissue', $payment->id) }}" method="POST">
                @csrf
                <input type="number" step="0.01" name="amount" placeholder="Corrected Amount" required
                    class="border p-2 rounded">
                <input type="date" name="payment_date" required class="border p-2 rounded">
                <button class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Re-Issue Payment</button>
            </form>
        @endcan


    </div>

</div>
@endsection
