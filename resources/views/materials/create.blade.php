@extends('layouts.app')

@section('content')
<div class="p-10 max-w-3xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Add Material</h2>

    <form action="{{ route('materials.store') }}" method="POST"
          class="bg-white p-8 rounded-lg shadow space-y-6">
        @csrf

        <div>
            <label class="font-medium">Material Name</label>
            <input type="text" name="name" class="w-full border p-3 rounded" required>
        </div>

        <div>
            <label class="font-medium">Category</label>
            <input type="text" name="category" class="w-full border p-3 rounded">
        </div>

        <div>
            <label class="font-medium">Unit</label>
            <input type="text" name="unit" class="w-full border p-3 rounded" placeholder="pcs, drum, liter">
        </div>

        <div>
            <label class="font-medium">Price per Unit</label>
            <input type="number" step="0.01" name="price_per_unit" class="w-full border p-3 rounded" required>
        </div>

        <div>
            <label class="font-medium">Supplier (optional)</label>
            <select name="supplier_id" class="w-full border p-3 rounded">
                <option value="">— None —</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="font-medium">Notes</label>
            <textarea name="notes" class="w-full border p-3 rounded" rows="3"></textarea>
        </div>

        <button class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Save Material
        </button>


    </form>

</div>
@endsection
