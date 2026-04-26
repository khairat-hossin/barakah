<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SavingsEntryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
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
        Route::get('/create', [MemberController::class, 'create'])
            ->middleware('can:create members')
            ->name('create');
        Route::post('/', [MemberController::class, 'store'])
            ->middleware('can:create members')
            ->name('store');
    });

Route::middleware(['auth', 'can:view savings'])
    ->prefix('savings')
    ->name('savings.')
    ->group(function (): void {
        Route::get('/', [SavingsEntryController::class, 'index'])->name('index');
        Route::get('/create', [SavingsEntryController::class, 'create'])
            ->middleware('can:create savings')
            ->name('create');
        Route::post('/', [SavingsEntryController::class, 'store'])
            ->middleware('can:create savings')
            ->name('store');
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
