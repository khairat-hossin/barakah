<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Otp\OtpResult;
use App\Services\Otp\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Second-factor OTP step that runs after a successful password login.
 * The user is already authenticated by the login package but is held here
 * by the EnsureOtpVerified middleware until the code is verified.
 */
class OtpChallengeController extends Controller
{
    public function __construct(private readonly OtpService $otp) {}

    public function show(Request $request): View|RedirectResponse
    {
        // Already verified (or OTP disabled) → nothing to do here.
        if (! $this->otp->enabled() || $request->session()->get('otp.verified')) {
            return redirect()->intended($this->afterLogin());
        }

        $user = $request->user();
        $this->otp->ensureChallenge($user);
        $request->session()->put('otp.pending', true);

        return view('auth.otp-verify', [
            'maskedDestination' => $this->maskEmail($user->email),
            'cooldownRemaining' => $this->otp->cooldownRemaining($user),
            'resendCooldown' => (int) config('auth_otp.resend_cooldown_seconds', 30),
            'otpLength' => (int) config('auth_otp.length', 6),
        ]);
    }

    public function verify(VerifyOtpRequest $request): RedirectResponse
    {
        $user = $request->user();
        $result = $this->otp->verify($user, $request->validated('otp'));

        if ($result->status === OtpResult::SUCCESS) {
            $this->otp->clear($user);

            // Prevent session fixation, then mark this session fully verified.
            $request->session()->regenerate();
            $request->session()->put('otp.verified', true);
            $request->session()->forget('otp.pending');

            return redirect()->intended($this->afterLogin())
                ->with('success', 'You are now signed in.');
        }

        return back()->withErrors(['otp' => $result->message]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $result = $this->otp->resend($request->user());

        return $result->ok()
            ? back()->with('success', $result->message)
            : back()->withErrors(['otp' => $result->message]);
    }

    private function afterLogin(): string
    {
        return config('tyro-login.redirects.after_login', '/dashboard');
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        $visible = mb_substr($name, 0, 2);
        $masked = $visible.str_repeat('*', max(1, mb_strlen($name) - mb_strlen($visible)));

        return $masked.'@'.$domain;
    }
}
