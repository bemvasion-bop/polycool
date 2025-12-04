@extends('layouts.app')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
        Client Details
    </h2>
@endsection

@section('content')

<style>
    .glass-card {
        border-radius: 28px;
        background: rgba(255,255,255,0.78);
        backdrop-filter: blur(22px);
        border: 1px solid rgba(255,255,255,0.45);
        padding: 40px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.10);
        margin-bottom: 50px;
    }

    .detail-label {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        margin-bottom: 2px;
    }

    .detail-value {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 20px;
    }

    .ios-btn {
        padding: 10px 20px;
        border-radius: 16px;
        font-weight: 600;
        transition: .2s ease;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.4);
    }

    .ios-edit {
        background: linear-gradient(to right, #6366f1, #8b5cf6);
        color: white;
    }
    .ios-edit:hover {
        opacity: .9;
        transform: translateY(-2px);
    }

    .ios-back {
        background: rgba(255,255,255,0.55);
        color: #374151;
    }
    .ios-back:hover {
        background: rgba(255,255,255,0.85);
        transform: translateY(-1px);
    }
</style>


<div class="max-w-3xl mx-auto">

    <div class="glass-card">

        {{-- DETAILS --}}
        <div class="space-y-5">

            <div>
                <div class="detail-label">Client Name</div>
                <div class="detail-value">{{ $client->name }}</div>
            </div>

            <div>
                <div class="detail-label">Contact Person</div>
                <div class="detail-value">{{ $client->contact_person }}</div>
            </div>

            <div>
                <div class="detail-label">Email Address</div>
                <div class="detail-value">{{ $client->email }}</div>
            </div>

            <div>
                <div class="detail-label">Phone Number</div>
                <div class="detail-value">{{ $client->phone }}</div>
            </div>

            <div>
                <div class="detail-label">Full Address</div>
                <div class="detail-value">{{ $client->address }}</div>
            </div>

        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3 mt-10">

            <a href="{{ route('clients.index') }}" class="ios-btn ios-back">
                Back
            </a>

            <a href="{{ route('clients.edit', $client->id) }}" class="ios-btn ios-edit">
                Edit Client
            </a>

        </div>

    </div>

</div>

@endsection
