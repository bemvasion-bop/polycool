@extends('layouts.app')

@section('page-header')
<a href="{{ route('quotations.index') }}" class="ios-back-btn mb-4 inline-flex">
    <i data-lucide="chevron-left" class="w-4 h-4"></i>
    <span>Back</span>
</a>

<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Quotation Details
</h2>
@endsection

@section('content')

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

@php
    $statusColors = [
        'pending'   => 'bg-yellow-200 text-yellow-800',
        'approved'  => 'bg-green-200 text-green-800',
        'declined'  => 'bg-red-200 text-red-800',
        'converted' => 'bg-blue-200 text-blue-800',
    ];
@endphp

<div class="p-10">
    <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-8">

        {{-- HEADER --}}
        <div class="flex justify-between items-start mb-6">
            <div>
                <p class="text-sm text-gray-500">
                    Quotation Date: {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F d, Y') }}
                </p>

                <p class="text-sm">
                    Status:
                    <span class="px-2 py-1 rounded text-sm {{ $statusColors[$quotation->status] ?? '' }}">
                        {{ ucfirst($quotation->status) }}
                    </span>
                </p>
            </div>

            <span class="px-3 py-1 rounded text-sm {{ $statusColors[$quotation->status] ?? '' }}">
                {{ ucfirst($quotation->status) }}
            </span>

            <img src="/logo.png" alt="Logo" class="h-10">
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex space-x-4 mb-6">

            @php
                $role = auth()->user()->system_role;
                $status = $quotation->status;
            @endphp

            {{-- 🔹 Pending --}}
            @if ($status === 'pending')

                {{-- Edit (Owner + Manager) --}}
                @if(in_array($role, ['owner','manager']))
                    <a href="{{ route('quotations.edit', $quotation->id) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Edit
                    </a>
                @endif

                {{-- Approve + Decline (OWNER ONLY) --}}
                @if($role === 'owner')
                    <form action="{{ route('quotations.approve', $quotation->id) }}" method="POST">
                        @csrf
                        <button type="button"
                            onclick="confirmApprove({{ $quotation->id }})"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Approve
                        </button>
                    </form>

                    <form action="{{ route('quotations.decline', $quotation->id) }}" method="POST">
                        @csrf
                        <button type="button"
                            onclick="confirmDecline({{ $quotation->id }})"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Decline
                        </button>
                    </form>
                @endif

            @endif


            {{-- 🔹 Approved --}}
            @if ($status === 'approved')

                {{-- Convert to Project (Owner + Manager) --}}
                @if(in_array($role, ['owner','manager']))
                    <button type="button"
                        class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
                        onclick="confirmConvert({{ $quotation->id }})">
                        Convert to Project
                    </button>
                @endif

            @endif


            {{-- 🔹 Converted --}}
            @if ($status === 'converted')

                {{-- View Project --}}
                @if($quotation->project)
                    <a href="{{ route('projects.show', $quotation->project->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        View Project
                    </a>
                @endif

            @endif
        </div>



        {{-- CLIENT INFO --}}
        <h3 class="text-xl font-semibold mb-2">Client Information</h3>
        <p><strong>Client Name:</strong> {{ $quotation->client->name }}</p>
        <p><strong>Address:</strong> {{ $quotation->address }}</p>
        <hr class="my-6">

        {{-- PROJECT INFO --}}
        <h3 class="text-xl font-semibold mb-2">Project Information</h3>
        <p><strong>Project / Vessel Name:</strong> {{ $quotation->project_name }}</p>
        <p><strong>System:</strong> {{ $quotation->system }}</p>
        <p><strong>Duration:</strong> {{ $quotation->duration_days }} day(s)</p>
        <p class="mt-3"><strong>Scope of Work</strong></p>
        <p>{{ $quotation->scope_of_work }}</p>
        <hr class="my-6">

        {{-- PARTICULARS --}}
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

        {{-- TERMS --}}
        <h3 class="text-xl font-semibold mb-2">Terms & Conditions</h3>
        <p class="whitespace-pre-line text-gray-700">
            {{ $quotation->conditions }}
        </p>

    </div>
</div>



<script>
function confirmApprove(id) {
    Swal.fire({
        title: "Approve Quotation?",
        text: "This will lock editing and allow conversion.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#16a34a",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, approve",
    }).then((result) => {
        if (result.isConfirmed) {
            document.createElement('form').submit.call(
                Object.assign(
                    document.createElement('form'),
                    { action: `/quotations/${id}/approve`, method: 'POST' }
                ),
                {
                    innerHTML: '@csrf'
                }
            );
        }
    });
}

function confirmDecline(id) {

    Swal.fire({
        title: "Decline this quotation?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, decline",
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement("form");
            form.action = `/quotations/${id}/decline`;
            form.method = "POST";
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();

            Swal.fire({
                title: "Declining...",
                html: "Please wait...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }
    });
}

function confirmApprove(id) {

    Swal.fire({
        title: "Approve Quotation?",
        text: "This will lock editing and allow conversion.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#16a34a", // green
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, approve",
    }).then((result) => {
        if (result.isConfirmed) {

            const form = document.createElement("form");
            form.action = `/quotations/${id}/approve`;
            form.method = "POST";
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();

            Swal.fire({
                title: "Approving...",
                html: "Please wait...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }
    });
}

</script>

@endsection
