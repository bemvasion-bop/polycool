<?php

namespace App\Providers;


use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\AttendanceLog;
use App\Policies\PaymentPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\ExpensePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Project::class      => ProjectPolicy::class,
        Quotation::class    => QuotationPolicy::class,
        AttendanceLog::class => AttendancePolicy::class,
        Payment::class => PaymentPolicy::class,
        Expense::class => ExpensePolicy::class,
        User::class => UserPolicy::class,


    ];

    public function boot(): void
    {
        $this->registerPolicies();

        /*
        |--------------------------------------------------------------------------
        | GLOBAL GATES
        |--------------------------------------------------------------------------
        */

        // QR Scanner Access — ONLY OWNER + MANAGER
        Gate::define('use-scanner', function ($user) {
            return in_array($user->system_role, ['owner', 'manager']);
        });
    }
}
