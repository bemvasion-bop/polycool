@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">My Dashboard</h2>

    {{-- TODAY'S ATTENDANCE --}}
    <div class="bg-white shadow p-6 rounded-lg mb-8">
        <h3 class="text-xl font-semibold mb-4">Today's Attendance</h3>

        @if($todayLog)
            <p><strong>Project:</strong> {{ $todayLog->project->project_name ?? '-' }}</p>

            <p>
                <strong>Time In:</strong>
                {{ $todayLog->time_in
                    ? \Carbon\Carbon::parse($todayLog->time_in)->format('h:i A')
                    : '—' }}
            </p>

            <p>
                <strong>Time Out:</strong>
                {{ $todayLog->time_out
                    ? \Carbon\Carbon::parse($todayLog->time_out)->format('h:i A')
                    : '—' }}
            </p>

            <p><strong>Status:</strong> {{ ucfirst($todayLog->status) }}</p>

        @else
            <p class="text-gray-600">No attendance recorded for today.</p>
        @endif
    </div>


    {{-- RECENT LOGS --}}
    <div class="bg-white shadow p-6 rounded-lg">
        <h3 class="text-xl font-semibold mb-4">Recent Attendance Logs</h3>
        @php $user = auth()->user(); @endphp


        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-2 text-left">Date</th>
                    <th class="p-2 text-left">Project</th>
                    <th class="p-2 text-left">Time In</th>
                    <th class="p-2 text-left">Time Out</th>
                    <th class="p-2 text-left">Hours</th>
                    <th class="p-2 text-left">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($assignedProjects as $proj)
                    @php
                        $logs = $proj->attendanceLogs()
                            ->where('user_id', auth()->id())
                            ->latest()
                            ->take(10)
                            ->get();
                    @endphp

                    @forelse($logs as $log)
                        <tr class="border-b">
                            <td class="p-2">{{ $log->date }}</td>

                            <td class="p-2">{{ $proj->project_name }}</td>

                            <td class="p-2">
                                {{ $log->time_in
                                    ? \Carbon\Carbon::parse($log->time_in)->format('h:i A')
                                    : '—' }}
                            </td>

                            <td class="p-2">
                                {{ $log->time_out
                                    ? \Carbon\Carbon::parse($log->time_out)->format('h:i A')
                                    : '—' }}
                            </td>

                            <td class="p-2">{{ $log->hours_worked ?? 0 }}</td>

                            <td class="p-2 capitalize">
                                {{ $log->status }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">
                            No attendance logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
