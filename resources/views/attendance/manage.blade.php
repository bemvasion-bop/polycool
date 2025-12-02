@extends('layouts.app')

@section('content')
<div class="px-8 py-6">

    <h2 class="text-2xl font-semibold mb-6">Attendance Management</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- EMPLOYEE LIST --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="font-semibold text-lg mb-4">Employees</h3>

            <input type="text" id="empSearch" placeholder="Search employee..."
                   class="w-full mb-3 p-2 border rounded">

            <ul id="employeeList" class="space-y-2">
                @foreach($employees as $emp)
                    <li class="p-2 border rounded hover:bg-gray-100">
                        <a href="{{ route('attendance.employee', $emp->id) }}">
                            {{ $emp->full_name }}
                            <span class="text-gray-500 text-sm">({{ $emp->role }})</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- PROJECT LIST --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="font-semibold text-lg mb-4">Projects</h3>

            <input type="text" id="projSearch" placeholder="Search project..."
                   class="w-full mb-3 p-2 border rounded">

            <ul id="projectList" class="space-y-2">
                @foreach($projects as $proj)
                    <li class="p-2 border rounded hover:bg-gray-100">
                        <a href="{{ route('attendance.project', $proj->id) }}">
                            {{ $proj->project_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- 
        <form action="{{ route('attendance.markAbsentsToday') }}" method="POST" class="mb-4">
          @csrf
            <a href="{{ route('attendance.scanner') }}"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                Open QR Scanner
            </a>
        </form>
         --}}


    </div>
</div>

{{-- Simple search filtering --}}
<script>
    const empSearch = document.getElementById("empSearch");
    const empList = document.getElementById("employeeList").getElementsByTagName("li");

    empSearch.addEventListener("input", function() {
        let filter = empSearch.value.toLowerCase();
        for (let li of empList) {
            li.style.display = li.textContent.toLowerCase().includes(filter) ? "" : "none";
        }
    });

    const projSearch = document.getElementById("projSearch");
    const projList = document.getElementById("projectList").getElementsByTagName("li");

    projSearch.addEventListener("input", function() {
        let filter = projSearch.value.toLowerCase();
        for (let li of projList) {
            li.style.display = li.textContent.toLowerCase().includes(filter) ? "" : "none";
        }
    });
</script>

@endsection
