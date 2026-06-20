<?php

namespace App\Mail;

use App\Models\SavingsEntry;
use App\Support\PdfRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public SavingsEntry $deposit)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deposit Receipt — Tk ' . number_format($this->deposit->amount, 0),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.deposit-receipt',
            with: [
                'deposit' => $this->deposit,
                'org' => \App\Models\OrganizationProfile::first(),
            ],
        );
    }

    public function attachments(): array
    {
        $this->deposit->loadMissing(['member', 'recorder', 'paymentMethod']);

        $pdf = PdfRenderer::raw('deposits.receipt', [
            'deposit' => $this->deposit,
            'org' => \App\Models\OrganizationProfile::first(),
        ], ['margin_top' => 10, 'margin_bottom' => 10, 'margin_left' => 10, 'margin_right' => 10]);

        $name = 'deposit-receipt-' . ($this->deposit->transaction_id ?: $this->deposit->id) . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf, $name)->withMime('application/pdf'),
        ];
    }
}
