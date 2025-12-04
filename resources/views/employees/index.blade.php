@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Employees</h2>
@endsection

@section('content')

<style>
/* ============================================================
   🌈 GLASS TABLE WRAPPER
============================================================ */
.glass-card {
    border-radius: 26px;
    background: rgba(255,255,255,0.45);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.55);
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    padding: 28px 32px;
}

/* ============================================================
   🌈 TABLE — Clean (No borders)
============================================================ */
table {
    border-collapse: separate;
    border-spacing: 0;
}
table th, table td {
    padding: 18px 24px;
    border: none !important;
}
table thead tr {
    background: #F7F7F9;
    font-weight: 600;
}
.table-row {
    transition: .2s ease;
}
.table-row:hover {
    background: rgba(255,255,255,0.65);
}

/* ============================================================
   🌈 Minimal Pills
============================================================ */
.pill {
    padding: 6px 12px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
}

/* ============================================================
   🌈 Minimal Action Bar Buttons (NO background)
============================================================ */
.glass-btn {
    padding: 0;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    color: #444;
    font-weight: 500;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: 0.2s ease;
}
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

</style>


{{-- ============================================================
   🌈 ACTION BAR — Minimal style like screenshot
============================================================ --}}
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">

    <a href="{{ route('employees.create') }}"
       class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg shadow">
        + New Employee
    </a>

    <div class="flex items-center gap-3">

        {{-- Search --}}
        <input type="text"
               class="rounded-full bg-white/60 px-4 py-2 text-sm border border-white/60 backdrop-blur-sm"
               placeholder="Search…">

        {{-- Sort --}}
        <select class="text-sm">
            <option>Sort: Default</option>
            <option value="name">Name</option>
            <option value="role">System Role</option>
            <option value="status">Status</option>
        </select>

        {{-- Filter --}}
        <button class="glass-btn">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filters
        </button>

    </div>
</div>


{{-- ============================================================
   🌈 TABLE CARD
============================================================ --}}
<div class="glass-card">

    <table class="w-full text-left">

        <thead>
            <tr class="text-gray-600">
                <th>Name</th>
                <th>Email</th>
                <th>System Role</th>
                <th>Employment Type</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>

        <tbody>

            @forelse($employees as $emp)
            <tr class="table-row">

                <td class="font-medium text-gray-900">
                    {{ $emp->given_name }} {{ $emp->last_name }}
                </td>

                <td class="text-gray-700">{{ $emp->email }}</td>

                <td>
                    <span class="pill bg-indigo-100 text-indigo-700">
                        {{ ucfirst($emp->system_role) }}
                    </span>
                </td>

                <td>
                    @if($emp->employment_type === 'field_worker')
                        <span class="pill bg-blue-100 text-blue-700">Field Worker</span>
                    @else
                        <span class="pill bg-yellow-100 text-yellow-700">Office Staff</span>
                    @endif
                </td>

                <td>
                    <span class="pill status-toggle
                        {{ $emp->employee_status === 'active'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-200 text-gray-700'
                        }}">
                        {{ ucfirst($emp->employee_status) }}
                    </span>
                </td>

                <td class="text-right">
                    <a href="{{ route('employees.edit', $emp->id) }}"
                       class="text-blue-600 hover:underline">
                        Edit
                    </a>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-6 text-gray-500">
                    No employees found.
                </td>
            </tr>
            @endforelse

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
</script>

@endsection
