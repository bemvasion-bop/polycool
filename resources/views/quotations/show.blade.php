@extends('layouts.app')

@section('content')
<div class="p-10">
    <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-8">

        <h2 class="text-2xl font-semibold mb-4">Quotation Details</h2>

        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-sm text-gray-500">
                    Quotation Date: {{ $quotation->quotation_date->format('F d, Y') }}
                </p>

                <p class="text-sm">
                    Status:
                    @if ($quotation->status === 'pending')
                        <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-sm">Pending</span>
                    @elseif ($quotation->status === 'approved')
                        <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-sm">Approved</span>
                    @elseif ($quotation->status === 'declined')
                        <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-sm">Declined</span>
                    @elseif ($quotation->status === 'converted')
                        <span class="bg-purple-200 text-purple-800 px-2 py-1 rounded text-sm">Converted to Project</span>
                    @endif
                </p>
            </div>

            <img src="/logo.png" alt="Logo" class="h-10">
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex space-x-4 mb-6">

            {{-- Back --}}
            <a href="{{ route('quotations.index') }}"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Back
            </a>

            {{-- Edit --}}
            @if ($quotation->status !== 'converted')
            <a href="{{ route('quotations.edit', $quotation->id) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit
            </a>
            @endif

            {{-- Approve --}}
            @if ($quotation->status === 'pending')
            <form action="{{ route('quotations.approve', $quotation->id) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Approve
                </button>
            </form>
            @endif

            {{-- Decline --}}
            @if ($quotation->status === 'pending')
            <form action="{{ route('quotations.decline', $quotation->id) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Decline
                </button>
            </form>
            @endif

            {{-- Convert to Project (ONLY when approved) --}}
            @if ($quotation->status === 'approved')
            <form action="{{ route('quotations.convert-to-project', $quotation->id) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    Convert to Project
                </button>
            </form>
            @endif

        </div>

        {{-- CLIENT INFORMATION --}}
        <h3 class="text-xl font-semibold mb-2">Client Information</h3>
        <p><strong>Client Name:</strong> {{ $quotation->client->name }}</p>
        <p><strong>Address:</strong> {{ $quotation->address }}</p>

        <hr class="my-6">

        {{-- PROJECT INFORMATION --}}
        <h3 class="text-xl font-semibold mb-2">Project Information</h3>
        <p><strong>Project / Vessel Name:</strong> {{ $quotation->project_name }}</p>
        <p><strong>System:</strong> {{ $quotation->system }}</p>
        <p><strong>Duration:</strong> {{ $quotation->duration_days }} day(s)</p>

        <p class="mt-3"><strong>Scope of Work</strong></p>
        <p>{{ $quotation->scope_of_work }}</p>

        <hr class="my-6">

        {{-- PARTICULARS TABLE --}}
        <h3 class="text-xl font-semibold mb-2">Particulars</h3>

        <table class="w-full border text-left mb-6">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">Substrate</th>
                    <th class="p-3 border">Thickness</th>
                    <th class="p-3 border">Volume (bd.ft)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quotation->items as $item)
                <tr>
                    <td class="p-3 border">{{ $item->substrate }}</td>
                    <td class="p-3 border">{{ $item->thickness }}</td>
                    <td class="p-3 border">{{ number_format($item->volume, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- COSTING SUMMARY --}}
        <h3 class="text-xl font-semibold mb-2">Costing Summary</h3>

        <div class="grid grid-cols-2 gap-6">

            <div>
                <p><strong>Total Bd.Ft:</strong> {{ number_format($quotation->total_bdft, 2) }}</p>
                <p><strong>Discount:</strong> ₱{{ number_format($quotation->discount, 2) }}</p>
                <p><strong>Down Payment:</strong> ₱{{ number_format($quotation->down_payment, 2) }}</p>
            </div>

            <div>
                <p><strong>Rate per Bd.Ft:</strong> ₱{{ number_format($quotation->rate_per_bdft, 2) }}</p>
                <p><strong>Contract Price:</strong>
                    <span class="text-green-600 font-bold">₱{{ number_format($quotation->contract_price, 2) }}</span>
                </p>
                <p><strong>Balance:</strong> ₱{{ number_format($quotation->balance, 2) }}</p>
            </div>
        </div>

        <hr class="my-6">

        {{-- CONDITIONS --}}
        <h3 class="text-xl font-semibold mb-2">Terms & Conditions</h3>
        <p class="whitespace-pre-line text-gray-700">
            {{ $quotation->conditions }}
        </p>

    </div>
</div>
@endsection
