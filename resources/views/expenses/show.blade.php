@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Expense Details</h1>

        <a href="{{ route('expenses.index') }}"
           class="text-sm text-gray-600 hover:underline">
            ← Back to Expenses
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Project</p>
                <p class="font-semibold">
                    {{ $expense->project->project_name ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Category</p>
                <p class="font-semibold">{{ $expense->category }}</p>
            </div>

            <div>
                <p class="text-gray-500">Amount</p>
                <p class="font-semibold">
                    ₱{{ number_format($expense->amount, 2) }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Date</p>
                <p class="font-semibold">
                    {{ \Carbon\Carbon::parse($expense->expense_date)->toFormattedDateString() }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Added By</p>
                <p class="font-semibold">
                    {{ $expense->user->full_name ?? $expense->user->name ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Status</p>
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
            </div>
        </div>

        @if($expense->description)
            <div class="pt-4 border-t text-sm">
                <p class="text-gray-500 mb-1">Description</p>
                <p>{{ $expense->description }}</p>
            </div>
        @endif

        @if($expense->receipt_path)
            <div class="pt-4 border-t text-sm">
                <p class="text-gray-500 mb-1">Receipt</p>
                <a href="{{ asset('storage/' . $expense->receipt_path) }}"
                   target="_blank"
                   class="text-blue-600 hover:underline">
                    View uploaded receipt
                </a>
            </div>
        @endif

        {{-- Approve / Reject shortcuts (only for owner + accounting on pending) --}}
        @if($expense->status === 'pending' && in_array(auth()->user()->system_role, ['owner','accounting']))
            <div class="pt-4 border-t flex gap-3">

                <form action="{{ route('expenses.approve', $expense) }}"
                      method="POST"
                      onsubmit="return confirm('Approve this expense?');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        Approve
                    </button>
                </form>

                <form action="{{ route('expenses.reject', $expense) }}"
                      method="POST"
                      onsubmit="return confirm('Reject this expense?');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                        Reject
                    </button>
                </form>

            </div>
        @endif

    </div>
</div>
@endsection
