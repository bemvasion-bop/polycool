@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-semibold mb-6">Manager Dashboard</h1>

    {{-- ========== TOP STAT CARDS ========== --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-xs text-gray-500 mb-1">ACTIVE / ON-GOING PROJECTS</p>
            <p class="text-3xl font-bold text-purple-700">{{ $activeProjects }}</p>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-xs text-gray-500 mb-1">AT-RISK PROJECTS</p>
            <p class="text-3xl font-bold text-red-500">{{ $atRiskProjects }}</p>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-xs text-gray-500 mb-1">COMPLETED PROJECTS</p>
            <p class="text-3xl font-bold text-green-600">{{ $completedProjects }}</p>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-xs text-gray-500 mb-1">FIELD WORKERS</p>
            <p class="text-3xl font-bold text-blue-600">{{ $fieldWorkers }}</p>
            <p class="text-xs text-gray-500 mt-1">
                Avg. progress:
                <span class="font-semibold">{{ $averageProgress }}%</span>
            </p>
        </div>

    </div>

    {{-- ========== CHARTS ========== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Project Progress --}}
        <div class="bg-white p-6 rounded-lg shadow w-full overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Project Progress</h2>
                <span class="text-xs text-gray-500">
                    Top {{ count($progressLabels) }} projects
                </span>
            </div>
            <canvas id="progressChart" class="w-full"></canvas>
        </div>

        {{-- Workforce by Project --}}
        <div class="bg-white p-6 rounded-lg shadow w-full overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Workforce Allocation</h2>
                <span class="text-xs text-gray-500">
                    Employees assigned per project
                </span>
            </div>
            <canvas id="workforceChart" class="w-full"></canvas>
        </div>

    </div>

</div>
@endsection

{{-- ========== SCRIPTS (Chart.js) ========== --}}
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ---- Convert PHP collections to JS arrays ----
        const progressLabels  = {!! json_encode($progressLabels->values()) !!};
        const progressValues  = {!! json_encode($progressValues->values()) !!};

        const workforceLabels = {!! json_encode($workforceLabels->values()) !!};
        const workforceValues = {!! json_encode($workforceValues->values()) !!};

        // ---- Project Progress (bar) ----
        const ctxProgress = document.getElementById('progressChart').getContext('2d');
        new Chart(ctxProgress, {
            type: 'bar',
            data: {
                labels: progressLabels,
                datasets: [{
                    label: 'Progress (%)',
                    data: progressValues,
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function (value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // ---- Workforce Allocation (bar) ----
        const ctxWorkforce = document.getElementById('workforceChart').getContext('2d');
        new Chart(ctxWorkforce, {
            type: 'bar',
            data: {
                labels: workforceLabels,
                datasets: [{
                    label: 'Employees Assigned',
                    data: workforceValues,
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>


<style>
    /* Prevent charts from overflowing horizontally */
    canvas {
        max-width: 100% !important;
    }
</style>
@endsection



