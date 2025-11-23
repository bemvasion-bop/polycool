@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold mb-6">Owner Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Card -->
        <div class="bg-white p-6 shadow rounded border border-gray-200">
            <h2 class="text-gray-600 text-sm">TOTAL PROJECTS</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>

        <div class="bg-white p-6 shadow rounded border border-gray-200">
            <h2 class="text-gray-600 text-sm">ACTIVE EMPLOYEES</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">0</p>
        </div>

        <div class="bg-white p-6 shadow rounded border border-gray-200">
            <h2 class="text-gray-600 text-sm">TOTAL REVENUE</h2>
            <p class="text-3xl font-bold text-purple-600 mt-2">Php 0.00</p>
        </div>

    </div>

    <div class="mt-8">
        <h2 class="text-lg font-semibold mb-3">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <a href="/projects"
               class="bg-blue-600 text-white p-4 rounded shadow hover:bg-blue-700 text-center">
                View Projects
            </a>

            <a href="/clients"
               class="bg-green-600 text-white p-4 rounded shadow hover:bg-green-700 text-center">
                View Clients
            </a>

            <a href="/employees"
               class="bg-purple-600 text-white p-4 rounded shadow hover:bg-purple-700 text-center">
                Manage Employees
            </a>

        </div>
    </div>

</div>
@endsection
