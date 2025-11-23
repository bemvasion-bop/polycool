@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">New Quotation</h2>

            <form action="{{ route('quotations.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Client</label>
                        <select name="client_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" name="title"
                               class="w-full border rounded px-3 py-2"
                               value="{{ old('title') }}">
                        @error('title')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Valid Until</label>
                        <input type="date" name="valid_until"
                               class="w-full border rounded px-3 py-2"
                               value="{{ old('valid_until') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tax Rate (%)</label>
                        <input type="number" step="0.01" min="0" name="tax_rate"
                               class="w-full border rounded px-3 py-2"
                               value="{{ old('tax_rate', 0) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Discount Amount</label>
                        <input type="number" step="0.01" min="0" name="discount_amount"
                               class="w-full border rounded px-3 py-2"
                               value="{{ old('discount_amount', 0) }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Description / Notes</label>
                    <textarea name="description" rows="3"
                              class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
                </div>

                <h3 class="font-semibold mt-6 mb-2">Items</h3>
                @include('quotations._items_form')

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('quotations.index') }}"
                       class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save Quotation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
