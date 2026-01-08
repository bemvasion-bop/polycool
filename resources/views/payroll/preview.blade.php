@extends('layouts.app')

@section('content')

<style>
.payslip-wrapper {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 40px 50px;
    border-radius: 16px;
    box-shadow: 0 20px 55px rgba(0,0,0,0.08);
    font-size: 14px;
}

.payslip-header {
    display: flex;
    justify-content: space-between;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 16px;
}

.payslip-title {
    text-align: center;
    font-weight: 700;
    letter-spacing: 6px;
    margin: 24px 0 30px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
}

.section {
    margin-top: 28px;
}

.section h4 {
    font-weight: 600;
    margin-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 6px;
}

.row {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
}

.total {
    border-top: 1px solid #000;
    margin-top: 8px;
    padding-top: 6px;
    font-weight: 600;
}

.net-pay {
    margin-top: 30px;
    padding: 14px;
    background: #ecfdf5;
    color: #065f46;
    font-size: 18px;
    font-weight: 700;
    text-align: right;
    border-radius: 10px;
}
</style>

<div class="payslip-wrapper">

    {{-- HEADER --}}
    <div class="payslip-header">
        <div>
            <h3 class="text-lg font-semibold">Polycool Spray Foam Services</h3>
            <p class="text-gray-500">Payroll Slip (Preview)</p>

            <p class="mt-3">
                <strong>Employee:</strong>
                {{ $slip['employee']->given_name }} {{ $slip['employee']->last_name }}
            </p>
        </div>

        <div class="text-right">
            <p><strong>Payroll Period</strong></p>
            <p>{{ $start->format('M d, Y') }} – {{ $end->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="payslip-title">PAYSLIP</div>

    {{-- EARNINGS --}}
    <div class="section">
        <h4>EARNINGS</h4>

        <div class="row">
            <span>Gross Earnings</span>
            <span>₱ {{ number_format($slip['gross_pay'], 2) }}</span>
        </div>

        <div class="row total">
            <span>Total Earnings</span>
            <span>₱ {{ number_format($slip['gross_pay'], 2) }}</span>
        </div>
    </div>

    {{-- DEDUCTIONS --}}
    <div class="section">
        <h4>DEDUCTIONS</h4>

        <div class="row">
            <span>Cash Advance</span>
            <span>₱ {{ number_format($slip['cash_advance'], 2) }}</span>
        </div>

        <div class="row total">
            <span>Total Deductions</span>
            <span>₱ {{ number_format($slip['cash_advance'], 2) }}</span>
        </div>
    </div>

    {{-- NET PAY --}}
    <div class="net-pay">
        NET PAY: ₱ {{ number_format($slip['net_pay'], 2) }}
    </div>

    {{-- ACTIONS --}}
    <form action="{{ route('payroll.generate') }}" method="POST" class="mt-8 text-right">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $slip['employee']->id }}">
        <input type="hidden" name="start_date" value="{{ $start->toDateString() }}">
        <input type="hidden" name="end_date" value="{{ $end->toDateString() }}">

        <button class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Generate Final Payroll
        </button>

        <a href="{{ route('payroll.create') }}"
           class="ml-4 text-gray-600 hover:underline">
            Cancel
        </a>
    </form>

</div>

@endsection
