<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SavingsEntryController;
use App\Http\Controllers\OrganizationProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\ShareTransferController;
use App\Http\Controllers\NomineeController;
use App\Http\Controllers\ExecutiveCommitteeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentTypeController;
use App\Http\Controllers\InvestmentTransactionController;
use App\Http\Controllers\InvestmentDocumentController;
use App\Http\Controllers\InvestmentDashboardController;
use App\Http\Controllers\InvestmentAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('tyro-login.login');
})->name('home');

Route::view('/dashboard', 'dashboard.index')
    ->middleware(['auth', 'can:view dashboard'])
    ->name('dashboard');

Route::middleware(['auth', 'can:view projects'])
    ->prefix('projects')
    ->name('projects.')
    ->group(function (): void {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])
            ->middleware('can:create projects')
            ->name('create');
        Route::post('/', [ProjectController::class, 'store'])
            ->middleware('can:create projects')
            ->name('store');
    });

Route::middleware(['auth', 'can:view members'])
    ->prefix('members')
    ->name('members.')
    ->group(function (): void {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('/api/data', [MemberController::class, 'datatable'])->name('datatable');
        Route::get('/create', [MemberController::class, 'create'])
            ->middleware('can:create members')
            ->name('create');
        Route::get('/{member}', [MemberController::class, 'show'])->name('show');
        Route::post('/', [MemberController::class, 'store'])
            ->middleware('can:create members')
            ->name('store');
        Route::get('/{member}/edit', [MemberController::class, 'edit'])
            ->middleware('can:update members')
            ->name('edit');
        Route::put('/{member}', [MemberController::class, 'update'])
            ->middleware('can:update members')
            ->name('update');
        Route::delete('/{member}', [MemberController::class, 'destroy'])
            ->middleware('can:delete members')
            ->name('destroy');
    });

Route::middleware(['auth', 'can:view deposits'])
    ->prefix('deposits')
    ->name('deposits.')
    ->group(function (): void {
        Route::get('/', [SavingsEntryController::class, 'index'])->name('index');
        Route::get('/api/data', [SavingsEntryController::class, 'datatable'])->name('datatable');
        Route::get('/create', [SavingsEntryController::class, 'create'])
            ->middleware('can:create deposits')
            ->name('create');
        Route::post('/', [SavingsEntryController::class, 'store'])
            ->middleware('can:create deposits')
            ->name('store');
        Route::get('/{savingsEntry}', [SavingsEntryController::class, 'show'])->name('show');
        Route::get('/{savingsEntry}/edit', [SavingsEntryController::class, 'edit'])
            ->middleware('can:create deposits')
            ->name('edit');
        Route::put('/{savingsEntry}', [SavingsEntryController::class, 'update'])
            ->middleware('can:create deposits')
            ->name('update');
        Route::delete('/{savingsEntry}', [SavingsEntryController::class, 'destroy'])
            ->middleware('can:create deposits')
            ->name('destroy');
    });

// Share Management Routes
Route::middleware(['auth', 'can:view shares'])
    ->prefix('shares')
    ->name('shares.')
    ->group(function (): void {
        Route::get('/', [ShareController::class, 'index'])->name('index');
        Route::get('/distribution', [ShareController::class, 'distribution'])
            ->middleware('can:manage shares')
            ->name('distribution');
        Route::put('/member/{member}/shares', [ShareController::class, 'updateMemberShares'])
            ->middleware('can:manage shares')
            ->name('update-member-shares');
        Route::get('/{share}', [ShareController::class, 'show'])->name('show');
    });

