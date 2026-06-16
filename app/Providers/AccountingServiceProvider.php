<?php

namespace App\Providers;

use App\Services\Accounting\AccountingEventService;
use App\Services\Accounting\ChartOfAccountsService;
use App\Services\Accounting\FinancialStatementsService;
use App\Services\Accounting\GeneralLedgerService;
use App\Services\Accounting\JournalEngine;
use App\Services\Accounting\PostingEngine;
use App\Services\Accounting\TrialBalanceService;
use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChartOfAccountsService::class);
        $this->app->singleton(AccountingEventService::class);
        $this->app->singleton(JournalEngine::class);
        $this->app->singleton(GeneralLedgerService::class);
        $this->app->singleton(TrialBalanceService::class);
        $this->app->singleton(FinancialStatementsService::class);

        $this->app->singleton(PostingEngine::class, function ($app) {
            return new PostingEngine(
                $app->make(JournalEngine::class),
                $app->make(AccountingEventService::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
