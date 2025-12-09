@extends('layouts.app')

@section('page-header')
<h2 class="text-3xl font-semibold text-gray-900 tracking-tight">
    Attendance Logs — {{ $user->full_name }}
</h2>
@endsection

@section('content')

<style>
    /* 🌈 Polysync Glass Table */
    .glass-table {
        border-radius: 26px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(22px);
        -webkit-backdrop-filter: blur(22px);
        border: 1px solid rgba(255,255,255,0.45);
        box-shadow: 0 18px 55px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    thead th {
        padding: 14px 18px;
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 600;
        color: #4b5563;
        background: rgba(255,255,255,0.85);
        cursor: pointer;
        backdrop-filter: blur(16px);
    }
    tbody td {
        padding: 16px 18px;
        font-size: 14px;
        color: #111;
        border-top: 1px solid rgba(0,0,0,0.10);
    }
    tbody tr:hover {
        background: rgba(255,255,255,0.80);
        transition: 0.2s ease;
    }

    .status-pill {
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .present-pill { background:#bbf7d0; color:#166534; }
    .absent-pill  { background:#fecaca; color:#991b1b; }
    .unknown-pill { background:#e5e7eb; color:#374151; }
</style>


<div class="glass-table">

    <table id="empLogsTable" class="w-full border-collapse text-left">

        <thead>
            <tr>
                <th onclick="sortTable('empLogsTable',0)">Date</th>
                <th onclick="sortTable('empLogsTable',1)">Project</th>
                <th onclick="sortTable('empLogsTable',2)">Time In</th>
                <th onclick="sortTable('empLogsTable',3)">Time Out</th>
                <th onclick="sortTable('empLogsTable',4)">Hours</th>
                <th onclick="sortTable('empLogsTable',5)">Status</th>
            </tr>
        </thead>

        <tbody>
        @forelse ($logs as $log)
            @php
                $in  = $log->time_in  ? \Carbon\Carbon::parse($log->time_in)->timezone('Asia/Manila')->format('M d, Y — h:i A') : '—';
                $out = $log->time_out ? \Carbon\Carbon::parse($log->time_out)->timezone('Asia/Manila')->format('M d, Y — h:i A') : '—';
                $date = \Carbon\Carbon::parse($log->date)->timezone('Asia/Manila')->format('M d, Y');
            @endphp

            <tr>
                <td>{{ $date }}</td>
                <td>{{ $log->project->project_name ?? '—' }}</td>
                <td class="text-green-700 font-medium">{{ $in }}</td>
                <td class="text-red-700 font-medium">{{ $out }}</td>
                <td>{{ $log->hours ?? '—' }}</td>
                <td>
                    @if($log->status == 'present')
                        <span class="status-pill present-pill">Present</span>
                    @elseif($log->status == 'absent')
                        <span class="status-pill absent-pill">Absent</span>
                    @else
                        <span class="status-pill unknown-pill">{{ ucfirst($log->status) }}</span>
                    @endif
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="6" class="p-5 text-center text-gray-500">
                    No attendance logs found.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>


<script>
function sortTable(tableId, colIndex) {
    const table = document.getElementById(tableId);
    let switching = true;
    let dir = "asc";
    let switchCount = 0;

    while (switching) {
        switching = false;
        let rows = table.rows;

        for (let i = 1; i < rows.length - 1; i++) {
            let shouldSwitch = false;

            let x = rows[i].getElementsByTagName("TD")[colIndex];
            let y = rows[i + 1].getElementsByTagName("TD")[colIndex];

            let xVal = x.innerText.toLowerCase();
            let yVal = y.innerText.toLowerCase();

            if ([0,2,3].includes(colIndex)) {
                xVal = new Date(xVal).getTime();
                yVal = new Date(yVal).getTime();
            }

            if (dir === "asc" && xVal > yVal) shouldSwitch = true;
            if (dir === "desc" && xVal < yVal) shouldSwitch = true;

            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                switchCount++;
                break;
            }
        }

        if (!switchCount && dir === "asc") {
            dir = "desc";
            switching = true;
        }
    }
}
</script>

@endsection
