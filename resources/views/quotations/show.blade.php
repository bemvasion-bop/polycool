@extends('layouts.app')

@section('page-header')

{{-- iOS Back Button --}}
<a href="{{ route('quotations.index') }}" class="ios-back-btn mb-4 inline-flex">
    <i data-lucide="chevron-left" class="w-4 h-4"></i>
    <span>Back</span>
</a>

<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Quotation Details
</h2>

@endsection



@section('content')

{{-- ================================ --}}
{{-- iOS Back Button Styling --}}
{{-- ================================ --}}
<style>
    .ios-back-btn {
        padding: 8px 16px;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.65);
        background: rgba(255,255,255,0.45);
        backdrop-filter: blur(12px);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: .2s ease;
    }
    .ios-back-btn:hover {
        background: rgba(255,255,255,0.75);
        transform: translateY(-1px);
    }
</style>


<div class="p-10">
    <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-8">


        {{-- ================================ --}}
        {{-- HEADER INFO --}}
        {{-- ================================ --}}
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



        {{-- ================================ --}}
        {{-- ACTION BUTTONS --}}
        {{-- ================================ --}}
        <div class="flex space-x-4 mb-6">

            {{-- Edit --}}
            @if ($quotation->status !== 'converted')
            <a href="{{ route('quotations.edit', $quotation->id) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit
            </a>
            @endif

            {{-- Approve and Decline--}}
            @if($quotation->status == 'pending')
                <form action="{{ route('quotations.approve', $quotation->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="px-3 py-1 bg-green-600 text-white rounded text-sm">Approve</button>
                </form>

                <form action="{{ route('quotations.decline', $quotation->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="px-3 py-1 bg-red-600 text-white rounded text-sm">Decline</button>
                </form>
            @endif


            {{-- Convert to Project --}}
            @if ($quotation->status === 'approved')
            <form action="{{ route('quotations.convert-to-project', $quotation->id) }}" method="POST">
                @csrf
                <button
                    onclick="
                    if(!navigator.onLine){
                        saveOffline('convert_project', {quotation_id: {{ $quotation->id }}});
                        event.preventDefault();
                    }">
                    Convert to Project
                </button>
            </form>
            @endif

        </div>



        {{-- ================================ --}}
        {{-- CLIENT INFO --}}
        {{-- ================================ --}}
        <h3 class="text-xl font-semibold mb-2">Client Information</h3>
        <p><strong>Client Name:</strong> {{ $quotation->client->name }}</p>
        <p><strong>Address:</strong> {{ $quotation->address }}</p>

        <hr class="my-6">


        {{-- ================================ --}}
        {{-- PROJECT INFO --}}
        {{-- ================================ --}}
        <h3 class="text-xl font-semibold mb-2">Project Information</h3>
        <p><strong>Project / Vessel Name:</strong> {{ $quotation->project_name }}</p>
        <p><strong>System:</strong> {{ $quotation->system }}</p>
        <p><strong>Duration:</strong> {{ $quotation->duration_days }} day(s)</p>

        <p class="mt-3"><strong>Scope of Work</strong></p>
        <p>{{ $quotation->scope_of_work }}</p>

        <hr class="my-6">



        {{-- ================================ --}}
        {{-- PARTICULARS --}}
        {{-- ================================ --}}
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



        {{-- ================================ --}}
        {{-- COST SUMMARY --}}
        {{-- ================================ --}}
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


        {{-- ================================ --}}
        {{-- TERMS & CONDITIONS --}}
        {{-- ================================ --}}
        <h3 class="text-xl font-semibold mb-2">Terms & Conditions</h3>

        <p class="whitespace-pre-line text-gray-700">
            {{ $quotation->conditions }}
        </p>

    </div>
</div>

@endsection
