@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Add Employee</h2>

    <form action="{{ route('employees.store') }}" method="POST" class="bg-white shadow p-8 rounded-lg">
        @csrf

        {{-- 3 COLUMN GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- COLUMN 1 --}}
            <div class="space-y-4">

                <div>
                    <label class="font-medium">Given Name</label>
                    <input type="text" name="given_name" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="font-medium">Email</label>
                    <input type="email" name="email" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="font-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">City</label>
                    <input type="text" name="city" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Password</label>
                    <input type="password" name="password" class="w-full border rounded p-2" required>
                </div>

            </div>

            {{-- COLUMN 2 --}}
            <div class="space-y-4">

                <div>
                    <label class="font-medium">Middle Name</label>
                    <input type="text" name="middle_name" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Phone Number</label>
                    <input type="text" name="phone_number" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Date Hired</label>
                    <input type="date" name="date_hired" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Province</label>
                    <input type="text" name="province" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
                </div>

            </div>

            {{-- COLUMN 3 --}}
            <div class="space-y-4">

                <div>
                    <label class="font-medium">Last Name</label>
                    <input type="text" name="last_name" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="font-medium">Gender</label>
                    <select name="gender" class="w-full border rounded p-2">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Street Address</label>
                    <input type="text" name="street_address" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="font-medium">Postal Code</label>
                    <input type="text" name="postal_code" class="w-full border rounded p-2">
                </div>

                {{-- SYSTEM ROLE --}}
                <div>
                    <label class="font-medium">System Role</label>
                    <select name="system_role" class="w-full border rounded p-2" required>
                        <option value="">Select</option>
                        <option value="manager">Manager</option>
                        <option value="employee">Employee</option>
                        <option value="accounting">Accounting</option>
                        <option value="audit">Audit</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Employment Type</label>
                    <select name="employment_type" class="w-full border rounded p-2" required>
                        <option value="">Select</option>
                        <option value="field_worker">Field Worker</option>
                        <option value="office_staff">Office Staff</option>
                    </select>
                </div>

                {{-- EMPLOYEE STATUS (Restored) --}}
                <div>
                    <label class="font-medium">Employee Status</label>
                    <select name="employee_status" class="w-full border rounded p-2">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

            </div>

        </div>

        <div class="mt-8">
            <button class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded">
                Save Employee
            </button>
        </div>

    </form>
</div>
@endsection
