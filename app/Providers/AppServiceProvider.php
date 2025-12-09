<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        view()->composer('*', function ($view) {
            $pendingExpensesCount = \App\Models\Expense::where('status', 'pending')->count();
            $view->with('pendingExpensesCount', $pendingExpensesCount);


        Carbon::setLocale('en_PH');
        date_default_timezone_set('Asia/Manila');


        });
    }


}
