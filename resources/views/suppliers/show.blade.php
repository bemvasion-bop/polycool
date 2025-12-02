@extends('layouts.app')

@section('content')
<div class="p-10 max-w-4xl mx-auto">

    <div class="bg-white p-8 rounded-lg shadow">

        <h2 class="text-2xl font-semibold mb-4">{{ $supplier->name }}</h2>

        <p><strong>Contact Person:</strong> {{ $supplier->contact_person ?? '—' }}</p>
        <p><strong>Phone:</strong> {{ $supplier->phone ?? '—' }}</p>
        <p><strong>Email:</strong> {{ $supplier->email ?? '—' }}</p>
        <p><strong>Address:</strong> {{ $supplier->address ?? '—' }}</p>
        <p><strong>Notes:</strong> {{ $supplier->notes ?? '—' }}</p>

        <hr class="my-6">

        <div class="flex space-x-3">
            <a href="{{ route('suppliers.edit', $supplier) }}"
               class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Edit
            </a>

            <form action="{{ route('suppliers.destroy', $supplier) }}"
                  method="POST" onsubmit="return confirm('Delete this supplier?')">
                @csrf @method('DELETE')
                <button class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                </button>
            </form>

            <a href="{{ route('suppliers.index') }}"
               class="px-5 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Back
            </a>
        </div>

    </div>

</div>
@endsection
