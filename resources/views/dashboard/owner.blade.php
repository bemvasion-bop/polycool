@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Owner Dashboard</h1>


        <button id="syncBtn"
            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
            Sync Data to Cloud
        </button>

        <script>
        document.getElementById('syncBtn').addEventListener('click', function () {
            if (!confirm("Sync all pending data to cloud?")) return;

            let btn = this;
            btn.disabled = true;
            btn.innerHTML = 'Syncing... ⏳';

            fetch("{{ route('sync.all') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message ?? "Sync completed!");
            })
            .catch(() => {
                alert("Sync failed! Check network.");
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Sync Data to Cloud';
            });
        });
        </script>


    </div>




    {{-- ======================= --}}
    {{-- TOP CARDS --}}
    {{-- ======================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">TOTAL PROJECTS</p>
            <p class="text-3xl font-bold text-purple-700">{{ $totalProjects }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">ACTIVE EMPLOYEES</p>
            <p class="text-3xl font-bold text-green-600">{{ $activeEmployees }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">TOTAL REVENUE</p>
            <p class="text-3xl font-bold text-pink-600">
                Php {{ number_format($totalRevenue, 2) }}
            </p>
        </div>

    </div>


    {{-- ======================= --}}
    {{-- ANALYTICS GRAPHS --}}
    {{-- ======================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">

        {{-- LINE GRAPH — MONTHLY REVENUE --}}
        <div class="bg-white p-6 shadow rounded">
            <h3 class="text-xl font-semibold mb-4">Monthly Revenue</h3>
            <canvas id="revenueChart"></canvas>
        </div>

        {{-- STACKED BAR — PROJECT STATUS --}}
        <div class="bg-white p-6 shadow rounded">
            <h3 class="text-xl font-semibold mb-4">Project Status Distribution</h3>
            <canvas id="projectStatusChart"></canvas>
        </div>

        {{-- HORIZONTAL BAR — EXPENSE BREAKDOWN --}}
        <div class="bg-white p-6 shadow rounded">
            <h3 class="text-xl font-semibold mb-4">Expense Breakdown</h3>
            <canvas id="expenseChart"></canvas>
        </div>

    </div>
</div>


{{-- ========================================= --}}
{{-- CHART.JS DATA --}}
{{-- ========================================= --}}
<script>
    // Monthly Revenue (Line Graph)
    const revenueLabels = {!! json_encode($monthlyRevenue->keys()) !!};
    const revenueData   = {!! json_encode($monthlyRevenue->values()) !!};

    // Project Status (Stacked Bar)
    const projectLabels = {!! json_encode($projectStatus->keys()) !!};
    const projectValues = {!! json_encode($projectStatus->values()) !!};

    // Expense Breakdown (Horizontal Bar)
    const expenseLabels = {!! json_encode($expenseBreakdown->keys()) !!};
    const expenseValues = {!! json_encode($expenseBreakdown->values()) !!};



    // ---------------------------------
    // 1️⃣ MONTHLY REVENUE — LINE GRAPH
    // ---------------------------------
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue (PHP)',
                data: revenueData,
                borderColor: '#4F46E5',
                backgroundColor: '#4F46E5',
                tension: 0.3,
                fill: false,
                borderWidth: 3,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });



    // ---------------------------------
    // 2️⃣ PROJECT STATUS — STACKED BAR
    // ---------------------------------
    new Chart(document.getElementById('projectStatusChart'), {
        type: 'bar',
        data: {
            labels: projectLabels,
            datasets: [{
                label: 'Projects',
                data: projectValues,
                backgroundColor: ['#22C55E', '#3B82F6', '#F59E0B', '#EF4444'],
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false }},
            responsive: true,
            scales: {
                x: { stacked: true, beginAtZero: true },
                y: { stacked: true }
            }
        }
    });



    // --------------------------------------------
    // 3️⃣ EXPENSE BREAKDOWN — HORIZONTAL BAR CHART
    // --------------------------------------------
    new Chart(document.getElementById('expenseChart'), {
        type: 'bar',
        data: {
            labels: expenseLabels,
            datasets: [{
                label: 'Expenses (PHP)',
                data: expenseValues,
                backgroundColor: '#6366F1'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y', // HORIZONTAL
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

</script>
@endsection
