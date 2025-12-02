@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">
        Attendance Logs for {{ $user->full_name }}
    </h2>

    <table class="w-full border-collapse bg-white shadow rounded-lg">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="p-2 text-left">Date</th>
                <th class="p-2 text-left">Project</th>
                <th class="p-2 text-left">Time In</th>
                <th class="p-2 text-left">Time Out</th>
                <th class="p-2 text-left">Hours</th>
            </tr>
        </thead>

        <tbody>
            @forelse($logs as $log)
            <tr class="border-b">
                <td class="p-2">{{ $log->date }}</td>
                <td class="p-2">{{ $log->project->project_name ?? '-' }}</td>
                <td class="p-2">{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '-' }}</td>
                <td class="p-2">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}</td>
                <td class="p-2">{{ $log->hours_worked ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center p-4 text-gray-500">No logs yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
