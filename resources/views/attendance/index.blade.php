@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">

        <h2 class="text-2xl font-semibold mb-6">My Attendance</h2>

        {{-- TODAY STATUS --}}
        <div class="bg-gray-100 p-4 rounded mb-6">
            <h3 class="text-lg font-semibold mb-2">Today</h3>

            @php
                $today = $logs->where('date', now()->toDateString())->first();
            @endphp

            <p><strong>Date:</strong> {{ now()->format('M d, Y') }}</p>

            @if($today)
                <p><strong>Time In:</strong> {{ $today->time_in ? \Carbon\Carbon::parse($today->time_in)->format('h:i A') : '—' }}</p>
                <p><strong>Time Out:</strong> {{ $today->time_out ? \Carbon\Carbon::parse($today->time_out)->format('h:i A') : '—' }}</p>
                <p><strong>Status:</strong> {{ ucfirst($today->status) }}</p>
            @else
                <p>No attendance yet.</p>
            @endif

            {{-- ACTION BUTTONS --}}
            <div class="mt-4 flex space-x-4">

                {{-- TIME IN --}}
                @if(!$today || !$today->time_in)
                    <form action="{{ route('attendance.timein') }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            TIME IN
                        </button>
                    </form>
                @endif

                {{-- TIME OUT --}}
                @if($today && $today->time_in && !$today->time_out)
                    <form action="{{ route('attendance.timeout') }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            TIME OUT
                        </button>
                    </form>
                @endif

            </div>
        </div>

        {{-- HISTORY TABLE --}}
        <h3 class="text-xl font-semibold mb-4">Attendance History</h3>

        <table class="w-full border text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Time In</th>
                    <th class="p-2 border">Time Out</th>
                    <th class="p-2 border">Hours Worked</th>
                    <th class="p-2 border">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td class="p-2 border">{{ $log->date }}</td>

                        <td class="p-2 border">
                            {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '—' }}
                        </td>

                        <td class="p-2 border">
                            {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '—' }}
                        </td>

                        <td class="p-2 border">{{ number_format($log->hours_worked, 2) }}</td>

                        <td class="p-2 border">{{ ucfirst($log->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
