<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;


use App\Http\Controllers\{
    AttendanceController,
    CashAdvanceController,
    ClientController,
    DashboardController,
    CloudSyncController,
    EmployeeController,
    ExpenseController,
    MaterialController,
    PaymentController,
    PayrollController,
    ProjectController,
    QuotationController,
    SupplierController,
    SyncAllController,
    WeatherController
};



//Route::get('/fix-deploy', function () {
 //   try {
 //       // Generate APP_KEY if not set
 //       Artisan::call('key:generate', ['--show' => true]);

        // Run migrations to create all tables
//        Artisan::call('migrate', ['--force' => true]);

        // If you need default data from seeders, uncomment:
        // Artisan::call('db:seed', ['--force' => true]);

  //      return nl2br("✔ Deployment Fix SUCCESS!\n\nAPP_KEY: " . Artisan::output());
  //  } catch (\Exception $e) {
  //      return "❌ ERROR: " . $e->getMessage();
  //  }
//   });



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

Route::post('/offline-sync', [\App\Http\Controllers\OfflineSyncController::class, 'process'])
    ->middleware('auth')
    ->name('offline.sync');

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
    Route::post('/sync-all', [SyncAllController::class, 'syncAll'])
        ->name('sync.all')
        ->middleware('auth');


    // Quotations
    Route::post('/quotations/{quotation}/approve', [QuotationController::class, 'approve'])
        ->name('quotations.approve');
    Route::post('/quotations/{quotation}/decline', [QuotationController::class, 'decline'])
        ->name('quotations.decline');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToProject'])
        ->name('quotations.convert');


    // Attendance overrides
    Route::post('/attendance/{user}/time-in', [AttendanceController::class, 'manualTimeIn'])->name('attendance.manualIn');
    Route::post('/attendance/{user}/time-out', [AttendanceController::class, 'manualTimeOut'])->name('attendance.manualOut');
    Route::get('/attendance/{log}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{log}', [AttendanceController::class, 'update'])->name('attendance.update');


    Route::post('/expenses/{expense}/cancel', [ExpenseController::class, 'cancel'])
     ->name('expenses.cancel');

     // EXPENSE APPROVAL WORKFLOW
    Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])
        ->name('expenses.approve');

    Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])
        ->name('expenses.reject');
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
    Route::post('/expenses/{expense}/reissue', [ExpenseController::class, 'reissue'])
     ->name('expenses.reissue');

     // Re-Issue Expense (Manager Save)
    Route::post('/expenses/{expense}/reissue-save',
    [ExpenseController::class, 'saveReIssue'])
    ->name('expenses.reissue-save');


    // Adjust Material Quantity
    //Route::post('/expenses/{expense}/adjust-quantity',
    //  [ExpenseController::class, 'adjustMaterialQuantity'])
    //        ->name('expenses.adjust-quantity');



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
        ->middleware('can:view,project')
        ->name('projects.show');

    Route::resource('projects', ProjectController::class)->except(['show']);

    Route::post('/projects/{project}/extra-work', [ProjectController::class, 'storeExtraWork'])->name('projects.extra-work.store');
    Route::delete('/projects/{project}/extra-work/{extraWork}', [ProjectController::class, 'destroyExtraWork'])
        ->name('projects.extra-work.destroy');
    // Project Progress Logs
    Route::post('/projects/{project}/progress', [ProjectController::class, 'storeProgress'])
        ->name('projects.progress.store');


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

    Route::post('/expenses/{id}/adjust-qty',
        [ExpenseController::class, 'adjustQty']
    )->name('expenses.adjustQty');



    // ============================
    // 🔹 EXPENSES ROUTES
    // ============================
    Route::resource('expenses', ExpenseController::class);

    // Approval workflow
    Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])
        ->name('expenses.approve');

    Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])
        ->name('expenses.reject');

    Route::post('/expenses/{expense}/cancel', [ExpenseController::class, 'cancel'])
        ->name('expenses.cancel');

    Route::put('/expenses/{expense}/reissue', [ExpenseController::class, 'reissue'])
        ->name('expenses.reissue');


});


/*
|--------------------------------------------------------------------------
| 🧑‍💼 OWNER + ACCOUNTING (FINANCE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,accounting'])->group(function () {

    /* =======================
       QUOTATIONS
    ======================= */
    Route::post('/quotations/{quotation}/approve', [QuotationController::class, 'approve'])
        ->name('quotations.approve');

    Route::post('/quotations/{quotation}/decline', [QuotationController::class, 'decline'])
        ->name('quotations.decline');


    /* =======================
       PROJECTS
    ======================= */
    Route::post('/projects/{project}/extra-work/{extraWork}/approve',
        [ProjectController::class, 'approveExtraWork'])->name('projects.extra-work.approve');

    Route::post('/projects/{project}/extra-work/{extraWork}/reject',
        [ProjectController::class, 'rejectExtraWork'])->name('projects.extra-work.reject');


    /* =======================
       PAYMENTS
    ======================= */
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

    // Approval Workflow
    Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');


    // Edit Replacement Amount
    Route::post('/payments/{payment}/update', [PaymentController::class, 'update'])
        ->name('payments.update');

    // History & Audit PDF
    Route::get('/payments/{payment}/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{payment}/audit-pdf', [PaymentController::class, 'auditPdf'])->name('payments.auditPdf');
    Route::get('/payments/{payment}/audit/pdf', [PaymentController::class, 'printAudit'])->name('payments.audit.pdf');


    // Project Payment Summary PDF
    Route::get('/projects/{project}/payment-summary/pdf',
        [PaymentController::class, 'printSummary'])->name('payments.summary.pdf');



    /* =======================
       CASH ADVANCE
    ======================= */
    Route::get('/cashadvance', [CashAdvanceController::class, 'index'])->name('cashadvance.index');
    Route::post('/cashadvance/{advance}/approve', [CashAdvanceController::class, 'approve'])->name('cashadvance.approve');
    Route::post('/cashadvance/{advance}/reject', [CashAdvanceController::class, 'reject'])->name('cashadvance.reject');


    /* =======================
       EXPENSES
    ======================= */
    Route::resource('expenses', ExpenseController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::post('/expenses/{expense}/cancel', [ExpenseController::class, 'cancel'])->name('expenses.cancel');
    Route::post('/expenses/{expense}/reissue', [ExpenseController::class, 'reissue'])->name('expenses.reissue');





    /* =======================
       PAYROLL
    ======================= */
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');

    // ⭐ THIS IS THE MISSING ROUTE
    Route::post('/payroll/preview', [PayrollController::class, 'preview'])->name('payroll.preview');

    // Generate payroll and save to DB
    Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');

    // Show payroll run details
    Route::get('/payroll/{run}', [PayrollController::class, 'show'])->name('payroll.show');

    // Finalize payroll
    Route::post('/payroll/{run}/finalize', [PayrollController::class, 'finalize'])->name('payroll.finalize');







});


Route::middleware(['auth', 'role:accounting'])->group(function () {

    // ACCOUNTING DASHBOARD ROUTE
    Route::get('/accounting/dashboard', [DashboardController::class, 'accountingDashboard'])
        ->name('accounting.dashboard');

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
