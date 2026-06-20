<?php

namespace App\Console\Commands;

use App\Mail\DepositReminderMail;
use App\Models\Member;
use App\Models\MemberDepositMonth;
use App\Support\Notify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDepositReminders extends Command
{
    protected $signature = 'deposits:send-reminders {--month= : Target month as YYYY-MM (defaults to current)}';

    protected $description = 'Email active members who have not deposited for the month, and notify admins with a summary.';

    public function handle(): int
    {
        $monthDate = $this->option('month')
            ? \Carbon\Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth()
            : now()->startOfMonth();

        $month = $monthDate->month;
        $year = $monthDate->year;
        $label = $monthDate->format('F Y');

        $paidMemberIds = MemberDepositMonth::where('month', $month)
            ->where('year', $year)
            ->pluck('member_id')
            ->all();

        $unpaid = Member::where('status', 'active')
            ->whereNotIn('id', $paidMemberIds)
            ->get();

        $emailed = 0;
        foreach ($unpaid as $member) {
            if (! $member->email) {
                continue;
            }
            Notify::mailQuietly(
                $member->email,
                new DepositReminderMail($member, $label, $member->getCalculatedMonthlyDepositAmount())
            );
            $emailed++;
        }

        if ($unpaid->isNotEmpty()) {
            Notify::admins(
                'Monthly deposit reminders sent',
                $unpaid->count() . ' member(s) have not deposited for ' . $label . '. ' . $emailed . ' reminder email(s) sent.',
                'bell',
                route('deposit-status', ['month' => $monthDate->format('Y-m')]),
            );
        }

        $this->info("Deposit reminders for {$label}: {$unpaid->count()} unpaid, {$emailed} emailed.");

        return self::SUCCESS;
    }
}
