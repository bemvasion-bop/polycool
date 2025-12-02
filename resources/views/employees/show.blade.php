@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <div class="max-w-4xl mx-auto bg-white shadow p-8 rounded-lg">

        <h2 class="text-2xl font-semibold mb-6">Employee Details</h2>

        {{-- STATUS BADGE --}}
        <p class="mb-4">
            Status:
            <span class="px-3 py-1 rounded text-white
                {{ $employee->employee_status == 'active' ? 'bg-green-600' : 'bg-red-600' }}">
                {{ ucfirst($employee->employee_status) }}
            </span>
        </p>

        {{-- PERSONAL INFO --}}
        <h3 class="text-lg font-semibold mt-6 mb-2">Personal Information</h3>
        <hr class="mb-4">

        <p><strong>Full Name:</strong>
            {{ $employee->given_name }} {{ $employee->middle_name }} {{ $employee->last_name }}</p>

        <p><strong>Gender:</strong> {{ ucfirst($employee->gender) }}</p>

        <p><strong>Date of Birth:</strong>
            {{ $employee->date_of_birth ?? 'Not set' }}</p>

        {{-- CONTACT INFO --}}
        <h3 class="text-lg font-semibold mt-6 mb-2">Contact Information</h3>
        <hr class="mb-4">

        <p><strong>Email:</strong> {{ $employee->email }}</p>
        <p><strong>Phone Number:</strong> {{ $employee->phone_number ?? 'N/A' }}</p>

        {{-- ADDRESS --}}
        <h3 class="text-lg font-semibold mt-6 mb-2">Address</h3>
        <hr class="mb-4">

        <p><strong>Street:</strong> {{ $employee->street_address }}</p>
        <p><strong>City:</strong> {{ $employee->city }}</p>
        <p><strong>Province:</strong> {{ $employee->province }}</p>
        <p><strong>Postal Code:</strong> {{ $employee->postal_code }}</p>

        {{-- EMPLOYMENT INFO --}}
        <h3 class="text-lg font-semibold mt-6 mb-2">Employment Information</h3>
        <hr class="mb-4">

        <p><strong>Position Title:</strong> {{ $employee->position_title }}</p>
        <p><strong>Date Hired:</strong> {{ $employee->date_hired ?? 'Not set' }}</p>

        {{-- SYSTEM ACCESS --}}
        <h3 class="text-lg font-semibold mt-6 mb-2">System Access</h3>
        <hr class="mb-4">

        <p><strong>Role:</strong> {{ ucfirst($employee->role) }}</p>

        {{-- BUTTONS --}}
        <div class="mt-8 flex space-x-4">
            <a href="{{ route('employees.edit', $employee->id) }}"
               class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Edit Employee
            </a>

            <a href="{{ route('employees.index') }}"
               class="px-5 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Back
            </a>
        </div>

    </div>

</div>
@endsection
