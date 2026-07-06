<?php

use App\Services\Otp\Channels\LogOtpChannel;

return [

    /*
    |--------------------------------------------------------------------------
    | Login 2FA OTP
    |--------------------------------------------------------------------------
    |
    | Configuration for the second-factor OTP challenge that runs after a
    | successful password login. The OTP layer is delivery-agnostic: the
    | active "channel" decides how the code reaches the user. During
    | development a fixed default code is used and simply logged.
    |
    */

    // Master switch. When false, the OTP step is skipped entirely.
    'enabled' => (bool) env('ADMIN_OTP_ENABLED', true),

    // Fixed development OTP. Leave non-empty to always issue this code
    // (no real gateway needed). Set to null in production to generate a
    // random code that the configured channel must actually deliver.
    'default_otp' => env('ADMIN_DEFAULT_OTP', '123456'),

    // Number of digits in a generated OTP.
    'length' => (int) env('ADMIN_OTP_LENGTH', 6),

    // Minutes a challenge stays valid.
    'expire_minutes' => (int) env('ADMIN_OTP_EXPIRE_MINUTES', 5),

    // Wrong-code attempts allowed before the challenge is locked.
    'max_attempts' => (int) env('ADMIN_OTP_MAX_ATTEMPTS', 5),

    // Seconds a user must wait between resend requests.
    'resend_cooldown_seconds' => (int) env('ADMIN_OTP_RESEND_COOLDOWN_SECONDS', 30),

    // Logical purpose stored on each challenge (kept for future reuse:
    // e.g. "password_reset", "sensitive_action").
    'purpose' => 'login_2fa',

    /*
    | Delivery channel. Swap this class for a real implementation later
    | (MailOtpChannel / SmsOtpChannel / WhatsAppOtpChannel). Any class
    | implementing App\Services\Otp\Contracts\OtpChannel works.
    */
    'channel' => env('ADMIN_OTP_CHANNEL', LogOtpChannel::class),

];
