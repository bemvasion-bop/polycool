@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Add Expense</h2>

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white shadow p-8 rounded-lg">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="space-y-4">
                <div>
                    <label class="font-medium">Project</label>
                    <select name="project_id" class="w-full border rounded p-2" required>
                        <option value="">Select project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-medium">Category</label>
                    <input type="text" name="category" class="w-full border rounded p-2" required>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="font-medium">Amount</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="font-medium">Date</label>
                    <input type="date" name="expense_date" class="w-full border rounded p-2" required>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="font-medium">Receipt Image (Optional)</label>
                    <input type="file" name="receipt" accept="image/*" class="w-full">
                </div>

                <div>
                    <label class="font-medium">Description</label>
                    <textarea name="description" rows="4" class="w-full border rounded p-2"></textarea>
                </div>
            </div>

        </div>

        <div class="mt-8">
            <button class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded">
                Save Expense
            </button>
        </div>





    </form>
</div>
@endsection
