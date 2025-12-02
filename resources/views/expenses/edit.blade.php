@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Edit Expense</h2>

    <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data"
          class="bg-white shadow p-8 rounded-lg">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="space-y-4">
                <div>
                    <label class="font-medium">Project</label>
                    <select name="project_id" class="w-full border rounded p-2" required>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}"
                                {{ $project->id == $expense->project_id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-medium">Category</label>
                    <input type="text" name="category" class="w-full border rounded p-2"
                           value="{{ $expense->category }}" required>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="font-medium">Amount</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded p-2"
                           value="{{ $expense->amount }}" required>
                </div>

                <div>
                    <label class="font-medium">Date</label>
                    <input type="date" name="expense_date" class="w-full border rounded p-2"
                           value="{{ $expense->expense_date }}" required>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="font-medium">Receipt Image (Optional)</label>
                    <input type="file" name="receipt" accept="image/*" class="w-full">
                    @if($expense->receipt_path)
                        <p class="mt-2 text-sm text-gray-500">Current: {{ $expense->receipt_path }}</p>
                    @endif
                </div>

                <div>
                    <label class="font-medium">Description</label>
                    <textarea name="description" rows="4" class="w-full border rounded p-2">{{ $expense->description }}</textarea>
                </div>
            </div>

        </div>

        <div class="mt-8">
            <button class="px-6 py-3 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Save Changes
            </button>
        </div>

    </form>
</div>
@endsection
