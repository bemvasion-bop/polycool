<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\{
    WeatherController,
    AttendanceController,
    ClientController,
    DashboardController,
    EmployeeController,
    QuotationController,
    ProjectController,
    PaymentController,
    ExpenseController,
    MaterialController,
    SupplierController,
    PayrollController,
    CashAdvanceController
};

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Auth::routes();
Route::get('/', fn () => redirect()->route('login'))->name('home');

/*
|--------------------------------------------------------------------------
| UNIVERSAL DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $role = auth()->user()->system_role;

    return match ($role) {
        'owner'      => redirect()->route('owner.dashboard'),
        'manager'    => redirect()->route('manager.dashboard'),
        'employee'   => redirect()->route('employee.dashboard'),
        'accounting' => redirect()->route('accounting.dashboard'),
        'audit'      => redirect()->route('audit.dashboard'),
        default      => abort(403),
    };
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| PROJECTS — SHARED (OWNER + MANAGER)
|--------------------------------------------------------------------------
| One set of routes so we don't conflict on /projects.
| Owner has full power via policies; manager is limited by ProjectPolicy.
*/
Route::middleware(['auth', 'role:owner,manager'])->group(function () {
    Route::resource('projects', ProjectController::class);
});


/*
|--------------------------------------------------------------------------
| OWNER — FULL ACCESS (except shared finance below)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->group(function () {

     Route::get('/owner/dashboard', [DashboardController::class, 'ownerDashboard'])
        ->name('owner.dashboard');

    Route::resource('employees', EmployeeController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('quotations', QuotationController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('materials', MaterialController::class);

    // Expenses
    Route::resource('expenses', ExpenseController::class)->only(['edit', 'update', 'destroy']);

    // Quotation actions
    Route::get('/quotations/{q}/pdf', [QuotationController::class, 'downloadPdf'])
        ->name('quotations.pdf');

    Route::post('/quotations/{q}/approve', [QuotationController::class, 'approve'])
        ->name('quotations.approve');

    Route::post('/quotations/{q}/decline', [QuotationController::class, 'decline'])
        ->name('quotations.decline');

    Route::post('/quotations/{q}/convert-to-project', [QuotationController::class, 'convertToProject'])
        ->name('quotations.convert-to-project');

    // Attendance manual override
    Route::post('/attendance/{user}/time-in',  [AttendanceController::class, 'manualTimeIn'])
        ->name('attendance.manualIn');

    Route::post('/attendance/{user}/time-out', [AttendanceController::class, 'manualTimeOut'])
        ->name('attendance.manualOut');

    Route::get('/attendance/{log}/edit', [AttendanceController::class, 'edit'])
        ->name('attendance.edit');

    Route::put('/attendance/{log}', [AttendanceController::class, 'update'])
        ->name('attendance.update');
});


/*
|--------------------------------------------------------------------------
| MANAGER — ATTENDANCE + CREATE EXPENSE
|--------------------------------------------------------------------------
| (Projects now come from the shared group above.)
*/
Route::middleware(['auth', 'role:manager'])->group(function () {

    Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])
        ->name('manager.dashboard');

    // Attendance manager + QR
    Route::get('/attendance/manage', [AttendanceController::class, 'manage'])
        ->name('attendance.manage');

    // Manager can CREATE expenses (owner will approve)
    Route::resource('expenses', ExpenseController::class)->only(['create', 'store']);
});


