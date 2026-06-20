<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Email unpaid members a deposit reminder on the 20th of each month at 9am.
Schedule::command('deposits:send-reminders')
    ->monthlyOn(20, '09:00')
    ->withoutOverlapping();
