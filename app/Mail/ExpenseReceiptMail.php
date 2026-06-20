<?php

namespace App\Mail;

use App\Models\Expense;
use App\Support\PdfRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpenseReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Expense $expense)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Expense Voucher — ' . ($this->expense->expense_number ?: ('EXP-' . $this->expense->id)),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.expense-receipt',
            with: [
                'expense' => $this->expense,
                'org' => \App\Models\OrganizationProfile::first(),
            ],
        );
    }

    public function attachments(): array
    {
        $this->expense->loadMissing(['category', 'member', 'creator', 'approver']);

        $pdf = PdfRenderer::raw('expenses.receipt', [
            'expense' => $this->expense,
            'org' => \App\Models\OrganizationProfile::first(),
        ], ['margin_top' => 10, 'margin_bottom' => 10, 'margin_left' => 10, 'margin_right' => 10]);

        $name = 'expense-voucher-' . ($this->expense->expense_number ?: $this->expense->id) . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf, $name)->withMime('application/pdf'),
        ];
    }
}
