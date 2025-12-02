@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white shadow p-8 rounded-lg mt-10">

    <h2 class="text-2xl font-semibold mb-4">
        Project QR Code
    </h2>

    <p class="text-gray-600 mb-2">
        <strong>Project:</strong> {{ $project->project_name }}
    </p>

    <p class="text-gray-600 mb-4">
        <strong>Location:</strong> {{ $project->location }}
    </p>

    <hr class="my-4">

    <div class="text-center">
        <h3 class="text-lg font-semibold mb-3">Scan This QR in Scanner Page</h3>

        <div class="border p-4 rounded bg-gray-50 inline-block">
            {!! $qrCode !!}
        </div>

        <p class="mt-3 text-gray-500 text-sm">
            This QR links employees to this project for attendance.
        </p>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('projects.show', $project->id) }}"
           class="px-5 py-2 bg-gray-300 rounded hover:bg-gray-400">
            Back to Project
        </a>
    </div>

</div>
@endsection
