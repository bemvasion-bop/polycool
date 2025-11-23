@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Employees</h1>

        <a href="{{ route('employees.create') }}"
           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md">
            + New Employee
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <table class="w-full table-auto text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Full Name</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Position</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($employees as $employee)
                    <tr class="border-b">
                        <td class="px-4 py-3">
                            {{ $employee->given_name }} {{ $employee->middle_name }} {{ $employee->last_name }}
                        </td>

                        <td class="px-4 py-3 capitalize">
                            {{ $employee->role }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $employee->position_title }}
                        </td>

                        <td class="px-4 py-3 capitalize">
                            <span class="{{ $employee->employee_status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $employee->employee_status }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('employees.show', $employee) }}"
                               class="text-blue-600 hover:underline mr-3">
                                View
                            </a>

                            <a href="{{ route('employees.edit', $employee) }}"
                               class="text-yellow-600 hover:underline mr-3">
                                Edit
                            </a>

                            <form action="{{ route('employees.destroy', $employee) }}"
                                  method="POST" class="inline-block"
                                  onsubmit="return confirm('Delete this employee?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            No employees found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Employees</h1>

        <a href="{{ route('employees.create') }}"
           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md">
            + New Employee
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <table class="w-full table-auto text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Full Name</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Position</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($employees as $employee)
                    <tr class="border-b">
                        <td class="px-4 py-3">
                            {{ $employee->given_name }} {{ $employee->middle_name }} {{ $employee->last_name }}
                        </td>

                        <td class="px-4 py-3 capitalize">
                            {{ $employee->role }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $employee->position_title }}
                        </td>

                        <td class="px-4 py-3 capitalize">
                            <span class="{{ $employee->employee_status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $employee->employee_status }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('employees.show', $employee) }}"
                               class="text-blue-600 hover:underline mr-3">
                                View
                            </a>

                            <a href="{{ route('employees.edit', $employee) }}"
                               class="text-yellow-600 hover:underline mr-3">
                                Edit
                            </a>

                            <form action="{{ route('employees.destroy', $employee) }}"
                                  method="POST" class="inline-block"
                                  onsubmit="return confirm('Delete this employee?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            No employees found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
