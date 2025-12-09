@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Material Details
</h2>
@endsection

@section('content')

<style>
    /* 🌈 GLASS CARD */
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
        margin-right: 4px;
    }

    .detail-value {
        color: #374151;
    }

    /* 🔘 BUTTONS */
    .btn {
        padding: 10px 18px;
        border-radius: 16px;
        font-size: 14px;
        font-weight: 600;
        transition: .25s ease;
    }

    .btn-edit {
        background: linear-gradient(to right, #7c3aed, #6366f1);
        color: white;
    }
    .btn-edit:hover {
        opacity: .9;
        transform: translateY(-2px);
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

    <h3 class="text-2xl font-semibold mb-6">{{ $material->name }}</h3>

    <div class="space-y-2 text-sm">
        <p><span class="label-text">Category:</span>
           <span class="detail-value">{{ $material->category ?? '—' }}</span></p>

        <p><span class="label-text">Unit:</span>
           <span class="detail-value">{{ $material->unit ?? '—' }}</span></p>

        <p><span class="label-text">Price/Unit:</span>
           <span class="detail-value">₱{{ number_format($material->price_per_unit, 2) }}</span></p>

        <p><span class="label-text">Supplier:</span>
           <span class="detail-value">{{ $material->supplier->name ?? '—' }}</span></p>

        <p><span class="label-text">Notes:</span>
           <span class="detail-value">{{ $material->notes ?? '—' }}</span></p>
    </div>

    <hr class="my-8 border-gray-300/60">

    <div class="flex items-center gap-3">
        <a href="{{ route('materials.edit', $material->id) }}" class="btn btn-edit">
            Edit
        </a>

        <form action="{{ route('materials.destroy', $material->id) }}"
              method="POST"
              onsubmit="return confirm('Delete this material?')">
            @csrf @method('DELETE')
            <button class="btn btn-delete">
                Delete
            </button>
        </form>

        <a href="{{ route('materials.index') }}" class="btn btn-back">
            Back
        </a>
    </div>

</div>

@endsection
