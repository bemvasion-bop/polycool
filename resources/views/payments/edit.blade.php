@extends('layouts.app')

@section('content')
<div class="p-10 max-w-3xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Edit Payment</h2>

    <form action="{{ route('payments.update', $payment->id) }}" method="POST"
          class="bg-white shadow p-8 rounded space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block mb-1 font-medium">Project</label>
            <select name="project_id" class="border p-3 rounded w-full">
                @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ $project->id == $payment->project_id ? 'selected' : '' }}>
                    {{ $project->project_name }} — {{ $project->client->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Amount</label>
            <input type="number" step="0.01" name="amount"
                   value="{{ $payment->amount }}"
                   class="border rounded p-3 w-full">
        </div>

        <div>
            <label class="block mb-1 font-medium">Payment Date</label>
            <input type="date" name="payment_date"
                   value="{{ $payment->payment_date }}"
                   class="border rounded p-3 w-full">
        </div>

        <div>
            <label class="block mb-1 font-medium">Payment Method</label>
            <select name="payment_method" class="border rounded p-3 w-full">
                <option value="cash" {{ $payment->payment_method=='cash'?'selected':'' }}>Cash</option>
                <option value="gcash" {{ $payment->payment_method=='gcash'?'selected':'' }}>GCash</option>
                <option value="bank" {{ $payment->payment_method=='bank'?'selected':'' }}>Bank</option>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Notes</label>
            <textarea name="notes" class="border p-3 rounded w-full">{{ $payment->notes }}</textarea>
        </div>

        <button class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Update Payment
        </button>

    </form>
</div>
@endsection
