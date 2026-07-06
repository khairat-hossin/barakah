<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ensure.organization.setup' => \App\Http\Middleware\EnsureOrganizationSetup::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        // Block inactive/disabled users on every authenticated web request.
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureUserActive::class);
        // Hold users at the OTP step until login 2FA is verified for the session.
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureOtpVerified::class);
        // Force member-role users to complete their profile before using the app.
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureMemberProfileComplete::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
