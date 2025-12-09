@extends('layouts.app')

@section('content')

<style>
    /* Reduce spacing between sections */
    .section-box {
        margin-bottom: 32px; /* smaller spacing, same as edit blade */
    }

    /* Glass Box */
    .glass-box {
        border-radius: 28px;
        padding: 32px;
        backdrop-filter: blur(22px);
        background: rgba(255,255,255,0.55);
        border: 1px solid rgba(255,255,255,0.5);
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    }

    /* iOS Modal */
    .modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        backdrop-filter: blur(6px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .modal-box {
        background: white;
        border-radius: 24px;
        padding: 28px;
        width: 380px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
</style>

<div class="max-w-5xl mx-auto">

    <h2 class="text-3xl font-semibold text-gray-900 mb-6">Create New Quotation</h2>


    {{-- ======================= --}}
    {{--  ERRORS --}}
    {{-- ======================= --}}
    @if ($errors->any())
        <div class="glass-box mb-6 bg-red-100 border-red-300 text-red-700">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('quotations.store') }}" id="createQuotationForm">
        @csrf


        {{-- ======================= --}}
        {{-- CLIENT INFORMATION --}}
        {{-- ======================= --}}
        <div class="glass-box section-box">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Client Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="font-medium">Client</label>
                    <select name="client_id" id="client"
                        class="w-full border rounded-xl p-3 mt-1 bg-white"
                        required>
                        <option value="">Select Client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                data-address="{{ $client->address }}">
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-medium">Client Address</label>
                    <input type="text" name="address" id="client_address"
                        class="w-full border rounded-xl p-3 mt-1 bg-gray-100"
                        placeholder="Auto-filled..." readonly>
                </div>

            </div>
        </div>


        {{-- ======================= --}}
        {{-- PROJECT INFORMATION --}}
        {{-- ======================= --}}
        <div class="glass-box section-box">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Project Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="font-medium">Project / Vessel Name</label>
                    <input type="text" name="project_name"
                        class="w-full border rounded-xl p-3 mt-1" required>
                </div>

                <div>
                    <label class="font-medium">System</label>
                    <input type="text" name="system"
                        class="w-full border rounded-xl p-3 mt-1 bg-gray-100"
                        value="Spray in place polyurethane foam" readonly>
                </div>

                <div class="md:col-span-2">
                    <label class="font-medium">Scope of Work</label>
                    <textarea name="scope_of_work"
                        class="w-full border rounded-xl p-3 mt-1"
                        rows="3"></textarea>
                </div>

                <div>
                    <label class="font-medium">Duration (days)</label>
                    <input type="number" name="duration_days"
                        class="w-full border rounded-xl p-3 mt-1">
                </div>

            </div>
        </div>


        {{-- ======================= --}}
        {{-- PARTICULARS --}}
        {{-- ======================= --}}
        <div class="glass-box section-box">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Particulars</h3>

            <table class="w-full border rounded-xl overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-3">Substrate</th>
                        <th class="border p-3">Thickness</th>
                        <th class="border p-3">Volume (bd.ft)</th>
                        <th class="border p-3">Action</th>
                    </tr>
                </thead>

                <tbody id="items_table">
                    <tr>
                        <td class="border p-2">
                            <input type="text" name="items[0][substrate]"
                                class="w-full p-2 border rounded-lg">
                        </td>

                        <td class="border p-2">
                            <input type="text" name="items[0][thickness]"
                                class="w-full p-2 border rounded-lg">
                        </td>

                        <td class="border p-2">
                            <input type="number" step="0.01"
                                name="items[0][volume]"
                                class="item-volume w-full p-2 border rounded-lg">
                        </td>

                        <td class="border p-2 text-center">
                            <button type="button" class="remove-row text-red-600">Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button"
                id="add_row_btn"
                class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-xl">
                + Add Row
            </button>
        </div>


        {{-- ======================= --}}
        {{-- COSTING --}}
        {{-- ======================= --}}
        <div class="glass-box section-box">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Costing</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <label class="font-medium">Total Bd.Ft</label>
                    <input type="number" id="total_bdft" name="total_bdft"
                        class="w-full border rounded-xl p-3 mt-1 bg-gray-100" readonly>
                </div>

                <div>
                    <label class="font-medium">Rate per Bd.Ft</label>
                    <input type="number" id="rate_per_bdft" name="rate_per_bdft"
                        class="w-full border rounded-xl p-3 mt-1" value="45">
                </div>

                <div>
                    <label class="font-medium">Discount</label>
                    <input type="number" id="discount" name="discount"
                        class="w-full border rounded-xl p-3 mt-1" value="0">
                </div>

                <div>
                    <label class="font-medium">Contract Price</label>
                    <input type="number" id="contract_price" name="contract_price"
                        class="w-full border rounded-xl p-3 mt-1 bg-gray-100" readonly>
                </div>

                <div>
                    <label class="font-medium">Down Payment</label>
                    <input type="text" id="down_payment" name="down_payment"
                    class="w-full border rounded-xl p-3 mt-1 cursor-text"
                    placeholder="0.00"
                    inputmode="decimal">
                </div>

                <div>
                    <label class="font-medium">Balance</label>
                    <input type="number" id="balance" name="balance"
                        class="w-full border rounded-xl p-3 mt-1 bg-gray-100" readonly>
                </div>

            </div>
        </div>


        {{-- ======================= --}}
        {{-- TERMS --}}
        {{-- ======================= --}}
        <div class="glass-box section-box">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Terms & Conditions</h3>

            <textarea name="conditions" rows="10"
                class="w-full border-gray-300 rounded-xl bg-gray-50">
