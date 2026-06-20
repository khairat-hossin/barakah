<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AdminAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class Notify
{
    /** Roles that should receive admin bell notifications. */
    private const ADMIN_ROLES = ['Super Admin', 'Association Admin'];

    /**
     * Send an in-app bell notification to all admins.
     * Never throws — notification failures must not break the triggering action.
     */
    public static function admins(string $title, string $message, string $icon = 'bell', ?string $url = null): void
    {
        try {
            $admins = User::role(self::ADMIN_ROLES)->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new AdminAlert($title, $message, $icon, $url));
            }
        } catch (\Throwable $e) {
            Log::warning('Admin notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Queue an email to a recipient without ever throwing (e.g. if SMTP is
     * misconfigured) — so core actions like recording a deposit never break.
     */
    public static function mailQuietly(?string $email, \Illuminate\Contracts\Mail\Mailable $mailable): void
    {
        if (! $email) {
            return;
        }
        try {
            Mail::to($email)->queue($mailable);
        } catch (\Throwable $e) {
            Log::warning('Email queue failed for ' . $email . ': ' . $e->getMessage());
        }
    }
}
