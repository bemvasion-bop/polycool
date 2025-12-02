@extends('layouts.app')

@section('content')
<div class="px-10 py-6">

    {{-- PAGE TITLE --}}
    <h2 class="text-2xl font-bold mb-6">My Dashboard</h2>

    {{-- ============================
       TODAY'S ATTENDANCE SUMMARY
       ============================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

        {{-- Status Card --}}
        <div class="bg-white shadow p-6 rounded-xl">
            <h4 class="text-sm font-semibold text-gray-500 mb-1">Today's Status</h4>

            @if(!$todayLog)
                <p class="text-xl font-bold text-gray-700 mt-2">Not Yet Timed In</p>
            @else
                <p class="text-xl font-bold text-green-600 mt-2">Present</p>
            @endif
        </div>

        {{-- Time In --}}
        <div class="bg-white shadow p-6 rounded-xl">
            <h4 class="text-sm font-semibold text-gray-500 mb-1">Time In</h4>
            <p class="text-xl font-bold text-blue-600 mt-2">
                {{ $todayLog->time_in ?? '—' }}
            </p>
        </div>

        {{-- Time Out --}}
        <div class="bg-white shadow p-6 rounded-xl">
            <h4 class="text-sm font-semibold text-gray-500 mb-1">Time Out</h4>
            <p class="text-xl font-bold text-purple-600 mt-2">
                {{ $todayLog->time_out ?? '—' }}
            </p>
        </div>

    </div>


    {{-- ===============================
       ASSIGNED PROJECTS SECTION
       =============================== --}}
    <h3 class="text-xl font-bold mb-4">Projects Assigned to You</h3>

    @forelse($assignedProjects as $proj)
        <div class="bg-white p-6 shadow rounded-xl mb-5">

            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-xl font-semibold">{{ $proj->project_name }}</h4>
                    <p class="text-gray-600">{{ $proj->client->name }}</p>
                    <p class="text-gray-500 text-sm">{{ $proj->location }}</p>
                </div>
            </div>

            {{-- TIME-IN / TIME-OUT BUTTONS --}}
            <div class="mt-5">
                @if(!$todayLog)
                    {{-- TIME IN --}}
                    <form action="{{ route('attendance.timein', $proj->id) }}" method="POST">
                        @csrf
                        <button class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Time In
                        </button>
                    </form>

                @elseif($todayLog->project_id == $proj->id && !$todayLog->time_out)
                    {{-- TIME OUT --}}
                    <form action="{{ route('attendance.timeout', $proj->id) }}" method="POST">
                        @csrf
                        <button class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Time Out
                        </button>
                    </form>
                @endif
            </div>

        </div>

    @empty
        <p class="text-gray-600 bg-white p-6 shadow rounded-xl">
            You are not assigned to any project yet.
        </p>
    @endforelse


    {{-- ===============================
        RECENT ATTENDANCE LOGS
       =============================== --}}
    <h3 class="text-xl font-bold mt-10 mb-4">Recent Attendance Logs</h3>

    <div class="bg-white p-6 shadow rounded-xl">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="p-3">Date</th>
                    <th class="p-3">Project</th>
                    <th class="p-3">Time In</th>
                    <th class="p-3">Time Out</th>
                    <th class="p-3">Hours</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentLogs as $log)
                    <tr class="border-b">
                        <td class="p-3">{{ $log->date }}</td>
                        <td class="p-3">{{ $log->project->project_name }}</td>
                        <td class="p-3">{{ $log->time_in }}</td>
                        <td class="p-3">{{ $log->time_out ?? '—' }}</td>
                        <td class="p-3">{{ $log->hours_worked }}</td>
                        <td class="p-3">
                            <span class="px-3 py-1 rounded text-white
                                {{ $log->status === 'present' ? 'bg-green-600' : 'bg-gray-500' }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
