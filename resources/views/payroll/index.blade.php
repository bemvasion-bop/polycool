@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Payroll Runs
</h2>
@endsection

@section('content')

<style>
    /* Glass Container */
    .glass-container {
        border-radius: 28px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(22px);
        border: 1px solid rgba(255,255,255,0.55);
        box-shadow: 0 15px 45px rgba(0,0,0,0.08);
        padding: 28px 32px;
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    thead th {
        padding: 16px 22px;
        font-weight: 600;
        color: #4b5563;
        background: transparent;
    }
    tbody td {
        padding: 18px 22px;
        color: #111;
        font-size: 14px;
        border-top: 1px solid rgba(255,255,255,0.4);
    }
    tbody tr:hover {
        background: rgba(255,255,255,0.8);
        transition: .22s ease;
    }

    /* Status Badges */
    .status-pill {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,0.4);
    }
    .status-draft { background:#fde68a; color:#92400e; }
    .status-finalized { background:#bbf7d0; color:#166534; }

    /* Glass Buttons */
    .glass-btn {
        padding: 10px 18px;
        font-size: 14px;
        border-radius: 9999px;
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(255,255,255,0.7);
        backdrop-filter: blur(16px);
        color: #4b5563;
        transition: .2s ease;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .glass-btn:hover {
        background: rgba(255,255,255,1);
        transform: translateY(-1px);
    }
    .primary-glass {
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        border: none;
    }
    .primary-glass:hover {
        opacity: .9;
    }
</style>


{{-- ================= ACTION BAR ================= --}}
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">

    {{-- Left: Add --}}
    <a href="{{ route('payroll.create') }}"
       class="glass-btn primary-glass">
        + Generate Payroll
    </a>

    {{-- Right Controls --}}
    <div class="flex items-center gap-3">

        {{-- Search --}}
        <input type="text"
               id="searchInput"
               class="glass-btn w-52 text-sm"
               placeholder="Search…">

        {{-- Sort --}}
        <select id="sortSelect" class="glass-btn text-sm">
            <option value="date">Period</option>
            <option value="gross">Total Gross</option>
            <option value="net">Total Net</option>
            <option value="status">Status</option>
        </select>

        {{-- Filters Button --}}
        <button id="filterBtn" class="glass-btn flex items-center gap-2 text-sm">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
        </button>

    </div>
</div>


{{-- ========== FILTER DRAWER ========== --}}
<div id="filterDrawer"
     class="fixed inset-y-0 right-0 w-80 p-6 bg-white/80 backdrop-blur-lg hidden shadow-xl z-50 rounded-l-3xl">

    <h3 class="text-xl font-semibold mb-4">Filters</h3>

    <label class="text-sm font-medium">Status</label>
    <select id="filterStatus" class="glass-btn w-full mb-5 text-sm">
        <option value="all">All</option>
        <option value="draft">Draft</option>
        <option value="finalized">Finalized</option>
    </select>

    <button id="filterClose" class="glass-btn w-full mt-3 text-sm">Close</button>
</div>



{{-- ================= GLASS TABLE ================= --}}
<div class="glass-container">

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Period</th>
                <th>Type</th>
                <th>Total Gross</th>
                <th>Total Deductions</th>
                <th>Net</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>

        <tbody id="tableBody">
            @foreach($runs as $i => $run)
                @php
                    $periodStart = \Carbon\Carbon::parse($run->period_start);
                    $periodEnd   = \Carbon\Carbon::parse($run->period_end);
                @endphp

                <tr class="table-row"
                    data-status="{{ $run->status }}"
                    data-text="{{ strtolower($run->payroll_type) }}"
                    data-date="{{ $periodStart->timestamp }}"
                    data-gross="{{ $run->total_gross }}"
                    data-net="{{ $run->total_net }}"
                >
                    <td>{{ $i+1 }}</td>

                    <td>{{ $periodStart->format('M d') }} — {{ $periodEnd->format('M d, Y') }}</td>

                    <td class="capitalize">{{ $run->payroll_type }}</td>

                    <td>₱{{ number_format($run->total_gross, 2) }}</td>

                    <td>₱{{ number_format($run->total_deductions, 2) }}</td>

                    <td class="font-semibold text-gray-900">
                        ₱{{ number_format($run->total_net, 2) }}
                    </td>

                    <td>
                        <span class="status-pill status-{{ $run->status }}">
                            {{ ucfirst($run->status) }}
                        </span>
                    </td>

                    <td class="text-right">
                        <a href="{{ route('payroll.show', $run->id) }}"
                        class="text-blue-600 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>

    </table>

</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const rows = [...document.querySelectorAll("#tableBody tr")];

    document.getElementById("searchInput").oninput = e => {
        let q = e.target.value.toLowerCase();
        rows.forEach(r => r.style.display = r.dataset.text.includes(q) ? "" : "none");
    };

    document.getElementById("sortSelect").onchange = e => {
        let tbody = document.getElementById("tableBody");
        let sorted = [...rows];
        switch(e.target.value){
            case "date": sorted.sort((a,b)=>a.dataset.date - b.dataset.date); break;
            case "gross": sorted.sort((a,b)=>a.dataset.gross - b.dataset.gross); break;
            case "net": sorted.sort((a,b)=>a.dataset.net - b.dataset.net); break;
            case "status": sorted.sort((a,b)=>a.dataset.status.localeCompare(b.dataset.status)); break;
        }
        sorted.forEach(r => tbody.appendChild(r));
    };

    document.getElementById("filterBtn").onclick =
        () => document.getElementById("filterDrawer").classList.remove("hidden");

    document.getElementById("filterClose").onclick =
        () => document.getElementById("filterDrawer").classList.add("hidden");

    document.getElementById("filterStatus").onchange = () => {
        let val = document.getElementById("filterStatus").value;
        rows.forEach(r => {
            r.style.display = (val === "all" || r.dataset.status === val) ? "" : "none";
        });
    };
});
</script>

@endsection
