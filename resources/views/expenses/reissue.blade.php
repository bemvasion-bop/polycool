@extends('layouts.app')

@section('content')
<div class="p-8">
    <h2 class="text-xl font-semibold mb-4">Re-Issue Expense</h2>

    <form action="{{ route('expenses.reissue', $expense->id) }}" method="POST">
        @csrf
        <label class="block mb-2 text-sm">Corrected Amount</label>
        <input type="number" name="amount" step="0.01" class="border p-2 w-64 mb-4" required>

        <label class="block mb-2 text-sm">Notes</label>
        <input type="text" name="notes" class="border p-2 w-64 mb-4">

        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded">
            Save Re-Issued Expense
        </button>
    </form>
</div>
@endsection
