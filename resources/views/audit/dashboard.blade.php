@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    System Audit Logs
</h2>
<p class="text-gray-500 text-sm mt-1">
    Read-only system activity monitoring
</p>
@endsection

@section('content')

{{-- ========================= --}}
{{-- FILTER BAR --}}
{{-- ========================= --}}
<div class="bg-white/60 backdrop-blur rounded-2xl p-4 shadow mb-6">

    <form method="GET" action="{{ route('audit.dashboard') }}"
          class="flex flex-wrap items-end gap-4">

        {{-- MONTH --}}
        <div>
            <label class="text-xs text-gray-500">Month</label>
            <select name="month"
                class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}"
                        {{ ($month ?? '') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0,0,0,$m,1)) }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- YEAR --}}
        <div>
            <label class="text-xs text-gray-500">Year</label>
            <select name="year"
                class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All</option>
                @for ($y = now()->year; $y >= 2023; $y--)
                    <option value="{{ $y }}"
                        {{ ($year ?? '') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                Apply Filter
            </button>

            <a href="{{ route('audit.dashboard') }}"
                class="px-4 py-2 bg-gray-200 rounded-lg text-sm hover:bg-gray-300">
                Reset
            </a>
        </div>

        {{-- PRINT --}}
        @if(!$logs->isEmpty())
        <div class="ml-auto">
            <a href="{{ route('audit.print', ['month'=>$month, 'year'=>$year]) }}"
               target="_blank"
               class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700">
                🖨 Print Logs
            </a>
        </div>
        @endif

    </form>
</div>

{{-- ========================= --}}
{{-- AUDIT TABLE --}}
{{-- ========================= --}}
<div class="bg-white/60 backdrop-blur rounded-2xl p-6 shadow">

    @if($logs->isEmpty())
        <p class="text-gray-500 text-sm">
            No audit activities found for the selected period.
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-gray-600">
                        <th class="py-2 text-left">Timestamp</th>
                        <th class="text-left">User</th>
                        <th class="text-left">Action</th>
                        <th class="text-left">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr class="border-b last:border-0">
                        <td class="py-2">
                            {{ $log->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            {{ $log->user->full_name ?? 'System' }}
                        </td>
                        <td class="font-medium">
                            {{ $log->action }}
                        </td>
                        <td class="text-gray-600 text-xs">
                            {{ $log->details ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection
