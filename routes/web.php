<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Laravel UI)
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| Redirect root → login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Universal Dashboard Redirect
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'owner'     => redirect()->route('owner.dashboard'),
            'manager'   => redirect()->route('manager.dashboard'),
            'employee'  => redirect()->route('employee.dashboard'),
            default     => abort(403),
        };
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | OWNER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:owner'])->group(function () {

        Route::get('/owner/dashboard', [DashboardController::class, 'ownerDashboard'])
            ->name('owner.dashboard');

        Route::resource('employees', EmployeeController::class)
            ->parameters(['employees' => 'employee']);

        Route::resource('clients', ClientController::class);

        Route::resource('quotations', QuotationController::class);

        Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])
            ->name('quotations.pdf');

        Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])
            ->name('quotations.approve');

        Route::post('quotations/{quotation}/decline', [QuotationController::class, 'decline'])
            ->name('quotations.decline');

        Route::post('quotations/{quotation}/convert-to-project', [QuotationController::class, 'convertToProject'])
            ->name('quotations.convert-to-project');

    });


    /*
    |--------------------------------------------------------------------------
    | MANAGER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:manager'])->group(function () {

        Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])
            ->name('manager.dashboard');

        Route::get('/attendance', [AttendanceController::class, 'index'])
            ->name('attendance.index');

        Route::post('/attendance', [AttendanceController::class, 'store'])
            ->name('attendance.store');
    });


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:employee'])->group(function () {

        Route::get('/employee/dashboard', [DashboardController::class, 'employeeDashboard'])
            ->name('employee.dashboard');
    });

});
