@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h1 class="text-3xl font-semibold mb-6">System Audit Dashboard</h1>

    {{-- TOP CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <div class="bg-white shadow p-6 rounded-lg">
            <h3 class="text-gray-600">Logs Today</h3>
            <p class="text-4xl font-bold text-blue-600">{{ $logsToday ?? 0 }}</p>
        </div>

        <div class="bg-white shadow p-6 rounded-lg">
            <h3 class="text-gray-600">Flagged Entries</h3>
            <p class="text-4xl font-bold text-red-600">{{ $flagged ?? 0 }}</p>
        </div>

        <div class="bg-white shadow p-6 rounded-lg">
            <h3 class="text-gray-600">Users Monitored</h3>
            <p class="text-4xl font-bold text-purple-600">{{ $userCount ?? 0 }}</p>
        </div>

    </div>

    {{-- ATTENDANCE AUDIT --}}
    <div class="bg-white shadow p-6 rounded-lg mb-10">
        <h3 class="text-xl font-semibold mb-4">Recent Attendance Logs</h3>

        <table class="w-full border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-2 text-left">Employee</th>
                    <th class="p-2 text-left">Project</th>
                    <th class="p-2 text-left">Time In</th>
                    <th class="p-2 text-left">Time Out</th>
                    <th class="p-2 text-left">Hours</th>
                </tr>
            </thead>

            <tbody>
                @forelse($attendanceLogs ?? [] as $log)
                    <tr class="border-b">
                        <td class="p-2">{{ $log->user->full_name }}</td>
                        <td class="p-2">{{ $log->project->project_name }}</td>
                        <td class="p-2">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                        <td class="p-2">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}</td>
                        <td class="p-2">{{ $log->hours_worked ?? '0.00' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">
                            No logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>  

    {{-- FLAGGED ENTRIES --}}
    <div class="bg-white shadow p-6 rounded-lg">
        <h3 class="text-xl font-semibold mb-4">Flagged Items</h3>

        <ul class="space-y-2">
            @forelse($flaggedItems ?? [] as $flag)
                <li class="border-b pb-2">
                    <span class="font-medium text-red-600">{{ $flag->title }}</span>
                    <span class="float-right text-gray-600">{{ $flag->created_at->format('M d, Y') }}</span>
                </li>
            @empty
                <p class="text-gray-500">No flagged entries.</p>
            @endforelse
        </ul>
    </div>

</div>
@endsection 
