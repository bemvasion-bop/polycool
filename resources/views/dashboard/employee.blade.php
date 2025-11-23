@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-semibold mb-6">Welcome, {{ auth()->user()->given_name }}!</h1>

    <div class="grid grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">ASSIGNED PROJECTS</p>
            <p class="text-3xl font-bold text-blue-600">{{ $assignedProjects }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">DAYS PRESENT</p>
            <p class="text-3xl font-bold text-green-600">{{ $daysPresent }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">LATEST LOG</p>
            <p class="text-xl font-semibold text-purple-700">{{ $latestLog }}</p>
        </div>
    </div>

    <h2 class="mt-8 text-xl font-semibold">Projects Assigned</h2>

    <div class="bg-white mt-4 shadow rounded p-4">
        @forelse ($projectList as $project)
            <div class="border-b py-2">
                <strong>{{ $project->name }}</strong>
                <p class="text-sm text-gray-600">{{ $project->location }}</p>
            </div>
        @empty
            <p class="text-gray-500">No assigned projects yet.</p>
        @endforelse
    </div>
</div>
@endsection
