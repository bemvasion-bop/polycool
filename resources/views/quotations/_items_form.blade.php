<table class="w-full text-left border mt-4">
    <thead>
        <tr class="bg-gray-100">
            <th class="px-2 py-1">Description</th>
            <th class="px-2 py-1">Unit</th>
            <th class="px-2 py-1">Qty</th>
            <th class="px-2 py-1">Unit Price</th>
            <th class="px-2 py-1">Line Total</th>
        </tr>
    </thead>
    <tbody id="items-table-body">
        @php
            $oldItems = old('items', isset($quotation) ? $quotation->items->toArray() : [['description' => '', 'unit' => '', 'quantity' => 1, 'unit_price' => 0]]);
        @endphp

        @foreach($oldItems as $index => $item)
        <tr>
            <td class="px-2 py-1">
                <input type="text" name="items[{{ $index }}][description]"
                       class="w-full border rounded px-2 py-1"
                       value="{{ $item['description'] ?? '' }}">
            </td>
            <td class="px-2 py-1">
                <input type="text" name="items[{{ $index }}][unit]"
                       class="w-full border rounded px-2 py-1"
                       value="{{ $item['unit'] ?? '' }}">
            </td>
            <td class="px-2 py-1">
                <input type="number" step="0.01" min="0"
                       name="items[{{ $index }}][quantity]"
                       class="w-full border rounded px-2 py-1"
                       value="{{ $item['quantity'] ?? 1 }}">
            </td>
            <td class="px-2 py-1">
                <input type="number" step="0.01" min="0"
                       name="items[{{ $index }}][unit_price]"
                       class="w-full border rounded px-2 py-1"
                       value="{{ $item['unit_price'] ?? 0 }}">
            </td>
            <td class="px-2 py-1 text-gray-500 text-sm">
                (auto-calculated on save)
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<p class="mt-2 text-xs text-gray-500">
    * For now, line totals are computed on the server when you save.
</p>