// Share Transfer Routes
Route::middleware(['auth', 'can:view share transfers'])
    ->prefix('share-transfers')
    ->name('share-transfers.')
    ->group(function (): void {
        Route::get('/', [ShareTransferController::class, 'index'])->name('index');
        Route::get('/create', [ShareTransferController::class, 'create'])
            ->middleware('can:create share transfers')
            ->name('create');
        Route::post('/', [ShareTransferController::class, 'store'])
            ->middleware('can:create share transfers')
            ->name('store');
        Route::get('/{transfer}', [ShareTransferController::class, 'show'])->name('show');
        Route::get('/{transfer}/approve', [ShareTransferController::class, 'approve'])
            ->middleware('can:approve share transfers')
            ->name('approve');
        Route::put('/{transfer}/approve', [ShareTransferController::class, 'approveStore'])
            ->middleware('can:approve share transfers')
            ->name('approve-store');
        Route::get('/{transfer}/reject', [ShareTransferController::class, 'reject'])
            ->middleware('can:approve share transfers')
            ->name('reject');
        Route::put('/{transfer}/reject', [ShareTransferController::class, 'rejectStore'])
            ->middleware('can:approve share transfers')
            ->name('reject-store');
    });

// Nominee Routes
Route::middleware(['auth', 'can:manage nominees'])
    ->prefix('members/{member}/nominees')
    ->name('nominees.')
    ->group(function (): void {
        Route::get('/', [NomineeController::class, 'index'])->name('index');
        Route::get('/create', [NomineeController::class, 'create'])->name('create');
        Route::post('/', [NomineeController::class, 'store'])->name('store');
        Route::get('/{nominee}/edit', [NomineeController::class, 'edit'])->name('edit');
        Route::put('/{nominee}', [NomineeController::class, 'update'])->name('update');
        Route::delete('/{nominee}', [NomineeController::class, 'destroy'])->name('destroy');
        Route::put('/{nominee}/set-primary', [NomineeController::class, 'setPrimary'])->name('set-primary');
    });

// Executive Committee Routes
Route::middleware(['auth', 'can:manage executive committee'])
    ->prefix('executive-committee')
    ->name('executive-committee.')
    ->group(function (): void {
        Route::get('/', [ExecutiveCommitteeController::class, 'index'])->name('index');
        Route::get('/assign', [ExecutiveCommitteeController::class, 'assign'])->name('assign');
        Route::post('/', [ExecutiveCommitteeController::class, 'store'])->name('store');
        Route::get('/{committee}/edit', [ExecutiveCommitteeController::class, 'edit'])->name('edit');
        Route::put('/{committee}', [ExecutiveCommitteeController::class, 'update'])->name('update');
        Route::put('/{committee}/end-position', [ExecutiveCommitteeController::class, 'endPosition'])->name('end-position');
        Route::delete('/{committee}', [ExecutiveCommitteeController::class, 'destroy'])->name('destroy');
    });

