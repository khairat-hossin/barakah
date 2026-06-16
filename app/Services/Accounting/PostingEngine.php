<?php

namespace App\Services\Accounting;

use App\Models\AccountingEvent;
use App\Models\JournalVoucher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PostingEngine
{
    private JournalEngine $journalEngine;
    private AccountingEventService $eventService;

    public function __construct(
        JournalEngine $journalEngine,
        AccountingEventService $eventService
    ) {
        $this->journalEngine = $journalEngine;
        $this->eventService = $eventService;
    }

    public function postEvent(
        string $eventCode,
        float $amount,
        $sourceRecordId = null,
        string $sourceModule = null,
        array $additionalData = null,
        User $user = null
    ): ?JournalVoucher {
        return DB::transaction(function () use ($eventCode, $amount, $sourceRecordId, $sourceModule, $additionalData, $user) {
            if (!$user) {
                $user = auth()->user();
            }

            $event = $this->eventService->getEvent($eventCode);

            if (!$event || !$event->is_active) {
                return null;
            }

            $mappings = $event->mappings()->ordered()->get();

            if ($mappings->isEmpty()) {
                return null;
            }

            // Create journal voucher
            $voucher = $this->journalEngine->createVoucher([
                'voucher_date' => now()->toDateString(),
                'voucher_type' => $event->event_type,
                'source_module' => $sourceModule,
                'source_record_id' => $sourceRecordId,
                'description' => $event->event_name,
            ], $user);

            // Add entries based on mappings
            foreach ($mappings as $mapping) {
                $debitAmount = $mapping->debit_multiplier * $amount;
                $creditAmount = $mapping->credit_multiplier * $amount;

                $this->journalEngine->addEntry(
                    $voucher,
                    $mapping->debitAccount,
                    $debitAmount > 0 ? $debitAmount : null,
                    $creditAmount > 0 ? $creditAmount : null,
                    $event->event_name,
                    $mapping->sequence
                );
            }

            // Post the voucher automatically
            $this->journalEngine->postVoucher($voucher, $user);

            return $voucher->fresh();
        });
    }

    public function postDepositApproved(int $depositId, float $amount, User $user = null): ?JournalVoucher
    {
        return $this->postEvent(
            'DEPOSIT_APPROVED',
            $amount,
            $depositId,
            'deposits',
            null,
            $user
        );
    }

    public function postExpenseApproved(int $expenseId, float $amount, User $user = null): ?JournalVoucher
    {
        return $this->postEvent(
            'EXPENSE_APPROVED',
            $amount,
            $expenseId,
            'expenses',
            null,
            $user
        );
    }

    public function postInvestmentCreated(int $investmentId, float $amount, User $user = null): ?JournalVoucher
    {
        return $this->postEvent(
            'INVESTMENT_CREATED',
            $amount,
            $investmentId,
            'investments',
            null,
            $user
        );
    }

    public function postInvestmentProfit(int $investmentId, float $profitAmount, User $user = null): ?JournalVoucher
    {
        return $this->postEvent(
            'INVESTMENT_PROFIT',
            $profitAmount,
            $investmentId,
            'investments',
            null,
            $user
        );
    }

    public function postShareIssued(int $memberId, float $shareAmount, User $user = null): ?JournalVoucher
    {
        return $this->postEvent(
            'SHARE_ISSUED',
            $shareAmount,
            $memberId,
            'shares',
            null,
            $user
        );
    }

    public function canPostEvent(string $eventCode): bool
    {
        $event = $this->eventService->getEvent($eventCode);

        return $event && $event->is_active && !$event->mappings()->isEmpty();
    }

    public function getEventDescription(string $eventCode): ?string
    {
        $event = $this->eventService->getEvent($eventCode);

        return $event ? $event->description : null;
    }
}
