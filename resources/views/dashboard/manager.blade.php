@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('page-header')
<h1 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Manager Dashboard
</h1>
@endsection

@section('content')

<style>
    .kpi-card {
        position: relative;
        padding: 28px;
        border-radius: 26px;
        backdrop-filter: blur(26px) saturate(180%);
        -webkit-backdrop-filter: blur(26px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.45);
        overflow: hidden;
        transition: 0.35s ease;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0 22px rgba(140,120,255,0.45);
    }
    .kpi-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08rem;
        font-weight: 600;
        margin-bottom: 4px;
        color: rgba(0,0,0,0.65);
    }
    .kpi-value {
        font-size: 34px;
        font-weight: 700;
    }

    .glass-box {
        border-radius: 26px;
        padding: 28px 32px;
        border: 1px solid rgba(255,255,255,0.45);
        background: rgba(255,255,255,0.45);
        backdrop-filter: blur(26px);
        -webkit-backdrop-filter: blur(26px);
    }
</style>

<div class="space-y-10">

    {{-- ================================
        KPI CARDS (top 4)
    ================================= --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="kpi-card">
            <p class="kpi-title">Active / Ongoing Projects</p>
            <h2 class="kpi-value text-purple-600">{{ $activeProjects ?? 4 }}</h2>
            <p class="text-xs text-gray-500">On schedule</p>
        </div>

        <div class="kpi-card">
            <p class="kpi-title">At-Risk Projects</p>
            <h2 class="kpi-value text-red-500">{{ $atRisk ?? 0 }}</h2>
            <p class="text-xs text-gray-500">Needs attention</p>
        </div>

        <div class="kpi-card">
            <p class="kpi-title">Completed Projects</p>
            <h2 class="kpi-value text-green-500">{{ $completed ?? 0 }}</h2>
            <p class="text-xs text-gray-500">Good performance</p>
        </div>

        <div class="kpi-card">
            <p class="kpi-title">Field Workers</p>
            <h2 class="kpi-value text-indigo-600">{{ $workers ?? 6 }}</h2>
            <p class="text-xs text-gray-500">Avg. progress: 45%</p>
        </div>
    </div>

    {{-- ================================
        CHART ROW (same as owner)
    ================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- PROJECT PROGRESS --}}
        <div class="glass-box h-[400px]">
            <div class="flex justify-between items-center mb-4">
                <p class="font-semibold text-gray-900">Project Progress</p>
                <span class="text-xs text-gray-500">Top 4 projects</span>
            </div>
            <div id="progressChart" class="h-[320px]"></div>
        </div>

        {{-- EMPLOYEE ALLOCATION --}}
        <div class="glass-box h-[400px]">
            <div class="flex justify-between items-center mb-4">
                <p class="font-semibold text-gray-900">Workforce Allocation</p>
                <span class="text-xs text-gray-500">Employees per project</span>
            </div>
            <div id="workforceChart" class="h-[320px]"></div>
        </div>

    </div>

</div>

{{-- ======================================
     INSERT YOUR CHART SCRIPTS (apex)
====================================== --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Dummy data for now – replace when your controller sends real data
    const progressOptions = {
        chart: { type: 'bar', height: '100%' },
        series: [{ name: 'Progress', data: [45, 70, 20, 55] }],
        xaxis: { categories: ['Proj A', 'Proj B', 'Proj C', 'Proj D'] }
    };
    new ApexCharts(document.querySelector("#progressChart"), progressOptions).render();

    const allocationOptions = {
        chart: { type: 'bar', height: '100%' },
        series: [{ name: 'Employees', data: [2,3,1,4] }],
        xaxis: { categories: ['Proj A', 'Proj B', 'Proj C', 'Proj D'] }
    };
    new ApexCharts(document.querySelector("#workforceChart"), allocationOptions).render();
});
</script>

@endsection
