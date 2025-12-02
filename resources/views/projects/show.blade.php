@extends('layouts.app')

@section('content')
<div class="p-10">
    <div class="max-w-5xl mx-auto bg-white shadow p-8 rounded-lg">

        {{-- ========================================= --}}
        {{-- WEATHER SECTION --}}
        {{-- ========================================= --}}
        @if($weatherData)

            {{-- RISK BANNER --}}
            @if($weatherData['risk'] === 'high')
                <div class="bg-red-200 text-red-900 p-4 rounded mb-6">
                    ⛈️ High chance of rain — avoid spraying.
                </div>

            @elseif($weatherData['risk'] === 'moderate')
                <div class="bg-yellow-200 text-yellow-900 p-4 rounded mb-6">
                    🌦️ Possible rain — evaluate conditions carefully.
                </div>

            @else
                <div class="bg-green-200 text-green-900 p-4 rounded mb-6">
                    ☀️ Low chance of rain — good spraying weather.
                </div>
            @endif

            {{-- 5-DAY FORECAST --}}

            <p><strong>Progress:</strong></p>
            <x-progress-bar :value="$project->progress" />
            <hr class="my-6">

            <h3 class="text-xl font-semibold mb-4">
                5-Day Forecast for: {{ $weatherData['location'] }}
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-10">
                @foreach($weatherData['forecast'] as $day)
                    <div class="border rounded-lg p-4 text-center shadow-sm bg-white">
                        <p class="font-semibold mb-2">
                            {{ \Carbon\Carbon::parse($day['date'])->format('D, M j') }}
                        </p>

                        <img src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png"
                             class="mx-auto w-16 h-16">

                        <p class="mt-2 font-medium">
                            {{ $day['temp_min'] }}°C / {{ $day['temp_max'] }}°C
                        </p>

                        <p class="text-gray-500 text-sm">
                            {{ ucfirst($day['condition']) }}
                        </p>

                        @if($day['rain'])
                            <p class="text-blue-600 text-sm mt-1">
                                🌧️ Rain expected
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

        @else
            {{-- If weather fails --}}
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6">
                ⚠️ Weather data unavailable — project location not found or API failed.
            </div>
        @endif



        {{-- ========================================= --}}
        {{-- PROJECT HEADER --}}
        {{-- ========================================= --}}
        <h2 class="text-2xl font-semibold mb-6">Project Details</h2>

        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-xl font-semibold">{{ $project->project_name }}</p>
                <p class="text-gray-600">Client: {{ $project->client->name }}</p>
            </div>

            {{-- STATUS --}}
            @switch($project->status)
                @case('pending')   <span class="px-3 py-1 bg-gray-400 text-white rounded">Pending</span> @break
                @case('active')    <span class="px-3 py-1 bg-blue-600 text-white rounded">Active</span> @break
                @case('on_hold')   <span class="px-3 py-1 bg-yellow-400 text-black rounded">On Hold</span> @break
                @case('delayed')   <span class="px-3 py-1 bg-red-500 text-white rounded">Delayed</span> @break
                @case('completed') <span class="px-3 py-1 bg-green-600 text-white rounded">Completed</span> @break
            @endswitch
        </div>

        <hr class="my-4">

        {{-- ========================================= --}}
        {{-- PROJECT INFO --}}
        {{-- ========================================= --}}
        <p><strong>Location:</strong> {{ $project->location }}</p>
        <p><strong>Total Price:</strong> ₱{{ number_format($project->total_price, 2) }}</p>
        <p><strong>Start Date:</strong> {{ $project->start_date ?? 'Not set' }}</p>
        <p><strong>End Date:</strong> {{ $project->end_date ?? 'Not set' }}</p>

        <hr class="my-6">

        @if ($project->quotation)
            <a href="{{ route('quotations.show', $project->quotation->id) }}"
            class="text-blue-600 hover:underline">View Quotation</a>
        @else
            <span class="text-gray-500">No linked quotation</span>
        @endif


        <hr class="my-6">



        {{-- ========================================= --}}
        {{-- WORKFORCE --}}
        {{-- ========================================= --}}
        <h3 class="text-xl font-semibold mt-8 mb-4">Assigned Workforce</h3>

        <div class="bg-white shadow p-6 rounded">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">Employee</th>
                        <th class="p-2 text-left">Role in Project</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($project->users as $emp)
                        <tr class="border-b">
                            <td class="p-2">{{ $emp->full_name }}</td>
                            <td class="p-2">{{ $emp->pivot->role_in_project ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-3 text-gray-500">No employees assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



        {{-- ========================================= --}}
        {{-- EXTRA WORK --}}
        {{-- ========================================= --}}
        <div class="mt-8">
            <h3 class="text-xl font-semibold mb-4">Extra Work (Actual Area Not in Quotation)</h3>

            @if($extraWorks->count())
                <table class="w-full border-collapse mb-4">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2">Description</th>
                            <th class="p-2">Bd.Ft</th>
                            <th class="p-2">Rate / Bd.Ft</th>
                            <th class="p-2">Amount</th>
                            <th class="p-2">Added By</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($extraWorks as $extra)
                            <tr class="border-b">
                                <td class="p-2">{{ $extra->description }}</td>
                                <td class="p-2">{{ $extra->volume_bdft ?? '—' }}</td>
                                <td class="p-2">{{ $extra->rate_per_bdft ? '₱'.number_format($extra->rate_per_bdft, 2) : '—' }}</td>
                                <td class="p-2">₱{{ number_format($extra->amount, 2) }}</td>
                                <td class="p-2">{{ optional($extra->addedBy)->given_name ?? 'System' }}</td>
                                <td class="p-2">
                                    <form method="POST"
                                          action="{{ route('projects.extra-work.destroy', [$project->id, $extra->id]) }}"
                                          onsubmit="return confirm('Remove this extra work?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline text-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 mb-4">No extra work recorded.</p>
            @endif


            {{-- ADD EXTRA FORM --}}
            <form method="POST" action="{{ route('projects.extra-work.store', $project->id) }}" class="grid md:grid-cols-4 gap-3">
                @csrf

                <div class="md:col-span-2">
                    <label class="text-sm font-medium mb-1">Description</label>
                    <input type="text" name="description" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="text-sm font-medium mb-1">Bd.Ft</label>
                    <input type="number" step="0.01" name="volume_bdft" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium mb-1">Rate / Bd.Ft</label>
                    <input type="number" step="0.01" name="rate_per_bdft" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-medium mb-1">Amount</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded p-2">
                </div>

                <div class="md:col-span-4 flex justify-end">
                    <button class="px-4 py-2 bg-purple-600 text-white rounded">+ Add Extra Work</button>
                </div>
            </form>
        </div>




        {{-- ========================================= --}}
        {{-- PAYMENT SUMMARY --}}
        {{-- ========================================= --}}
        <div class="mt-8 bg-white rounded shadow p-5">
            <h3 class="text-lg font-semibold mb-2">Payment Summary</h3>

            <p><strong>Base Contract Price:</strong> ₱{{ number_format($basePrice, 2) }}</p>
            <p><strong>Extra Work Total:</strong> ₱{{ number_format($extraWorkTotal, 2) }}</p>

            <p class="mt-2"><strong>Total Project Price:</strong> ₱{{ number_format($totalProjectPrice, 2) }}</p>
            <p><strong>Total Paid:</strong> ₱{{ number_format($totalPaid, 2) }}</p>
            <p><strong>Remaining Balance:</strong> ₱{{ number_format($remainingBalance, 2) }}</p>
        </div>


        {{-- ========================================= --}}
        {{-- PAYMENTS TABLE --}}
        {{-- ========================================= --}}
        <div class="flex justify-between items-center mt-6 mb-3">
            <h3 class="text-xl font-semibold">Payments</h3>

            <button onclick="openPaymentModal()"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                + Add Payment
            </button>
        </div>

        <table class="w-full text-left border border-gray-300 mb-10">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">Amount</th>
                    <th class="p-3 border">Method</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">Payment Date</th>
                    <th class="p-3 border">Notes</th>
                    <th class="p-3 border">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($project->payments as $payment)
                    <tr>
                        <td class="p-3 border">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="p-3 border">{{ ucfirst($payment->payment_method) }}</td>

                        <td class="p-3 border">
                            <span class="px-2 py-1 rounded text-white
                                @if($payment->status === 'approved') bg-green-600
                                @elseif($payment->status === 'pending') bg-yellow-500
                                @else bg-red-600 @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>

                        <td class="p-3 border">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                        </td>

                        <td class="p-3 border">{{ $payment->notes ?? '—' }}</td>

                        <td class="p-3 border flex gap-4">

                            {{-- CANCEL BUTTON (only if still approved) --}}
                            @if ($payment->status === 'approved')
                                <form action="{{ route('payments.cancel', $payment->id) }}" method="POST"
                                    onsubmit="return confirm('Cancel this payment? This will mark it as CANCELLED.');">
                                    @csrf
                                    <button class="text-red-600 hover:underline">Cancel</button>
                                </form>
                            @endif

                            {{-- RE-ISSUE BUTTON (only visible after payment is cancelled) --}}
                            @if ($payment->status === 'cancelled')
                                <a href="{{ route('payments.reissueForm', $payment->id) }}"
                                class="text-indigo-600 hover:underline">
                                    Re-Issue
                                </a>
                            @endif

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-500">
                            No payments recorded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        {{-- ========================================= --}}
        {{-- EXPENSES --}}
        {{-- ========================================= --}}
        <h3 class="text-xl font-semibold mb-4">Project Expenses</h3>

        <div class="bg-gray-50 p-5 rounded-lg shadow mb-6">
            <p><strong>Total Expenses:</strong>
                ₱{{ number_format($project->expenses->sum('amount'), 2) }}
            </p>

            <p class="mt-2">
                <strong>Remaining Balance:</strong>
                ₱{{ number_format($project->total_price - $project->expenses->sum('amount'), 2) }}
            </p>
        </div>

        <button onclick="document.getElementById('expenseModal').classList.remove('hidden')"
            class="px-5 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 mb-4">
            + Add Expense
        </button>

        <div class="bg-white shadow rounded-lg overflow-hidden mb-12">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Category</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Added By</th>
                        <th class="p-3">Receipt</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($project->expenses as $expense)
                        <tr class="border-b">
                            <td class="p-3">
                                @if($expense->material)
                                    {{ $expense->material->name }}
                                    <div class="text-gray-500 text-sm">
                                        Supplier: {{ $expense->supplier->name }}
                                    </div>
                                @else
                                    {{ ucfirst($expense->category) }}
                                @endif
                            </td>

                            <td class="p-3">₱{{ number_format($expense->amount, 2) }}</td>
                            <td class="p-3">{{ $expense->expense_date }}</td>

                            <td class="p-3">
                                {{ $expense->user->given_name }} {{ $expense->user->last_name }}
                            </td>

                            <td class="p-3">
                                @if($expense->receipt_path)
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}"
                                       class="text-blue-600 hover:underline" target="_blank">View</a>
                                @else
                                    <span class="text-gray-400">None</span>
                                @endif
                            </td>

                            <td class="p-3">
                                @if($expense->status == 'pending')
                                    <span class="px-3 py-1 bg-gray-300 rounded">Pending</span>
                                @elseif($expense->status == 'approved')
                                    <span class="px-3 py-1 bg-green-500 text-white rounded">Approved</span>
                                @else
                                    <span class="px-3 py-1 bg-red-500 text-white rounded">Rejected</span>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No expenses recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>




        {{-- ACTION BUTTONS --}}
        <div class="flex space-x-3 mb-10">
            <a href="{{ route('projects.edit', $project->id) }}"
               class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Edit Project
            </a>

            <a href="{{ route('projects.index') }}"
               class="px-5 py-2 bg-gray-300 rounded hover:bg-gray-400">Back</a>
        </div>

    </div>
</div>
@endsection
