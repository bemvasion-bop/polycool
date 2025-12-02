@extends('layouts.app')

@section('content')
<div class="px-10 py-8 max-w-4xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Payroll Details</h2>

    <div class="bg-white shadow rounded-lg p-6">

        <h3 class="text-xl font-semibold mb-2">
            Period: {{ $run->period_start->format('M d, Y') }} – 
            {{ $run->period_end->format('M d, Y') }}
        </h3>

        <p class="mb-4">
            <strong>Type:</strong> {{ ucfirst($run->payroll_type) }}
        </p>

        {{-- Entries Table --}}
        <div class="mt-4">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">Employee</th>
                        <th class="p-2">Gross</th>
                        <th class="p-2">Deductions</th>
                        <th class="p-2">Net</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($run->entries as $entry)
                    <tr class="border-b">
                        <td class="p-2">
                            {{ $entry->user->given_name }} {{ $entry->user->last_name }}
                        </td>
                        <td class="p-2">₱{{ number_format($entry->gross_pay, 2) }}</td>
                        <td class="p-2">₱{{ number_format($entry->deductions, 2) }}</td>
                        <td class="p-2 font-semibold">₱{{ number_format($entry->net_pay, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Finalize --}}
        @if($run->status === 'draft')
            <form action="{{ route('payroll.finalize', $run->id) }}" method="POST" class="mt-6">
                @csrf
                <button class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    Finalize Payroll
                </button>
            </form>
        @else
            <div class="mt-6 p-3 bg-green-100 text-green-800 rounded">
                Payroll finalized by: {{ optional($run->finalizedBy)->given_name ?? 'Owner' }}
            </div>
        @endif

    </div>

</div>
@endsection
