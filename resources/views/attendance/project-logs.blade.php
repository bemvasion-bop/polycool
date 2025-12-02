@extends('layouts.app')

@section('content')
<div class="p-8">

    <h2 class="text-2xl font-semibold mb-4">
        Attendance Logs for Project – {{ $project->project_name }}
    </h2>

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Employee</th>
                <th class="p-2 border">Date</th>
                <th class="p-2 border">Time In</th>
                <th class="p-2 border">Time Out</th>
                <th class="p-2 border">Hours</th>
                <th class="p-2 border">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
            <tr class="border">
                <td class="p-2">{{ $log->user->full_name }}</td>
                <td class="p-2">{{ $log->date }}</td>
                <td class="p-2">{{ $log->time_in ?? '—' }}</td>
                <td class="p-2">{{ $log->time_out ?? '—' }}</td>
                <td class="p-2">{{ $log->hours ?? '—' }}</td>
                <td class="p-2">{{ ucfirst($log->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-3 text-center text-gray-500">
                    No attendance recorded for this project.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
