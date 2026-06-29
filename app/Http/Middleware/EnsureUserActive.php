<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActive
{
    /**
     * Log out and block any authenticated user whose account is not active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'Your account is not active. Please contact the administrator.';

            $loginUrl = \Illuminate\Support\Facades\Route::has('login')
                ? route('login')
                : (\Illuminate\Support\Facades\Route::has('tyro-login.login') ? route('tyro-login.login') : url('/login'));

            return redirect($loginUrl)->withErrors([
                'login' => $message,
                'email' => $message,
                'username' => $message,
            ]);
        }

        return $next($request);
    }
}
