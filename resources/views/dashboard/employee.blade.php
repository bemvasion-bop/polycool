@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <h1 class="text-3xl font-semibold text-gray-800">
        Welcome back, {{ auth()->user()->given_name }}!
    </h1>
    <p class="text-gray-500 mt-1">{{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
@endsection

@section('content')
<style>
    .glass-card {
        position: relative;
        padding: 28px;
        border-radius: 26px;
        background: rgba(255,255,255,0.45);
        backdrop-filter: blur(26px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.40);
        box-shadow: 0px 20px 50px rgba(0,0,0,0.08);
        transition: .25s ease;
    }
    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0 22px rgba(140,120,255,0.35);
    }
</style>

<div class="space-y-10">

    {{-- ============================================================
        🌈 KPI CARDS — Employee Spend Summary
    ============================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="glass-card text-center">
            <p class="text-sm text-gray-600">Attendance Days</p>
            <h2 class="text-3xl font-bold text-indigo-600">{{ $attendanceCount }}</h2>
        </div>
        <div class="glass-card text-center">
            <p class="text-sm text-gray-600">Hours Worked (This Week)</p>
            @php
                $hours = floor($hoursWorked);
                $minutes = ($hoursWorked - $hours) * 60;
            @endphp
            <h2 class="text-3xl font-bold text-indigo-600">
                {{ sprintf('%02d:%02d', $hours, $minutes) }}
            </h2>
        </div>
        <div class="glass-card text-center">
            <p class="text-sm text-gray-600">Active Projects</p>
            <h2 class="text-3xl font-bold text-indigo-600">{{ $activeProjectsCount }}</h2>
        </div>
        <div class="glass-card text-center">
            <p class="text-sm text-gray-600">Latest Payroll</p>
            <h2 class="text-3xl font-bold text-indigo-600">
                ₱{{ number_format($latestPayrollAmount, 2) }}
            </h2>
        </div>
    </div>


    {{-- ============================================================
        🧱 CURRENT PROJECTS
    ============================================================ --}}
    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-lg">Active Projects</h3>
            <a href="{{ route('employee.attendance') }}"
                class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                View My Attendance →
            </a>
        </div>

        @if($activeProjects->isEmpty())
            <p class="text-gray-500 text-sm">No active assigned projects.</p>
        @else
            <table class="w-full text-sm">
                <thead class="text-gray-500">
                    <tr>
                        <th class="text-left">Project</th>
                        <th class="text-right">Status</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeProjects as $project)
                    <tr class="border-b border-gray-200">
                        <td class="py-3">{{ $project->name }}</td>
                        <td class="text-right capitalize">{{ $project->status }}</td>
                        <td class="text-right">
                            <a href="{{ route('projects.show', $project->id) }}"
                                class="text-indigo-600 hover:text-indigo-800">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>


    {{-- ============================================================
        💸 LATEST PAYSLIP
    ============================================================ --}}
    <div class="glass-card p-6">
        <h3 class="font-semibold text-lg mb-4">Latest Payslip</h3>

        @if($latestPayroll)
            <p class="text-sm text-gray-700 mb-3">
                Total: <strong>₱{{ number_format($latestPayrollAmount, 2) }}</strong>
            </p>
            <a href="{{ route('payroll.show', $latestPayroll->id) }}"
               class="px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm">
               View Payslip
            </a>
        @else
            <p class="text-gray-500 text-sm">No payroll entries yet.</p>
        @endif
    </div>

</div>
@endsection
