<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function ownerDashboard()
    {
        $totalProjects     = Project::count();
        $activeEmployees   = User::where('role', 'employee')
                                 ->where('employee_status', 'active')
                                 ->count();

        // Later this will be replaced with sum of payments or revenues
        $totalRevenue = 0;

        return view('dashboard.owner', [
            'totalProjects'   => $totalProjects,
            'activeEmployees' => $activeEmployees,
            'totalRevenue'    => $totalRevenue,
        ]);
    }

    public function managerDashboard()
    {
        $user = Auth::user();

        // Manager sees employees and projects they handle
        $managedProjects = Project::where('manager_id', $user->id)->count();
        $employees = User::where('role', 'employee')->count();

        return view('dashboard.manager', [
            'managedProjects' => $managedProjects,
            'employees'       => $employees
        ]);
    }

    public function employeeDashboard()
    {
        $user = Auth::user();

        // Employee attendance
        $totalPresent = AttendanceLog::where('user_id', $user->id)
                                  ->where('status', 'present')
                                  ->count();

        $totalAbsent = AttendanceLog::where('user_id', $user->id)
                                 ->where('status', 'absent')
                                 ->count();

        return view('dashboard.employee', [
            'totalPresent' => $totalPresent,
            'totalAbsent'  => $totalAbsent,
        ]);
    }
}
