@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Employees</h2>
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

.table-row {
    transition: .2s ease;
}
.table-row:hover {
    background: rgba(255,255,255,0.65);
}

/* ============================================================
   🌈 TABLE — Clean (No borders)
============================================================ */

.table-row {
    transition: .2s ease;
}
.table-row:hover {
    background: rgba(255,255,255,0.65);
}


/* ============================================================
   🌈 Minimal Action Bar Buttons (NO background)
============================================================ */

.glass-btn:hover {
    color: #000;
    opacity: 0.75;
}

select {
    background: transparent;
    border: none;
    padding: 6px 0;
    cursor: pointer;
}

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
#filterDrawer.open {
    right: 0;
}

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



#filterDrawer { ... }
#filterDrawer.open { right: 0; }
.filter-label { ... }
.filter-select { ... }
</style>


{{-- ============================================================
   🌈 ACTION BAR — Minimal style like screenshot
============================================================ --}}
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">

    <a href="{{ route('employees.create') }}"
       class="glass-btn bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
        + New Employee
    </a>

    <div class="flex items-center gap-3">

        <input type="text" id="searchInput"
               class="rounded-full bg-white/60 px-4 py-2 text-sm border border-white/60 backdrop-blur-sm"
               placeholder="Search…">

        <select id="sortSelect" class="glass-btn text-sm">
            <option value="default">Sort: Default</option>
            <option value="name">Name</option>
            <option value="role">System Role</option>
            <option value="status">Status</option>
        </select>

        <button id="filterBtn" class="glass-btn flex items-center gap-2">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
            Filters
        </button>
    </div>
</div>

<div id="filterDrawer">
    <h3 class="text-xl font-semibold mb-4">Filters</h3>

    <div class="mb-4">
        <label class="filter-label">Status</label>
        <select id="statusFilter" class="filter-select">
            <option value="all">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <button id="closeFilter" class="glass-btn w-full mt-2">Close</button>
</div>


{{-- ============================================================
   🌈 TABLE CARD
============================================================ --}}
<div class="glass-panel p-0 overflow-hidden">

    <table class="w-full border-collapse">
        <thead>
            <tr class="text-left border-b border-white/50">
                {{--  <th class="p-3">#</th> --}}
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">System Role</th>
                <th class="p-3">Employment</th>
                <th class="p-3">Status</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>

        <tbody id="tableBody">
        @foreach($employees as $index => $emp)
        <tr class="table-row border-b border-white/30"
            data-name="{{ strtolower($emp->given_name.' '.$emp->last_name) }}"
            data-status="{{ $emp->employee_status }}">

            {{--  <td class="p-3">{{ $index + 1 }}</td> --}}

            <td class="p-3 font-medium">
                {{ $emp->given_name }} {{ $emp->last_name }}
            </td>

            <td class="p-3">{{ $emp->email }}</td>

            <td class="p-3">
                <span class="status-pill status-converted">
                    {{ ucfirst($emp->system_role) }}
                </span>
            </td>

            <td class="p-3">
                <span class="status-pill
                    {{ $emp->employment_type === 'field_worker'
                        ? 'status-approved'
                        : 'status-pending' }}">
                    {{ $emp->employment_type === 'field_worker'
                        ? 'Field Worker'
                        : 'Office Staff' }}
                </span>
            </td>

            <td class="p-3">
                <span class="status-pill
                    {{ $emp->employee_status === 'active'
                        ? 'status-approved'
                        : 'status-pending' }}">
                    {{ ucfirst($emp->employee_status) }}
                </span>
            </td>

            <td class="p-3">
                <a href="{{ route('employees.edit', $emp->id) }}"
                class="text-blue-600 hover:underline">
                    Edit
                </a>
            </td>
        </tr>
        @endforeach
        </tbody>

    </table>



</div>


<script>
document.querySelectorAll(".status-toggle").forEach(tag => {
    tag.addEventListener("click", () => {
        if (tag.textContent.trim() === "Active") {
            tag.textContent = "Inactive";
            tag.classList.remove("bg-green-100", "text-green-700");
            tag.classList.add("bg-gray-200", "text-gray-700");
        } else {
            tag.textContent = "Active";
            tag.classList.remove("bg-gray-200", "text-gray-700");
            tag.classList.add("bg-green-100", "text-green-700");
        }
    });
});


document.addEventListener("DOMContentLoaded", () => {

    const drawer = document.getElementById("filterDrawer");
    document.getElementById("filterBtn").onclick = () => drawer.classList.add("open");
    document.getElementById("closeFilter").onclick = () => drawer.classList.remove("open");

    const rows = [...document.querySelectorAll("#tableBody tr")];

    document.getElementById("searchInput").addEventListener("input", e => {
        let txt = e.target.value.toLowerCase();
        rows.forEach(r => {
            r.style.display = r.dataset.name.includes(txt) ? "" : "none";
        });
    });

    document.getElementById("statusFilter").onchange = () => {
        let val = statusFilter.value;
        rows.forEach(r => {
            r.style.display =
                val === "all" || r.dataset.status === val ? "" : "none";
        });
    };
});

</script>



@endsection
