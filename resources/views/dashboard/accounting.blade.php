@extends('layouts.app')

@section('title', 'Accounting Dashboard')

@section('page-header')
    <h1 class="text-3xl font-semibold text-gray-800 tracking-tight">Accounting Dashboard</h1>
@endsection

@section('content')

<style>
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(26px) saturate(180%);
        -webkit-backdrop-filter: blur(26px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.45);
        box-shadow: 0 20px 55px rgba(0,0,0,0.08);
        padding: 28px 32px;
    }
    .status-pill {
        padding: 5px 14px;
        border-radius: 22px;
        font-size: 12px;
        font-weight: 600;
        width: max-content;
    }
    .pending { background: #fff2ce; color: #B98500; }
    .approved { background: #d4fce1; color: #007443; }
    .rejected { background: #ffcfcf; color: #8A0E0E; }
</style>

{{-- ============================================================ --}}
{{-- 🌈 KPI CARDS --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Pending Payments</p>
        <p class="text-3xl font-semibold text-gray-900">{{ $pendingPayments }}</p>
    </div>

    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Cash Advances</p>
        <p class="text-3xl font-semibold text-gray-900">{{ $cashAdvancePending }}</p>
    </div>

    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Approved Payments (This Month)</p>
        <p class="text-3xl font-semibold text-gray-900">₱{{ number_format($approvedPayments, 2) }}</p>
    </div>


    {{--
    <div class="glass-card">
        <p class="text-sm text-gray-600 mb-1">Reversal History</p>
        <p class="text-3xl font-semibold text-gray-900">{{ $reversalCount }}</p>
    </div>
    --}}

</div>


{{-- ============================================================ --}}
{{-- 🧾 PENDING PAYMENTS --}}
{{-- ============================================================ --}}
<div class="glass-card mb-8">
    <h2 class="text-lg font-semibold mb-4">Pending Payments</h2>

    <table class="w-full text-sm">
        <thead class="border-b border-gray-300">
            <tr>
                <th class="text-left py-2">Project</th>
                <th class="text-left py-2">Client</th>
                <th class="text-left py-2">Amount</th>
                <th class="text-left py-2">Submitted By</th>
                <th class="text-left py-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingPaymentList as $pay)
                <tr class="border-b border-gray-200">
                    <td class="py-3">{{ $pay->project->project_name }}</td>
                    <td>{{ $pay->project->client->client_name }}</td>
                    <td>₱{{ number_format($pay->amount, 2) }}</td>
                    <td>{{ $pay->submittedBy->name }}</td>
                    <td>
                        <button class="text-green-600 hover:underline">Approve</button>
                        <button class="text-red-600 hover:underline">Reject</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-center text-gray-500">No pending payments.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ============================================================ --}}
{{-- 📦 PENDING EXPENSES --}}
{{-- ============================================================ --}}
<div class="glass-card mb-8">
    <h2 class="text-lg font-semibold mb-4">Pending Expenses</h2>

    <table class="w-full text-sm">
        <thead class="border-b border-gray-300">
            <tr>
                <th class="text-left py-2">Type</th>
                <th class="text-left py-2">Amount</th>
                <th class="text-left py-2">Submitted By</th>
                <th class="text-left py-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingExpenseList as $exp)
                <tr class="border-b border-gray-200">
                    <td class="py-3">{{ ucfirst($exp->expense_type) }}</td>
                    <td>₱{{ number_format($exp->total_cost ?? $exp->amount, 2) }}</td>
                    <td>{{ $exp->submittedBy->name ?? '—' }} </td>
                    <td>
                        <button class="text-green-600 hover:underline">Approve</button>
                        <button class="text-red-600 hover:underline">Reject</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-500">No pending expenses.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ============================================================ --}}
{{-- 🔄 REVERSAL HISTORY --}}
{{-- ============================================================ --}}
<div class="glass-card mb-10">
    <h2 class="text-lg font-semibold mb-4">Reversal History</h2>

    <table class="w-full text-sm">
        <thead class="border-b border-gray-300">
            <tr>
                <th class="text-left py-2">Reference</th>
                <th class="text-left py-2">Type</th>
                <th class="text-left py-2">Amount</th>
                <th class="text-left py-2">Date</th>
            </tr>
        </thead>
        <tbody>

            {{--
            @forelse($reversals as $rev)
                <tr class="border-b border-gray-200">
                    <td class="py-3">{{ $rev->reference_id }}</td>
                    <td>{{ ucfirst($rev->type) }}</td>
                    <td>₱{{ number_format($rev->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($rev->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-500">No reversals yet.</td></tr>
            @endforelse

            --}}
        </tbody>
    </table>
</div>

@endsection
