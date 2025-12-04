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
                        <td class="px-4 py-2">
                            {{ $expense->project->project_name ?? '—' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $expense->category }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            ₱{{ number_format($expense->amount, 2) }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($expense->expense_date)->toDateString() }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $expense->user->full_name ?? $expense->user->name ?? '—' }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            @switch($expense->status)
                                @case('approved')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                        Approved
                                    </span>
                                    @break

                                @case('rejected')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">
                                        Rejected
                                    </span>
                                    @break

                                @default
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                        Pending
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-4 py-2">

                                {{-- CANCELLED --}}
                                @if($expense->status === 'cancelled')
                                    <span class="text-gray-400 italic">Cancelled</span>
                                    @continue
                                @endif

                                {{-- REVERSED --}}
                                @if($expense->status === 'reversed')
                                    <span class="text-red-500 italic">Reversed</span>
                                    <a href="{{ route('expenses.show', $expense->reversal_of) }}"
                                    class="text-xs underline text-blue-600 ml-2">View original</a>
                                    @continue
                                @endif

                                {{-- APPROVED --}}
                                @if($expense->status === 'approved')
                                    <span class="px-2 py-1 text-xs bg-green-600 text-white rounded">Approved</span>
                                    @continue
                                @endif

                                {{-- PENDING — ALLOWED FOR OWNER + ACCOUNTING --}}
                                @if($expense->status === 'pending')
                                    <form action="{{ route('expenses.cancel', $expense) }}"
                                        method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Cancel this expense?');">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-xs bg-red-500 text-white rounded">
                                            Cancel
                                        </button>
                                    </form>

                                    <a href="{{ route('expenses.reissueForm', $expense) }}"
                                        class="px-3 py-1 text-xs bg-yellow-500 text-white rounded ml-1">
                                        Re-Issue
                                    </a>
                                @endif

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
