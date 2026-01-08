<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /* ============================================================
       EMPLOYEE SIDE
    ============================================================ */

    /** Employee dashboard attendance list */
    public function index()
    {
        $logs = Attendance::with('project')
            ->where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.index', compact('logs'));
    }


    /** Employee time-in using QR */
    public function employeeTimeIn(Project $project)
    {
        $user = auth()->user();

        Attendance::create([
            'user_id'    => $user->id,
            'project_id' => $project->id,
            'time_in'    => now(),
            'status'     => 'present'
        ]);

        return back()->with('success', 'Time-in recorded!');
    }


    /** Employee time-out */
    public function employeeTimeOut(Project $project)
    {
        $user = auth()->user();

        $log = Attendance::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->whereNull('time_out')
            ->first();

        if (!$log) {
            return back()->with('error', 'No active attendance log found.');
        }

        $log->update([
            'time_out' => now()
        ]);

        return back()->with('success', 'Time-out recorded!');
    }


    /** Employee QR code page */
    public function myQR()
    {
        $user = auth()->user();
        return view('attendance.my-qr', compact('user'));
    }

    /* ============================================================
       MANAGER/OWNER SIDE
    ============================================================ */

    public function manage()
    {
        if (!in_array(auth()->user()->system_role, ['owner', 'manager'])) {
            abort(403);
        }

        $employees = User::whereIn('system_role', ['employee', 'manager'])
            ->orderBy('given_name')
            ->get();

        $projects = Project::orderBy('project_name')->get();

        return view('attendance.manage', compact('employees', 'projects'));
    }


    public function employeeLogs(User $user)
    {
        $logs = Attendance::where('user_id', $user->id)->latest()->get();

        return view('attendance.manager.employee-logs', compact('user','logs'));
    }

    public function projectLogs(Project $project)
    {
        $logs = Attendance::where('project_id', $project->id)->latest()->get();

        return view('attendance.manager.project-logs', compact('project','logs'));
    }


        /* ============================================================
        QR Scanner API (public endpoint)
        ============================================================ */
        public function scan(Request $request)
        {
            $qr = $request->qr_code;
            $projectId = $request->project_id;

            if (!$qr || !$projectId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing QR code or project.'
                ], 400);
            }

            // Find user by QR code
            $user = User::where('qr_code', $qr)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR code not recognized.'
                ], 404);
            }

            // Block non-employees (manager, accounting, audit, owner)
            if ($user->system_role !== 'employee') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This user is not a field employee.'
                ], 403);
            }

            // Today's date
            $today = now()->toDateString();

            // Try to find existing record
            $attendance = Attendance::where('user_id', $user->id)
                ->where('project_id', $projectId)
                ->where('date', $today)
                ->first();

            if (!$attendance) {
                // TIME IN
                Attendance::create([
                    'user_id'    => $user->id,
                    'project_id' => $projectId,
                    'date'       => $today,
                    'time_in'    => now(),
                    'status'     => 'present'
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Time-in recorded.',
                    'employee' => $user->full_name
                ]);
            }

            // If already time-in but no time-out → TIME OUT
            if ($attendance->time_in && !$attendance->time_out) {
                $attendance->update([
                    'time_out' => now(),
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Time-out recorded.',
                    'employee' => $user->full_name
                ]);
            }

            // Already time-in AND time-out
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance already completed for today.'
            ], 409);
        }


    public function qrScanner()
    {
        $projects = Project::with('client')->where('status','active')->get();

        return view('attendance.scanner.index', compact('projects'));
    }




    public function byEmployee(User $user)
    {
        if (!in_array(auth()->user()->system_role, ['owner', 'manager'])) {
            abort(403);
        }

        $logs = Attendance::with('project')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.employee-logs', compact('user', 'logs'));
    }

    public function byProject(Project $project)
    {
        if (!in_array(auth()->user()->system_role, ['owner', 'manager'])) {
            abort(403);
        }

        $logs = Attendance::with('user')
            ->where('project_id', $project->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.project-logs', compact('project', 'logs'));
    }


    public function overview()
    {
        if (auth()->user()->system_role !== 'owner') {
            abort(403);
        }

        $logs = Attendance::with(['user', 'project'])
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.overview', compact('logs'));
    }

}
