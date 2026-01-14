@extends('layouts.app')

@section('page-header')
    {{-- 🟦 iOS Back Button
    <a href="{{ route('quotations.index') }}" class="ios-back-btn inline-flex items-center gap-1 mb-4">
        <i data-lucide="chevron-left" class="w-4 h-4"></i>
        <span>Back</span>
    </a>

    --}}

    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Edit Quotation</h2>

    <style>
:root {
    --ps-primary: linear-gradient(135deg,#6366f1,#8b5cf6);
    --ps-glass-bg: rgba(255,255,255,0.6);
    --ps-border: rgba(255,255,255,0.55);
    --ps-radius: 22px;
}

/* ================= BACK BUTTON ================= */
.ios-back-btn {
    padding: 8px 16px;
    border-radius: 999px;
    background: var(--ps-glass-bg);
    border: 1px solid var(--ps-border);
    backdrop-filter: blur(12px);
    font-weight: 500;
    box-shadow: 0 6px 20px rgba(0,0,0,.08);
    transition: .2s ease;
}
.ios-back-btn:hover {
    transform: translateY(-1px);
    background: rgba(255,255,255,.8);
}

/* ================= GLASS CARD ================= */
.glass-card {
    background: var(--ps-glass-bg);
    border-radius: var(--ps-radius);
    backdrop-filter: blur(20px);
    border: 1px solid var(--ps-border);
    box-shadow: 0 20px 50px rgba(0,0,0,.08);
    padding: 32px;
    margin-bottom: 32px;
}

/* ================= INPUTS ================= */
.glass-input {
    background: rgba(255,255,255,.7);
    border: 1px solid rgba(0,0,0,.08);
    padding: 12px 16px;
    border-radius: 14px;
    width: 100%;
    transition: .2s ease;
}
.glass-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,.25);
}

/* ================= TABLE ================= */
.soft-table {
    border-radius: 18px;
    overflow: hidden;
}
.soft-table th {
    background: rgba(99,102,241,.08);
    font-weight: 600;
}
.soft-table th, .soft-table td {
    border: 1px solid rgba(0,0,0,.06);
    padding: 12px;
}

/* ================= BUTTONS ================= */
.ps-btn {
    padding: 10px 20px;
    border-radius: 14px;
    font-weight: 600;
    transition: .2s ease;
}
.ps-btn-primary {
    background: var(--ps-primary);
    color: white;
}
.ps-btn-primary:hover {
    opacity: .9;
    transform: translateY(-1px);
}
.ps-btn-outline {
    background: rgba(255,255,255,.7);
    border: 1px solid var(--ps-border);
}

/* ================= MODAL ================= */
.modal-bg {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.35);
    backdrop-filter: blur(8px);
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
    box-shadow: 0 30px 80px rgba(0,0,0,.18);
}
</style>

@endsection



@section('content')

<style>
    /* ---------------------- INPUTS ---------------------- */
    .glass-input {
        background: rgba(255,255,255,0.65);
        border: 1px solid rgba(0,0,0,0.08);
        padding: 12px 16px;
        border-radius: 14px;
        width: 100%;
        transition: .2s ease;
        backdrop-filter: blur(8px);
    }
    .glass-input:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 2px #6c63ff30;
    }

    /* ---------------------- SECTION CARD ---------------------- */
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.75);
        padding: 32px;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.45);
        box-shadow: 0 18px 60px rgba(0,0,0,0.12);
        margin-bottom: 32px;
    }

    /* ---------------------- TABLE ---------------------- */
    .soft-table th {
        background: rgba(240,240,255,0.6);
        font-weight: 600;
    }
    .soft-table td, .soft-table th {
        border: 1px solid rgba(0,0,0,0.08);
        padding: 12px;
    }

    /* ---------------------- BUTTONS ---------------------- */
    .primary-btn {
        padding: 12px 22px;
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        border-radius: 14px;
        font-weight: 600;
        transition: .2s ease;
    }
    .primary-btn:hover {
        opacity: .85;
        transform: translateY(-2px);
    }

    .danger-btn {
        padding: 10px 16px;
        background: #ef4444;
        color: white;
        border-radius: 12px;
    }

    /* ---------------------- CANCEL MODAL ---------------------- */
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



