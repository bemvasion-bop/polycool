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
<div class="bg-white/60 backdrop-blur rounded-2xl p-6 shadow">

    @if($logs->isEmpty())
        <p class="text-gray-500 text-sm">
            No audit activities recorded yet.
        </p>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-gray-600">
                    <th class="py-2 text-left">Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr class="border-b last:border-0">
                    <td class="py-2">
                        {{ $log->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td>
                        {{ $log->user->full_name ?? 'Unknown' }}
                    </td>
                    <td class="font-medium">
                        {{ $log->action }}
                    </td>
                    <td class="text-gray-600">
                        {{ $log->details ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection
