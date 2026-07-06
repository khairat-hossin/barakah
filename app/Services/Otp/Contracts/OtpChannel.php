<?php

namespace App\Services\Otp\Contracts;

use App\Models\LoginOtpChallenge;
use App\Models\User;

/**
 * Delivery mechanism for a login OTP.
 *
 * Implement this to plug in a real gateway later (email, SMS, WhatsApp).
 * The implementation receives the freshly generated plain code once — it
 * is never persisted in plain text — and is responsible for delivering it
 * and recording the channel/destination back on the challenge.
 */
interface OtpChannel
{
    public function send(User $user, LoginOtpChallenge $challenge, string $plainCode): void;
}
