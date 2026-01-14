<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\PayrollRun;
use App\Models\Project;
use App\Models\Payment;
use App\Models\User;
use App\Models\Expense;
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

    public function kpi()
{
    return response()->json([
        'totalProjects'   => Project::count(),
        'activeEmployees' => User::where('employment_type', 'field_worker')->count(),
        'totalRevenue'    => Payment::where('status', 'approved')->sum('amount'),
        'pendingPayments' => Payment::where('status', 'pending')->count(),
        'pendingExpenses' => Expense::where('status', 'pending')->count(),
        'updated_at'      => now()->toDateTimeString(),
    ]);
}

    private function projectKPIs()
    {
        return [
            'total'     => Project::count(),
            'active'    => Project::whereIn('status', ['active', 'pending', 'on_hold'])->count(),
            'at_risk'   => Project::whereIn('status', ['delayed', 'on_hold'])->count(),
            'completed' => Project::where('status', 'completed')->count(),
        ];
    }

    private function workforceKPIs()
    {
        return [
            'field_workers' => User::where('employment_type', 'field_worker')->count(),
            'employees'     => User::where('system_role', 'employee')->count(),
        ];
    }

    private function financialKPIs()
    {
        return [
            'revenue'        => Payment::where('status', 'approved')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'pending_expenses' => Expense::where('status', 'pending')->count(),
        ];
    }

    /* ============================================================
     * MANAGER DASHBOARD
     * ============================================================ */
    public function managerDashboard()
    {
        $projects  = $this->projectKPIs();
        $workforce = $this->workforceKPIs();

        /* ===========================
        | PROJECT PROGRESS (TOP 8)
        =========================== */
        $progressProjects = Project::whereIn('status', ['active', 'pending', 'on_hold', 'delayed'])
            ->orderBy('start_date', 'asc')
            ->take(8)
            ->get();

        $progressLabels = [];
        $progressValues = [];

        foreach ($progressProjects as $project) {
            $progressLabels[] = $project->project_name;
            $progressValues[] = round($project->calculateProgress(), 1);
        }

        /* ===========================
        | WORKFORCE ALLOCATION
        =========================== */
        $workforceLabels = [];
        $workforceValues = [];

        foreach ($progressProjects as $project) {
            $workforceLabels[] = $project->project_name;
            $workforceValues[] = $project->users()->count();
        }

        $averageProgress = count($progressValues)
            ? round(array_sum($progressValues) / count($progressValues), 1)
            : 0;

        return view('dashboard.manager', [
            'activeProjects'    => $projects['active'],
            'atRiskProjects'    => $projects['at_risk'],
            'completedProjects' => $projects['completed'],
            'fieldWorkers'      => $workforce['field_workers'],

            'progressLabels'    => $progressLabels,
            'progressValues'    => $progressValues,
            'workforceLabels'   => $workforceLabels,
            'workforceValues'   => $workforceValues,
            'averageProgress'   => $averageProgress,
        ]);
    }

    /* ============================================================
 * EMPLOYEE DASHBOARD (CLEANED)
 * ============================================================ */
    public function employeeDashboard()
    {
        $user = auth()->user();

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
        // Logged-in user only (employee, manager, accounting)
        $user = auth()->user();
        $this->authorize('view', $user);

        return view('employee.profile-settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // BASIC INFO VALIDATION
        $request->validate([
            'given_name' => 'required|string|max:255',
            'surname'    => 'required|string|max:255',
            'email'      => 'required|email|max:255',
        ]);

        $user->update([
            'given_name' => $request->given_name,
            'surname'    => $request->surname,
            'email'      => $request->email,
        ]);

        // ================= PASSWORD UPDATE (OPTIONAL) =================
        if ($request->filled('new_password')) {

            $request->validate([
                'current_password' => 'required',
                'new_password'     => 'min:8',
                'confirm_password' => 'same:new_password',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Current password is incorrect.',
                ]);
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');

    }


    /* ============================================================
    * MY ATTENDANCE PAGE (EMPLOYEE)
    * ============================================================ */
    public function attendance()
    {
        $user = auth()->user();

        // Load logs sorted by most recent
        $logs = \App\Models\Attendance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance.index', compact('user', 'logs'));
    }

    public function myQR()
    {
        $user = auth()->user();

        return view('attendance.my-qr', compact('user'));
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
            ->with(['project.client', 'addedBy'])
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

    //public function syncAll()
    //{
    //    $pendingClients = \App\Models\Client::where('sync_status', 'pending')->get();
    //    $syncedCount = 0;

    //    foreach ($pendingClients as $client) {
    //        try {
    //            \DB::connection('cloud')->table('clients')->updateOrInsert(
    //    ['email' => $client->email],
    //            [
    //               'name' => $client->name,
    //                'contact_person' => $client->contact_person,
    //                'phone' => $client->phone,
    //                'address' => $client->address,
    //                'created_at' => $client->created_at,
    //                'updated_at' => now(),
    //            ]
    //        );

    //            $client->sync_status = 'synced';
    //            $client->save();
    //           $syncedCount++;
    //        } catch (\Exception $e) {
    //            \Log::error("Client Sync Fail: " . $e->getMessage());
    //        }
    //        }

    //      return response()->json([
    //            'status' => 'success',

    //        ]);

//}
}
