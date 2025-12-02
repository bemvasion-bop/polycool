@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Attendance Overview (Owner)</h2>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white shadow p-6 rounded-lg">
            <h3 class="text-gray-600 font-medium">Present Today</h3>
            <p class="text-3xl font-bold text-green-600">{{ $presentCount }}</p>
        </div>

        <div class="bg-white shadow p-6 rounded-lg">
            <h3 class="text-gray-600 font-medium">Absent Today</h3>
            <p class="text-3xl font-bold text-red-600">{{ $absentCount }}</p>
        </div>

        <div class="bg-white shadow p-6 rounded-lg">
            <h3 class="text-gray-600 font-medium">Total Employees</h3>
            <p class="text-3xl font-bold">{{ $presentCount + $absentCount }}</p>
        </div>
    </div>

    {{-- TODAY'S ATTENDANCE TABLE --}}
    <div class="bg-white shadow p-6 rounded-lg mb-10">
        <h3 class="text-xl font-semibold mb-4">Today's Attendance</h3>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-2 text-left">Employee</th>
                    <th class="p-2 text-left">Project</th>
                    <th class="p-2 text-left">Time In</th>
                    <th class="p-2 text-left">Time Out</th>
                    <th class="p-2 text-left">Hours</th>
                </tr>
            </thead>

            <tbody>
                @forelse($todayLogs as $log)
                <tr class="border-b">
                    <td class="p-2">{{ $log->user->full_name }}</td>
                    <td class="p-2">{{ $log->project->project_name }}</td>

                    {{-- TIME-IN (12-HOUR FORMAT) --}}
                    <td class="p-2">
                        {{ $log->time_in 
                            ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') 
                            : '—' 
                        }}
                    </td>

                    {{-- TIME-OUT (12-HOUR FORMAT) --}}
                    <td class="p-2">
                        {{ $log->time_out 
                            ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') 
                            : '—' 
                        }}
                    </td>

                    <td class="p-2">{{ $log->hours_worked ?? '0' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        No attendance logs yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ATTENDANCE PER PROJECT --}}
    <div class="bg-white shadow p-6 rounded-lg mb-10">
        <h3 class="text-xl font-semibold mb-4">Attendance by Project (Today)</h3>

        <div class="space-y-4">
            @foreach($projects as $proj)
                <div class="border rounded p-4">
                    <h4 class="font-medium mb-2">{{ $proj->project_name }}</h4>

                    @if($proj->attendanceLogs->count() > 0)
                        <ul class="space-y-1">
                            @foreach($proj->attendanceLogs as $log)
                                <li class="text-gray-700">
                                    - {{ $log->user->full_name }}

                                    {{-- TIME RANGE --}}
                                    (
                                    {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '-' }}
                                    →
                                    {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}
                                    )
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">No attendance recorded for this project.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- MONTHLY SUMMARY --}}
    <div class="bg-white shadow p-6 rounded-lg mb-10">
        <h3 class="text-xl font-semibold mb-4">Monthly Attendance</h3>
        <p class="text-gray-600 mb-3">
            This month's total logs: {{ $monthlyLogs->count() }}
        </p>
    </div>


    <form action="{{ route('attendance.markAbsentsToday') }}" method="POST" class="mb-4">
        @csrf
        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Mark Absents for Today
        </button>
    </form>

</div>
@endsection
