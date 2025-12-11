@extends('layouts.app')

@section('title', 'Profile Settings')

@section('page-header')
    <h2 class="text-3xl font-semibold text-gray-800">
        Profile Settings
    </h2>
    <p class="text-gray-500 mt-1">Manage your personal information</p>
@endsection

@section('content')
<style>
    .glass-panel {
        border-radius: 26px;
        background: rgba(255,255,255,0.45);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.55);
        box-shadow: 0 20px 50px rgba(0,0,0,0.08);
        padding: 28px 32px;
    }
</style>

<div class="glass-panel max-w-xl mx-auto space-y-10">

    {{-- ==================== BASIC INFO ==================== --}}
    <form action="{{ route('employee.profile.update') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="font-semibold text-sm text-gray-700">Given Name</label>
            <input type="text" name="given_name" value="{{ $user->given_name }}"
                   class="w-full rounded-lg border px-3 py-2" required>
        </div>

        <div>
            <label class="font-semibold text-sm text-gray-700">Surname</label>
            <input type="text" name="surname" value="{{ $user->surname }}"
                   class="w-full rounded-lg border px-3 py-2" required>
        </div>

        <div>
            <label class="font-semibold text-sm text-gray-700">Email</label>
            <input type="email" name="email" value="{{ $user->email }}"
                   class="w-full rounded-lg border px-3 py-2" required>
        </div>

        <button class="w-full bg-indigo-600 text-white rounded-lg py-2 font-semibold hover:bg-indigo-700">
            Save Changes
        </button>
    </form>


    {{-- ==================== PASSWORD UPDATE ==================== --}}
    <div class="border-t"></div>

    <form action="{{ route('employee.profile.password') }}" method="POST" class="space-y-4">
        @csrf

        <h3 class="text-lg font-semibold text-gray-800">Change Password</h3>

        <div>
            <input type="password" name="current_password"
                   placeholder="Current Password"
                   class="w-full rounded-lg border px-3 py-2" required>
        </div>

        <div>
            <input type="password" name="new_password"
                   placeholder="New Password"
                   class="w-full rounded-lg border px-3 py-2" required>
        </div>

        <div>
            <input type="password" name="confirm_password"
                   placeholder="Confirm New Password"
                   class="w-full rounded-lg border px-3 py-2" required>
        </div>

        <button class="w-full bg-purple-600 text-white rounded-lg py-2 font-semibold hover:bg-purple-700">
            Update Password
        </button>
    </form>

</div>
@endsection
