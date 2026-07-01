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
        \App\Models\SavingsEntry::observe(\App\Observers\SavingsEntryObserver::class);
        \App\Models\Expense::observe(\App\Observers\ExpenseObserver::class);
        \App\Models\Loan::observe(\App\Observers\LoanObserver::class);
        \App\Models\LoanRepayment::observe(\App\Observers\LoanRepaymentObserver::class);

        // Register policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\SavingsEntry::class, \App\Policies\SavingsEntryPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Expense::class, \App\Policies\ExpensePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Loan::class, \App\Policies\LoanPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Investment::class, \App\Policies\InvestmentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\InvestmentTransaction::class, \App\Policies\InvestmentTransactionPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\InvestmentDocument::class, \App\Policies\InvestmentDocumentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ChartOfAccount::class, \App\Policies\ChartOfAccountPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\JournalVoucher::class, \App\Policies\JournalVoucherPolicy::class);
    }
}
