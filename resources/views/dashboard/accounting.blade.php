@extends('layouts.app')

@section('title', 'Accounting Dashboard')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-semibold mb-6">Accounting Dashboard</h1>

    {{-- TOP CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">Pending Payments</p>
            <p class="text-3xl font-bold text-purple-700">{{ $pendingPayments }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">Approved (This Month)</p>
            <p class="text-3xl font-bold text-green-600">₱{{ number_format($approvedPayments,2) }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">Expenses (This Month)</p>
            <p class="text-3xl font-bold text-red-600">₱{{ number_format($monthlyExpenses,2) }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-500">Cash Advance Pending</p>
            <p class="text-3xl font-bold text-blue-600">{{ $cashAdvancePending }}</p>
        </div>

    </div>

    {{-- PROFIT --}}
    <div class="bg-white p-6 rounded shadow mt-6">
        <p class="text-lg font-semibold">Profit Snapshot</p>
        <p class="text-3xl font-bold mt-2 {{ $profit < 0 ? 'text-red-600' : 'text-green-600' }}">
            ₱{{ number_format($profit,2) }}
        </p>
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

        {{-- Monthly Expense Trend --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl mb-4">Monthly Expense Trend</h3>
            <canvas id="expenseTrendChart"></canvas>
        </div>

        {{-- Expense Breakdown --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl mb-4">Expense Breakdown</h3>
            <canvas id="expenseBreakdownChart"></canvas>
        </div>

        {{-- Payment Summary --}}
        <div class="bg-white p-6 rounded shadow col-span-full">
            <h3 class="text-xl mb-4">Payment Summary</h3>
            <canvas id="paymentSummaryChart"></canvas>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const expenseTrendLabels = {!! json_encode($expenseTrend->keys()) !!};
    const expenseTrendData = {!! json_encode($expenseTrend->values()) !!};

    new Chart(document.getElementById('expenseTrendChart'), {
        type: 'line',
        data: {
            labels: expenseTrendLabels,
            datasets: [{
                label: "Expenses (PHP)",
                data: expenseTrendData,
                borderColor: "#4F46E5",
                backgroundColor: "rgba(79,70,229,0.2)",
                tension: 0.4
            }]
        }
    });

    const breakdownLabels = {!! json_encode($expenseBreakdown->keys()) !!};
    const breakdownData = {!! json_encode($expenseBreakdown->values()) !!};

    new Chart(document.getElementById('expenseBreakdownChart'), {
        type: 'bar',
        data: {
            labels: breakdownLabels,
            datasets: [{
                label: "Expenses (PHP)",
                data: breakdownData,
                backgroundColor: "#10B981"
            }]
        }
    });

    const paymentLabels = Object.keys(@json($paymentSummary));
    const paymentData = Object.values(@json($paymentSummary));

    new Chart(document.getElementById('paymentSummaryChart'), {
        type: 'bar',
        data: {
            labels: paymentLabels,
            datasets: [{
                label: "Count",
                data: paymentData,
                backgroundColor: ["#F59E0B", "#10B981", "#EF4444"]
            }]
        }
    });
</script>
@endsection
