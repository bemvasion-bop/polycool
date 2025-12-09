@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Quotations
</h2>
@endsection

@section('content')

<style>
/* ============================================================
   🌈 GLASS TABLE WRAPPER
============================================================ */
.glass-panel {
    border-radius: 26px;
    background: rgba(255,255,255,0.45);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.55);
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    padding: 28px 32px;
}

/* ============================================================
   🌈 TABLE + HOVER EFFECT
============================================================ */
.table-row {
    transition: .2s ease;
}
.table-row:hover {
    background: rgba(255,255,255,0.65);
}

/* ============================================================
   🌈 STATUS BADGES
============================================================ */
.status-pill {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.5);
}

.status-pending { background: #e5e7ebcc; color: #4b5563; }
.status-approved { background: #bbf7d0cc; color: #166534; }
.status-declined { background: #fecacae0; color: #b91c1c; }
.status-converted { background: #bfdbfecc; color: #1e3a8a; }

/* ============================================================
   🌈 FILTER DRAWER (SLIDE-OUT)
============================================================ */
#filterDrawer {
    position: fixed;
    top: 0;
    right: -340px;
    width: 320px;
    height: 100vh;
    padding: 26px;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(20px);
    border-left: 1px solid rgba(255,255,255,0.5);
    box-shadow: -10px 0 35px rgba(0,0,0,0.15);
    transition: .35s ease;
    z-index: 999;
}
#filterDrawer.open { right: 0; }

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #4b5563;
}
.filter-select {
    width: 100%;
    padding: 8px 10px;
    border-radius: 12px;
    background: rgba(255,255,255,0.85);
    border: 1px solid rgba(180,180,180,0.5);
}

/* ============================================================
   🌈 MOBILE CARD MODE
============================================================ */
@media(max-width: 768px){
    table { display: none; }

    .quote-card {
        border-radius: 26px;
        padding: 20px;
        background: rgba(255,255,255,0.6);
        border: 1px solid rgba(255,255,255,0.5);
        backdrop-filter: blur(18px);
        margin-bottom: 18px;
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    }
}
</style>

{{-- ============================================================
   🌈 ACTION BAR (New + Search + Sort + Filter)
============================================================ --}}
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">

    <a href="{{ route('quotations.create') }}"
       class="glass-btn bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
        + New Quotation
    </a>

    <div class="flex items-center gap-3">

        {{-- Search --}}
        <input type="text" id="searchInput"
               class="rounded-full bg-white/60 px-4 py-2 text-sm border border-white/60 backdrop-blur-sm"
               placeholder="Search…">

        {{-- Sort --}}
        <select id="sortSelect" class="glass-btn text-sm">
            <option value="default">Sort: Default</option>
            <option value="client">Client</option>
            <option value="price">Contract Price</option>
            <option value="date">Date</option>
            <option value="status">Status</option>
        </select>

        {{-- Filter Drawer Button --}}
        <button id="filterBtn" class="glass-btn flex items-center gap-2">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
        </button>
    </div>
</div>

{{-- ============================================================
   🌈 FILTER DRAWER (Slide-out)
============================================================ --}}
<div id="filterDrawer">

    <h3 class="text-xl font-semibold mb-4">Filters</h3>

    <div class="mb-4">
        <label class="filter-label">Status</label>
        <select id="statusFilter" class="filter-select">
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="declined">Declined</option>
            <option value="converted">Converted</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="filter-label">Date</label>
        <select id="dateFilter" class="filter-select">
            <option value="all">All</option>
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
        </select>
    </div>

    <button id="closeFilter" class="glass-btn w-full mt-2">Close</button>

</div>

{{-- ============================================================
   🌈 GLASS TABLE
============================================================ --}}
<div class="glass-panel">

    {{-- Desktop Table --}}
    <table class="w-full border-collapse hidden md:table">
        <thead>
            <tr class="text-left border-b border-white/50">
                <th class="p-3">#</th>
                <th class="p-3">Client</th>
                <th class="p-3">Project/Vessel</th>
                <th class="p-3">Date</th>
                <th class="p-3">Price</th>
                <th class="p-3">Status</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>

        <tbody id="tableBody">
            @foreach ($quotations as $index => $q)
                <tr class="table-row border-b border-white/30"
                    data-client="{{ $q->client->name }}"
                    data-price="{{ $q->contract_price }}"
                    data-date="{{ \Carbon\Carbon::parse($q->quotation_date)->timestamp }}"
                    data-status="{{ $q->project ? 'converted' : $q->status }}"
                    data-text="{{ strtolower($q->client->name.' '.$q->project_name) }}">


                    <td class="p-3">{{ $index + 1 }}</td>
                    <td class="p-3">{{ $q->client->name }}</td>
                    <td class="p-3">{{ $q->project_name }}</td>
                    <td class="p-3">{{ \Carbon\Carbon::parse($q->quotation_date)->format('M d, Y') }}</td>
                    <td class="p-3">₱{{ number_format($q->contract_price, 2) }}</td>

                    <td class="p-3">
                        @php
                            $status = $q->status;
                        @endphp

                        <span class="status-pill status-{{ $status }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>

                    <td class="p-3">
                        <a href="{{ route('quotations.show', $q->id) }}" class="text-blue-600 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Mobile Cards --}}
    <div class="md:hidden">
        @foreach ($quotations as $q)
        <div class="quote-card">
            <h3 class="text-lg font-semibold">{{ $q->project_name }}</h3>
            <p class="text-gray-600 text-sm">{{ $q->client->name }}</p>

            <p class="mt-3 text-sm">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($q->quotation_date)->format('M d, Y') }}
            </p>

            <p class="mt-1 text-sm">
                <strong>Price:</strong> ₱{{ number_format($q->contract_price, 2) }}
            </p>

            <p class="mt-2">
                @php
                    $status = $q->status;
                @endphp

                <span class="status-pill status-{{ $status }}">
                    {{ ucfirst($status) }}
                </span>
            </p>

            <a href="{{ route('quotations.show', $q->id) }}"
               class="text-blue-700 mt-3 block font-medium">
                View →
            </a>
        </div>
        @endforeach
    </div>