/*
|--------------------------------------------------------------------------
| EMPLOYEE — SELF SERVICE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:employee'])->group(function () {

    Route::get('/employee/dashboard', [DashboardController::class, 'employeeDashboard'])
        ->name('employee.dashboard');

    // Attendance (self)
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::get('/attendance/daily', [AttendanceController::class, 'daily'])
        ->name('attendance.daily');

    Route::post('/attendance/time-in/{project}', [AttendanceController::class, 'employeeTimeIn'])
        ->name('attendance.timein');

    Route::post('/attendance/time-out/{project}', [AttendanceController::class, 'employeeTimeOut'])
        ->name('attendance.timeout');

    Route::get('/my-qr', [AttendanceController::class, 'myQR'])
        ->name('attendance.myQR');

    // Project view only (policy makes sure it's assigned)
    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->middleware('can:view-project,project')
        ->name('projects.show.employee');

    // Cash advance requests (employee)
    Route::get('/cashadvance/request', [CashAdvanceController::class, 'requestForm'])
        ->name('cashadvance.requestForm');

    Route::post('/cashadvance/request', [CashAdvanceController::class, 'submitRequest'])
        ->name('cashadvance.submit');

    Route::get('/cashadvance/my-requests', [CashAdvanceController::class, 'myRequests'])
        ->name('cashadvance.myRequests');
});


/*
|--------------------------------------------------------------------------
| OWNER + MANAGER — SHARED (ATTENDANCE VIEWS, EXTRA WORK, EXPENSE CREATE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,manager'])->group(function () {

    // Shared attendance overview
    Route::get('/attendance/manage', [AttendanceController::class, 'manage'])
        ->name('attendance.manage');

    Route::get('/attendance/employee/{user}', [AttendanceController::class, 'employeeLogs'])
        ->name('attendance.employee');

    Route::get('/attendance/project/{project}', [AttendanceController::class, 'projectLogs'])
        ->name('attendance.project');

    Route::get('/attendance/qr-generator', [AttendanceController::class, 'qrGenerator'])
        ->name('attendance.qrGenerator');

    // Extra work on projects
    Route::post('/projects/{project}/extra-work', [ProjectController::class, 'storeExtraWork'])
        ->name('projects.extra-work.store');

    Route::delete(
        '/projects/{project}/extra-work/{extraWork}',
        [ProjectController::class, 'destroyExtraWork']
    )->name('projects.extra-work.destroy');

    // (Manager + Owner can create expenses – create/store already defined above in manager group
    // and owner can still hit those routes because of this combined middleware)
    Route::resource('expenses', ExpenseController::class)->only(['create', 'store']);
    Route::resource('projects', ProjectController::class);

    // Attendance manager + QR
    Route::get('/attendance/manage', [AttendanceController::class, 'manage'])
        ->name('attendance.manage');

    Route::get('/attendance/scanner', function () {
        $projects = \App\Models\Project::orderBy('project_name')->get();
        return view('attendance.scanner', compact('projects'));
    })->name('attendance.scanner');
});


/*
|--------------------------------------------------------------------------
| ACCOUNTING — DASHBOARD ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:accounting'])->group(function () {
    Route::get('/accounting/dashboard', [DashboardController::class, 'accountingDashboard'])
        ->name('accounting.dashboard');
});


/*
|--------------------------------------------------------------------------
| OWNER + ACCOUNTING — SHARED FINANCE (PAYMENTS, EXPENSES, CASH ADV, PAYROLL)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,accounting'])->group(function () {

   // PAYMENTS
    Route::resource('payments', PaymentController::class)->only([
        'index', 'show', 'create', 'store',
    ]);

    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])
        ->name('payments.approve');

    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])
        ->name('payments.reject');

    // EXPENSES (shared)
    Route::resource('expenses', ExpenseController::class)->only(['index', 'show']);

    // CASH ADVANCE
    Route::get('/cashadvance', [CashAdvanceController::class, 'index'])
        ->name('cashadvance.index');

    Route::post('/cashadvance/{advance}/approve', [CashAdvanceController::class, 'approve'])
        ->name('cashadvance.approve');

    Route::post('/cashadvance/{advance}/reject', [CashAdvanceController::class, 'reject'])
        ->name('cashadvance.reject');

    // PAYROLL
    Route::resource('payroll', PayrollController::class)->only(['index', 'create']);

    // Cancel a payment
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])
        ->name('payments.cancel');

    // Show the reissue form
    Route::get('/payments/{payment}/reissue', [PaymentController::class, 'reissueForm'])
        ->name('payments.reissueForm');

    // Submit the reissued corrected payment
    Route::post('/payments/{payment}/reissue', [PaymentController::class, 'reissue'])
        ->name('payments.reissue');
});


/*
|--------------------------------------------------------------------------
| AUDIT — READ ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:audit'])->group(function () {
    Route::get('/audit/dashboard', [DashboardController::class, 'auditDashboard'])
        ->name('audit.dashboard');
});


/*
|--------------------------------------------------------------------------
| PUBLIC QR SCAN ENDPOINT (no role check, just CSRF)
|--------------------------------------------------------------------------
*/
Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
    ->name('attendance.scan');
