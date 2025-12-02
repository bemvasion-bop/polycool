@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">New Cash Advance</h2>

<form method="POST" action="{{ route('cashadvance.store') }}"
      class="bg-white p-6 rounded shadow w-full max-w-xl">
    @csrf

    <label class="block mb-2 font-medium">Employee</label>
    <select name="user_id" class="w-full border p-2 rounded mb-4">
        @foreach($employees as $emp)
            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
        @endforeach
    </select>

    <label class="block mb-2 font-medium">Request Date</label>
    <input type="date" name="request_date" class="w-full border p-2 rounded mb-4">

    <label class="block mb-2 font-medium">Amount</label>
    <input type="number" name="amount" step="0.01" class="w-full border p-2 rounded mb-4">

    <label class="block mb-2 font-medium">Reason (Optional)</label>
    <textarea name="reason" class="w-full border p-2 rounded mb-4"></textarea>

    <button class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
        Save Cash Advance
    </button>
</form>
@endsection