</div>

{{-- ============================================================
   🌈 SCRIPT — SEARCH + SORT + FILTER
============================================================ --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    const drawer = document.getElementById("filterDrawer");
    document.getElementById("filterBtn").onclick = () => drawer.classList.add("open");
    document.getElementById("closeFilter").onclick = () => drawer.classList.remove("open");

    const rows = [...document.querySelectorAll("#tableBody tr")];

    /* SEARCH */
    document.getElementById("searchInput").addEventListener("input", e => {
        let txt = e.target.value.toLowerCase();

        rows.forEach(r => {
            r.style.display = r.dataset.text.includes(txt) ? "" : "none";
        });
    });

    /* STATUS FILTER */
    document.getElementById("statusFilter").onchange = filterAll;
    /* DATE FILTER */
    document.getElementById("dateFilter").onchange = filterAll;

    function filterAll() {
        let statusVal = document.getElementById("statusFilter").value;
        let dateVal = document.getElementById("dateFilter").value;
        let now = Date.now();

        rows.forEach(row => {
            let match = true;

            if (statusVal !== "all" && row.dataset.status !== statusVal) {
                match = false;
            }

            if (dateVal !== "all") {
                let diff = (now - (row.dataset.date * 1000)) / (1000*60*60*24);
                if (diff > parseInt(dateVal)) match = false;
            }

            row.style.display = match ? "" : "none";
        });
    }

    /* SORTING */
    document.getElementById("sortSelect").onchange = e => {
        let val = e.target.value;
        let tbody = document.getElementById("tableBody");

        let sorted = [...rows];

        switch(val){
            case "client":
                sorted.sort((a,b)=>a.dataset.client.localeCompare(b.dataset.client));
                break;
            case "price":
                sorted.sort((a,b)=>a.dataset.price - b.dataset.price);
                break;
            case "date":
                sorted.sort((a,b)=>a.dataset.date - b.dataset.date);
                break;
            case "status":
                sorted.sort((a,b)=>a.dataset.status.localeCompare(b.dataset.status));
                break;
        }

        sorted.forEach(r => tbody.appendChild(r));
    };

});
</script>

@endsection
