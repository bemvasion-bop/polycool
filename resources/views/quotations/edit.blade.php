@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Edit Quotation</h2>

            <form action="{{ route('quotations.update', $quotation) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- same fields as create, but value="{{ old('...', $quotation->...) }}" --}}
                {{-- ... --}}
                {{-- reuse the same layout but with $quotation --}}
                {{-- You can basically copy from create and replace old() defaults with $quotation --}}

                @include('quotations._items_form')

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('quotations.show', $quotation) }}"
                       class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update Quotation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
