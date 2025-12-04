@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">Suppliers</h2>
@endsection

@section('content')

<style>
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.78);
        backdrop-filter: blur(22px);
        border: 1px solid rgba(255,255,255,0.45);
        box-shadow: 0 18px 60px rgba(0,0,0,0.12);
        padding: 0;
        overflow: hidden;
        width: 100%;
    }

    table {
        border-collapse: separate;
        border-spacing: 0;
    }
    table th, table td {
        padding: 18px 24px;
        border: none !important;
        white-space: nowrap;
    }

    thead tr {
        background: rgba(255,255,255,0.6);
        font-weight: 600;
        color: #4b5563;
    }

    tbody tr {
        transition: .2s ease;
    }
    tbody tr:hover {
        background: rgba(0,0,0,0.03);
    }

    .primary-action {
        padding: 10px 18px;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.45);
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: .2s;
    }
    .primary-action:hover {
        transform: translateY(-2px);
        opacity: .9;
    }

    .action-link {
        font-size: 14px;
        font-weight: 500;
        transition: .2s;
    }
</style>


{{-- ALERT --}}
@if(session('success'))
    <div class="mb-6 px-4 py-3 bg-green-100 text-green-700 rounded-xl border border-green-200">
        {{ session('success') }}
    </div>
@endif


{{-- ACTION BAR --}}
<div class="flex justify-between items-center mb-8">

    <p class="text-sm text-gray-600 tracking-tight">
        Total: <strong>{{ count($suppliers) }}</strong> Suppliers
    </p>

    <a href="{{ route('suppliers.create') }}" class="primary-action">
        + Add Supplier
    </a>

</div>


{{-- SUPPLIERS TABLE --}}
<div class="glass-card">

    <table class="w-full text-left">

        <thead>
            <tr>
                <th>Supplier Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>

        <tbody>
        @forelse($suppliers as $supplier)
            <tr>

                <td class="font-medium text-gray-900">
                    {{ $supplier->name }}
                </td>

                <td class="text-gray-700">
                    {{ $supplier->contact_person ?? '—' }}
                </td>

                <td class="text-gray-700">
                    {{ $supplier->phone ?? '—' }}
                </td>

                <td class="text-right flex justify-end items-center gap-3">

                    {{-- VIEW --}}
                    <a href="{{ route('suppliers.show', $supplier) }}"
                       class="action-link text-indigo-600 hover:text-indigo-800">
                        View
                    </a>

                    {{-- EDIT --}}
                    <a href="{{ route('suppliers.edit', $supplier) }}"
                       class="action-link text-yellow-600 hover:text-yellow-700">
                        Edit
                    </a>

                    {{-- DELETE --}}
                    <form action="{{ route('suppliers.destroy', $supplier) }}"
                          method="POST"
                          onsubmit="return confirm('Delete this supplier?')">
                        @csrf @method('DELETE')
                        <button class="action-link text-red-600 hover:text-red-700">
                            Delete
                        </button>
                    </form>

                </td>

            </tr>

        @empty
            <tr>
                <td colspan="4" class="text-center py-6 text-gray-500">
                    No suppliers found.
                </td>
            </tr>
        @endforelse
        </tbody>

    </table>

</div>

@endsection
