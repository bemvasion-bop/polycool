@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">

    <h2 class="text-2xl font-semibold mb-4">
        Re-Issue Payment
    </h2>

    <p class="text-gray-600 mb-4">
        Correcting cancelled payment of:
        <strong>₱{{ number_format($payment->amount, 2) }}</strong>
    </p>

    <form action="{{ route('payments.reissue', $payment->id) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="font-medium">Amount</label>
            <input type="number" step="0.01" name="amount" required
                   class="w-full border rounded p-2"
                   value="{{ old('amount') }}">
        </div>

        <div class="mb-4">
            <label class="font-medium">Method</label>
            <select name="method" required class="w-full border rounded p-2">
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cheque">Cheque</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="font-medium">Payment Date</label>
            <input type="date" name="payment_date" required
                   class="w-full border rounded p-2"
                   value="{{ now()->toDateString() }}">
        </div>

        <div class="mb-4">
            <label class="font-medium">Notes (Optional)</label>
            <textarea name="notes" class="w-full border rounded p-2"
                      placeholder="Explain the correction…"></textarea>
        </div>

        <button class="bg-indigo-600 text-white px-4 py-2 rounded">
            Re-Issue Payment
        </button>

    </form>

</div>
@endsection
