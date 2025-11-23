@extends('layouts.app')

@section('content')
<div class="p-8">
    <h1 class="text-2xl font-semibold mb-6">Add Employee</h1>

    <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- ROW 1 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label>Given Name</label>
                <input type="text" name="given_name" required class="input" value="{{ old('given_name') }}">
            </div>

            <div>
                <label>Middle Name</label>
                <input type="text" name="middle_name" class="input" value="{{ old('middle_name') }}">
            </div>

            <div>
                <label>Last Name</label>
                <input type="text" name="last_name" required class="input" value="{{ old('last_name') }}">
            </div>
        </div>

        {{-- ROW 2 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label>Email</label>
                <input type="email" name="email" required class="input" value="{{ old('email') }}">
            </div>

            <div>
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="input" value="{{ old('phone_number') }}">
            </div>

            <div>
                <label>Gender</label>
                <select name="gender" class="input">
                    <option value="">Select</option>
                    <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                    <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                    <option value="other" {{ old('gender')=='other'?'selected':'' }}>Other</option>
                </select>
            </div>
        </div>

        {{-- ROW 3 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="input" value="{{ old('date_of_birth') }}">
            </div>

            <div>
                <label>Date Hired</label>
                <input type="date" name="date_hired" class="input" value="{{ old('date_hired') }}">
            </div>

            <div>
                <label>Street Address</label>
                <input type="text" name="street_address" class="input" value="{{ old('street_address') }}">
            </div>
        </div>

        {{-- ROW 4 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label>City</label>
                <input type="text" name="city" class="input" value="{{ old('city') }}">
            </div>

            <div>
                <label>Province</label>
                <input type="text" name="province" class="input" value="{{ old('province') }}">
            </div>

            <div>
                <label>Postal Code</label>
                <input type="text" name="postal_code" class="input" value="{{ old('postal_code') }}">
            </div>
        </div>

        {{-- ROW 5 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label>Position Title</label>
                <input type="text" name="position_title" required class="input" value="{{ old('position_title') }}">
            </div>

            <div>
                <label>Employee Status</label>
                <select name="employee_status" required class="input">
                    <option value="active" {{ old('employee_status')=='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ old('employee_status')=='inactive'?'selected':'' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label>System Role</label>
                <select name="system_role" required class="input">
                    <option value="employee" {{ old('system_role')=='employee'?'selected':'' }}>Employee</option>
                    <option value="manager" {{ old('system_role')=='manager'?'selected':'' }}>Manager</option>
                </select>
            </div>
        </div>

        {{-- ROW 6 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label>Password</label>
                <input type="password" name="password" required class="input">
            </div>

            <div>
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required class="input">
            </div>
        </div>

        <button class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">
            Save Employee
        </button>
    </form>
</div>

@endsection
