<?php

namespace App\Services\Otp;

use App\Models\LoginOtpChallenge;
use App\Models\User;
use App\Services\Otp\Contracts\OtpChannel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Owns the full lifecycle of login 2FA OTP challenges: creation, delivery
 * (via a swappable channel), verification, resend and cleanup. Controllers
 * should only ever talk to this class, never to the model directly.
 */
class OtpService
{
    public function __construct(private readonly OtpChannel $channel) {}

    public function enabled(): bool
    {
        return (bool) config('auth_otp.enabled', true);
    }

    public function purpose(): string
    {
        return config('auth_otp.purpose', 'login_2fa');
    }

    /**
     * Return the current usable challenge for the user, creating a fresh one
     * if none is active. Used when rendering the verification page.
     */
    public function ensureChallenge(User $user): LoginOtpChallenge
    {
        $active = $this->activeChallenge($user);

        return $active ?? $this->startChallenge($user);
    }

    /** The most recent non-verified, non-expired challenge, if any. */
    public function activeChallenge(User $user): ?LoginOtpChallenge
    {
        $challenge = $this->latestChallenge($user);

        return $challenge && $challenge->isActive() ? $challenge : null;
    }

    /** Create, persist and dispatch a new challenge, superseding older ones. */
    public function startChallenge(User $user): LoginOtpChallenge
    {
        // Supersede any outstanding challenges for this purpose.
        LoginOtpChallenge::query()
            ->where('user_id', $user->id)
            ->where('purpose', $this->purpose())
            ->whereNull('verified_at')
            ->delete();

        $code = $this->generateCode();

        $challenge = LoginOtpChallenge::create([
            'user_id' => $user->id,
            'purpose' => $this->purpose(),
            'otp_code' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('auth_otp.expire_minutes', 5)),
            'attempts' => 0,
        ]);

        $this->channel->send($user, $challenge, $code);

        return $challenge;
    }

    /** Verify a submitted code against the user's active challenge. */
    public function verify(User $user, string $code): OtpResult
    {
        $challenge = $this->latestChallenge($user);

        if (! $challenge || $challenge->isVerified()) {
            return OtpResult::expired();
        }

        if ($challenge->isExpired()) {
            return OtpResult::expired();
        }

        $maxAttempts = (int) config('auth_otp.max_attempts', 5);

        if ($challenge->attempts >= $maxAttempts) {
            return OtpResult::locked();
        }

        if (! Hash::check($code, $challenge->otp_code)) {
            $challenge->increment('attempts');
            $remaining = max(0, $maxAttempts - $challenge->attempts);

            return $remaining === 0 ? OtpResult::locked() : OtpResult::invalid($remaining);
        }

        $challenge->forceFill(['verified_at' => now()])->save();

        return OtpResult::success();
    }

    /** Issue a new code, honouring the resend cooldown. */
    public function resend(User $user): OtpResult
    {
        $cooldown = (int) config('auth_otp.resend_cooldown_seconds', 30);
        $latest = $this->latestChallenge($user);

        if ($latest && $cooldown > 0) {
            $elapsed = $latest->created_at->diffInSeconds(now());
            if ($elapsed < $cooldown) {
                return OtpResult::cooldown($cooldown - (int) $elapsed);
            }
        }

        $this->startChallenge($user);

        return OtpResult::sent('A new verification code has been sent to your registered contact method.');
    }

    /** Remove any challenges for the user once login is finalised. */
    public function clear(User $user): void
    {
        LoginOtpChallenge::query()
            ->where('user_id', $user->id)
            ->where('purpose', $this->purpose())
            ->delete();
    }

    /** Seconds remaining before a resend is allowed (0 = allowed now). */
    public function cooldownRemaining(User $user): int
    {
        $cooldown = (int) config('auth_otp.resend_cooldown_seconds', 30);
        $latest = $this->latestChallenge($user);

        if (! $latest || $cooldown <= 0) {
            return 0;
        }

        $elapsed = (int) $latest->created_at->diffInSeconds(now());

        return max(0, $cooldown - $elapsed);
    }

    private function latestChallenge(User $user): ?LoginOtpChallenge
    {
        return LoginOtpChallenge::query()
            ->where('user_id', $user->id)
            ->where('purpose', $this->purpose())
            ->latest('id')
            ->first();
    }

    /**
     * Development uses the fixed default code so no gateway is required.
     * In production set ADMIN_DEFAULT_OTP=null to generate a random code.
     */
    private function generateCode(): string
    {
        $default = config('auth_otp.default_otp');

        if (! empty($default)) {
            return (string) $default;
        }

        $length = (int) config('auth_otp.length', 6);

        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
