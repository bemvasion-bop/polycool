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
                        <td class="px-4 py-2 text-center">
                            <div class="inline-flex items-center gap-2">

                                {{-- VIEW --}}
                                <a href="{{ route('expenses.show', $expense) }}"
                                   class="px-3 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100">
                                    View
                                </a>

                                @if($expense->status === 'pending' && in_array(auth()->user()->system_role, ['owner','accounting']))
                                    {{-- APPROVE --}}
                                    <form action="{{ route('expenses.approve', $expense) }}"
                                          method="POST"
                                          onsubmit="return confirm('Approve this expense?');">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
                                            Approve
                                        </button>
                                    </form>

                                    {{-- REJECT --}}
                                    <form action="{{ route('expenses.reject', $expense) }}"
                                          method="POST"
                                          onsubmit="return confirm('Reject this expense?');">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                            Reject
                                        </button>
                                    </form>
                                @endif

                            </div>
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
