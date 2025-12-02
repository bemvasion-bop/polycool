@extends('layouts.app')

@section('content')
<div class="p-10">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Suppliers</h2>

        <a href="{{ route('suppliers.create') }}"
           class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            + Add Supplier
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
                    <th class="p-3 border">Supplier Name</th>
                    <th class="p-3 border">Contact Person</th>
                    <th class="p-3 border">Phone</th>
                    <th class="p-3 border">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($suppliers as $supplier)
                <tr class="border-b">
                    <td class="p-3">{{ $supplier->name }}</td>
                    <td class="p-3">{{ $supplier->contact_person ?? '—' }}</td>
                    <td class="p-3">{{ $supplier->phone ?? '—' }}</td>

                    <td class="p-3">
                        <a href="{{ route('suppliers.show', $supplier) }}"
                            class="text-blue-600 hover:underline">View</a>

                        <a href="{{ route('suppliers.edit', $supplier) }}"
                            class="text-yellow-600 hover:underline mx-3">Edit</a>

                        <form action="{{ route('suppliers.destroy', $supplier) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Delete this supplier?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">No suppliers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
