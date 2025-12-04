@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">Re-Issue Payment</h2>

    <form action="{{ route('payments.reissue', $payment->id) }}" method="POST">
        @csrf

        <label>New Amount</label>
        <input type="number" step="0.01" name="amount" class="w-full border p-2 rounded mb-3" required>

        <label>Payment Method</label>
        <input type="text" name="method" class="w-full border p-2 rounded mb-3" required>

        <label>Payment Date</label>
        <input type="date" name="payment_date" class="w-full border p-2 rounded mb-3" required>

        <label>Reason for Correction</label>
        <textarea name="correction_reason" class="w-full border p-2 rounded mb-3" required></textarea>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
    </form>

</div>
@endsection
