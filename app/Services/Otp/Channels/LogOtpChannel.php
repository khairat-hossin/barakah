<?php

namespace App\Services\Otp\Channels;

use App\Models\LoginOtpChallenge;
use App\Models\User;
use App\Services\Otp\Contracts\OtpChannel;
use Illuminate\Support\Facades\Log;

/**
 * Development channel: does not deliver anything for real. It records the
 * user's email as the (masked) destination and logs the code so it can be
 * read from the log during testing. In development the code is the fixed
 * config('auth_otp.default_otp'), so no delivery is actually required.
 *
 * To go live, create e.g. MailOtpChannel / SmsOtpChannel implementing
 * OtpChannel and point config('auth_otp.channel') at it.
 */
class LogOtpChannel implements OtpChannel
{
    public function send(User $user, LoginOtpChallenge $challenge, string $plainCode): void
    {
        $challenge->forceFill([
            'channel' => 'log',
            'destination' => $user->email,
        ])->save();

        Log::debug('[auth_otp] OTP issued', [
            'user_id' => $user->id,
            'destination' => $user->email,
            'code' => $plainCode, // dev only — real channels must not log the code
        ]);
    }
}
