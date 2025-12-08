@extends('layouts.app')

@section('content')
<div class="p-10">
    <div class="max-w-5xl mx-auto bg-white shadow p-8 rounded-lg">

        {{-- ========================================= --}}
        {{-- WEATHER SECTION --}}
        {{-- ========================================= --}}
        @if($weatherData)
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
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6">
                ⚠️ Weather data unavailable — project location not found.
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

            @switch($project->status)
                @case('pending')   <span class="px-3 py-1 bg-gray-400 text-white rounded">Pending</span> @break
                @case('active')    <span class="px-3 py-1 bg-blue-600 text-white rounded">Active</span> @break
                @case('on_hold')   <span class="px-3 py-1 bg-yellow-400 text-black rounded">On Hold</span> @break
                @case('delayed')   <span class="px-3 py-1 bg-red-500 text-white rounded">Delayed</span> @break
                @case('completed') <span class="px-3 py-1 bg-green-600 text-white rounded">Completed</span> @break
            @endswitch
        </div>

        <hr class="my-4">

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

        <table class="w-full border-collapse bg-white shadow rounded mb-6">
            <thead class="bg-gray-100">
                <tr>
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


        {{-- ================================ --}}
        {{-- PROGRESS BAR + LOG BUTTON --}}
        {{-- ================================ --}}
        @if(auth()->user()->system_role === 'manager' || auth()->user()->system_role === 'owner')
        <div class="mb-6 p-4 rounded-xl border bg-white/70 backdrop-blur-md">

            <h3 class="font-semibold text-lg mb-2">Work Progress</h3>

            {{-- Progress Bar --}}
            <div class="w-full bg-gray-200 rounded-full h-4 mb-3">
                <div class="bg-green-500 h-4 rounded-full"
                    style="width: {{ $project->bdft_progress }}%;"></div>
            </div>

            <p class="text-sm mb-3">
                <strong>{{ $project->bdft_progress }}%</strong> completed
            </p>

            {{-- Add Progress Form --}}
            <form action="{{ route('projects.progress.store', $project->id) }}" method="POST" class="flex flex-wrap gap-3">
                @csrf
                <input type="date" name="log_date" required class="border rounded-lg p-2">
                <input type="number" step="0.01" name="bdft_completed" required placeholder="Completed bd.ft"
                    class="border rounded-lg p-2" style="width: 160px;">
                <input type="text" name="notes" placeholder="Notes (optional)" class="border rounded-lg p-2 flex-1">
                <button class="bg-indigo-600 text-white px-3 py-1 rounded-lg">Add Log</button>
            </form>


            {{-- ================================ --}}
            {{-- FINANCIAL OVERVIEW --}}
            {{-- ================================ --}}
            <div class="mt-8 p-6 rounded-xl bg-white/70 backdrop-blur border">

                <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <i data-lucide="wallet" class="w-5 h-5"></i> Project Financials
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">

                    <div>
                        <p class="text-gray-600">Contract Price</p>
                        <p class="font-semibold text-green-700">
                            ₱{{ number_format($project->total_price, 2) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-600">Approved Payments</p>
                        <p class="font-semibold text-blue-600">
                            ₱{{ number_format($totalPaid, 2) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-600">Approved Expenses</p>
                        <p class="font-semibold text-red-600">
                            ₱{{ number_format($totalApprovedExpenses, 2) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-600">Remaining Balance</p>
                        <p class="font-semibold text-purple-700">
                            ₱{{ number_format($remainingBalance, 2) }}
                        </p>
                    </div>

                </div>

                <hr class="my-4">

                <p class="text-sm">
                    <strong>Net Profit After Expenses:</strong>
                    <span class="font-bold text-gray-900">
                        ₱{{ number_format($remainingAfterExpenses, 2) }}
                    </span>
                </p>

            </div>


            {{-- ================================ --}}
            {{-- PROGRESS HISTORY TABLE --}}
            {{-- ================================ --}}
            @if($project->progressLogs->count() > 0)
            <table class="w-full border text-sm mt-4">
                <thead class="bg-gray-100">
                    <tr class="text-left">
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">bd.ft Completed</th>
                        <th class="p-2 border">Notes</th>
                        <th class="p-2 border">Logged By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->progressLogs as $log)
                    <tr>
                        <td class="p-2 border">{{ $log->log_date }}</td>
                        <td class="p-2 border">{{ $log->bdft_completed }}</td>
                        <td class="p-2 border">{{ $log->notes }}</td>
                        <td class="p-2 border">{{ $log->user->full_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

        </div>
        @endif





        {{-- ========================================= --}}
        {{-- EXTRA WORK (Collapsible Section) --}}
        {{-- ========================================= --}}
        <div x-data="{ open: false }" class="mt-8">

            {{-- Header w/ Toggle --}}
            <div class="flex items-center justify-between cursor-pointer"
                @click="open = !open">
                <h3 class="text-xl font-semibold">Extra Work</h3>
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4"/>
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 12H4"/>
                </svg>
            </div>

            {{-- Collapsible Content --}}
            <div x-show="open" x-transition class="mt-4">

                {{-- Add Extra Work Form --}}
                <form action="{{ route('projects.extra-work.store', $project->id) }}"
                    method="POST"
                    class="flex gap-3 mb-4">
                    @csrf

                    <input type="text" name="description" required placeholder="Description"
                        class="border rounded-lg p-2 w-2/5">

                    <input type="number" step="0.01" name="volume_bdft" required placeholder="Bd.Ft"
                        class="border rounded-lg p-2 w-1/5">

                    <input type="number" step="0.01" name="rate_per_bdft" required placeholder="Rate"
                        class="border rounded-lg p-2 w-1/5">

                    <button
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Add
                    </button>
                </form>

                {{-- Table --}}
                <table class="w-full border-collapse bg-white shadow rounded mb-6">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Description</th>
                            <th class="p-3">Bd.Ft</th>
                            <th class="p-3">Rate</th>
                            <th class="p-3">Amount</th>
                            <th class="p-3">Added By</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($extraWorks as $extra)
                        <tr class="border-b">
                            <td class="p-3">{{ $extra->description }}</td>
                            <td class="p-3">{{ $extra->volume_bdft ?? '—' }}</td>
                            <td class="p-3">
                                {{ $extra->rate_per_bdft ? '₱'.number_format($extra->rate_per_bdft, 2) : '—' }}
                            </td>
                            <td class="p-3">₱{{ number_format($extra->amount, 2) }}</td>
                            <td class="p-3">{{ optional($extra->addedBy)->given_name ?? 'System' }}</td>

                            <td class="p-3">
                                @if($extra->status == 'pending')

                                    <form action="{{ route('projects.extra-work.approve', [$project->id, $extra->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button class="text-green-600 hover:underline text-sm">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('projects.extra-work.reject', [$project->id, $extra->id]) }}"
                                        method="POST" class="inline ml-2">
                                        @csrf
                                        <button class="text-red-600 hover:underline text-sm">
                                            Reject
                                        </button>
                                    </form>

                                @else
                                    <span class="text-gray-500 text-sm">
                                        {{ ucfirst($extra->status) }}
                                    </span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-3 text-center text-sm text-gray-500">
                                No extra work added yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>





        {{-- ================================ --}}
        {{-- PAYMENT SUMMARY --}}
        {{-- ================================ --}}
        <h3 class="text-xl font-semibold mt-10 mb-3">Payment Summary</h3>

        @php
            // Base Contract Price (from quotation)
            $baseContract = $quotation->contract_price ?? 0;

            // Total Extra Work Approved
            $extraTotal = $project->extraWorks()
                ->where('status', 'approved')
                ->sum('amount');

            // Updated Total Project Price
            $totalProject = $baseContract + $extraTotal;

            // Total Approved Payments
            $totalPaid = $project->payments()
                ->where('status', 'approved')
                ->sum('amount');

            // Remaining Balance
            $remaining = $totalProject - $totalPaid;
        @endphp

        <div class="bg-white shadow rounded-lg p-6 space-y-1">

            <p><strong>Base Contract Price:</strong>
                ₱{{ number_format($baseContract, 2) }}
            </p>

            <p><strong>Extra Work Total:</strong>
                ₱{{ number_format($extraTotal, 2) }}
            </p>

            <p class="font-bold text-lg mt-3">
                <strong>Total Project Price:</strong>
                ₱{{ number_format($totalProject, 2) }}
            </p>

            <p class="text-blue-700 mt-3">
                <strong>Total Paid:</strong>
                ₱{{ number_format($totalPaid, 2) }}
            </p>

            <p class="text-purple-700 font-semibold text-lg">
                <strong>Remaining Balance:</strong>
                ₱{{ number_format($remaining, 2) }}
            </p>


            {{-- PRINT PAYMENT SUMMARY PDF BUTTON --}}
            <div class="mt-4">
                <a href="{{ route('payments.summary.pdf', $project->id) }}"
                class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-black transition">
                Print Payment Summary PDF
                </a>
            </div>

        </div>





        {{-- ========================================= --}}
        {{-- PAYMENTS TABLE --}}
        {{-- ========================================= --}}
        <div class="flex justify-between items-center mt-10 mb-4">
            <h3 class="text-xl font-semibold">Payments</h3>

            {{-- Only MANAGER can add payments --}}
            @if(auth()->user()->system_role === 'manager')
                <button
                    onclick="document.getElementById('addPaymentModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    + Add Payment
                </button>
            @endif
        </div>


        <div class="bg-white shadow rounded-lg overflow-hidden mb-10">
            <table class="w-full text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border">Amount</th>
                        <th class="p-3 border">Method</th>
                        <th class="p-3 border">Status</th>
                        <th class="p-3 border">Payment Date</th>
                        <th class="p-3 border">Notes</th>
                        <th class="p-3 border">Added By</th>
                        <th class="p-3 border">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($payments as $payment)
                    <tr>
                        {{-- Amount --}}
                        <td class="p-3 border">
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>

                        {{-- Method --}}
                        <td class="p-3 border">{{ $payment->payment_method }}</td>

                        {{-- Status --}}
                        <td class="p-3 border">
                            @if($payment->status === 'pending')
                                <span class="text-green-600 font-medium">Approved</span>
                            @elseif($payment->status === 'pending')
                                <span class="text-yellow-600 font-medium">Pending</span>
                            @elseif($payment->status === 'rejected')
                                <span class="text-red-600 font-medium">Rejected</span>
                            @elseif($payment->status === 'reissued')
                                <span class="text-red-700 font-medium">Reversed</span>
                            @else
                                <span class="text-gray-500 italic">Downpayment</span>
                            @endif
                        </td>

                        {{-- Payment Date --}}
                        <td class="p-3 border">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                        </td>

                        {{-- Notes --}}
                        <td class="p-3 border">
                            {{ $payment->notes ?? '—' }}
                        </td>

                        {{-- Added By --}}
                        <td class="p-3 border text-sm text-gray-700">
                            {{-- 1) If this payment was re-issued, show the manager who corrected it --}}
                            @if($payment->corrected_by && $payment->correctedBy)
                                {{ $payment->correctedBy->given_name  }}

                            {{-- 2) Else if it has an added_by (manual entry by owner/accounting) --}}
                            @elseif($payment->added_by && $payment->addedBy)
                                {{ $payment->addedBy->given_name  }}


                            {{-- 3) Else if this is the quotation downpayment (auto-imported) --}}
                            @elseif(str_contains(strtolower($payment->notes ?? ''), 'auto-imported'))
                                System Administrator

                            {{-- 4) Fallback --}}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>



                        {{-- Actions --}}
                        <td class="p-3 border">

                            {{-- 🟡 If Auto Imported from quotation --}}
                            @if(str_contains(strtolower($payment->notes ?? ''), 'auto-imported'))
                                <span class="text-gray-400 text-sm italic">—</span>

                            {{-- 🟠 Pending (Owner/Accounting Only) → Approve / Reject --}}
                            @elseif($payment->status === 'pending'
                                && in_array(auth()->user()->system_role,['owner','accounting']))

                                <form action="{{ route('payments.approve',$payment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:underline text-sm">Approve</button>
                                </form>

                                <form action="{{ route('payments.reject',$payment->id) }}" method="POST" class="inline ml-2">
                                    @csrf
                                    <button class="text-red-600 hover:underline text-sm">Reject</button>
                                </form>

                            {{-- 🟢 Approved (Owner/Accounting) → Cancel & Re-Issue --}}
                            @elseif($payment->status === 'approved'
                                && in_array(auth()->user()->system_role,['owner','accounting']))

                                <form action="{{ route('payments.cancel',$payment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-red-600 hover:underline text-sm">Cancel & Re-Issue</button>
                                </form>

                            {{-- 🔵 Reversed (Manager Only) → Re-Issue Modal --}}
                            @elseif($payment->status === 'reversed'
                                && in_array(auth()->user()->system_role, ['manager','owner']))


                                <button onclick="showReIssueModal({{ $payment->id }})"
                                    class="text-purple-600 hover:underline text-sm">
                                    Re-Issue Payment
                                </button>

                            {{-- Reversed (Owner/Accounting) → No action --}}
                            @elseif($payment->status === 'reversed'
                                && in_array(auth()->user()->system_role,['owner','accounting']))

                                <span class="text-gray-400 text-sm italic">—</span>

                            {{-- 🚫 Default: No Actions Allowed --}}
                            @else
                                <span class="text-gray-400 text-sm italic">—</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach

                    </tbody>


            </table>
        </div>


          <!-- Reversal History Modal -->
                <div id="reversalHistoryModal"
                    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-xl p-8 w-[480px] shadow-2xl">

                        <h2 class="text-2xl font-semibold mb-4">Reversal History</h2>

                        <div id="reversalHistoryContent" class="space-y-3 text-sm">
                            <!-- Loaded dynamically -->
                        </div>

                        {{-- PRINT AUDIT PDF — Only Owner + Accounting + Audit --}}
                        @if(in_array(auth()->user()->system_role, ['owner','accounting','audit']))
                        <div class="mt-6 text-right">
                            <button
                                onclick="window.location.href='{{ route('payments.audit.pdf', $payment->id) }}'"
                                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                                Print Audit Review PDF
                            </button>
                        </div>
                        @endif

                        <div class="mt-4 text-right">
                            <button
                                onclick="document.getElementById('reversalHistoryModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                Close
                            </button>
                        </div>

                    </div>
                </div>

                <script>
                function openReversalHistoryModal(paymentId) {
                    fetch(`/payments/${paymentId}/history`)
                        .then(res => res.json())
                        .then(data => {
                            let html = `
                                <p><strong>Original Amount:</strong> ₱${ data.original_amount !== '—' ? parseFloat(data.original_amount).toLocaleString() : '—' }</p>
                                <p><strong>Cancelled By:</strong> ${data.cancelled_by || '—'}</p>
                                <p><strong>Cancel Reason:</strong> ${data.cancel_reason || '—'}</p>
                                <p><strong>Replacement Amount:</strong> ₱${ data.new_amount !== '—' ? parseFloat(data.new_amount).toLocaleString() : '—' }</p>
                                <p><strong>Correction Notes:</strong> ${data.correction_reason || '—'}</p>
                                <p><strong>Date Modified:</strong> ${data.updated_at}</p>`;

                            document.getElementById('reversalHistoryContent').innerHTML = html;
                            document.getElementById('reversalHistoryModal').classList.remove('hidden');
                        });
                }
                </script>





        {{-- ========================================= --}}
        {{-- PAYMENT MODAL --}}
        {{-- ========================================= --}}
        <div id="addPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-xl relative">

                <h2 class="text-xl font-semibold mb-4 text-center">Add Payment</h2>

                <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <label class="block text-sm font-medium">Amount</label>
                    <input type="number" step="0.01" name="amount"
                        class="w-full border rounded px-3 py-2 mb-4" required>

                    <label class="block text-sm font-medium">Payment Method</label>
                    <select name="payment_method" class="w-full border rounded px-3 py-2 mb-4" required>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>

                    <label class="block text-sm font-medium">Payment Date</label>
                    <input type="date" name="payment_date"
                        class="w-full border rounded px-3 py-2 mb-4" required>

                    <label class="block text-sm font-medium">Proof of Payment (optional)</label>
                    <input type="file" name="proof"
                        class="w-full border rounded px-3 py-2 mb-4"
                        accept="image/*,application/pdf">

                    <label class="block text-sm font-medium">Notes (optional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full border rounded px-3 py-2 mb-6"></textarea>

                    <div class="flex justify-end space-x-2">
                        <button type="button"
                                onclick="document.getElementById('addPaymentModal').classList.add('hidden')"
                                class="px-4 py-2 rounded bg-gray-200">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 rounded bg-purple-600 text-white">
                            Save Payment
                        </button>


                    </div>
                </form>

            </div>
        </div>





        {{-- ==========================================================
            🔁 RE-ISSUE PAYMENT MODAL
        ========================================================== --}}
        <div id="reIssueModal"
            class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">

            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg">

                <h2 class="text-xl font-semibold mb-4">Re-Issue Payment</h2>

                <form id="reIssueForm" method="POST">
                    @csrf


                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Corrected Amount</label>
                        <input type="number" step="0.01" name="amount" required
                            class="w-full border rounded-lg p-2">
                    </div>

                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Payment Date</label>
                        <input type="date" name="payment_date" required
                            class="w-full border rounded-lg p-2">
                    </div>

                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Correction Notes</label>
                        <textarea name="correction_reason" required
                            class="w-full border rounded-lg p-2"
                            rows="3">Re-issued payment due to correction.</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button"
                                onclick="hideReIssueModal()"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                            Save New Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function showReIssueModal(paymentId) {
                const form = document.getElementById('reIssueForm');
                form.action = `/payments/${paymentId}/reissue`;
                document.getElementById('reIssueModal').classList.remove('hidden');
            }

            function hideReIssueModal() {
                document.getElementById('reIssueModal').classList.add('hidden');
            }
        </script>




        {{-- ========================================= --}}
        {{-- EXPENSES --}}
        {{-- ========================================= --}}
        <h3 class="text-xl font-semibold mb-4">Project Expenses</h3>

            {{-- ========================================= --}}
            {{-- PROJECT EXPENSES SUMMARY --}}
            {{-- ========================================= --}}
            <div class="bg-gray-50 p-5 rounded-lg shadow mb-6">
                <p><strong>Total Approved Expenses:</strong>
                    ₱{{ number_format($totalApprovedExpenses, 2) }}
                </p>

                <p><strong>Remaining Balance (After Expenses):</strong>
                    ₱{{ number_format($remainingAfterExpenses, 2) }}
                </p>
            </div>


            <button onclick="document.getElementById('expenseModal').classList.remove('hidden')"
                class="px-5 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 mb-4">
                + Add Expense
            </button>

            <div class="bg-white shadow rounded-lg overflow-hidden mb-12">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border">Type</th>
                        <th class="p-3 border">Details</th>
                        <th class="p-3 border">Cost</th>
                        <th class="p-3 border">Date</th>
                        <th class="p-3 border">Added By</th>
                        <th class="p-3 border">Receipt</th>
                        <th class="p-3 border">Status</th>
                        <th class="px-3 py-2 text-sm text-center">Actions</th>

                    </tr>
                </thead>

                <tbody>
                    @forelse($project->expenses as $expense)
                        <tr class="border-b">

                            {{-- TYPE --}}
                            <td class="p-3 border">
                                @if($expense->material_id)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                        Material
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                        Custom
                                    </span>
                                @endif
                            </td>

                            {{-- DETAILS --}}
                            <td class="p-3 border">
                                @if($expense->material_id)
                                    <p class="font-medium">{{ $expense->material->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        Supplier: {{ $expense->material->supplier->name }}
                                    </p>

                                    <p class="text-sm mt-1">
                                        Unit Cost: ₱{{ number_format($expense->unit_cost, 2) }}<br>
                                        Quantity: {{ $expense->quantity_used }}<br>
                                        <span class="font-semibold">
                                            Total: ₱{{ number_format($expense->amount, 2) }}
                                        </span>
                                    </p>
                                @else
                                    <p class="font-medium">{{ $expense->category }}</p>

                                    <p class="text-sm mt-1">
                                        Amount: ₱{{ number_format($expense->amount, 2) }}
                                    </p>

                                    @if($expense->description)
                                        <p class="text-sm text-gray-600 mt-1">
                                            Notes: {{ $expense->description }}
                                        </p>
                                    @endif
                                @endif
                            </td>

                            {{-- COST CALC --}}
                            <td class="p-3 border">
                                @if($expense->material_id)
                                    ₱{{ number_format($expense->total_cost, 2) }}
                                @else
                                    ₱{{ number_format($expense->amount, 2) }}
                                @endif
                            </td>

                            {{-- DATE --}}
                            <td class="p-3 border">
                                {{ \Carbon\Carbon::parse($expense->expense_date)->format('M j, Y') }}
                            </td>

                            {{-- ADDED BY --}}
                            <td class="p-3 border">
                                {{ $expense->user->given_name ?? 'System' }}
                            </td>

                            {{-- RECEIPT --}}
                            <td class="p-3 border">
                                @if($expense->receipt_path)
                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}"
                                    target="_blank"
                                    class="text-blue-600 hover:underline">
                                        View
                                    </a>
                                @else
                                    <span class="text-gray-400">None</span>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="p-3 border">
                                @if($expense->status === 'pending')
                                    <span class="px-3 py-1 bg-yellow-400 text-black rounded text-xs">Pending</span>
                                @elseif($expense->status === 'approved')
                                    <span class="px-3 py-1 bg-green-500 text-white rounded text-xs">Approved</span>
                                @elseif($expense->status === 'cancelled')
                                    <span class="px-3 py-1 bg-red-600 text-white rounded text-xs">Cancelled</span>
                                @elseif($expense->status === 'reissued')
                                    <span class="px-3 py-1 bg-purple-600 text-white rounded text-xs">Reissued</span>
                                @else
                                    <span class="text-gray-400 text-xs italic">—</span>
                                @endif
                            </td>


                            <td class="px-3 py-1 text-sm text-center">

                                {{-- ================================
                                    MANAGER correcting REISSUED
                                    ================================ --}}
                                @if(auth()->user()->system_role === 'manager'
                                    && $expense->status === 'reissued')

                                    @if($expense->material_id)
                                        {{-- MATERIAL Correction → Adjust Qty --}}
                                        <button onclick="showAdjustQuantityModal({{ $expense->id }}, {{ $expense->quantity_used ?? 0 }})"
                                                class="text-purple-600 hover:underline text-sm">
                                            Adjust Qty
                                        </button>
                                    @else
                                        {{-- CUSTOM Correction → Re-Issue Expense --}}
                                        <button onclick="showExpenseReIssueModal({{ $expense->id }})"
                                                class="text-purple-600 hover:underline text-sm">
                                            Re-Issue Expense
                                        </button>
                                    @endif


                                {{-- =====================================
                                    OWNER/ACCOUNTING: Pending CUSTOM
                                    ===================================== --}}
                                @elseif(in_array(auth()->user()->system_role, ['owner','accounting'])
                                    && !$expense->material_id
                                    && $expense->status === 'pending')

                                    <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-green-600 hover:underline text-sm">Approve</button>
                                    </form>

                                    <form action="{{ route('expenses.reject', $expense->id) }}" method="POST" class="inline ml-2">
                                        @csrf
                                        <button class="text-red-600 hover:underline text-sm">Reject</button>
                                    </form>


                                {{-- ====================================
                                    OWNER/ACCOUNTING: Approved CUSTOM
                                    ==================================== --}}
                                @elseif(in_array(auth()->user()->system_role, ['owner','accounting'])
                                    && !$expense->material_id
                                    && $expense->status === 'approved')

                                    <form action="{{ route('expenses.cancel', $expense->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-red-600 hover:underline text-sm"
                                            onclick="return confirm('Cancel & Re-issue this expense?')">
                                            Cancel & Re-Issue
                                        </button>
                                    </form>


                                {{-- ====================================
                                    MANAGER: Approved MATERIAL
                                    ==================================== --}}
                                @elseif(auth()->user()->system_role === 'manager'
                                    && $expense->material_id
                                    && $expense->status === 'approved')

                                    <button onclick="showAdjustQuantityModal({{ $expense->id }}, {{ $expense->quantity_used ?? 0 }})"
                                            class="text-purple-600 hover:underline text-sm">
                                        Adjust Qty
                                    </button>


                                {{-- ====================================
                                    Everyone else → No permission
                                    ==================================== --}}
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif

                            </td>
                    </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">
                                No expenses recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>





        {{-- ========================================= --}}
        {{-- EXPENSE MODAL --}}
        {{-- ========================================= --}}
        <div id="expenseModal" class="fixed inset-0 bg-black/40 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-lg">

                <h2 class="text-2xl font-semibold mb-5">Add Expense</h2>

                <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    {{-- EXPENSE TYPE --}}
                    <label class="block text-sm font-medium mb-1">Expense Type</label>
                    <select id="expenseType" name="expense_type"
                            class="w-full border rounded-lg p-2 mb-4">
                        <option value="material">Material Expense</option>
                        <option value="custom" selected>Custom Category</option>
                    </select>

                    {{-- ======================================================= --}}
                    {{-- MATERIAL EXPENSE MODE --}}
                    {{-- ======================================================= --}}
                    <div id="materialFields" class="hidden">

                        <label class="block text-sm font-medium mb-1">Search Material</label>

                        {{-- SEARCH INPUT --}}
                        <div class="relative mb-3">
                            <input type="text"
                                id="materialSearch"
                                placeholder="Type to search..."
                                class="w-full border rounded-lg p-2"
                                oninput="filterMaterials()"
                                onclick="showMaterialList()">

                            <input type="hidden" name="material_id" id="material_id">

                            {{-- SEARCH DROPDOWN --}}
                            <div id="materialList"
                                class="absolute left-0 right-0 bg-white border rounded-lg shadow max-h-48 overflow-y-auto hidden z-50">

                                @foreach($materials as $m)
                                    <div class="px-3 py-2 cursor-pointer hover:bg-purple-100 material-item"
                                        data-id="{{ $m->id }}"
                                        data-name="{{ $m->name }}"
                                        data-price="{{ $m->price_per_unit }}"
                                        data-supplier="{{ $m->supplier->name }}"
                                        onclick="selectMaterial(this)">
                                        <strong>{{ $m->name }}</strong>
                                        <div class="text-xs text-gray-600">
                                            ₱{{ number_format($m->price_per_unit, 2) }} — {{ $m->supplier->name }}
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        {{-- UNIT PRICE --}}
                        <label class="block text-sm font-medium mb-1">Unit Price</label>
                        <input type="text" id="unitPrice" name="unit_cost"
                            class="w-full border rounded-lg p-2 mb-3" readonly>

                        {{-- QUANTITY --}}
                        <label class="block text-sm font-medium mb-1">Quantity</label>
                        <input type="number" id="quantity" name="quantity_used"
                            step="0.01"
                            class="w-full border rounded-lg p-2 mb-3"
                            oninput="updateTotalCost()">

                        {{-- TOTAL COST --}}
                        <label class="block text-sm font-medium mb-1">Total Cost</label>
                        <input type="text" id="totalCost" name="total_cost"
                            class="w-full border rounded-lg p-2 mb-3" readonly>
                    </div>

                    {{-- ======================================================= --}}
                    {{-- CUSTOM EXPENSE MODE --}}
                    {{-- ======================================================= --}}
                    <div id="customFields">

                        <label class="block text-sm font-medium mb-1">Category</label>
                        <input type="text" name="category"
                            class="w-full border rounded-lg p-2 mb-3">

                        <label class="block text-sm font-medium mb-1">Amount</label>
                        <input type="number" name="amount" step="0.01"
                            class="w-full border rounded-lg p-2 mb-3">
                    </div>

                    {{-- COMMON FIELDS --}}
                    <label class="block text-sm font-medium mb-1">Expense Date</label>
                    <input type="date" name="expense_date"
                        class="w-full border rounded-lg p-2 mb-3" required>

                    <label class="block text-sm font-medium mb-1">Receipt (optional)</label>
                    <input type="file" name="receipt"
                        class="w-full border rounded-lg mb-4">

                    {{-- ACTION BUTTONS --}}
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button"
                            onclick="document.getElementById('expenseModal').classList.add('hidden')"
                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">
                            Back
                        </button>

                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 text-sm">
                            Save Expense
                        </button>
                    </div>


                </form>

            </div>
        </div>


        <script>
        document.getElementById('expenseType').addEventListener('change', function () {
            const type = this.value;
            const mat = document.getElementById('materialFields');
            const custom = document.getElementById('customFields');

            if (type === 'material') {
                mat.classList.remove('hidden');
                custom.classList.add('hidden');
            } else {
                mat.classList.add('hidden');
                custom.classList.remove('hidden');
            }
        });

        function showMaterialList() {
            document.getElementById('materialList').classList.remove('hidden');
        }

        function filterMaterials() {
            let query = document.getElementById('materialSearch').value.toLowerCase();
            document.querySelectorAll('.material-item').forEach(item => {
                let name = item.dataset.name.toLowerCase();
                item.style.display = name.includes(query) ? 'block' : 'none';
            });
        }

        function selectMaterial(el) {
            document.getElementById('material_id').value = el.dataset.id;
            document.getElementById('materialSearch').value = el.dataset.name;
            document.getElementById('unitPrice').value = "₱" + parseFloat(el.dataset.price).toFixed(2);
            updateTotalCost();
            document.getElementById('materialList').classList.add('hidden');
        }

        function updateTotalCost() {
            let price = parseFloat(document.getElementById('unitPrice').value.replace('₱', '')) || 0;
            let qty = parseFloat(document.getElementById('quantity').value) || 0;
            document.getElementById('totalCost').value = "₱" + (price * qty).toFixed(2);
        }

        document.addEventListener('click', function(e) {
            if (!document.getElementById('materialFields').contains(e.target)) {
                document.getElementById('materialList').classList.add('hidden');
            }
        });
        </script>



        <script>
        function openEditPaymentModal(id, amount, date) {
            document.getElementById('editPaymentModal').classList.remove('hidden');
            document.getElementById('editAmount').value = amount;
            document.getElementById('editDate').value = date;
            document.getElementById('editNotes').value = '';

            // Update action URL dynamically
            document.getElementById('editPaymentForm').action =
                "/payments/" + id + "/update";
        }
        </script>







        {{-- ========================================= --}}
        {{-- FOOTER ACTIONS --}}
        {{-- ========================================= --}}
        <div class="flex space-x-3 mt-10">
            <a href="{{ route('projects.edit', $project->id) }}"
               class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Edit Project
            </a>

            <a href="{{ route('projects.index') }}"
               class="px-5 py-2 bg-gray-300 rounded hover:bg-gray-400">Back</a>
        </div>

    </div>
</div>

{{-- =============================== --}}
{{-- 📌 RE-ISSUE EXPENSE MODAL       --}}
{{-- =============================== --}}
<div id="expenseReIssueModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">

        {{-- Close Button --}}
        <button onclick="closeExpenseReIssueModal()"
                class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl">
            &times;
        </button>

        <h2 class="text-lg font-semibold mb-4">Re-Issue Expense</h2>

        <form id="expenseReIssueForm" method="POST">
            @csrf

            {{-- Cost Input --}}
            <label class="block text-sm text-gray-700 mb-1">New Corrected Cost</label>
            <input type="number" step="0.01" name="amount"
                    class="w-full border rounded-lg px-3 py-2 mb-4" required>
                <textarea name="description" rows="3"
            class="w-full border rounded-lg px-3 py-2 mb-4"
            placeholder="Reason or corrected details..."></textarea>

            {{-- Optional Notes --}}
            <label class="block text-sm text-gray-700 mb-1">Correction Notes</label>
            <textarea name="details" rows="3"
                      class="w-full border rounded-lg px-3 py-2 mb-4"
                      placeholder="Reason or corrected details..."></textarea>

            <button type="submit"
                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 text-sm">
                Save Corrected Expense
            </button>
        </form>

    </div>

    <script>
        function showExpenseReIssueModal(expenseId) {
            // Open modal
            document.getElementById('expenseReIssueModal').classList.remove('hidden');

            // Point form to correct route
            const form = document.getElementById('expenseReIssueForm');
            form.action = `/expenses/${expenseId}/reissue-save`;
        }

        function closeExpenseReIssueModal() {
            document.getElementById('expenseReIssueModal').classList.add('hidden');
        }
    </script>

</div>



{{-- =============================== --}}
{{-- 📌 ADJUST QUANTITY MODAL       --}}
{{-- =============================== --}}
<div id="adjustQtyModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">

        {{-- Close Button --}}
        <button onclick="closeAdjustQtyModal()"
                class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl">
            &times;
        </button>

        <h2 class="text-lg font-semibold mb-4">Adjust Material Quantity</h2>

        <form id="adjustQtyForm" method="POST">
            @csrf

            <label class="block text-sm text-gray-700 mb-1">New Quantity Used</label>
            <input type="number" step="0.01" name="quantity_used"
                   class="w-full border rounded-lg px-3 py-2 mb-4" required>

            <textarea name="reason" rows="3"
                class="w-full border rounded-lg px-3 py-2 mb-4"
                placeholder="Reason for adjustment..."></textarea>

            <button type="submit"
                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 text-sm">
                Save Adjustment
            </button>
        </form>
    </div>
</div>

<script>
    function showAdjustQuantityModal(expenseId, currentQty) {
        document.getElementById('adjustQtyModal').classList.remove('hidden');

        // Preload current qty
        document.querySelector('#adjustQtyForm [name="quantity_used"]').value = currentQty;

        // Set dynamic route
        document.getElementById('adjustQtyForm').action =
            `/expenses/${expenseId}/adjust-qty`;
    }

    function closeAdjustQtyModal() {
        document.getElementById('adjustQtyModal').classList.add('hidden');
    }
</script>





@endsection
