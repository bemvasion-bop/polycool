@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">Edit Cash Advance</h2>

<form method="POST" action="{{ route('cashadvance.update', $cashadvance) }}"
      class="bg-white p-6 rounded shadow w-full max-w-xl">
    @csrf @method('PUT')

    <label class="block mb-2 font-medium">Amount</label>
    <input type="number" name="amount" value="{{ $cashadvance->amount }}"
           step="0.01" class="w-full border p-2 rounded mb-4">

    <label class="block mb-2 font-medium">Request Date</label>
    <input type="date" name="request_date" value="{{ $cashadvance->request_date }}"
           class="w-full border p-2 rounded mb-4">

    <label class="block mb-2 font-medium">Notes</label>
    <textarea name="notes" class="w-full border p-2 rounded mb-4">{{ $cashadvance->notes }}</textarea>

    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Update
    </button>
</form>
@endsection
