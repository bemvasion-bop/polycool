@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6">Edit Employee</h1>

    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-3 gap-4">

            <div>
                <label>Given Name</label>
                <input name="given_name" class="input"
                       value="{{ $employee->given_name }}" required>
            </div>

            <div>
                <label>Middle Name</label>
                <input name="middle_name" class="input"
                       value="{{ $employee->middle_name }}">
            </div>

            <div>
                <label>Last Name</label>
                <input name="last_name" class="input"
                       value="{{ $employee->last_name }}" required>
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" class="input"
                       value="{{ $employee->email }}" required>
            </div>

            <div>
                <label>Phone Number</label>
                <input name="phone_number" class="input"
                       value="{{ $employee->phone_number }}">
            </div>

            <div>
                <label>Gender</label>
                <select name="gender" class="input">
                    <option value="">Select</option>
                    <option {{ $employee->gender=='male'?'selected':'' }}>male</option>
                    <option {{ $employee->gender=='female'?'selected':'' }}>female</option>
                    <option {{ $employee->gender=='other'?'selected':'' }}>other</option>
                </select>
            </div>

            <div>
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="input"
                       value="{{ $employee->date_of_birth }}">
            </div>

            <div>
                <label>Date Hired</label>
                <input type="date" name="date_hired" class="input"
                       value="{{ $employee->date_hired }}">
            </div>

            <div>
                <label>Street Address</label>
                <input name="street_address" class="input"
                       value="{{ $employee->street_address }}">
            </div>

            <div>
                <label>City</label>
                <input name="city" class="input"
                       value="{{ $employee->city }}">
            </div>

            <div>
                <label>Province</label>
                <input name="province" class="input"
                       value="{{ $employee->province }}">
            </div>

            <div>
                <label>Postal Code</label>
                <input name="postal_code" class="input"
                       value="{{ $employee->postal_code }}">
            </div>

            <div>
                <label>Position Title</label>
                <input name="position_title" class="input"
                       value="{{ $employee->position_title }}" required>
            </div>

            <div>
                <label>Employee Status</label>
                <select name="employee_status" class="input">
                    <option value="active" {{ $employee->employee_status=='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ $employee->employee_status=='inactive'?'selected':'' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label>System Role</label>
                <select name="system_role" class="input">
                    <option value="employee" {{ $employee->role=='employee'?'selected':'' }}>Employee</option>
                    <option value="manager" {{ $employee->role=='manager'?'selected':'' }}>Manager</option>
                </select>
            </div>

            <div>
                <label>New Password (optional)</label>
                <input type="password" name="password" class="input">
            </div>

            <div>
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" class="input">
            </div>
        </div>

        <button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            Update Employee
        </button>
    </form>
</div>
@endsection
