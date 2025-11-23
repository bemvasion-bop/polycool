@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <h1 class="text-2xl font-bold mb-6">Manager Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-white p-6 shadow rounded border border-gray-200">
            <h2 class="text-gray-600 text-sm">ASSIGNED PROJECTS</h2>
            <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
        </div>

        <div class="bg-white p-6 shadow rounded border border-gray-200">
            <h2 class="text-gray-600 text-sm">PENDING QUOTATIONS</h2>
            <p class="text-3xl font-bold text-yellow-600 mt-2">0</p>
        </div>

    </div>

    <div class="mt-8">
        <h2 class="text-lg font-semibold mb-3">Quick Actions</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <a href="/quotations"
               class="bg-blue-600 text-white p-4 rounded shadow hover:bg-blue-700 text-center">
                Manage Quotations
            </a>

            <a href="/projects"
               class="bg-green-600 text-white p-4 rounded shadow hover:bg-green-700 text-center">
                Manage Projects
            </a>

        </div>
    </div>

</div>
@endsection
