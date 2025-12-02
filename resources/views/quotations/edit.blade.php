@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Edit Quotation</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-6 py-4 rounded mb-6">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('quotations.update', $quotation->id) }}"
          class="bg-white shadow p-8 rounded-lg space-y-8">
        @csrf
        @method('PUT')

        <!-- ========================= -->
        <!-- CLIENT INFORMATION       -->
        <!-- ========================= -->
        <h3 class="text-lg font-semibold">Client Information</h3>

        <div class="grid grid-cols-2 gap-6">

            <div>
                <label class="font-medium">Client</label>
                <select name="client_id" id="client"
                        class="w-full border rounded-lg p-3 mt-1" required>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}"
                            data-address="{{ $client->address }}"
                            {{ $quotation->client_id == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-medium">Client Address</label>
                <input type="text" name="address" id="client_address"
                       class="w-full border rounded-lg p-3 mt-1 bg-gray-100"
                       value="{{ $quotation->address }}" readonly>
            </div>

        </div>

        <!-- ========================= -->
        <!-- PROJECT INFORMATION      -->
        <!-- ========================= -->
        <h3 class="text-lg font-semibold">Project Information</h3>

        <div class="grid grid-cols-2 gap-6">

            <div>
                <label class="font-medium">Project / Vessel Name</label>
                <input type="text" name="project_name"
                       value="{{ $quotation->project_name }}"
                       class="w-full border rounded-lg p-3 mt-1" required>
            </div>

            <div>
                <label class="font-medium">System</label>
                <input type="text" name="system"
                       value="{{ $quotation->system }}"
                       class="w-full border rounded-lg p-3 mt-1">
            </div>

            <div class="col-span-2">
                <label class="font-medium">Scope of Work</label>
                <textarea name="scope_of_work"
                          class="w-full border rounded-lg p-3 mt-1"
                          rows="3">{{ $quotation->scope_of_work }}</textarea>
            </div>

            <div>
                <label class="font-medium">Duration (days)</label>
                <input type="number" name="duration_days"
                       value="{{ $quotation->duration_days }}"
                       class="w-full border rounded-lg p-3 mt-1">
            </div>

        </div>

        <!-- ========================= -->
        <!-- PARTICULARS              -->
        <!-- ========================= -->
        <h3 class="text-lg font-semibold">Particulars</h3>

        <table class="w-full border">
            <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">Substrate</th>
                <th class="border p-2">Thickness</th>
                <th class="border p-2">Volume (bd.ft)</th>
                <th class="border p-2">Action</th>
            </tr>
            </thead>

            <tbody id="items_table">

            @foreach($quotation->items as $index => $item)
                <tr>
                    <td class="border p-2">
                        <input type="text" name="items[{{ $index }}][substrate]"
                               class="w-full p-2 border rounded"
                               value="{{ $item->substrate }}">
                    </td>

                    <td class="border p-2">
                        <input type="text" name="items[{{ $index }}][thickness]"
                               class="w-full p-2 border rounded"
                               value="{{ $item->thickness }}">
                    </td>

                    <td class="border p-2">
                        <input type="number" step="0.01"
                               name="items[{{ $index }}][volume]"
                               class="item-volume w-full p-2 border rounded"
                               value="{{ $item->volume }}">
                    </td>

                    <td class="border p-2 text-center">
                        <button type="button" class="remove-row text-red-600">Remove</button>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

        <button type="button"
                id="add_row_btn"
                class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg">
            + Add Row
        </button>

        <!-- ========================= -->
        <!-- COSTING                  -->
        <!-- ========================= -->
        <h3 class="text-lg font-semibold">Costing</h3>

        <div class="grid grid-cols-3 gap-6">

            <div>
                <label class="font-medium">Total Bd.Ft</label>
                <input type="number" id="total_bdft" name="total_bdft"
                       value="{{ $quotation->total_bdft }}"
                       class="w-full border rounded-lg p-3 mt-1 bg-gray-100"
                       readonly>
            </div>

            <div>
                <label class="font-medium">Rate per Bd.Ft</label>
                <input type="number" id="rate_per_bdft" name="rate_per_bdft"
                       value="{{ $quotation->rate_per_bdft }}"
                       class="w-full border rounded-lg p-3 mt-1">
            </div>

            <div>
                <label class="font-medium">Discount</label>
                <input type="number" id="discount" name="discount"
                       value="{{ $quotation->discount }}"
                       class="w-full border rounded-lg p-3 mt-1">
            </div>

            <div>
                <label class="font-medium">Contract Price</label>
                <input type="number" id="contract_price" name="contract_price"
                       value="{{ $quotation->contract_price }}"
                       class="w-full border rounded-lg p-3 mt-1 bg-gray-100"
                       readonly>
            </div>

            <div>
                <label class="font-medium">Down Payment</label>
                <input type="number" id="down_payment" name="down_payment"
                       value="{{ $quotation->down_payment }}"
                       class="w-full border rounded-lg p-3 mt-1">
            </div>

            <div>
                <label class="font-medium">Balance</label>
                <input type="number" id="balance" name="balance"
                       value="{{ $quotation->balance }}"
                       class="w-full border rounded-lg p-3 mt-1 bg-gray-100"
                       readonly>
            </div>

        </div>

        <!-- ========================= -->
        <!-- CONDITIONS               -->
        <!-- ========================= -->
        <h3 class="text-lg font-semibold">Terms & Conditions</h3>

        <textarea name="conditions"
                  class="w-full border rounded-lg p-3 mt-1"
                  rows="5">{{ $quotation->conditions }}</textarea>

        {{-- Back --}}
            <a href="{{ route('quotations.index') }}"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Back
            </a>

        <!-- SUBMIT -->
        <button type="submit"
                class="px-6 py-3 bg-purple-600 text-white rounded-lg shadow">
            Update Quotation
        </button>

    </form>
</div>

{{-- ==================================== --}}
{{-- JAVASCRIPT — ADD ROW & AUTO MATH     --}}
{{-- ==================================== --}}
<script>
// Autofill client address
document.getElementById('client').addEventListener('change', function () {
    let address = this.options[this.selectedIndex].getAttribute('data-address');
    document.getElementById('client_address').value = address;
});

// CALCULATE TOTALS
function recalcTotals() {
    let total = 0;

    document.querySelectorAll('.item-volume').forEach(el => {
        total += parseFloat(el.value) || 0;
    });

    document.getElementById('total_bdft').value = total.toFixed(2);

    let rate = parseFloat(document.getElementById('rate_per_bdft').value) || 0;
    let discount = parseFloat(document.getElementById('discount').value) || 0;

    let contract = (total * rate) - discount;
    document.getElementById('contract_price').value = contract.toFixed(2);

    let down = parseFloat(document.getElementById('down_payment').value) || 0;
    document.getElementById('balance').value = (contract - down).toFixed(2);
}

// Trigger recalculation
document.addEventListener('input', function (e) {
    if (
        e.target.classList.contains('item-volume') ||
        e.target.id === 'discount' ||
        e.target.id === 'rate_per_bdft' ||
        e.target.id === 'down_payment'
    ) {
        recalcTotals();
    }
});

// ADD NEW ROW
document.getElementById('add_row_btn').addEventListener('click', function () {
    let table = document.getElementById('items_table');
    let index = table.rows.length;

    let row = `
        <tr>
            <td class="border p-2">
                <input type="text" name="items[${index}][substrate]"
                       class="w-full p-2 border rounded">
            </td>
            <td class="border p-2">
                <input type="text" name="items[${index}][thickness]"
                       class="w-full p-2 border rounded">
            </td>
            <td class="border p-2">
                <input type="number" step="0.01"
                       name="items[${index}][volume]"
                       class="item-volume w-full p-2 border rounded">
            </td>
            <td class="border p-2 text-center">
                <button type="button" class="remove-row text-red-600">Remove</button>
            </td>
        </tr>
    `;

    table.insertAdjacentHTML('beforeend', row);
});

// REMOVE ROW
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        recalcTotals();
    }
});
</script>

@endsection