{{-- ========================== --}}
{{-- MAIN WRAPPER --}}
{{-- ========================== --}}
<div class="max-w-6xl mx-auto pb-20">

    {{-- ERROR CARD --}}
    @if ($errors->any())
        <div class="glass-card bg-red-50/60 border-red-200 text-red-700">
            <ul class="list-disc ml-6">
                @foreach ($errors->all() as $error)
                    <li class="py-1">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('quotations.update', $quotation->id) }}">
        @csrf
        @method('PUT')


        {{-- ========================== --}}
        {{-- CLIENT INFO --}}
        {{-- ========================== --}}
        <div class="glass-card">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Client Information</h3>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="font-medium text-gray-700">Client</label>
                    <select name="client_id" id="client" class="glass-input mt-1" required>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}"
                            data-address="{{ $client->address }}"
                            @selected($quotation->client_id == $client->id)>
                            {{ $client->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-medium text-gray-700">Client Address</label>
                    <input type="text" name="address" id="client_address"
                        class="glass-input bg-gray-100/70"
                        value="{{ $quotation->address }}" readonly>
                </div>

            </div>
        </div>



        {{-- ========================== --}}
        {{-- PROJECT INFORMATION --}}
        {{-- ========================== --}}
        <div class="glass-card">
            <h3 class="text-xl font-semibold mb-4">Project Information</h3>

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <label class="font-medium">Project / Vessel Name</label>
                    <input type="text" name="project_name"
                        value="{{ $quotation->project_name }}"
                        class="glass-input mt-1" required>
                </div>

                <div>
                    <label class="font-medium">System</label>
                    <input type="text" name="system"
                        value="{{ $quotation->system }}"
                        class="glass-input mt-1">
                </div>

                <div class="md:col-span-2">
                    <label class="font-medium">Scope of Work</label>
                    <textarea name="scope_of_work" rows="3"
                        class="glass-input mt-1">{{ $quotation->scope_of_work }}</textarea>
                </div>

                <div>
                    <label class="font-medium">Duration (days)</label>
                    <input type="number" name="duration_days"
                        value="{{ $quotation->duration_days }}"
                        class="glass-input mt-1">
                </div>
            </div>
        </div>



        {{-- ========================== --}}
        {{-- PARTICULARS --}}
        {{-- ========================== --}}
        <div class="glass-card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Particulars</h3>

                <button type="button" id="add_row_btn"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg">
                    + Add Row
                </button>
            </div>

            <table class="w-full soft-table text-left">
                <thead>
                    <tr>
                        <th>Substrate</th>
                        <th>Thickness</th>
                        <th>Volume (bd.ft)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="items_table">

                    @foreach($quotation->items as $index => $item)
                    <tr>
                        <td><input type="text" name="items[{{ $index }}][substrate]" class="glass-input" value="{{ $item->substrate }}"></td>
                        <td><input type="text" name="items[{{ $index }}][thickness]" class="glass-input" value="{{ $item->thickness }}"></td>
                        <td><input type="number" step="0.01" name="items[{{ $index }}][volume]" class="item-volume glass-input" value="{{ $item->volume }}"></td>
                        <td class="text-center">
                            <button type="button" class="text-red-600 remove-row">Remove</button>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>



        {{-- ========================== --}}
        {{-- COSTING --}}
        {{-- ========================== --}}
        <div class="glass-card">
            <h3 class="text-xl font-semibold mb-4">Costing</h3>

            <div class="grid md:grid-cols-3 gap-6">

                <div>
                    <label class="font-medium">Total Bd.Ft</label>
                    <input type="number" id="total_bdft" name="total_bdft"
                        value="{{ $quotation->total_bdft }}"
                        class="glass-input bg-gray-100/70" readonly>
                </div>

                <div>
                    <label class="font-medium">Rate per Bd.Ft</label>
                    <input type="number" id="rate_per_bdft" name="rate_per_bdft"
                        value="{{ $quotation->rate_per_bdft }}" class="glass-input">
                </div>

                <div>
                    <label class="font-medium">Discount</label>
                    <input type="number" id="discount" name="discount"
                        value="{{ $quotation->discount }}" class="glass-input">
                </div>

                <div>
                    <label class="font-medium">Contract Price</label>
                    <input type="number" id="contract_price" name="contract_price"
                        value="{{ $quotation->contract_price }}"
                        class="glass-input bg-gray-100/70" readonly>
                </div>

                <div>
                    <label class="font-medium">Down Payment</label>
                    <input type="number" id="down_payment" name="down_payment"
                        value="{{ $quotation->down_payment }}" class="glass-input">
                </div>

                <div>
                    <label class="font-medium">Balance</label>
                    <input type="number" id="balance" name="balance"
                        value="{{ $quotation->balance }}"
                        class="glass-input bg-gray-100/70" readonly>
                </div>

            </div>
        </div>



        {{-- ========================== --}}
        {{-- TERMS & CONDITIONS --}}
        {{-- ========================== --}}
        <div class="glass-card">
            <h3 class="text-xl font-semibold mb-4">Terms & Conditions</h3>

            <textarea name="conditions" rows="6"
                class="glass-input">{{ $quotation->conditions }}</textarea>
        </div>



        {{-- ========================== --}}
        {{-- BUTTONS --}}
        {{-- ========================== --}}
        <div class="flex justify-end gap-4 mt-6">

            {{-- CANCEL BUTTON (opens modal) --}}
            <button type="button"
                id="cancelBtn"
                class="px-4 py-2 rounded-lg bg-gray-300 text-gray-800 hover:bg-gray-400">
                Cancel
            </button>

            <button type="submit" class="primary-btn">
                Update Quotation
            </button>
        </div>

    </form>
</div>



{{-- ========================== --}}
{{-- CANCEL CONFIRMATION MODAL --}}
{{-- ========================== --}}
<div id="cancelModal" class="modal-bg">
    <div class="modal-box">
        <h3 class="text-lg font-semibold mb-3">Cancel Editing?</h3>
        <p class="text-gray-600 mb-6">Changes you made will not be saved. Are you sure?</p>

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



{{-- ================================================= --}}
{{-- JAVASCRIPT — ADD ROW + CALCULATIONS + MODAL --}}
{{-- ================================================= --}}
<script>
// Autofill client address
document.getElementById('client').addEventListener('change', function () {
    document.getElementById('client_address').value =
        this.options[this.selectedIndex].getAttribute('data-address');
});

// Cancel Modal
const modal = document.getElementById('cancelModal');
document.getElementById('cancelBtn').onclick = () => modal.style.display = 'flex';
document.getElementById('stayBtn').onclick = () => modal.style.display = 'none';

// Recalculate totals
function recalcTotals() {
    let total = 0;

    document.querySelectorAll('.item-volume').forEach(el => {
        total += parseFloat(el.value) || 0;
    });

    document.getElementById('total_bdft').value = total.toFixed(2);

    let rate = parseFloat(rate_per_bdft.value) || 0;
    let discount = parseFloat(discount.value) || 0;

    let contract = (total * rate) - discount;
    document.getElementById('contract_price').value = contract.toFixed(2);

    let down = parseFloat(down_payment.value) || 0;
    document.getElementById('balance').value = (contract - down).toFixed(2);
}

document.addEventListener('input', recalcTotals);

// Add row
document.getElementById('add_row_btn').onclick = () => {
    let table = document.getElementById('items_table');
    let index = table.rows.length;

    let row = `
    <tr>
        <td><input type="text" name="items[${index}][substrate]" class="glass-input"></td>
        <td><input type="text" name="items[${index}][thickness]" class="glass-input"></td>
        <td><input type="number" step="0.01" name="items[${index}][volume]" class="item-volume glass-input"></td>
        <td class="text-center"><button type="button" class="text-red-600 remove-row">Remove</button></td>
    </tr>`;

    table.insertAdjacentHTML("beforeend", row);
};

// Remove row
document.addEventListener("click", (e) => {
    if (e.target.classList.contains("remove-row")) {
        e.target.closest("tr").remove();
        recalcTotals();
    }
});
</script>

@endsection
