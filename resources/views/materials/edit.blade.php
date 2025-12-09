@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Edit Material
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
        border: 1px solid rgba(255,255,255,0.50);
        box-shadow: 0 18px 55px rgba(0,0,0,0.08);
        padding: 32px 36px;
        transition: .25s ease;
        max-width: 900px;
        margin: auto;
    }

    label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
        display: block;
    }

    /* 🔹 Input Style */
    .form-input {
        width: 100%;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.12);
        background: rgba(255,255,255,0.75);
        font-size: 14px;
        transition: .25s ease;
    }
    .form-input:focus {
        border-color: #6366f1;
        background: rgba(255,255,255,0.95);
        outline: none;
        box-shadow: 0 0 0 3px #A5B4FC60;
    }

    /* 💜 Update Button */
    .primary-btn {
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        padding: 11px 24px;
        border-radius: 18px;
        font-size: 14px;
        font-weight: 600;
        transition: .25s ease;
        border: none;
    }
    .primary-btn:hover {
        opacity: .92;
        transform: translateY(-2px);
    }

    /* 🔙 Back Button */
    .back-btn {
        padding: 11px 20px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 18px;
        background: #e5e7eb;
        color: #111;
        transition: .25s ease;
    }
    .back-btn:hover {
        background: #d1d5db;
    }
</style>

<div class="glass-card">

    <form action="{{ route('materials.update', $material->id) }}"
          method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label>Material Name</label>
            <input type="text" name="name"
                   value="{{ $material->name }}"
                   class="form-input" required>
        </div>

        <div>
            <label>Category</label>
            <input type="text" name="category"
                   value="{{ $material->category }}"
                   class="form-input">
        </div>

        <div>
            <label>Unit</label>
            <input type="text" name="unit"
                   value="{{ $material->unit }}"
                   class="form-input">
        </div>

        <div>
            <label>Price per Unit</label>
            <input type="number" step="0.01" name="price_per_unit"
                   value="{{ $material->price_per_unit }}"
                   class="form-input" required>
        </div>

        <div>
            <label>Supplier</label>
            <select name="supplier_id" class="form-input">
                <option value="">— None —</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ $material->supplier_id == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Notes</label>
            <textarea name="notes"
                      class="form-input"
                      rows="3">{{ $material->notes }}</textarea>
        </div>

        {{-- BUTTONS --}}
        <div class="flex justify-end gap-3 pt-3">
            <a href="{{ route('materials.index') }}" class="back-btn">
                Back
            </a>

            <button class="primary-btn">
                Update Material
            </button>
        </div>

    </form>

</div>

@endsection
