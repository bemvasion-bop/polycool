@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-8 rounded shadow">

    <h1 class="text-2xl font-bold mb-6">Quotations</h1>

    <a href="{{ route('quotations.create') }}"
       class="bg-purple-600 text-white px-4 py-2 rounded mb-4 inline-block">
        + New Quotation
    </a>

    <table class="w-full border-collapse mt-4">
        <thead>
            <tr class="bg-gray-100 text-left border-b">
                <th class="p-3">#</th>
                <th class="p-3">Client</th>
                <th class="p-3">Project / Vessel</th>
                <th class="p-3">Date</th>
                <th class="p-3">Contract Price</th>
                <th class="p-3">Status</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($quotations as $index => $q)
                <tr class="border-b">
                    <td class="p-3">{{ $index + 1 }}</td>

                    {{-- Client --}}
                    <td class="p-3">{{ $q->client->name }}</td>

                    {{-- Project Name --}}
                    <td class="p-3">{{ $q->project_name }}</td>

                    {{-- Date --}}
                    <td class="p-3">
                        {{ \Carbon\Carbon::parse($q->quotation_date)->format('M d, Y') }}
                    </td>

                    {{-- Contract Price --}}
                    <td class="p-3">₱{{ number_format($q->contract_price, 2) }}</td>

                    {{-- Status --}}
                    <td class="p-3">
                        @if ($q->status === 'pending')
                            <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded">Pending</span>
                        @elseif ($q->status === 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Approved</span>
                        @elseif ($q->status === 'declined')
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded">Declined</span>
                        @elseif ($q->status === 'converted')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">Converted</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="p-3 flex gap-3">

                        {{-- View --}}
                        <a href="{{ route('quotations.show', $q->id) }}"
                           class="text-blue-600 hover:underline">
                            View
                        </a>


                        {{-- 

                        // Delete \\ 

                        <form action="{{ route('quotations.destroy', $q->id) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this quotation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">
                                Delete
                            </button>
                        </form>

                         --}}

                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        No quotations found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
