@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Payroll Overview</h2>

    <!-- GRID WRAPPER -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Payroll This Month -->
        <div class="bg-white shadow p-6 rounded">
            <p class="text-gray-600">Payroll This Month</p>
            <p class="text-2xl font-bold text-purple-600">
                ₱{{ number_format($payrollThisMonth, 2) }}
            </p>
        </div>

        <!-- Payroll YTD -->
        <div class="bg-white shadow p-6 rounded">
            <p class="text-gray-600">Year-To-Date Payroll</p>
            <p class="text-2xl font-bold text-purple-600">
                ₱{{ number_format($payrollYTD, 2) }}
            </p>
        </div>

        <!-- Next Cutoff -->
        <div class="bg-white shadow p-6 rounded">
            <p class="text-gray-600">Next Cutoff</p>
            <p class="text-2xl font-bold text-purple-600">
                {{ $nextCutoff->format('M d, Y') }}
            </p>
        </div>

        <!-- Employees Paid -->
        <div class="bg-white shadow p-6 rounded">
            <p class="text-gray-600">Employees Paid (This Month)</p>
            <p class="text-2xl font-bold text-purple-600">
                {{ $employeesPaidThisMonth }}
            </p>
        </div>

        <!-- Cash Advance Deductions -->
        <div class="bg-white shadow p-6 rounded">
            <p class="text-gray-600">Cash Advance Deductions (Month)</p>
            <p class="text-2xl font-bold text-purple-600">
                ₱{{ number_format($cashAdvancesDeducted, 2) }}
            </p>
        </div>

    </div>

    <!-- TREND CHART -->
    <div class="bg-white shadow p-6 rounded mt-10">
        <h3 class="font-semibold text-lg mb-4">Payroll Trend (Last 6 Months)</h3>

        <canvas id="payrollChart" height="120"></canvas>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('payrollChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($lastSixMonths->pluck('month')) !!},
        datasets: [{
            label: 'Payroll Amount',
            data: {!! json_encode($lastSixMonths->pluck('total')) !!},
            backgroundColor: '#7C3AED',
        }]
    }
});
</script>

@endsection
