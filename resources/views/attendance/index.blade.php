@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-semibold mb-4">Daily Attendance</h1>

    {{-- Date Selector --}}
    <form method="GET" class="mb-6 flex items-center gap-3">
        <label>Select Date:</label>
        <input type="date" name="date" value="{{ $date }}" class="border rounded p-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Load</button>
    </form>

    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf

        <input type="hidden" name="date" value="{{ $date }}">

        <table class="w-full bg-white shadow rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Employee</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Time In</th>
                    <th class="p-2 border">Time Out</th>
                    <th class="p-2 border">Notes</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($employees as $emp)
                @php
                    $log = $logs[$emp->id] ?? null;
                @endphp

                <tr>
                    <td class="p-2 border">{{ $emp->name }}</td>

                    <td class="p-2 border">
                        <select name="attendance[{{ $loop->index }}][status]" class="border rounded p-1">
                            <option value="present"  {{ $log?->status=='present' ? 'selected':'' }}>Present</option>
                            <option value="absent"   {{ $log?->status=='absent' ? 'selected':'' }}>Absent</option>
                            <option value="on_leave" {{ $log?->status=='on_leave' ? 'selected':'' }}>On Leave</option>
                        </select>

                        <input type="hidden" name="attendance[{{ $loop->index }}][user_id]" value="{{ $emp->id }}">
                    </td>

                    <td class="p-2 border">
                        <input type="time" name="attendance[{{ $loop->index }}][time_in]"
                            value="{{ $log?->time_in }}" class="border rounded p-1">
                    </td>

                    <td class="p-2 border">
                        <input type="time" name="attendance[{{ $loop->index }}][time_out]"
                            value="{{ $log?->time_out }}" class="border rounded p-1">
                    </td>

                    <td class="p-2 border">
                        <input type="text" name="attendance[{{ $loop->index }}][notes]"
                            value="{{ $log?->notes }}" class="border rounded p-1 w-full">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button class="mt-4 px-6 py-2 bg-green-600 text-white rounded">
            Save Attendance
        </button>
    </form>

</div>
@endsection
