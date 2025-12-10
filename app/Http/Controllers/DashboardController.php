<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\PayrollRun;
use App\Models\Project;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /* ============================================================
     * OWNER DASHBOARD
     * ============================================================ */
    public function ownerDashboard()
    {
        // Top stats
        $totalProjects   = \App\Models\Project::count();
        $activeEmployees = \App\Models\User::where('system_role', 'employee')->count();
        $totalRevenue    = \App\Models\Payment::where('status', 'approved')->sum('amount');
        $pendingQuotations = \App\Models\Quotation::where('status', 'pending')->count();
        $completedProjects = \App\Models\Project::where('status', 'completed')->count();
        $totalEmployees    = \App\Models\User::where('system_role', 'employee')->count();


        // Monthly Revenue (last months with data)
        $monthlyRevenue = \App\Models\Payment::where('status', 'approved')
            ->selectRaw('DATE_FORMAT(payment_date, "%b %Y") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderByRaw('MIN(payment_date)')
            ->pluck('total', 'month');

        // Project Status Distribution
        $projectStatus = \App\Models\Project::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Expense Breakdown
        $expenseBreakdown = \App\Models\Expense::selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('dashboard.owner', compact(
  'totalProjects',
 'activeEmployees',
            'totalRevenue',
            'monthlyRevenue',
            'projectStatus',
            'expenseBreakdown',
            'pendingQuotations',
            'completedProjects',
            'totalEmployees'
        ));
    }

    /* ============================================================
     * MANAGER DASHBOARD
     * ============================================================ */
    public function managerDashboard()
    {
        // Basic metrics
        $activeProjects    = Project::whereIn('status', ['active', 'pending', 'on_hold', 'delayed'])->count();
        $atRiskProjects    = Project::whereIn('status', ['delayed', 'on_hold'])->count();
        $completedProjects = Project::where('status', 'completed')->count();

        // Field workers (all employees for now)
        $fieldWorkers = User::where('system_role', 'employee')->count();

        // Project progress chart (top 8 projects by start date)
        $progressProjects = Project::whereIn('status', ['active', 'pending', 'on_hold', 'delayed'])
            ->orderBy('start_date', 'asc')
            ->take(8)
            ->get();

        $progressLabels = $progressProjects->pluck('project_name');
        $progressValues = $progressProjects->map(function ($project) {
            // uses accessor getProgressAttribute()
            return round($project->progress ?? 0, 1);
        });

        // Workforce distribution chart (top 8 projects with most workers)
        $workforceProjects = Project::withCount('users')
            ->whereIn('status', ['active', 'pending', 'on_hold', 'delayed'])
            ->orderByDesc('users_count')
            ->take(8)
            ->get();

        $workforceLabels = $workforceProjects->pluck('project_name');
        $workforceValues = $workforceProjects->pluck('users_count');

        // Average progress for display
        $averageProgress = $progressValues->count()
            ? round($progressValues->avg(), 1)
            : 0;

        return view('dashboard.manager', compact(
            'activeProjects',
            'atRiskProjects',
            'completedProjects',
            'fieldWorkers',
            'progressLabels',
            'progressValues',
            'workforceLabels',
            'workforceValues',
            'averageProgress'
        ));
    }

    /* ============================================================
     * EMPLOYEE DASHBOARD
     * ============================================================ */
    public function employeeDashboard()
    {
        $user = auth()->user();

        // KPI STATS
        $attendanceCount = \App\Models\Attendance::where('user_id', $user->id)->count();

        $hoursWorked = \App\Models\Attendance::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) as total_hours')
            ->value('total_hours') ?? 0;

        $activeProjects = $user->projects()->where('status', 'active')->get();
        $activeProjectsCount = $activeProjects->count();

        $latestPayroll = \App\Models\PayrollEntry::where('user_id', $user->id)
            ->latest('created_at')->first();
        $latestPayrollAmount = $latestPayroll->net_pay ?? 0;

        return view('dashboard.employee', compact(
            'activeProjects',
            'activeProjectsCount',
            'attendanceCount',
            'hoursWorked',
            'latestPayroll',
            'latestPayrollAmount'
        ));
    }

    /* ============================================================
    * PROFILE PAGE
    * ============================================================ */
    public function profile()
    {
        $user = auth()->user();
        return view('employee.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'given_name' => 'required|string|max:255',
            'surname'    => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('given_name', 'surname', 'email'));

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed'
        ]);

        auth()->user()->update([
            'password' => \Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /* ============================================================
    * MY ATTENDANCE PAGE (EMPLOYEE ONLY)
    * ============================================================ */
    public function attendance()
    {
        $user = auth()->user();

        $attendanceLogs = \App\Models\Attendance::with('project')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $projects = $user->projects()->orderBy('created_at', 'desc')->get();

        return view('employee.attendance', compact('attendanceLogs', 'projects'));
    }
    /* ============================================================
    * PROFILE PAGE
    * ============================================================ */
    public function profile()
    {
        $user = auth()->user();
        return view('employee.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'given_name' => 'required|string|max:255',
            'surname'    => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('given_name', 'surname', 'email'));

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed'
        ]);

        auth()->user()->update([
                'password' => \Hash::make($request->new_password)
            ]);

            return back()->with('success', 'Password updated successfully!');
        }

        /* ============================================================
        * MY ATTENDANCE PAGE (EMPLOYEE ONLY)
        * ============================================================ */
        public function attendance()
        {
            $user = auth()->user();

            $attendanceLogs = \App\Models\Attendance::with('project')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $projects = $user->projects()->orderBy('created_at', 'desc')->get();

            return view('employee.attendance', compact('attendanceLogs', 'projects'));
        }
    }



    /* ============================================================
     * ACCOUNTING DASHBOARD
     * ============================================================ */
    public function accountingDashboard()
    {
        // Payments
        $pendingPayments  = \App\Models\Payment::where('status', 'pending')->count();
        $approvedPayments = \App\Models\Payment::where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // Expenses
        $monthlyExpenses = \App\Models\Expense::whereMonth('expense_date', now()->month)
            ->sum('amount');

        $cashAdvancePending = \App\Models\CashAdvance::where('status', 'pending')->count();

        // Profit snapshot
        $totalIncome   = \App\Models\Payment::where('status', 'approved')->sum('amount');
        $totalExpenses = \App\Models\Expense::sum('amount');
        $profit        = $totalIncome - $totalExpenses;

        // Chart: Monthly Expenses
        $expenseTrend = \App\Models\Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Chart: Expense Breakdown
        $expenseBreakdown = \App\Models\Expense::selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $pendingExpenseList = \App\Models\Expense::where('status', 'pending')
            ->with(['submittedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Chart: Payments Summary
        $paymentSummary = [
            'pending'  => \App\Models\Payment::where('status', 'pending')->count(),
            'approved' => \App\Models\Payment::where('status', 'approved')->count(),
            'rejected' => \App\Models\Payment::where('status', 'rejected')->count(),
        ];

        $pendingPaymentList = \App\Models\Payment::where('status', 'pending')
            ->with(['project.client', 'submittedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.accounting', compact(
            'pendingPayments',
            'approvedPayments',
            'monthlyExpenses',
            'cashAdvancePending',
            'profit',
            'expenseTrend',
            'expenseBreakdown',
            'paymentSummary',
            'pendingPaymentList',
            'pendingExpenseList' , //
    // 'reversalCount' optional, as discussed
        ));
    }

    /* ============================================================
     * AUDIT DASHBOARD
     * ============================================================ */
    public function auditDashboard()
    {
         return view('dashboard.audit', [
        'recentLogs' => \App\Models\AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get(),

        'paymentStats' => [
            'approved' => \App\Models\Payment::where('status', 'approved')->count(),
            'pending'  => \App\Models\Payment::where('status', 'pending')->count(),
            'rejected' => \App\Models\Payment::where('status', 'rejected')->count(),
        ],

        'expenseStats' => [
            'approved' => \App\Models\Expense::where('status', 'approved')->count(),
            'pending'  => \App\Models\Expense::where('status', 'pending')->count(),
            'rejected' => \App\Models\Expense::where('status', 'rejected')->count(),
        ],
    ]);
    }

    public function syncAll()
    {
        $pendingClients = \App\Models\Client::where('sync_status', 'pending')->get();
        $syncedCount = 0;

        foreach ($pendingClients as $client) {
            try {
                \DB::connection('cloud')->table('clients')->updateOrInsert(
        ['email' => $client->email],
                [
                    'name' => $client->name,
                    'contact_person' => $client->contact_person,
                    'phone' => $client->phone,
                    'address' => $client->address,
                    'created_at' => $client->created_at,
                    'updated_at' => now(),
                ]
            );

                $client->sync_status = 'synced';
                $client->save();
                $syncedCount++;
            } catch (\Exception $e) {
                \Log::error("Client Sync Fail: " . $e->getMessage());
            }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Synced {$syncedCount} clients to cloud!"
            ]);

    }

}