// Document Routes
Route::middleware(['auth', 'can:manage documents'])
    ->prefix('documents')
    ->name('documents.')
    ->group(function (): void {
        Route::get('/member/{member}', [DocumentController::class, 'index'])->name('index');
        Route::get('/member/{member}/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/member/{member}', [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
        Route::put('/{document}/verify', [DocumentController::class, 'verify'])->name('verify');
    });

// Member Profile Routes
Route::middleware(['auth'])
    ->prefix('members/{member}/profile')
    ->name('member-profiles.')
    ->group(function (): void {
        Route::get('/', [MemberProfileController::class, 'show'])->name('show');
        Route::get('/edit', [MemberProfileController::class, 'edit'])->name('edit');
        Route::put('/', [MemberProfileController::class, 'update'])->name('update');
        Route::get('/export-pdf', [MemberProfileController::class, 'exportPdf'])->name('export-pdf');
    });

// Audit Logs Routes
Route::middleware(['auth', 'can:view audit logs'])
    ->prefix('audit-logs')
    ->name('audit-logs.')
    ->group(function (): void {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
    });

Route::middleware(['auth'])
    ->prefix('user-management')
    ->name('user-management.')
    ->group(function (): void {
        Route::prefix('permissions')
            ->name('permissions.')
            ->middleware('can:manage permissions')
            ->group(function (): void {
                Route::get('/', [PermissionController::class, 'index'])->name('index');
                Route::get('/create', [PermissionController::class, 'create'])->name('create');
                Route::post('/', [PermissionController::class, 'store'])->name('store');
                Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
                Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
                Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('roles')
            ->name('roles.')
            ->middleware('can:manage roles')
            ->group(function (): void {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::get('/create', [RoleController::class, 'create'])->name('create');
                Route::post('/', [RoleController::class, 'store'])->name('store');
                Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
                Route::put('/{role}', [RoleController::class, 'update'])->name('update');
                Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('users')
            ->name('users.')
            ->middleware('can:manage users')
            ->group(function (): void {
                Route::get('/', [UserManagementController::class, 'index'])->name('index');
                Route::get('/create', [UserManagementController::class, 'create'])->name('create');
                Route::post('/', [UserManagementController::class, 'store'])->name('store');
                Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            });
    });

// Organization Profile Routes
Route::middleware(['auth', 'can:manage organization profile'])
    ->prefix('organization-profile')
    ->name('organization-profile.')
    ->group(function (): void {
        Route::get('/', [OrganizationProfileController::class, 'index'])->name('index');
        Route::get('/create', [OrganizationProfileController::class, 'create'])->name('create');
        Route::post('/', [OrganizationProfileController::class, 'store'])->name('store');
        Route::get('/{organizationProfile}', [OrganizationProfileController::class, 'show'])->name('show');
        Route::get('/{organizationProfile}/edit', [OrganizationProfileController::class, 'edit'])->name('edit');
        Route::put('/{organizationProfile}', [OrganizationProfileController::class, 'update'])->name('update');
        Route::delete('/{organizationProfile}', [OrganizationProfileController::class, 'destroy'])->name('destroy');
        Route::get('/{organizationProfile}/audit-logs', [OrganizationProfileController::class, 'auditLogs'])->name('audit-logs');
        Route::patch('/{organizationProfile}/section/{section}', [OrganizationProfileController::class, 'updateSection'])->name('update-section');
    });

// Expense Category Routes (Admin)
Route::middleware(['auth', 'can:manage expenses'])
    ->prefix('expense-categories')
    ->name('expense-categories.')
    ->group(function (): void {
        Route::get('/', [ExpenseCategoryController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseCategoryController::class, 'create'])->name('create');
        Route::post('/', [ExpenseCategoryController::class, 'store'])->name('store');
        Route::get('/{expenseCategory}/edit', [ExpenseCategoryController::class, 'edit'])->name('edit');
        Route::put('/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('update');
        Route::delete('/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
    });

// Expense Routes
Route::middleware(['auth', 'can:view expenses'])
    ->prefix('expenses')
    ->name('expenses.')
    ->group(function (): void {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/api/data', [ExpenseController::class, 'datatable'])->name('datatable');
        Route::get('/create', [ExpenseController::class, 'create'])
            ->middleware('can:create expenses')
            ->name('create');
        Route::post('/', [ExpenseController::class, 'store'])
            ->middleware('can:create expenses')
            ->name('store');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])
            ->middleware('can:update expenses')
            ->name('edit');
        Route::put('/{expense}', [ExpenseController::class, 'update'])
            ->middleware('can:update expenses')
            ->name('update');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])
            ->middleware('can:delete expenses')
            ->name('destroy');
        Route::get('/{expense}/approve', [ExpenseController::class, 'approve'])
            ->middleware('can:approve expenses')
            ->name('approve');
        Route::put('/{expense}/approve', [ExpenseController::class, 'approveStore'])
            ->middleware('can:approve expenses')
            ->name('approve-store');
        Route::put('/{expense}/paid', [ExpenseController::class, 'markAsPaid'])
            ->middleware('can:manage expenses')
            ->name('mark-paid');
        Route::post('/{expense}/attachments', [ExpenseController::class, 'storeAttachment'])
            ->middleware('can:create expenses')
            ->name('attachment-store');
        Route::get('/attachments/{attachment}/download', [ExpenseController::class, 'downloadAttachment'])
            ->name('attachment-download');
        Route::delete('/attachments/{attachment}', [ExpenseController::class, 'deleteAttachment'])
            ->middleware('can:delete expenses')
            ->name('attachment-delete');
    });

// Investment Type Routes (Admin)
Route::middleware(['auth', 'can:manage investment types'])
    ->prefix('investment-types')
    ->name('investment-types.')
    ->group(function (): void {
        Route::get('/', [InvestmentTypeController::class, 'index'])->name('index');
        Route::get('/create', [InvestmentTypeController::class, 'create'])->name('create');
        Route::post('/', [InvestmentTypeController::class, 'store'])->name('store');
        Route::get('/{investmentType}/edit', [InvestmentTypeController::class, 'edit'])->name('edit');
        Route::put('/{investmentType}', [InvestmentTypeController::class, 'update'])->name('update');
        Route::delete('/{investmentType}', [InvestmentTypeController::class, 'destroy'])->name('destroy');
    });

// Investment Routes
Route::middleware(['auth', 'can:view investments'])
    ->prefix('investments')
    ->name('investments.')
    ->group(function (): void {
        Route::get('/', [InvestmentController::class, 'index'])->name('index');
        Route::get('/api/data', [InvestmentController::class, 'datatable'])->name('datatable');
        Route::get('/dashboard', [InvestmentDashboardController::class, 'show'])
            ->middleware('can:view investment dashboard')
            ->name('dashboard');
        Route::get('/analytics', [InvestmentAnalyticsController::class, 'index'])
            ->middleware('can:view investment analytics')
            ->name('analytics');
        Route::get('/create', [InvestmentController::class, 'create'])
            ->middleware('can:create investments')
            ->name('create');
        Route::post('/', [InvestmentController::class, 'store'])
            ->middleware('can:create investments')
            ->name('store');
        Route::get('/{investment}', [InvestmentController::class, 'show'])->name('show');
        Route::get('/{investment}/edit', [InvestmentController::class, 'edit'])
            ->middleware('can:update investments')
            ->name('edit');
        Route::put('/{investment}', [InvestmentController::class, 'update'])
            ->middleware('can:update investments')
            ->name('update');
        Route::delete('/{investment}', [InvestmentController::class, 'destroy'])
            ->middleware('can:delete investments')
            ->name('destroy');
        Route::post('/{investment}/activate', [InvestmentController::class, 'activate'])
            ->middleware('can:manage investments')
            ->name('activate');
        Route::post('/{investment}/mature', [InvestmentController::class, 'mature'])
            ->middleware('can:manage investments')
            ->name('mature');
        Route::post('/{investment}/suspend', [InvestmentController::class, 'suspend'])
            ->middleware('can:manage investments')
            ->name('suspend');
        Route::post('/{investment}/close', [InvestmentController::class, 'close'])
            ->middleware('can:manage investments')
            ->name('close');
        Route::get('/{investment}/history', [InvestmentController::class, 'statusHistory'])->name('history');
        Route::get('/{investment}/performance', [InvestmentAnalyticsController::class, 'performance'])
            ->middleware('can:view investment analytics')
            ->name('performance');
        Route::post('/{investment}/snapshot', [InvestmentAnalyticsController::class, 'createSnapshot'])
            ->middleware('can:manage investments')
            ->name('snapshot');

        // Transaction Routes
        Route::post('/{investment}/transactions', [InvestmentTransactionController::class, 'store'])
            ->middleware('can:create investment transactions')
            ->name('transactions.store');
        Route::put('/{investment}/transactions/{transaction}/approve', [InvestmentTransactionController::class, 'approve'])
            ->middleware('can:approve investment transactions')
            ->name('transactions.approve');
        Route::put('/{investment}/transactions/{transaction}/reverse', [InvestmentTransactionController::class, 'reverse'])
            ->middleware('can:manage investment transactions')
            ->name('transactions.reverse');

        // Document Routes
        Route::post('/{investment}/documents', [InvestmentDocumentController::class, 'store'])
            ->middleware('can:manage investment documents')
            ->name('documents.store');
        Route::post('/{investment}/documents/{document}/verify', [InvestmentDocumentController::class, 'verify'])
            ->middleware('can:verify investment documents')
            ->name('documents.verify');
        Route::get('/{investment}/documents/{document}/download', [InvestmentDocumentController::class, 'download'])->name('documents.download');
        Route::delete('/{investment}/documents/{document}', [InvestmentDocumentController::class, 'destroy'])
            ->middleware('can:delete investment documents')
            ->name('documents.destroy');
    });
