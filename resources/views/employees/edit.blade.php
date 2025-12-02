@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Edit Employee</h2>

    <form action="{{ route('employees.update', $employee->id) }}" method="POST"
          class="bg-white shadow p-8 rounded-lg">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- COLUMN 1 --}}
            <div class="space-y-4">

                <div>
                    <label class="font-medium">Given Name</label>
                    <input type="text" name="given_name" value="{{ $employee->given_name }}"
                           class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="font-medium">Email</label>
                    <input type="email" name="email" value="{{ $employee->email }}"
                           class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="font-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ $employee->date_of_birth }}"
                           class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">City</label>
                    <input type="text" name="city" value="{{ $employee->city }}"
                           class="w-full border rounded p-2">
                </div>

            </div>

            {{-- COLUMN 2 --}}
            <div class="space-y-4">

                <div>
                    <label class="font-medium">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ $employee->middle_name }}"
                           class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ $employee->phone_number }}"
                           class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Date Hired</label>
                    <input type="date" name="date_hired" value="{{ $employee->date_hired }}"
                           class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Province</label>
                    <input type="text" name="province" value="{{ $employee->province }}"
                           class="w-full border rounded p-2">
                </div>

            </div>

            {{-- COLUMN 3 --}}
            <div class="space-y-4">

                <div>
                    <label class="font-medium">Last Name</label>
                    <input type="text" name="last_name" value="{{ $employee->last_name }}"
                           class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="font-medium">Gender</label>
                    <select name="gender" class="w-full border rounded p-2">
                        <option value="">Select</option>
                        <option value="male" {{ $employee->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $employee->gender == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $employee->gender == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Street Address</label>
                    <input type="text" name="street_address" value="{{ $employee->street_address }}"
                           class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Postal Code</label>
                    <input type="text" name="postal_code" value="{{ $employee->postal_code }}"
                           class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">System Role</label>
                    <select name="system_role" class="w-full border rounded p-2" required>
                        <option value="manager"   {{ $employee->system_role === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="employee"  {{ $employee->system_role === 'employee' ? 'selected' : '' }}>Employee</option>
                        <option value="accounting" {{ $employee->system_role === 'accounting' ? 'selected' : '' }}>Accounting</option>
                        <option value="audit"      {{ $employee->system_role === 'audit' ? 'selected' : '' }}>Audit</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Employee Status</label>
                    <select name="employee_status" class="w-full border rounded p-2">
                        <option value="active" {{ $employee->employee_status == 'active' ? 'selected':'' }}>Active</option>
                        <option value="inactive" {{ $employee->employee_status == 'inactive' ? 'selected':'' }}>Inactive</option>
                    </select>
                </div>


                <div>
                    @if($employee->system_role !== 'owner' && $employee->system_role !== 'admin')
                        <!-- Show employment type selector -->
                    @else
                        <input type="hidden" name="employment_type" value="office_staff">
                    @endif
                    <label class="block font-medium mb-1">Employment Type</label>
                    <select name="employment_type" class="border rounded w-full p-2" required>
                        <option value="field_worker" {{ $employee->employment_type == 'field_worker' ? 'selected' : '' }}>
                            Field Worker
                        </option>
                        <option value="office_staff" {{ $employee->employment_type == 'office_staff' ? 'selected' : '' }}>
                            Office Staff
                        </option>
                    </select>
                </div>

            </div>

        </div>

        <div class="mt-8">
            <button class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded">
                Update Employee
            </button>
        </div>

    </form>
</div>
@endsection
