@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Supplier Details
</h2>
@endsection

@section('content')

<style>
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(22px) saturate(180%);
        -webkit-backdrop-filter: blur(22px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.45);
        padding: 36px 42px;
        box-shadow: 0 18px 55px rgba(0,0,0,0.08);
        max-width: 900px;
        margin: auto;
        transition: .25s ease;
    }

    .label-text {
        font-weight: 600;
        color: #111;
    }

    .detail-value {
        color: #374151;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        transition: .25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-edit {
        background: linear-gradient(to right, #7c3aed, #5b21b6);
        color: white;
    }
    .btn-edit:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }
    .btn-delete:hover {
        background: #dc2626;
    }

    .btn-back {
        background: #e5e7eb;
        color: #111;
    }
    .btn-back:hover {
        background: #d1d5db;
    }
</style>

<div class="glass-card">

    <h3 class="text-2xl font-semibold mb-6">{{ $supplier->name }}</h3>

    <div class="space-y-2 text-sm">
        <p><span class="label-text">Contact Person:</span>
           <span class="detail-value">{{ $supplier->contact_person ?? '—' }}</span></p>

        <p><span class="label-text">Phone:</span>
           <span class="detail-value">{{ $supplier->phone ?? '—' }}</span></p>

        <p><span class="label-text">Email:</span>
           <span class="detail-value">{{ $supplier->email ?? '—' }}</span></p>

        <p><span class="label-text">Address:</span>
           <span class="detail-value">{{ $supplier->address ?? '—' }}</span></p>

        <p><span class="label-text">Notes:</span>
           <span class="detail-value">{{ $supplier->notes ?? '—' }}</span></p>
    </div>

    <hr class="my-8 border-gray-300/60">

    <div class="flex items-center gap-3">
        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-edit">
            Edit
        </a>

        <form action="{{ route('suppliers.destroy', $supplier->id) }}"
              method="POST"
              onsubmit="return confirm('Delete this supplier?')">
            @csrf @method('DELETE')
            <button class="btn btn-delete">Delete</button>
        </form>

        <a href="{{ route('suppliers.index') }}" class="btn btn-back">
            Back
        </a>
    </div>

</div>

@endsection
