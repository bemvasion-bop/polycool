@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <h1 class="text-3xl font-semibold text-gray-900">Dashboard</h1>
@endsection

@section('content')

<style>
/* ==============================================================
   🌈 KPI CARDS
============================================================== */
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
.kpi-card::before {
    content: "";
    position: absolute;
    top: -35%; left: -15%;
    width: 150%; height: 70%;
    background: linear-gradient(to bottom, rgba(255,255,255,0.55), rgba(255,255,255,0.10));
    opacity: 0.3;
}
.kpi-card::after {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(255,255,255,0.45), transparent 70%);
    opacity: 0.3;
}

.kpi-purple { background: linear-gradient(to bottom right, #dcd6f7, #c8c2f4); }
.kpi-blue   { background: linear-gradient(to bottom right, #d8e9ff, #c2dbff); }
.kpi-pink   { background: linear-gradient(to bottom right, #ffd6ec, #ffc2e3); }

.kpi-label {
    font-size: 12px;
    letter-spacing: .08em;
    font-weight: 600;
    color: #555;
}
.kpi-value {
    font-size: 42px;
    font-weight: 700;
    margin-top: 6px;
}
.kpi-trend {
    font-size: 14px;
    font-weight: 600;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.kpi-trend i {
    width: 16px;
    height: 16px;
}

/* ==============================================================
   🔔 Notice Cards
============================================================== */
.notice-card {
    background: rgba(255,255,255,0.65);
    backdrop-filter: blur(18px);
    border: 1px solid rgba(255,255,255,0.55);
    border-radius: 20px;
    padding: 16px 20px;
    display: flex;
    gap: 12px;
    align-items: center;
    transition: .25s ease;
}
.notice-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0 18px rgba(0,0,0,0.08);
}

/* ==============================================================
   📊 Charts
============================================================== */
.chart-card {
    background: rgba(237, 237, 245, 0.55);
    backdrop-filter: blur(26px);
    border-radius: 26px;
    padding: 22px;
    border: 1px solid rgba(255,255,255,0.45);
    transition: .35s ease;
}
.chart-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0 22px rgba(150,150,255,0.35);
}

/* ==============================================================
   🍿 Scroll Reveal
============================================================== */
.reveal {
    opacity: 0;
    transform: translateY(14px);
    transition: 0.55s ease;
}
.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}

.section-gap { margin-top: 24px; }
</style>



<div class="max-w-[1500px] mx-auto mt-3 space-y-6">



    <p class="text-sm text-gray-500 flex items-center gap-2">
        <span id="liveDot" class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        Live metrics · Updated <span id="lastUpdated">just now</span>
    </p>

    {{-- KPI ROW --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="kpi-card kpi-purple reveal">
            <p class="kpi-label">Total Projects</p>
            <p class="kpi-value text-purple-700">{{ $totalProjects }}</p>
            <p class="kpi-trend text-purple-700">
                <i data-lucide="trending-up"></i> +12% from last month
            </p>
        </div>

        <div class="kpi-card kpi-blue reveal">
            <p class="kpi-label">Active Employees</p>
            <p class="kpi-value text-blue-600">{{ $activeEmployees }}</p>
            <p class="kpi-trend text-blue-700">
                <i data-lucide="trending-up"></i> Stable
            </p>
        </div>

        <div class="kpi-card kpi-pink reveal">
            <p class="kpi-label">Total Revenue</p>
            <p class="kpi-value text-pink-600">₱{{ number_format($totalRevenue, 2) }}</p>
            <p class="kpi-trend text-pink-700">
                <i data-lucide="trending-up"></i> Growing
            </p>
        </div>
    </div>


    {{-- Notifications
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 section-gap">
        <div class="notice-card reveal">
            <i data-lucide="alert-triangle" class="text-yellow-600 w-6 h-6"></i>
            <div>
                <p class="text-sm font-semibold text-gray-800">Pending Approvals</p>
                <p class="text-xs text-gray-600">
                    {{ $pendingPayments ?? 0 }} payments & {{ $pendingExpenses ?? 0 }} expenses
                </p>
            </div>
        </div>

        <div class="notice-card reveal">
            <i data-lucide="calendar" class="text-blue-600 w-6 h-6"></i>
            <div>
                <p class="text-sm font-semibold text-gray-800">Upcoming Payroll</p>
                <p class="text-xs text-gray-600">Check attendance + overtime records</p>
            </div>
        </div>
    </div>

     --}}

    {{-- Charts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 section-gap">
        <div class="chart-card reveal">
            <p class="text-sm font-semibold text-gray-700 mb-2">Monthly Revenue</p>
            <canvas id="revenueChart" height="130"></canvas>
        </div>

        <div class="chart-card reveal">
            <p class="text-sm font-semibold text-gray-700 mb-2">Project Status Distribution</p>
            <canvas id="statusChart" height="130"></canvas>
        </div>
    </div>

    <div class="section-gap">
        <div class="chart-card reveal">
            <p class="text-sm font-semibold text-gray-700 mb-2">Expense Breakdown</p>
            <canvas id="expenseChart" height="130"></canvas>
        </div>
    </div>

</div>



{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    /* ===== Charts ===== */
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: @json($monthlyRevenue->keys()),
            datasets: [{
                label: 'Revenue (PHP)',
                data: @json($monthlyRevenue->values()),
                borderColor: '#7C3AED',
                backgroundColor: 'rgba(124,58,237,0.15)',
                borderWidth: 3,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#7C3AED'
            }]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: @json($projectStatus->keys()),
            datasets: [{
                label: 'Projects',
                data: @json($projectStatus->values()),
                backgroundColor: ['#4ade80','#60a5fa','#facc15']
            }]
        },
        options: { indexAxis: 'y', responsive: true }
    });

    new Chart(document.getElementById('expenseChart'), {
        type: 'bar',
        data: {
            labels: @json($expenseBreakdown->keys()),
            datasets: [{
                label: 'Expenses (PHP)',
                data: @json($expenseBreakdown->values()),
                backgroundColor: '#8b5cf6'
            }]
        },
        options: { indexAxis: 'y', responsive: true }
    });


    /* ===== Scroll Reveal ===== */
    const revealEls = document.querySelectorAll(".reveal");
    const observer = new IntersectionObserver((entries)=>{
        entries.forEach(entry=>{
            if(entry.isIntersecting){
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealEls.forEach(el => observer.observe(el));

});

</script>



<script>
setInterval(() => {
    fetch('/owner/dashboard/kpi')
        .then(res => res.json())
        .then(data => {

            document.querySelector('#kpi-projects').innerText =
                data.totalProjects;

            document.querySelector('#kpi-employees').innerText =
                data.activeEmployees;

            document.querySelector('#kpi-revenue').innerText =
                '₱' + Number(data.totalRevenue).toLocaleString();

            document.querySelector('#kpi-updated').innerText =
                'Updated: ' + data.updated_at;
        });
}, 15000); // every 15 seconds
</script>

@endsection
