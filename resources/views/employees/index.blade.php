@extends('layouts.app')

@section('content')
<div class="px-10 py-8">

    <h2 class="text-2xl font-semibold mb-6">Employees</h2>

    <div class="mb-6">
        <a href="{{ route('employees.create') }}"
           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded">
            + Add Employee
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">System Role</th>

                    {{-- NEW COLUMN --}}
                    <th class="p-3">Employment Type</th>

                    <th class="p-3">Status</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $emp)
                <tr class="border-b">
                    <td class="p-3">{{ $emp->given_name }} {{ $emp->last_name }}</td>
                    <td class="p-3">{{ $emp->email }}</td>
                    <td class="p-3 capitalize">{{ $emp->system_role }}</td>

                    {{-- NEW EMPLOYMENT TYPE DISPLAY --}}
                    <td class="p-3">
                        @if($emp->employment_type === 'field_worker')
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                Field Worker
                            </span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">
                                Office Staff
                            </span>
                        @endif
                    </td>

                    <td class="p-3">
                        @if($emp->employee_status === 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Active</span>
                        @else
                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded">Inactive</span>
                        @endif
                    </td>

                    <td class="p-3">
                        <a href="{{ route('employees.edit', $emp->id) }}" class="text-blue-600 hover:underline">Edit</a>
                        |
                        <form action="{{ route('employees.destroy', $emp->id) }}"
                              method="POST" class="inline-block"
                              onsubmit="return confirm('Delete this employee?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</div>
@endsection
