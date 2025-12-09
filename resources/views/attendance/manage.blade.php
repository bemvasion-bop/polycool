@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Attendance Management
</h2>
@endsection

@section('content')

<style>
    /* 🌈 Polysync Glass Card */
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(22px) saturate(180%);
        -webkit-backdrop-filter: blur(22px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.50);
        box-shadow: 0 18px 55px rgba(0,0,0,0.08);
        padding: 28px 32px;
        transition: .25s ease;
    }

    /* 🔎 Search Bars */
    .search-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.12);
        background: rgba(255,255,255,0.75);
        font-size: 14px;
        transition: .25s ease;
    }
    .search-input:focus {
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px #A5B4FC60;
    }

    /* List styling */
    .att-item {
        padding: 12px;
        border-radius: 16px;
        font-size: 14px;
        transition: .25s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid transparent;
    }
    .att-item:hover {
        background: rgba(255,255,255,0.85);
        border-color: rgba(0,0,0,0.12);
    }

    .role-text {
        font-size: 12px;
        color: #6b7280;
    }
</style>


<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

    {{-- EMPLOYEE LIST --}}
    <div class="glass-card">
        <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5"></i> Employees
        </h3>

        <input type="text"
               id="empSearch"
               class="search-input mb-4"
               placeholder="Search employee…">

        <ul id="employeeList" class="space-y-2">
            @foreach($employees as $emp)
                <li class="att-item">
                    <i data-lucide="user-round" class="w-4 h-4 text-gray-700"></i>
                    <a href="{{ route('attendance.employee', $emp->id) }}" class="flex-1">
                        {{ $emp->full_name }}
                        <span class="role-text">({{ ucfirst($emp->system_role) }})</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- PROJECT LIST --}}
    <div class="glass-card">
        <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
            <i data-lucide="folder-kanban" class="w-5 h-5"></i> Projects
        </h3>

        <input type="text"
               id="projSearch"
               class="search-input mb-4"
               placeholder="Search project…">

        <ul id="projectList" class="space-y-2">
            @foreach($projects as $proj)
                <li class="att-item">
                    <i data-lucide="map-pin" class="w-4 h-4 text-gray-700"></i>
                    <a href="{{ route('attendance.project', $proj->id) }}" class="flex-1">
                        {{ $proj->project_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

</div>


{{-- Search Filtering --}}
<script>
    function setupSearch(inputId, listId) {
        const input = document.getElementById(inputId);
        const listItems = document.getElementById(listId).getElementsByTagName("li");

        input.addEventListener("input", () => {
            const filter = input.value.toLowerCase();
            for (let li of listItems) {
                li.style.display = li.textContent.toLowerCase().includes(filter) ? "" : "none";
            }
        });
    }

    setupSearch("empSearch", "employeeList");
    setupSearch("projSearch", "projectList");
</script>

@endsection
