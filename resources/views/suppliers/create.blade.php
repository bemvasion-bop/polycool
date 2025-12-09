@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Add Supplier
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
        box-shadow: 0 15px 45px rgba(0,0,0,0.08);
        padding: 32px 36px;
        transition: .3s ease;
    }
    .glass-card:hover {
        box-shadow: 0 28px 65px rgba(0,0,0,0.10);
    }

    /* 🔹 Inputs */
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

    label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
        display: block;
    }

    /* 💜 CTA Button */
    .primary-btn {
        background: linear-gradient(to right, #8b5cf6, #6366f1);
        color: white;
        padding: 12px 22px;
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
</style>

<div class="max-w-3xl mx-auto glass-card">

    <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label>Supplier Name</label>
            <input type="text" name="name" class="form-input" required>
        </div>

        <div>
            <label>Contact Person</label>
            <input type="text" name="contact_person" class="form-input">
        </div>

        <div>
            <label>Phone</label>
            <input type="text" name="phone" class="form-input">
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" class="form-input">
        </div>

        <div>
            <label>Address</label>
            <input type="text" name="address" class="form-input">
        </div>

        <div>
            <label>Notes</label>
            <textarea name="notes" class="form-input" rows="3"></textarea>
        </div>

        <div class="text-right pt-4">
            <button type="submit" class="primary-btn">
                Save Supplier
            </button>
        </div>

    </form>

</div>

@endsection
