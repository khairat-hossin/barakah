<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Holds a password-authenticated user at the OTP step: until the login 2FA
 * challenge is verified for this session, every protected route redirects to
 * the verification page. Only the OTP routes and logout are allowed through.
 *
 * Appended to the "web" group, so it guards the whole app without needing to
 * be wired onto each route individually.
 */
class EnsureOtpVerified
{
    /** Route names reachable while an OTP challenge is still pending. */
    private const ALLOWED = [
        'otp.show',
        'otp.verify',
        'otp.resend',
        'tyro-login.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('auth_otp.enabled', true) || ! Auth::check()) {
            return $next($request);
        }

        // Session already cleared the 2FA gate.
        if ($request->session()->get('otp.verified')) {
            return $next($request);
        }

        // Let the OTP flow itself (and logout) proceed.
        if ($request->routeIs(self::ALLOWED)) {
            return $next($request);
        }

        return redirect()->route('otp.show');
    }
}
