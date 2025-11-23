@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-semibold mb-4">Employee Details</h1>

    <p><strong>Name:</strong> {{ $employee->name }}</p>
    <p><strong>Role:</strong> {{ ucfirst($employee->role) }}</p>
    <p><strong>Position:</strong> {{ $employee->position_title }}</p>
    <p><strong>Status:</strong> {{ ucfirst($employee->employee_status) }}</p>

    <p><strong>Email:</strong> {{ $employee->email }}</p>
    <p><strong>Phone:</strong> {{ $employee->phone_number }}</p>

    <p><strong>Address:</strong>
        {{ $employee->street_address }},
        {{ $employee->city }},
        {{ $employee->province }},
        {{ $employee->postal_code }}
    </p>

    <div class="mt-6">
        <a href="{{ route('employees.edit', $employee) }}"
           class="text-blue-600 hover:underline mr-4">Edit</a>

        <a href="{{ route('employees.index') }}"
           class="text-gray-600 hover:underline">Back</a>
    </div>
</div>
@endsection
