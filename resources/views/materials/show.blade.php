@extends('layouts.app')

@section('content')
<div class="p-10 max-w-4xl mx-auto">

    <div class="bg-white p-8 rounded-lg shadow">

        <h2 class="text-2xl font-semibold mb-4">{{ $material->name }}</h2>

        <p><strong>Category:</strong> {{ $material->category ?? '—' }}</p>
        <p><strong>Unit:</strong> {{ $material->unit ?? '—' }}</p>
        <p><strong>Price per Unit:</strong> ₱{{ number_format($material->price_per_unit, 2) }}</p>

        <p><strong>Supplier:</strong>
            {{ $material->supplier->name ?? '—' }}
        </p>

        <p><strong>Notes:</strong> {{ $material->notes ?? '—' }}</p>

        <hr class="my-6">

        <div class="flex space-x-3">
            <a href="{{ route('materials.edit', $material) }}"
               class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Edit
            </a>

            <form action="{{ route('materials.destroy', $material) }}"
                  method="POST"
                  onsubmit="return confirm('Delete this material?')">
                @csrf @method('DELETE')
                <button class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                </button>
            </form>

            <a href="{{ route('materials.index') }}"
               class="px-5 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Back
            </a>
        </div>

    </div>

</div>
@endsection
