@extends('layouts.app')

@section('title', 'Audit Dashboard')

@section('page-header')
    <h1 class="text-3xl font-semibold text-gray-800 tracking-tight">Audit Dashboard</h1>
@endsection

@section('content')

<style>
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(26px);
        border: 1px solid rgba(255,255,255,0.45);
        padding: 28px 32px;
        box-shadow: 0 20px 55px rgba(0,0,0,0.08);
    }
</style>

{{-- KPI STATS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Approved Payments</p>
        <p class="text-3xl font-semibold">{{ $paymentStats['approved'] }}</p>
    </div>

    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Pending Payments</p>
        <p class="text-3xl font-semibold">{{ $paymentStats['pending'] }}</p>
    </div>

    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Rejected Payments</p>
        <p class="text-3xl font-semibold">{{ $paymentStats['rejected'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Approved Expenses</p>
        <p class="text-3xl font-semibold">{{ $expenseStats['approved'] }}</p>
    </div>

    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Pending Expenses</p>
        <p class="text-3xl font-semibold">{{ $expenseStats['pending'] }}</p>
    </div>

    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Rejected Expenses</p>
        <p class="text-3xl font-semibold">{{ $expenseStats['rejected'] }}</p>
    </div>
</div>

{{-- RECENT SYSTEM LOGS --}}
<div class="glass-card">
    <h2 class="text-lg font-semibold mb-4">Recent Audit Logs</h2>

    <table class="w-full text-sm">
        <thead class="border-b border-gray-300">
            <tr>
                <th class="text-left py-2">Action</th>
                <th class="text-left py-2">Details</th>
                <th class="text-left py-2">Performed By</th>
                <th class="text-left py-2">Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentLogs as $log)
            <tr class="border-b border-gray-200">
                <td class="py-3">{{ $log->action }}</td>
                <td>{{ $log->details }}</td>
                <td>{{ $log->user->name ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y | h:i A') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-4 text-center text-gray-500">
                    No audit logs yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
