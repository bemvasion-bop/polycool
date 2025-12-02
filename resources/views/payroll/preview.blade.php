@extends('layouts.app')

@section('content')
<div class="px-10 py-8 max-w-4xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Payroll Preview</h2>

    <div class="bg-white shadow rounded-lg p-6">

        <div class="border-b pb-4 flex justify-between">
            <div>
                <h3 class="font-semibold text-xl">Polycool Spray Foam Services</h3>
                <p>Salary Slip</p>

                <p class="mt-2">
                    <strong>Employee:</strong> 
                    {{ $slip['employee']->given_name }} {{ $slip['employee']->last_name }}
                </p>
            </div>

            <div class="text-right">
                <p><strong>Period:</strong></p>
                <p>{{ $start->format('M d, Y') }} – {{ $end->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Earnings Section --}}
        <div class="mt-6">
            <h4 class="font-semibold mb-2">Earnings</h4>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">Description</th>
                        <th class="p-2">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="p-2">Gross Earnings</td>
                        <td class="p-2">₱{{ number_format($slip['gross_pay'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Deductions --}}
        <div class="mt-6">
            <h4 class="font-semibold mb-2">Deductions</h4>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">Description</th>
                        <th class="p-2">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="p-2">Cash Advance</td>
                        <td class="p-2">₱{{ number_format($slip['cash_advance'], 2) }}</td>
                    </tr>
                    <tr class="border-b bg-gray-50">
                        <td class="p-2 font-semibold">Total Deductions</td>
                        <td class="p-2 font-semibold">₱{{ number_format($slip['cash_advance'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Net Pay --}}
        <div class="mt-6 p-4 bg-green-100 text-green-800 rounded text-center text-xl font-semibold">
            Net Pay for this period: ₱{{ number_format($slip['net_pay'], 2) }}
        </div>

        {{-- Confirm Button --}}
        <form action="{{ route('payroll.generate') }}" method="POST" class="mt-6">
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

</div>
@endsection
