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
            @if($payment->status === 'pending')
                <span class="px-3 py-1 bg-yellow-400 text-black rounded">Pending</span>
            @elseif($payment->status === 'approved')
                <span class="px-3 py-1 bg-green-600 text-white rounded">Approved</span>
            @elseif($payment->status === 'rejected')
                <span class="px-3 py-1 bg-red-600 text-white rounded">Rejected</span>
            @elseif($payment->status === 'reversed')
                <span class="px-3 py-1 bg-gray-600 text-white rounded">Reversed</span>
            @endif
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

        {{-- BACK BUTTON --}}
        <div class="flex justify-between mt-4">
            <a href="{{ route('payments.index') }}"
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Back
            </a>
        </div>


        {{-- SHOW APPROVE / REJECT ONLY FOR PENDING --}}
        @if($payment->status === 'pending'
            && in_array(auth()->user()->system_role, ['owner','accounting']))

            <div class="mt-6 flex gap-3">

                {{-- Approve --}}
                <form action="{{ route('payments.approve', $payment->id) }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Approve Payment
                    </button>
                </form>

                {{-- Reject --}}
                <form action="{{ route('payments.reject', $payment->id) }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Reject Payment
                    </button>
                </form>

            </div>
        @endif

    </div>

</div>
@endsection
