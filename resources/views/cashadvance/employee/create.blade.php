@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">Request Cash Advance</h2>

<form action="{{ route('cashadvance.my.store') }}" method="POST"
      class="bg-white p-6 rounded shadow w-full max-w-xl">
    @csrf

    <label class="block mb-2 font-medium">Request Date</label>
    <input type="date" name="request_date" class="w-full border p-2 rounded mb-4" required>

    <label class="block mb-2 font-medium">Amount</label>
    <input type="number" name="amount" step="0.01"
           class="w-full border p-2 rounded mb-4" required>

    <label class="block mb-2 font-medium">Reason (Optional)</label>
    <textarea name="reason" class="w-full border p-2 rounded mb-4"></textarea>

    <button class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
        Submit Request
    </button>
</form>
@endsection
