<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Show the daily sheet (based on date)
    public function index(Request $request)
    {
        // Default: Today
        $date = $request->input('date', now()->format('Y-m-d'));

        $employees = User::where('role', 'employee')
            ->where('employee_status', 'active')
            ->orderBy('given_name')
            ->get();

        // Get existing logs for the selected date
        $logs = AttendanceLog::where('date', $date)
            ->get()
            ->keyBy('user_id');

        return view('attendance.index', compact('employees', 'logs', 'date'));
    }

    // Save the daily sheet
    public function store(Request $request)
    {
        $data = $request->validate([
            'date'           => 'required|date',
            'attendance'     => 'required|array',
            'attendance.*.user_id' => 'required|exists:users,id',
            'attendance.*.status'  => 'required|in:present,absent,on_leave',
            'attendance.*.time_in' => 'nullable',
            'attendance.*.time_out'=> 'nullable',
            'attendance.*.notes'   => 'nullable|string',
        ]);

        foreach ($data['attendance'] as $emp) {

            AttendanceLog::updateOrCreate(
                [
                    'user_id' => $emp['user_id'],
                    'date'    => $data['date'],
                ],
                [
                    'project_id'   => $emp['project_id'] ?? null,
                    'status'       => $emp['status'],
                    'time_in'      => $emp['time_in'],
                    'time_out'     => $emp['time_out'],
                    'notes'        => $emp['notes'] ?? null,
                    'hours_worked' => $this->calculateHours($emp['time_in'], $emp['time_out'], $emp['status']),
                ]
            );
        }

        return back()->with('success', 'Attendance saved successfully!');
    }

    private function calculateHours($time_in, $time_out, $status)
    {
        if ($status !== 'present' || !$time_in || !$time_out) {
            return 0;
        }

        try {
            $in  = Carbon::parse($time_in);
            $out = Carbon::parse($time_out);

            return round($in->floatDiffInHours($out), 2);

        } catch (\Exception $e) {
            return 0;
        }
    }
}
