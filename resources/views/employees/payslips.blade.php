@extends('layouts.app')

@section('content')

<style>
.payslip-wrapper {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 20px 55px rgba(0,0,0,0.08);
}

.payslip-title {
    text-align: center;
    letter-spacing: 6px;
    font-weight: 700;
    margin: 24px 0;
}

.section {
    margin-top: 24px;
}

.row {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
}

.total {
    border-top: 1px solid #000;
    margin-top: 6px;
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

.signature {
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
    text-align: center;
}
.signature div {
    width: 40%;
}
.signature hr {
    margin-top: 40px;
}
</style>

<div class="max-w-5xl mx-auto px-6">

    {{-- MONTH / YEAR SELECTOR --}}
    <form method="GET" class="flex gap-4 mb-6">
        <select name="month" class="border rounded px-3 py-2">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
            @endforeach
        </select>

        <select name="year" class="border rounded px-3 py-2">
            @foreach(range(now()->year - 3, now()->year) as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endforeach
        </select>

        <button class="px-4 py-2 bg-purple-600 text-white rounded">
            Show Payslip
        </button>
    </form>

    @if(!$payslip)
        <div class="text-center text-gray-500 mt-20">
            No payslip available for this period.
        </div>
    @else

    <div class="payslip-wrapper">

        <div class="flex justify-between border-b pb-4">
            <div>
                <h3 class="font-semibold text-lg">Polycool Spray Foam Services</h3>
                <p class="text-gray-500">Employee Payslip</p>
                <p class="mt-2"><strong>Name:</strong> {{ auth()->user()->full_name }}</p>
            </div>

            <div class="text-right">
                <p><strong>Month:</strong>
                    {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
                </p>
            </div>
        </div>

        <div class="payslip-title">PAYSLIP</div>

        {{-- EARNINGS --}}
        <div class="section">
            <h4 class="font-semibold mb-2">EARNINGS</h4>
            <div class="row">
                <span>Gross Pay</span>
                <span>₱ {{ number_format($payslip->gross_pay, 2) }}</span>
            </div>
            <div class="row total">
                <span>Total Earnings</span>
                <span>₱ {{ number_format($payslip->gross_pay, 2) }}</span>
            </div>
        </div>

        {{-- DEDUCTIONS --}}
        <div class="section">
            <h4 class="font-semibold mb-2">DEDUCTIONS</h4>
            <div class="row">
                <span>Cash Advance</span>
                <span>₱ {{ number_format($payslip->cash_advance, 2) }}</span>
            </div>
            <div class="row total">
                <span>Total Deductions</span>
                <span>₱ {{ number_format($payslip->cash_advance, 2) }}</span>
            </div>
        </div>

        <div class="net-pay">
            NET PAY: ₱ {{ number_format($payslip->net_pay, 2) }}
        </div>

        {{-- SIGNATURES --}}
        <div class="signature">
            <div>
                <hr>
                <p class="font-semibold">Prepared by</p>
                <p class="text-sm">Accounting</p>
            </div>

            <div>
                <hr>
                <p class="font-semibold">Approved by</p>
                <p class="text-sm">Owner</p>
            </div>
        </div>

    </div>
    @endif
</div>

@endsection
