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
                    <td class="space-x-2">

                        {{-- APPROVE --}}
                        <form method="POST"
                              action="{{ route('payments.approve', $pay) }}"
                              class="inline">
                            @csrf
                            <button class="text-green-600 hover:underline">
                                Approve
                            </button>
                        </form>

                        {{-- REJECT --}}
                        <form method="POST"
                              action="{{ route('payments.reject', $pay) }}"
                              class="inline">
                            @csrf
                            <button class="text-red-600 hover:underline">
                                Reject
                            </button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        No pending payments.
                    </td>
                </tr>
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
                    <td class="py-3">
                        {{ ucfirst($exp->expense_type) }}
                    </td>

                    <td>
                        ₱{{ number_format($exp->total_cost ?? $exp->amount, 2) }}
                    </td>

                    <td>
                        {{ $exp->submittedBy->name ?? '—' }}
                    </td>

                    <td class="space-x-2">

                        {{-- APPROVE --}}
                        <form method="POST"
                              action="{{ route('expenses.approve', $exp) }}"
                              class="inline">
                            @csrf
                            <button class="text-green-600 hover:underline">
                                Approve
                            </button>
                        </form>

                        {{-- REJECT --}}
                        <form method="POST"
                              action="{{ route('expenses.reject', $exp) }}"
                              class="inline">
                            @csrf
                            <button class="text-red-600 hover:underline">
                                Reject
                            </button>
                        </form>

                        {{-- REQUEST RE-ISSUE --}}
                        <button
                            onclick="openReissueModal({{ $exp->id }})"
                            class="text-purple-600 hover:underline">
                            Request Re-Issue
                        </button>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-4 text-center text-gray-500">
                        No pending expenses.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>



{{-- ============================================================ --}}
{{-- 🔁 REQUEST RE-ISSUE MODAL --}}
{{-- ============================================================ --}}
<div id="reissueModal"
     class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-50">

    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-3">Request Expense Re-Issue</h3>

        <form method="POST" id="reissueForm">
            @csrf

            <textarea name="reason"
                      required
                      class="w-full border rounded-lg p-3 text-sm"
                      placeholder="Explain what needs to be corrected..."></textarea>

            <div class="mt-4 flex justify-end gap-3">
                <button type="button"
                        onclick="closeReissueModal()"
                        class="px-4 py-2 text-sm border rounded">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white text-sm rounded">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>



<script>
    function openReissueModal(expenseId) {
        const form = document.getElementById('reissueForm');
        form.action = `/expenses/${expenseId}/request-reissue`;
        document.getElementById('reissueModal').classList.remove('hidden');
    }

    function closeReissueModal() {
        document.getElementById('reissueModal').classList.add('hidden');
    }
</script>

@endsection
