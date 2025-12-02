@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Payroll Runs</h2>

    <div class="mb-6">
        <a href="{{ route('payroll.create') }}"
           class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            + Generate Payroll
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3">Period</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Total Gross</th>
                    <th class="p-3">Total Deductions</th>
                    <th class="p-3">Net</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($runs as $run)
                <tr class="border-b">
                    <td class="p-3">
                        {{ $run->period_start->format('M d, Y') }} – 
                        {{ $run->period_end->format('M d, Y') }}
                    </td>

                    <td class="p-3 capitalize">
                        {{ $run->payroll_type }}
                    </td>

                    <td class="p-3">
                        ₱{{ number_format($run->total_gross, 2) }}
                    </td>

                    <td class="p-3">
                        ₱{{ number_format($run->total_deductions, 2) }}
                    </td>

                    <td class="p-3 font-semibold">
                        ₱{{ number_format($run->total_net, 2) }}
                    </td>

                    <td class="p-3">
                        @if($run->status === 'draft')
                            <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded">Draft</span>
                        @else
                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded">Finalized</span>
                        @endif
                    </td>

                    <td class="p-3">
                        <a href="{{ route('payroll.show', $run->id) }}"
                           class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        No payroll runs yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>
@endsection
