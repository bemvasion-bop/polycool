@extends('layouts.app')

@section('content')
<div class="p-10 max-w-3xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Add Payment</h2>

    <form action="{{ route('payments.store') }}" method="POST"
          class="bg-white shadow p-8 rounded space-y-6">
        @csrf

        <div>
            <label class="block mb-1 font-medium">Project</label>
            <select name="project_id" class="border p-3 rounded w-full">
                @foreach($projects as $project)
                <option value="{{ $project->id }}">
                    {{ $project->project_name }} — {{ $project->client->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Amount</label>
            <input type="number" step="0.01" name="amount"
                   class="border rounded p-3 w-full">
        </div>

        <div>
            <label class="block mb-1 font-medium">Payment Date</label>
            <input type="date" name="payment_date"
                   class="border rounded p-3 w-full">
        </div>

        <div>
            <label class="block mb-1 font-medium">Payment Method</label>
            <select name="payment_method" class="border rounded p-3 w-full">
                <option value="cash">Cash</option>
                <option value="gcash">GCash</option>
                <option value="bank">Bank Transfer</option>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Notes (Optional)</label>
            <textarea name="notes" class="border p-3 rounded w-full"></textarea>
        </div>

        <button class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Save Payment
        </button>

    </form>
</div>
@endsection
