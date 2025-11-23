@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Manager Dashboard</h1>

    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">ASSIGNED PROJECTS</p>
            <p class="text-3xl font-bold text-purple-700">{{ $assignedProjects }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">EMPLOYEES</p>
            <p class="text-3xl font-bold text-green-600">{{ $employeeCount }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">TODAY'S ATTENDANCE</p>
            <p class="text-3xl font-bold text-blue-600">{{ $todayAttendance }}</p>
        </div>
    </div>

    <h2 class="mt-8 text-xl font-semibold">Quick Actions</h2>

    <div class="grid grid-cols-3 gap-4 mt-4">
        <a href="/projects" class="py-3 text-center bg-blue-600 rounded text-white">View Projects</a>
        <a href="/attendance/scanner" class="py-3 text-center bg-indigo-600 rounded text-white">Attendance Scanner</a>
        <a href="/employees" class="py-3 text-center bg-purple-600 rounded text-white">Employees</a>
    </div>
</div>
@endsection
