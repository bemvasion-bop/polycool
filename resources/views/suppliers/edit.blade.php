@extends('layouts.app')

@section('content')
<div class="p-10 max-w-3xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Edit Supplier</h2>

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST"
          class="bg-white p-8 rounded-lg shadow space-y-6">
        @csrf @method('PUT')

        <div>
            <label class="font-medium">Supplier Name</label>
            <input type="text" name="name" value="{{ $supplier->name }}"
                class="w-full border p-3 rounded" required>
        </div>

        <div>
            <label class="font-medium">Contact Person</label>
            <input type="text" name="contact_person" value="{{ $supplier->contact_person }}"
                class="w-full border p-3 rounded">
        </div>

        <div>
            <label class="font-medium">Phone</label>
            <input type="text" name="phone" value="{{ $supplier->phone }}"
                class="w-full border p-3 rounded">
        </div>

        <div>
            <label class="font-medium">Email</label>
            <input type="email" name="email" value="{{ $supplier->email }}"
                class="w-full border p-3 rounded">
        </div>

        <div>
            <label class="font-medium">Address</label>
            <input type="text" name="address" value="{{ $supplier->address }}"
                class="w-full border p-3 rounded">
        </div>

        <div>
            <label class="font-medium">Notes</label>
            <textarea name="notes" class="w-full border p-3 rounded" rows="3">{{ $supplier->notes }}</textarea>
        </div>

        <button class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Update Supplier
        </button>
    </form>

</div>
@endsection
