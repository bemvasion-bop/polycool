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




        {{-- ========================================= --}}
        {{-- EXTRA WORK --}}
        {{-- ========================================= --}}
        <h3 class="text-xl font-semibold mt-8 mb-4">Extra Work</h3>

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
                @foreach($extraWorks as $extra)
                    <tr class="border-b">
                        <td class="p-3">{{ $extra->description }}</td>
                        <td class="p-3">{{ $extra->volume_bdft ?? '—' }}</td>
                        <td class="p-3">{{ $extra->rate_per_bdft ? '₱'.number_format($extra->rate_per_bdft, 2) : '—' }}</td>
                        <td class="p-3">₱{{ number_format($extra->amount, 2) }}</td>
                        <td class="p-3">{{ optional($extra->addedBy)->given_name ?? 'System' }}</td>

                        <td class="p-3">
                            <form method="POST"
                                  action="{{ route('projects.extra-work.destroy', [$project->id, $extra->id]) }}"
                                  onsubmit="return confirm('Remove this extra work?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>




        {{-- ========================================= --}}
        {{-- PAYMENT SUMMARY --}}
        {{-- ========================================= --}}
        <div class="mt-10 bg-gray-50 rounded shadow p-5">
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
        <div class="flex justify-between items-center mt-10 mb-4">
            <h3 class="text-xl font-semibold">Payments</h3>

            <button
                onclick="document.getElementById('addPaymentModal').classList.remove('hidden')"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
            >
                + Add Payment
            </button>
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
                        <th class="p-3 border">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        <tr class="border-b">
                            <td class="p-3 border">₱{{ number_format($payment->amount, 2) }}</td>

                            {{-- FIX: Show method correctly from DB --}}
                            <td class="p-3 border">{{ ucfirst($payment->payment_method) }}</td>

                            {{-- STATUS PILL --}}
                            <td class="p-3 border">
                                @if($payment->status === 'approved')
                                    <span class="px-2 py-1 bg-green-600 text-white rounded text-sm">
                                        Approved
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-500 text-white rounded text-sm">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-600 text-white rounded text-sm">
                                        Cancelled
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 border">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                            </td>

                            <td class="p-3 border">{{ $payment->notes ?? '—' }}</td>

                            <td class="p-3 border">

                                {{-- CANCEL / REISSUE --}}
                                @if($payment->status === 'pending')
                                    <form
                                        action="{{ route('payments.cancel', $payment->id) }}"
                                        method="POST"
                                        class="inline-block"
                                    >
                                        @csrf
                                        <input type="hidden" name="correction_reason" value="Cancelled before approval">
                                        <button class="text-red-600 hover:underline text-sm">
                                            Cancel
                                        </button>
                                    </form>

                                @elseif($payment->status === 'approved' && in_array(auth()->user()->system_role, ['owner','accounting']))
                                    <form
                                        action="{{ route('payments.cancel', $payment->id) }}"
                                        method="POST"
                                        class="inline-block"
                                    >
                                        @csrf
                                        <button class="text-red-600 hover:underline text-sm">
                                            Cancel
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No payments recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>




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
                                            Total: ₱{{ number_format($expense->total_cost, 2) }}
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
                                @if($expense->status == 'pending')
                                    <span class="px-3 py-1 bg-gray-300 rounded">Pending</span>
                                @elseif($expense->status == 'approved')
                                    <span class="px-3 py-1 bg-green-500 text-white rounded">Approved</span>
                                @elseif($expense->status == 'cancelled')
                                    <span class="px-3 py-1 bg-red-500 text-white rounded">Cancelled</span>
                                @else
                                    <span class="px-3 py-1 bg-indigo-500 text-white rounded">Reissued</span>
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
                    <select id="expenseType" class="w-full border rounded-lg p-2 mb-4">
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

                    {{-- ACTIONS --}}
                    <div class="flex justify-end gap-2">
                        <button type="button"
                                onclick="document.getElementById('expenseModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded-lg">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
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
@endsection
