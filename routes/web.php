<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\{
    AttendanceController,
    CashAdvanceController,
    ClientController,
    DashboardController,
    EmployeeController,
    ExpenseController,
    MaterialController,
    PaymentController,
    PayrollController,
    ProjectController,
    QuotationController,
    SupplierController,
    WeatherController
};

/*
|--------------------------------------------------------------------------
| 🔐 AUTH ROUTES
|--------------------------------------------------------------------------
*/
Auth::routes();
Route::get('/', fn() => redirect()->route('login'))->name('home');


/*
|--------------------------------------------------------------------------
| 🧭 DASHBOARD REDIRECT BASED ON ROLE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->system_role) {
        'owner'      => redirect()->route('owner.dashboard'),
        'manager'    => redirect()->route('manager.dashboard'),
        'employee'   => redirect()->route('employee.dashboard'),
        'accounting' => redirect()->route('accounting.dashboard'),
        'audit'      => redirect()->route('audit.dashboard'),
        default      => abort(403),
    };
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| 🟣 OFFLINE SYNC API
|--------------------------------------------------------------------------
*/
Route::post('/offline-sync', [App\Http\Controllers\SyncController::class, 'sync'])
    ->middleware('auth')
    ->name('offline.sync');


Route::post('/offline-sync', [\App\Http\Controllers\OfflineSyncController::class, 'process']);


/*
|--------------------------------------------------------------------------
| 👑 OWNER ONLY
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


    // 🔄 SYNC ALL UNSYNCED DATA
    Route::post('/sync-all', [DashboardController::class, 'syncAll'])->name('sync.all');




    // Quotation actions
    Route::get('/quotations/{q}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf');
    Route::post('/quotations/{q}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('/quotations/{q}/decline', [QuotationController::class, 'decline'])->name('quotations.decline');
    Route::post('/quotations/{q}/convert-to-project', [QuotationController::class, 'convertToProject'])
        ->name('quotations.convert-to-project');

    // Attendance overrides
    Route::post('/attendance/{user}/time-in', [AttendanceController::class, 'manualTimeIn'])->name('attendance.manualIn');
    Route::post('/attendance/{user}/time-out', [AttendanceController::class, 'manualTimeOut'])->name('attendance.manualOut');
    Route::get('/attendance/{log}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{log}', [AttendanceController::class, 'update'])->name('attendance.update');


    // Owner can view finance records only
    Route::resource('expenses', ExpenseController::class)->only(['edit', 'update', 'destroy']);
});


/*
|--------------------------------------------------------------------------
| 🧑‍💼 MANAGER ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manager'])->group(function () {

    Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])
        ->name('manager.dashboard');

    // Manager can CREATE & SUBMIT expenses/payments
    Route::resource('expenses', ExpenseController::class)->only(['create', 'store']);
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
});


/*
|--------------------------------------------------------------------------
| 🧑 EMPLOYEE ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee/dashboard', [DashboardController::class, 'employeeDashboard'])
        ->name('employee.dashboard');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/time-in/{project}', [AttendanceController::class, 'employeeTimeIn'])->name('attendance.timein');
    Route::post('/attendance/time-out/{project}', [AttendanceController::class, 'employeeTimeOut'])->name('attendance.timeout');

    Route::get('/attendance/daily', [AttendanceController::class, 'daily'])->name('attendance.daily');
    Route::get('/my-qr', [AttendanceController::class, 'myQR'])->name('attendance.myQR');

    Route::get('/cashadvance/request', [CashAdvanceController::class, 'requestForm'])->name('cashadvance.requestForm');
    Route::post('/cashadvance/request', [CashAdvanceController::class, 'submitRequest'])->name('cashadvance.submit');
    Route::get('/cashadvance/my-requests', [CashAdvanceController::class, 'myRequests'])->name('cashadvance.myRequests');
});


/*
|--------------------------------------------------------------------------
| 🧑‍💼 OWNER + MANAGER SHARED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,manager'])->group(function () {

    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->middleware('can:view-project,project')
        ->name('projects.show.employee');

    Route::resource('projects', ProjectController::class)->except(['show']);

    Route::post('/projects/{project}/extra-work', [ProjectController::class, 'storeExtraWork'])->name('projects.extra-work.store');
    Route::delete('/projects/{project}/extra-work/{extraWork}', [ProjectController::class, 'destroyExtraWork'])
        ->name('projects.extra-work.destroy');

    Route::get('/attendance/scanner', function () {
        $projects = \App\Models\Project::orderBy('project_name')->get();
        return view('attendance.scanner', compact('projects'));
    })->name('attendance.scanner');

    Route::get('/attendance/manage', [AttendanceController::class, 'manage'])
        ->name('attendance.manage');

    Route::get('/attendance/employee/{user}', [AttendanceController::class, 'employeeLogs'])
        ->name('attendance.employee');

    Route::get('/attendance/project/{project}', [AttendanceController::class, 'projectLogs'])
        ->name('attendance.project');

    Route::get('/attendance/qr-generator', [AttendanceController::class, 'qrGenerator'])
        ->name('attendance.qrGenerator');
});


/*
|--------------------------------------------------------------------------
| 🧑‍💼 OWNER + ACCOUNTING (FINANCE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,accounting'])->group(function () {

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
    Route::post('/payments/{payment}/reissue', [PaymentController::class, 'reissue'])->name('payments.reissue');

    // Expenses
    Route::resource('expenses', ExpenseController::class)->only(['index', 'show']);
    Route::post('/expenses/{expense}/cancel', [ExpenseController::class, 'cancel'])->name('expenses.cancel');
    Route::post('/expenses/{expense}/reissue', [ExpenseController::class, 'reissue'])->name('expenses.reissue');

    // Cash Advance
    Route::get('/cashadvance', [CashAdvanceController::class, 'index'])->name('cashadvance.index');
    Route::post('/cashadvance/{advance}/approve', [CashAdvanceController::class, 'approve'])->name('cashadvance.approve');
    Route::post('/cashadvance/{advance}/reject', [CashAdvanceController::class, 'reject'])->name('cashadvance.reject');

    // Payroll
    Route::resource('payroll', PayrollController::class)->only(['index', 'create']);
});


/*
|--------------------------------------------------------------------------
| 🧐 AUDIT READ ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:audit'])->group(function () {
    Route::get('/audit/dashboard', [DashboardController::class, 'auditDashboard'])
        ->name('audit.dashboard');
});


/*
|--------------------------------------------------------------------------
| 📡 PUBLIC QR SCAN
|--------------------------------------------------------------------------
*/
Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
    ->name('attendance.scan');
