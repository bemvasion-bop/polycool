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
</style>


<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-semibold text-gray-900"></h2>

    <a href="{{ route('materials.create') }}" class="primary-btn">
        + Add Material
    </a>
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


<div class="glass-table">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr>
                <th>Material</th>
                <th>Category</th>
                <th>Unit</th>
                <th>Price/Unit</th>
                <th>Supplier</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($materials as $material)
            <tr>
                <td>{{ $material->name }}</td>
                <td>{{ $material->category ?? '—' }}</td>
                <td>{{ $material->unit ?? '—' }}</td>
                <td>₱{{ number_format($material->price_per_unit, 2) }}</td>
                <td>{{ $material->supplier->name ?? '—' }}</td>

                <td class="text-center space-x-2">
                    <a href="{{ route('materials.show', $material) }}"
                       class="text-blue-600 hover:underline text-sm">
                        View
                    </a>
                    <a href="{{ route('materials.edit', $material) }}"
                       class="text-purple-600 hover:underline text-sm">
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

@endsection
