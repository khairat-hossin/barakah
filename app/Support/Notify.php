<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AdminAlert;
use Illuminate\Support\Facades\Notification;

class Notify
{
    /** Roles that should receive admin bell notifications. */
    private const ADMIN_ROLES = ['Super Admin', 'Association Admin'];

    /**
     * Send an in-app bell notification to all admins.
     */
    public static function admins(string $title, string $message, string $icon = 'bell', ?string $url = null): void
    {
        $admins = User::role(self::ADMIN_ROLES)->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminAlert($title, $message, $icon, $url));
        }
    }
}
