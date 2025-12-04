@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Cash Advance Requests</h2>
@endsection

@section('content')

<style>
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.78);
        backdrop-filter: blur(22px);
        border: 1px solid rgba(255,255,255,0.45);
        box-shadow: 0 18px 60px rgba(0,0,0,0.12);
        padding: 0;
        overflow: hidden;
        width: 100%;
    }

    table {
        border-collapse: separate;
        border-spacing: 0;
    }
    table th, table td {
        padding: 18px 24px;
        border: none !important;
    }

    thead tr {
        background: rgba(255,255,255,0.6);
        font-weight: 600;
        color: #4b5563;
    }

    tbody tr {
        transition: .2s ease;
    }
    tbody tr:hover {
        background: rgba(0,0,0,0.03);
    }

    .pill {
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 600;
    }

    .action-btn {
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        color: white;
        transition: .15s;
    }
    .action-approve {
        background: #22c55e;
    }
    .action-approve:hover { background: #16a34a; }

    .action-reject {
        background: #ef4444;
    }
    .action-reject:hover { background: #dc2626; }
</style>


{{-- SUCCESS MESSAGE --}}
@if(session('success'))
    <div class="mb-6 px-4 py-3 bg-green-100 text-green-700 rounded-xl border border-green-200">
        {{ session('success') }}
    </div>
@endif


{{-- TABLE CARD --}}
<div class="glass-card">

    <table class="w-full text-left">

        <thead>
        <tr>
            <th>Employee</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Request Date</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
        </tr>
        </thead>

        <tbody>
        @forelse($advances as $adv)
        <tr>

            <td class="text-gray-900 font-medium">
                {{ $adv->employee->full_name }}
            </td>

            <td class="text-gray-900">
                ₱{{ number_format($adv->amount, 2) }}
            </td>

            <td class="text-gray-700">
                {{ $adv->reason ?? '—' }}
            </td>

            <td class="text-gray-700">
                {{ \Carbon\Carbon::parse($adv->request_date)->format('M d, Y') }}
            </td>

            <td>
                @if($adv->status == 'pending')
                    <span class="pill bg-yellow-100 text-yellow-700">Pending</span>
                @elseif($adv->status == 'approved')
                    <span class="pill bg-green-100 text-green-700">Approved</span>
                @else
                    <span class="pill bg-red-100 text-red-700">Rejected</span>
                @endif
            </td>

            <td class="text-right">

                @if($adv->status === 'pending')
                <div class="flex justify-end gap-2">

                    {{-- Approve --}}
                    <form method="POST" action="{{ route('cashadvance.approve', $adv->id) }}">
                        @csrf
                        <button class="action-btn action-approve">Approve</button>
                    </form>

                    {{-- Reject --}}
                    <form method="POST" action="{{ route('cashadvance.reject', $adv->id) }}">
                        @csrf
                        <button class="action-btn action-reject">Reject</button>
                    </form>

                </div>
                @else
                    <span class="text-gray-400 text-sm italic">No actions</span>
                @endif

            </td>

        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-6 text-gray-500">
                No cash advance requests found.
            </td>
        </tr>
        @endforelse
        </tbody>

    </table>

</div>

@endsection
