<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces a member-role user with an incomplete profile to finish it before
 * using the rest of the app. Only the profile edit/update/show routes (plus
 * OTP and logout) are reachable until the required fields are filled.
 *
 * Appended to the "web" group, after the OTP gate.
 */
class EnsureMemberProfileComplete
{
    /** Routes reachable while the profile is still incomplete. */
    private const ALLOWED = [
        'member-profiles.edit',
        'member-profiles.update',
        'member-profiles.show',
        'member-profiles.export-pdf',
        'otp.show',
        'otp.verify',
        'otp.resend',
        'tyro-login.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only member-role accounts are subject to the profile-completion gate.
        if (! $user || ! $user->hasRole('Member')) {
            return $next($request);
        }

        $member = $user->member;

        if (! $member || $member->hasCompleteProfile()) {
            return $next($request);
        }

        if ($request->routeIs(self::ALLOWED)) {
            return $next($request);
        }

        // The edit page shows its own "complete your profile" notice, so no flash here.
        return redirect()->route('member-profiles.edit', $member);
    }
}
