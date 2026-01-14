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
/* ===== POLYSYNC GLASS CARD ===== */
.glass-card {
    border-radius: 26px;
    background: rgba(255,255,255,0.55);
    backdrop-filter: blur(22px);
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
}

/* ===== POLYSYNC SECTION ===== */
.section-divider {
    border-top: 1px solid rgba(0,0,0,0.08);
    margin: 28px 0;
}

/* ===== STATUS PILL ===== */
.status-pill {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
}

/* ===== PRIMARY BUTTON ===== */
.ps-btn {
    padding: 10px 18px;
    border-radius: 14px;
    font-weight: 600;
    transition: .2s ease;
}

.ps-btn-primary {
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    color: white;
}

.ps-btn-primary:hover {
    opacity: .9;
    transform: translateY(-1px);
}

.ps-btn-outline {
    background: rgba(255,255,255,.6);
    border: 1px solid rgba(255,255,255,.8);
}

/* ===== ACTION BAR ===== */
.action-bar {
    display: flex;
    gap: 12px;
    padding: 14px;
    border-radius: 18px;
    background: rgba(99,102,241,0.08);
    border: 1px solid rgba(99,102,241,0.15);
    margin-bottom: 24px;
}

/* Success / Danger Polysync */
.ps-btn-success {
    background: linear-gradient(135deg,#22c55e,#16a34a);
    color: white;
}

.ps-btn-danger {
    background: rgba(239,68,68,.12);
    color: #b91c1c;
    border: 1px solid rgba(239,68,68,.35);
}

.ps-btn-danger:hover {
    background: rgba(239,68,68,.18);
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
        <div class="max-w-5xl mx-auto glass-card p-10">

        {{-- HEADER --}}
        <div class="flex justify-between items-start mb-8">
            <div>
                <p class="text-sm text-gray-500">
                    Quotation Date • {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F d, Y') }}
                </p>

                <span class="inline-block mt-2 status-pill {{ $statusColors[$quotation->status] }}">
                    {{ ucfirst($quotation->status) }}
                </span>
            </div>

            <img src="/logo.png" class="h-10 opacity-80">
        </div>


        {{-- ACTION BUTTONS --}}
        <div class="flex flex-wrap gap-3 mb-6">

        @php
            $role = auth()->user()->system_role;
            $status = $quotation->status;
        @endphp

        {{-- 🔹 Pending --}}
        @if ($status === 'pending')

            {{-- Edit --}}
            @if($role === 'owner')
                <a href="{{ route('quotations.edit', $quotation->id) }}"
                class="ps-btn ps-btn-primary">
                    Edit
                </a>
            @endif

            {{-- Approve --}}
            @if($role === 'owner')
                <button type="button"
                    onclick="confirmApprove({{ $quotation->id }})"
                    class="ps-btn ps-btn-success">
                    Approve
                </button>

                <button type="button"
                    onclick="confirmDecline({{ $quotation->id }})"
                    class="ps-btn ps-btn-danger">
                    Decline
                </button>
            @endif

        @endif

        {{-- 🔹 Approved --}}
        @if ($status === 'approved' && in_array($role,['owner','manager']))
            <button type="button"
                onclick="confirmConvert({{ $quotation->id }})"
                class="ps-btn ps-btn-primary">
                Convert to Project
            </button>
        @endif

        {{-- 🔹 Converted --}}
        @if ($status === 'converted' && $quotation->project)
            <a href="{{ route('projects.show', $quotation->project->id) }}"
            class="ps-btn ps-btn-primary">
                View Project
            </a>
        @endif


        {{-- PRINT --}}
        <button onclick="window.print()"
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            🖨 Print
        </button>

        {{-- DOWNLOAD PDF --}}
        <a href="{{ route('quotations.pdf', $quotation->id) }}"
        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            📄 Download PDF
        </a>

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


<script>
function confirmConvert(id) {
    Swal.fire({
        title: "Convert to Project?",
        text: "This will create a project and lock this quotation.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#7c3aed", // purple
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, convert",
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement("form");
            form.method = "POST";
            form.action = `/quotations/${id}/convert`;
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();

            Swal.fire({
                title: "Converting...",
                html: "Please wait...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }
    });
}
</script>

@endsection
