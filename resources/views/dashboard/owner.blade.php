@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Owner Dashboard</h1>

    <div class="grid grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">TOTAL PROJECTS</p>
            <p class="text-3xl font-bold text-purple-700">{{ $totalProjects }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">ACTIVE EMPLOYEES</p>
            <p class="text-3xl font-bold text-green-600">{{ $activeEmployees }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">TOTAL REVENUE</p>
            <p class="text-3xl font-bold text-pink-600">Php {{ number_format($totalRevenue, 2) }}</p>
        </div>

    </div>

    <h2 class="mt-8 text-xl font-semibold">Quick Actions</h2>

    <div class="grid grid-cols-3 gap-4 mt-4">
        <a href="/projects" class="py-3 text-center bg-blue-600 rounded text-white">View Projects</a>
        <a href="/clients" class="py-3 text-center bg-green-600 rounded text-white">View Clients</a>
        <a href="/employees" class="py-3 text-center bg-purple-600 rounded text-white">Manage Employees</a>
    </div>
</div>
@endsection
