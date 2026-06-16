<?php

use App\Http\Controllers\Accounting\AccountingReportsController;
use App\Http\Controllers\Accounting\ChartOfAccountsController;
use App\Http\Controllers\Accounting\JournalVouchersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Chart of Accounts Routes
    Route::prefix('chart-of-accounts')
        ->name('chart-of-accounts.')
        ->controller(ChartOfAccountsController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tree', 'tree')->name('tree');
            Route::get('/by-type/{type}', 'byType')->name('by-type');
            Route::post('/', 'store')->name('store');
            Route::get('/create', 'create')->name('create');
            Route::get('/{chartOfAccount}', 'show')->name('show');
            Route::get('/{chartOfAccount}/edit', 'edit')->name('edit');
            Route::put('/{chartOfAccount}', 'update')->name('update');
            Route::delete('/{chartOfAccount}', 'destroy')->name('destroy');
            Route::get('/{chartOfAccount}/balance', 'getBalance')->name('get-balance');
            Route::post('/{chartOfAccount}/activate', 'activate')->name('activate');
            Route::post('/{chartOfAccount}/deactivate', 'deactivate')->name('deactivate');
        });

    // Journal Vouchers Routes
    Route::prefix('journal-vouchers')
        ->name('journal-vouchers.')
        ->controller(JournalVouchersController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/create', 'create')->name('create');
            Route::post('/validate', 'validate')->name('validate');
            Route::get('/{journalVoucher}', 'show')->name('show');
            Route::get('/{journalVoucher}/edit', 'edit')->name('edit');
            Route::put('/{journalVoucher}', 'update')->name('update');
            Route::delete('/{journalVoucher}', 'destroy')->name('destroy');
            Route::post('/{journalVoucher}/post', 'post')->name('post');
            Route::post('/{journalVoucher}/reverse', 'reverse')->name('reverse');
        });

    // Accounting Reports Routes
    Route::prefix('reports')
        ->name('reports.')
        ->controller(AccountingReportsController::class)
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/general-ledger', 'generalLedger')->name('general-ledger');
            Route::get('/general-ledger/export', 'generalLedgerExport')->name('general-ledger-export');
            Route::get('/trial-balance', 'trialBalance')->name('trial-balance');
            Route::get('/trial-balance/export', 'trialBalanceExport')->name('trial-balance-export');
            Route::get('/income-statement', 'incomeStatement')->name('income-statement');
            Route::get('/income-statement/export', 'incomeStatementExport')->name('income-statement-export');
            Route::get('/balance-sheet', 'balanceSheet')->name('balance-sheet');
            Route::get('/balance-sheet/export', 'balanceSheetExport')->name('balance-sheet-export');
            Route::get('/cash-flow', 'cashFlow')->name('cash-flow');
            Route::get('/fund-position', 'fundPosition')->name('fund-position');
            Route::get('/account-analysis', 'accountAnalysis')->name('account-analysis');
        });
});
