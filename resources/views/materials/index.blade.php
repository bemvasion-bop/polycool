@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Materials
</h2>
@endsection

@section('content')

<style>
    /* 🌈 Polysync Glass Table */
    .glass-table {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(22px) saturate(180%);
        -webkit-backdrop-filter: blur(22px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.50);
        overflow: hidden;
        box-shadow: 0 18px 55px rgba(0,0,0,0.08);
    }

    thead th {
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    color: #4b5563;
    padding: 16px 18px;
    background: rgba(255,255,255,0.8);
    }

    tbody td {
        padding: 18px 18px;
        border-top: 1px solid rgba(180,180,180,0.35);
        font-size: 14px;
        color: #111;
    }


    tbody tr:hover {
        background: rgba(255,255,255,0.75);
        transition: .22s ease;
    }

    .primary-btn {
        padding: 10px 20px;
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        font-weight: 600;
        border-radius: 18px;
        font-size: 14px;
        transition: .25s ease;
        box-shadow: 0 5px 12px rgba(120,120,255,0.35);
    }
    .primary-btn:hover { opacity: .92; transform: translateY(-2px); }

    .status-pill {
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

</style>

<div class="flex flex-wrap items-center justify-between mb-6 gap-3">

    <a href="{{ route('materials.create') }}"
       class="glass-btn bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
        + New Material
    </a>

    <div class="flex items-center gap-3">
        <input type="text"
            id="materialSearch"
            placeholder="Search materials…"
            class="rounded-full bg-white/60 px-4 py-2 text-sm border border-white/60 backdrop-blur-sm">
    </div>

</div>

@if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Swal.fire({
                icon: 'success',
                title: "{{ session('success') }}",
                toast: true,
                timer: 2200,
                position: 'top-end',
                showConfirmButton: false
            });
        });
    </script>
@endif


<<div class="glass-table">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr>
                <th>Material</th>
                <th>Category</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Supplier</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($materials as $material)
            <tr class="material-row"
                data-text="{{ strtolower(
                    $material->name.' '.
                    ($material->category ?? '').' '.
                    ($material->supplier->name ?? '')
                ) }}">

                <td>{{ $material->name }}</td>
                <td>{{ $material->category ?? '—' }}</td>
                <td>{{ $material->unit ?? '—' }}</td>
                <td>₱{{ number_format($material->price_per_unit, 2) }}</td>
                <td>{{ $material->supplier->name ?? '—' }}</td>

                <td class="text-center">
                    <a href="{{ route('materials.edit', $material) }}"
                       class="glass-btn text-xs px-3 py-1 text-indigo-600">
                        Edit
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-4 text-center text-gray-500">
                    No materials found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById("materialSearch");
    const rows = document.querySelectorAll(".material-row");

    searchInput.addEventListener("input", () => {
        const term = searchInput.value.toLowerCase().trim();

        rows.forEach(row => {
            row.style.display = row.dataset.text.includes(term)
                ? ""
                : "none";
        });
    });

});
</script>


@endsection
