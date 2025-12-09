@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Generate Payroll
</h2>
@endsection

@section('content')

<style>
    /* 🌈 GLASS PANEL */
    .glass-card {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(22px) saturate(180%);
        -webkit-backdrop-filter: blur(22px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.50);
        box-shadow: 0 15px 45px rgba(0,0,0,0.08);
        padding: 28px 32px;
        transition: .3s ease;
    }
    .glass-card:hover {
        box-shadow: 0 25px 65px rgba(0,0,0,0.10);
    }

    /* 🔹 INPUTS */
    .form-input {
        width: 100%;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.12);
        background: rgba(255,255,255,0.75);
        font-size: 14px;
        transition: .2s ease;
    }
    .form-input:focus {
        border-color: #6366f1;
        outline: none;
        background: rgba(255,255,255,0.95);
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


<div class="glass-card max-w-3xl mx-auto">

    <form action="{{ route('payroll.preview') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-input" required>
            </div>

            <div>
                <label>End Date</label>
                <input type="date" name="end_date" class="form-input" required>
            </div>

            <div class="md:col-span-2">
                <label>Employee</label>
                <select name="employee_id" class="form-input" required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->given_name }} {{ $emp->last_name }}
                            ({{ ucfirst($emp->employment_type) }})
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="text-right mt-8">
            <button type="submit" class="primary-btn">
                Generate Preview
            </button>
        </div>

    </form>

</div>

@endsection
