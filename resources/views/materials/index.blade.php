@extends('layouts.app')

@section('content')
<div class="p-10">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Materials</h2>

        <a href="{{ route('materials.create') }}"
           class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            + Add Material
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">Material</th>
                    <th class="p-3 border">Category</th>
                    <th class="p-3 border">Unit</th>
                    <th class="p-3 border">Price/Unit</th>
                    <th class="p-3 border">Supplier</th>
                    <th class="p-3 border">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($materials as $material)
                <tr class="border-b">
                    <td class="p-3">{{ $material->name }}</td>
                    <td class="p-3">{{ $material->category ?? '—' }}</td>
                    <td class="p-3">{{ $material->unit ?? '—' }}</td>
                    <td class="p-3">₱{{ number_format($material->price_per_unit, 2) }}</td>
                    <td class="p-3">{{ $material->supplier->name ?? '—' }}</td>

                    <td class="p-3">
                        <a href="{{ route('materials.show', $material) }}"
                            class="text-blue-600 hover:underline">View</a>

                        <a href="{{ route('materials.edit', $material) }}"
                            class="text-yellow-600 hover:underline mx-3">Edit</a>

                        <form action="{{ route('materials.destroy', $material) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Delete this material?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">No materials found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