→ Mobilization will take place seven days after down payment has been received, provided all necessary preparations have been completed.

→ The owner/customer shall provide sufficient free electricity 220 volts, 60 amps.

→ Surfaces and substrate must be clean and dry, painted with primers.

→ Additional areas not included will be billed based on actual area.

→ This quotation is valid for 30 days.

Thank you for your business! God bless!!!
            </textarea>

        </div>


        {{-- ======================= --}}
        {{-- BUTTONS --}}
        {{-- ======================= --}}
        <div class="flex gap-4 justify-end mb-10">

            {{-- CANCEL BUTTON --}}
            <button type="button"
                id="cancelBtn"
                class="px-6 py-3 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                Cancel
            </button>

            {{-- SAVE BUTTON --}}
            <button type="submit"
                class="px-6 py-3 bg-purple-600 text-white rounded-xl shadow hover:bg-purple-700">
                Save Quotation
            </button>

        </div>

    </form>
</div>


{{-- ======================= --}}
{{-- CANCEL CONFIRM MODAL --}}
{{-- ======================= --}}
<div id="cancelModal" class="modal-bg">
    <div class="modal-box">
        <h3 class="text-lg font-semibold mb-3">Cancel Quotation?</h3>
        <p class="text-gray-600 mb-6">Your changes will not be saved. Are you sure?</p>

        <div class="flex justify-center gap-4">
            <button id="stayBtn" class="px-5 py-2 bg-gray-200 rounded-xl hover:bg-gray-300">
                Stay
            </button>

            <a href="{{ route('quotations.index') }}"
                class="px-5 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                Yes, Cancel
            </a>
        </div>
    </div>
</div>



{{-- ======================= --}}
{{-- JAVASCRIPT --}}
{{-- ======================= --}}
<script>
// ===========================
// Autofill client address
// ===========================
document.getElementById('client').addEventListener('change', function () {
    let address = this.options[this.selectedIndex].getAttribute('data-address');
    document.getElementById('client_address').value = address;
});

// ===========================
// Cancel Modal
// ===========================
const modal = document.getElementById('cancelModal');
document.getElementById('cancelBtn').onclick = () => modal.style.display = 'flex';
document.getElementById('stayBtn').onclick = () => modal.style.display = 'none';

// ===========================
// RECALCULATE COSTING
// ===========================
function recalcTotals() {
    let total = 0;

    document.querySelectorAll('.item-volume').forEach(el => {
        const v = parseFloat(el.value);
        if (!isNaN(v)) total += v;
    });

    document.getElementById('total_bdft').value = total.toFixed(2);

    const rate = parseFloat(document.getElementById('rate_per_bdft').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;

    let contract = (total * rate) - discount;
    if (contract < 0) contract = 0;
    document.getElementById('contract_price').value = contract.toFixed(2);

    let dpInput = document.getElementById('down_payment');
    let dp = parseFloat(dpInput.value);

    if (isNaN(dp) || dp < 0) dp = 0;
    if (dp > contract) dp = contract;

    let balance = contract - dp;
    if (balance < 0) balance = 0;

    document.getElementById('balance').value = balance.toFixed(2);
}

// Format 2 decimals only after leaving field
document.getElementById('down_payment').addEventListener('blur', (e) => {
    let val = parseFloat(e.target.value);
    if (!isNaN(val)) {
        e.target.value = val.toFixed(2);
    } else {
        e.target.value = "";
    }
    recalcTotals();
});

// Recalc when typing numeric fields
['rate_per_bdft', 'discount', 'down_payment'].forEach(id => {
    document.getElementById(id).addEventListener('input', recalcTotals);
});

document.addEventListener('input', (e) => {
    if (e.target.classList.contains('item-volume')) {
        recalcTotals();
    }
});

window.addEventListener('DOMContentLoaded', recalcTotals);



// ===========================
// Add row
// ===========================
document.getElementById('add_row_btn').onclick = () => {
    let table = document.getElementById('items_table');
    let index = table.rows.length;

    let row = `
    <tr>
        <td class="border p-2">
            <input type="text" name="items[${index}][substrate]" class="w-full p-2 border rounded-lg">
        </td>
        <td class="border p-2">
            <input type="text" name="items[${index}][thickness]" class="w-full p-2 border rounded-lg">
        </td>
        <td class="border p-2">
            <input type="number" step="0.01" name="items[${index}][volume]" class="item-volume w-full p-2 border rounded-lg">
        </td>
        <td class="border p-2 text-center">
            <button type="button" class="remove-row text-red-600">Remove</button>
        </td>
    </tr>`;
    table.insertAdjacentHTML("beforeend", row);
};

// ===========================
// Remove row
// ===========================
document.addEventListener("click", e => {
    if (e.target.classList.contains("remove-row")) {
        e.target.closest("tr").remove();
        recalcTotals();
    }
});

// ===========================
// AUTO CALC ON PAGE LOAD
// ===========================
window.addEventListener('DOMContentLoaded', recalcTotals);

</script>


@endsection
