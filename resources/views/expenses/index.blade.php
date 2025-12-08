@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Expenses</h1>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 px-4 py-2 bg-blue-100 text-blue-800 rounded">
            {{ session('info') }}
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-sm text-gray-600">
                    <th class="px-4 py-2">Project</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2 text-right">Amount</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Added By</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
    @forelse($expenses as $expense)
        <tr class="border-t hover:bg-gray-50">

            {{-- Project --}}
            <td class="px-4 py-2">
                {{ $expense->project->project_name ?? '—' }}
            </td>

            {{-- Category --}}
            <td class="px-4 py-2">
                @if($expense->material_id)
                    {{ $expense->material->name }}
                @else
                    {{ $expense->category ?: 'Custom' }}
                @endif
            </td>

            {{-- Amount --}}
            <td class="px-4 py-2 text-right">
                ₱{{ number_format($expense->material_id ? $expense->total_cost : $expense->amount, 2) }}
            </td>

            {{-- Date --}}
            <td class="px-4 py-2">
                {{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}
            </td>

            {{-- Added By --}}
            <td class="px-4 py-2">
                @if($expense->corrected_by)
                    {{-- Show corrected_by user --}}
                    {{ optional($expense->correctedBy)->name ?? '—' }}
                @else
                    {{-- Show creator user --}}
                        {{ $expense->user->given_name ?? 'System' }}
                @endif
            </td>

            {{-- Status Badge --}}
            <td class="px-4 py-2 text-center">
                @if($expense->status === 'pending')
                    <span class="px-3 py-1 bg-yellow-400 text-black rounded text-xs">Pending</span>
                @elseif($expense->status === 'approved')
                    <span class="px-3 py-1 bg-green-500 text-white rounded text-xs">Approved</span>
                @elseif($expense->status === 'cancelled')
                    <span class="px-3 py-1 bg-red-600 text-white rounded text-xs">Cancelled</span>
                @elseif($expense->status === 'reissued')
                    <span class="px-3 py-1 bg-purple-600 text-white rounded text-xs">Reissued</span>
                @endif
            </td>

            {{-- ACTIONS --}}
            <td class="px-4 py-2 text-center">

                {{-- Auto-imported: disable --}}
                @if(str_contains(strtolower($expense->details ?? ''), 'auto-import'))
                    <span class="text-gray-400">—</span>

                {{-- Pending → Owner can Approve / Reject --}}
                @elseif($expense->status === 'pending'
                    && auth()->user()->system_role === 'owner')

                    <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="text-green-600 hover:underline text-sm">Approve</button>
                    </form>

                    <form action="{{ route('expenses.reject', $expense->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="text-red-600 hover:underline text-sm ml-2">Reject</button>
                    </form>

                {{-- Approved → Owner Cancel & Re-Issue --}}
                @elseif($expense->status === 'approved'
                    && auth()->user()->system_role === 'owner')

                    <form action="{{ route('expenses.cancel', $expense->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Cancel & re-issue this expense?')"
                            class="text-red-600 hover:underline text-sm">
                            Cancel & Re-Issue
                        </button>
                    </form>

                {{-- Reversed → Manager encodes corrected amount --}}
                @elseif($expense->status === 'reissued'
                    && auth()->user()->system_role === 'manager')

                    <button onclick="showExpenseReIssueModal({{ $expense->id }})"
                        class="text-purple-600 hover:underline text-sm">
                        Re-Issue Expense
                    </button>
                @endif



                    <script>
                    function showExpenseReIssueModal(id) {
                        const form = document.getElementById('expenseReIssueForm');
                        form.action = `/expenses/${id}/reissue`; // dynamic
                        document.getElementById('expenseReIssueModal').classList.remove('hidden');
                    }

                    function hideExpenseReIssueModal() {
                        document.getElementById('expenseReIssueModal').classList.add('hidden');
                    }
                    </script>

            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                No expenses recorded yet.
            </td>
        </tr>
    @endforelse
</tbody>

        </table>
    </div>
</div>
@endsection
