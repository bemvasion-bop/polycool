@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Quotations</h2>

            <a href="{{ route('quotations.create') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                + New Quotation
            </a>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-3 py-2">Reference</th>
                        <th class="px-3 py-2">Client</th>
                        <th class="px-3 py-2">Title</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Total</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($quotations as $quotation)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $quotation->reference }}</td>
                        <td class="px-3 py-2">{{ $quotation->client->name ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $quotation->title }}</td>
                        <td class="px-3 py-2 capitalize">{{ $quotation->status }}</td>
                        <td class="px-3 py-2">₱{{ number_format($quotation->total_amount, 2) }}</td>
                        <td class="px-3 py-2 text-right space-x-2">
                            <a href="{{ route('quotations.show', $quotation) }}"
                               class="text-blue-600 hover:underline text-sm">View</a>
                            <a href="{{ route('quotations.edit', $quotation) }}"
                               class="text-green-600 hover:underline text-sm">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                            No quotations found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $quotations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
