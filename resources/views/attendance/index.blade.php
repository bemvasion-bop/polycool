@extends('layouts.app')

@section('title', 'My Attendance')

@section('page-header')
<h1 class="text-3xl font-semibold text-gray-900 tracking-tight">
    My Attendance Logs
</h1>
<p class="text-gray-500 mt-1">
    Track your daily time-in & time-out history.
</p>
@endsection

@section('content')
<style>
    .timeline-wrapper {
        position: relative;
        margin-left: 32px;
        padding-left: 18px;
        border-left: 3px solid rgba(147,112,255,0.40);
    }
    .timeline-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #8b5cf6;
        border: 3px solid white;
        position: absolute;
        left: -27px;
        top: 10px;
        box-shadow: 0 0 12px rgba(140,120,255,0.6);
    }
    .log-card {
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(26px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.45);
        box-shadow: 0 20px 50px rgba(0,0,0,0.08);
        border-radius: 20px;
        padding: 20px 24px;
        transition: .25s;
    }
    .log-card:hover {
        transform: translateY(-3px);
        box-shadow: 0px 0px 20px rgba(140,120,255,0.35);
    }
</style>

<div class="space-y-6">

    @forelse ($logs as $log)
    <div class="timeline-wrapper">

        <div class="timeline-dot"></div>

        <div class="log-card">

            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold text-lg text-gray-900">
                    {{ \Carbon\Carbon::parse($log->date)->format('F d, Y') }}
                </h3>

                <span class="text-indigo-600 text-sm font-medium">
                    {{ $log->project->project_name ?? 'N/A' }}
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">

                <p><strong>Time In:</strong><br>
                    {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '—' }}</p>

                <p><strong>Time Out:</strong><br>
                    {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '—' }}</p>

                <p><strong>Hours:</strong><br>
                    {{ $log->hours ?? '—' }}</p>

                <p><strong>Status:</strong><br>
                    <span class="text-purple-600 font-medium">
                        {{ ucfirst($log->status) ?? 'Recorded' }}
                    </span>
                </p>

            </div>

        </div>
    </div>
    @empty
    <p class="text-gray-500 text-center mt-10">
        No attendance records yet.
    </p>
    @endforelse

</div>
@endsection
