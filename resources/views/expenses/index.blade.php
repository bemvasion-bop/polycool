@extends('layouts.app')

@section('title', 'Expenses')

@section('page-header')
<div class="flex justify-between items-center">
    <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Expenses</h1>
</div>
@endsection

@section('content')

<style>
    /* Glass Container */
    .glass-wrapper {
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(26px);
        -webkit-backdrop-filter: blur(26px);
        border-radius: 26px;
        border: 1px solid rgba(255,255,255,0.45);
        padding: 24px 32px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    }

    /* Table Header */
    table thead th {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .04rem;
        color: rgba(0,0,0,0.6);
        text-transform: uppercase;
        padding-bottom: 12px;
        white-space: nowrap;
    }

    /* Table Body */
    table tbody td {
        padding: 16px 12px;
        font-size: 14px;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }

    tr:hover td {
        background: rgba(255,255,255,0.6);
    }

    /* Status Pills */
    .pill {
        padding: 4px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
    }
    .pill-pending {
        background: #fff3cd;
        color: #856404;
    }
    .pill-approved {
        background: #c6f6d5;
        color: #087F37;
    }
    .pill-cancelled {
        background: #f8d7da;
        color: #842029;
    }
    .pill-reissued {
        background: #e9d5ff;
        color: #5b21b6;
    }

    /* Actions */
    .action-link {
        font-size: 13px;
        font-weight: 600;
    }
    .link-green { color:#0e7730; }
    .link-red { color:#d21d2a; }
    .link-purple { color:#5c27b7; }
    .action-link:hover { text-decoration: underline; }
</style>

<div class="max-w-7xl mx-auto space-y-8">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded">
            {{ session('info') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="glass-wrapper overflow-x-auto">

        <table class="w-full">
            <thead>
                <tr class="text-sm border-b border-gray-200">
                    <th class="text-left">Project</th>
                    <th class="text-left">Category</th>
                    <th class="text-right">Amount</th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Added By</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($expenses as $expense)
                <tr class="transition">

                    {{-- Project --}}
                    <td>{{ $expense->project->project_name ?? '—' }}</td>

                    {{-- Category --}}
                    <td>
                        @if($expense->material_id)
                            {{ $expense->material->name }}
                        @else
                            {{ $expense->category ?: 'Custom' }}
                        @endif
                    </td>

                    {{-- Amount --}}
                    <td class="text-right">
                        ₱{{ number_format($expense->material_id ? $expense->total_cost : $expense->amount, 2) }}
                    </td>

                    {{-- Date --}}
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>

                    {{-- Added By --}}
                    <td>
                        @if($expense->corrected_by)
                            {{ optional($expense->correctedBy)->name ?? '—' }}
                        @else
                            {{ $expense->user->given_name ?? 'System' }}
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="text-center">
                        @if($expense->status === 'pending')
                            <span class="pill pill-pending">Pending</span>
                        @elseif($expense->status === 'approved')
                            <span class="pill pill-approved">Approved</span>
                        @elseif($expense->status === 'cancelled')
                            <span class="pill pill-cancelled">Cancelled</span>
                        @elseif($expense->status === 'reissued')
                            <span class="pill pill-reissued">Reissued</span>
                        @endif
                    </td>

                    {{-- ACTIONS --}}
                    <td class="text-right space-x-3">

                        @if(str_contains(strtolower($expense->details ?? ''), 'auto-import'))
                            <span class="text-gray-400">—</span>

                        {{-- Pending: Owner only --}}
                        @elseif($expense->status === 'pending'
                            && auth()->user()->system_role === 'owner')

                            <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="action-link link-green">Approve</button>
                            </form>

                            <form action="{{ route('expenses.reject', $expense->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="action-link link-red">Reject</button>
                            </form>

                        {{-- Approved: Owner --}}
                        @elseif($expense->status === 'approved'
                            && auth()->user()->system_role === 'owner')

                            <form action="{{ route('expenses.cancel', $expense->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Cancel & re-issue this expense?')"
                                        class="action-link link-red">
                                    Cancel & Re-Issue
                                </button>
                            </form>

                        {{-- Reissued: Manager --}}
                        @elseif($expense->status === 'reissued'
                            && auth()->user()->system_role === 'manager')

                            <button onclick="showExpenseReIssueModal({{ $expense->id }})"
                                class="action-link link-purple">
                                Re-Issue Expense
                            </button>

                        @endif

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-gray-500">
                        No expenses recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>

{{-- MODAL SCRIPT (unchanged) --}}
<script>
function showExpenseReIssueModal(id) {
    document.getElementById('expenseReIssueForm').action = `/expenses/${id}/reissue`;
    document.getElementById('expenseReIssueModal').classList.remove('hidden');
}
function hideExpenseReIssueModal() {
    document.getElementById('expenseReIssueModal').classList.add('hidden');
}
</script>

@endsection
