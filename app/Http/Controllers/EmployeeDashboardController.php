<?php

namespace App\Http\Controllers;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.employee');
    }


    public function employeeDashboard()
    {
        $user = auth()->user();

        // Load their assigned projects
        $assignedProjects = $user->projects()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->get();

        // Load today’s attendance (if exists)
        $todayLog = AttendanceLog::where('user_id', $user->id)
            ->where('date', today())
            ->first();

        return view('employee.dashboard', compact('user', 'assignedProjects', 'todayLog'));
    }
}
