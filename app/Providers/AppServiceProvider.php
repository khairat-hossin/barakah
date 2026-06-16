<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        Gate::before(static function ($user, string $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Register model observers
        \App\Models\Investment::observe(\App\Observers\InvestmentObserver::class);
        \App\Models\InvestmentTransaction::observe(\App\Observers\InvestmentTransactionObserver::class);

        // Register policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Investment::class, \App\Policies\InvestmentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\InvestmentTransaction::class, \App\Policies\InvestmentTransactionPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\InvestmentDocument::class, \App\Policies\InvestmentDocumentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ChartOfAccount::class, \App\Policies\ChartOfAccountPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\JournalVoucher::class, \App\Policies\JournalVoucherPolicy::class);
    }
}
