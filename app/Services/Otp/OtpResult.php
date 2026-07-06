<?php

namespace App\Services\Otp;

/**
 * Outcome of an OTP verify/resend operation. Lets the controller stay lean:
 * it just maps ->status to a response.
 */
class OtpResult
{
    public const SUCCESS = 'success';
    public const INVALID = 'invalid';
    public const EXPIRED = 'expired';
    public const LOCKED = 'locked';       // too many attempts
    public const COOLDOWN = 'cooldown';   // resend requested too soon
    public const SENT = 'sent';

    private function __construct(
        public readonly string $status,
        public readonly string $message,
        public readonly int $retryAfter = 0,
    ) {}

    public function ok(): bool
    {
        return in_array($this->status, [self::SUCCESS, self::SENT], true);
    }

    public static function success(): self
    {
        return new self(self::SUCCESS, 'Verification successful.');
    }

    public static function sent(string $message): self
    {
        return new self(self::SENT, $message);
    }

    public static function invalid(int $remaining): self
    {
        $suffix = $remaining > 0 ? " You have {$remaining} attempt(s) left." : '';

        return new self(self::INVALID, 'The verification code is incorrect.'.$suffix);
    }

    public static function expired(): self
    {
        return new self(self::EXPIRED, 'Your verification code has expired. Please resend a new code.');
    }

    public static function locked(): self
    {
        return new self(self::LOCKED, 'Too many incorrect attempts. Please resend a new code.');
    }

    public static function cooldown(int $seconds): self
    {
        return new self(self::COOLDOWN, "Please wait {$seconds}s before requesting a new code.", $seconds);
    }
}
