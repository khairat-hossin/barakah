<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public string $monthLabel,
        public float $expectedAmount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deposit Reminder — ' . $this->monthLabel,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.deposit-reminder',
            with: [
                'member' => $this->member,
                'monthLabel' => $this->monthLabel,
                'expectedAmount' => $this->expectedAmount,
                'org' => \App\Models\OrganizationProfile::first(),
            ],
        );
    }
}
