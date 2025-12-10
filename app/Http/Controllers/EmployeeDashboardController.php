<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\PayrollEntry;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeDashboardController extends Controller
{
    /* ============================================================
     | EMPLOYEE DASHBOARD
     ============================================================ */
    public function employeeDashboard()
    {
        $user = auth()->user();

        // Assigned Projects Count
        $activeProjects = $user->projects()
            ->where('status', 'active')
            ->get();

        $activeProjectsCount = $activeProjects->count();

        // Attendance Count
        $attendanceCount = AttendanceLog::where('user_id', $user->id)->count();

        // Latest Payroll
        $latestPayroll = PayrollEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        $latestPayrollAmount = $latestPayroll ? $latestPayroll->total_salary : 0;

        // Hours Worked (current week)
        $hoursWorked = AttendanceLog::where('user_id', $user->id)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_hours');

        // Today's Attendance State
        $todayLog = AttendanceLog::where('user_id', $user->id)
            ->where('date', today())
            ->first();
        $hasAttendanceToday = $todayLog !== null;
        $hasTimeOut = $todayLog && $todayLog->timeout !== null;

        return view('employee.dashboard', compact(
            'user',
            'activeProjects',
            'activeProjectsCount',
            'attendanceCount',
            'latestPayroll',
            'latestPayrollAmount',
            'hoursWorked',
            'hasAttendanceToday',
            'hasTimeOut'
        ));
    }


    /* ============================================================
     | PROFILE PAGE VIEW
     ============================================================ */
    public function profile()
    {
        $user = auth()->user();
        return view('employee.profile', compact('user'));
    }


    /* ============================================================
     | UPDATE PERSONAL INFO
     ============================================================ */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'given_name' => 'required|string|max:255',
            'surname'    => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'given_name' => $request->given_name,
            'surname'    => $request->surname,
            'email'      => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }


    /* ============================================================
     | CHANGE PASSWORD
     ============================================================ */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed'
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}
