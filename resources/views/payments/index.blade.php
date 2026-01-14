@extends('layouts.app')

@section('title', 'Payments')

@section('page-header')
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Payments</h1>

        @if(in_array(auth()->user()->system_role, ['owner','accounting']))
            {{--  <button onclick="document.getElementById('addPaymentModal').classList.remove('hidden')"
                class="px-5 py-2 rounded-xl text-white font-semibold shadow-md
                       bg-gradient-to-r from-purple-500 to-fuchsia-500
                       hover:opacity-90 transition">
                + Add Payment
            </button>

            --}}
        @endif
    </div>
@endsection

@section('content')

<style>
    .glass-card {
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(26px);
        -webkit-backdrop-filter: blur(26px);
        border-radius: 26px;
        border: 1px solid rgba(255,255,255,0.45);
        box-shadow: 0 15px 35px rgba(0,0,0,0.06);
        padding: 24px 32px;
    }
    table thead th {
        font-size: 12px;
        font-weight: 600;
        color: rgba(0,0,0,0.65);
        text-transform: uppercase;
        letter-spacing: .05rem;
        padding-bottom: 12px;
    }
    table tbody td {
        padding: 16px 12px;
        font-size: 14px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        white-space: nowrap;
    }
    tr:hover td {
        background: rgba(255,255,255,0.7);
    }
    .pill { padding:4px 12px;border-radius:10px;font-size:12px;font-weight:600; }
    .pill-pending { background:#fff3cd;color:#856404; }
    .pill-approved { background:#c6f6d5;color:#087F37; }
    .pill-rejected { background:#f8d7da;color:#842029; }
    .pill-reversed { background:#e5e7eb;color:#1f2937; }
    .action-link { font-size:13px;font-weight:600;color:#7c3aed; }
    .action-link:hover { text-decoration: underline; }
</style>

<div class="max-w-7xl mx-auto space-y-8">

    @if(session('success'))
        <div class="px-4 py-2 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card overflow-x-auto">

        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left">Project</th>
                    <th class="text-right">Amount</th>
                    <th class="text-left">Method</th>
                    <th class="text-left">Date</th>
                    <th class="text-center">Status</th>
                    <th class="text-left">Added By</th>
                    <th class="text-left">Approved By</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->project->project_name }}</td>
                    <td class="text-right">₱{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>

                    <td class="text-center">
                        @if($payment->status === 'pending')
                            <span class="pill pill-pending">Pending</span>
                        @elseif($payment->status === 'approved')
                            <span class="pill pill-approved">Approved</span>
                        @elseif($payment->status === 'rejected')
                            <span class="pill pill-rejected">Rejected</span>
                        @elseif($payment->status === 'reversed')
                            <span class="pill pill-reversed">Reversed</span>
                        @endif
                    </td>

                    <td>{{ optional($payment->addedBy)->full_name ?? '—' }}</td>
                    <td>{{ optional($payment->approver)->full_name ?? '—' }}</td>

                    <td class="text-right">
                        <a href="{{ route('payments.show', $payment->id) }}" class="action-link">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@endsection
