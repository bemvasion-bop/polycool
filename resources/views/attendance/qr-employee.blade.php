@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white shadow p-8 rounded-lg mt-10">

    <h2 class="text-2xl font-semibold mb-4">
        Employee QR Code
    </h2>

    <p class="text-gray-600 mb-1">
        <strong>Employee:</strong> {{ $employee->full_name }}
    </p>

    <p class="text-gray-600 mb-4">
        <strong>Project:</strong> {{ $project->project_name }}
    </p>

    <hr class="my-4">

    <div class="text-center">
        <h3 class="text-lg font-semibold mb-3">Your QR Code</h3>

        <div class="border p-4 rounded bg-gray-50 inline-block">
            {!! $qrCode !!}
        </div>

        <p class="mt-3 text-gray-500 text-sm">
            Screenshot or save this QR to use for attendance scanning.
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
