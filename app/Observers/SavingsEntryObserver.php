<?php

namespace App\Observers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalVoucher;
use App\Models\SavingsEntry;
use Illuminate\Support\Facades\DB;

class SavingsEntryObserver
{
    private function getVoucherNumber(): string
    {
        $year = now()->year;
        $prefix = 'JV-' . $year . '-';

        $lastVoucher = \App\Models\JournalVoucher::where('voucher_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(voucher_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $sequence = 1;
        if ($lastVoucher) {
            $lastSequence = (int)substr($lastVoucher->voucher_number, strlen($prefix));
            $sequence = $lastSequence + 1;
        }

        $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);

        while (\App\Models\JournalVoucher::where('voucher_number', $voucher_number)->exists()) {
            $sequence++;
            $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        }

        return $voucher_number;
    }

    public function created(SavingsEntry $entry): void
    {
        // Only journalize if not already journalized
        if ($this->isAlreadyJournalized($entry)) {
            return;
        }

        DB::transaction(function () use ($entry) {
            $bankAccount = ChartOfAccount::where('code', '1200')->first();
            $memberDepositAccount = ChartOfAccount::where('code', '2100')->first();

            if (!$bankAccount || !$memberDepositAccount) {
                return; // Skip if accounts don't exist
            }

            $voucher = JournalVoucher::create([
                'voucher_number' => $this->getVoucherNumber(),
                'voucher_date' => $entry->deposit_date,
                'description' => "Member deposit from {$entry->member->name} - Ref: {$this->entryReference($entry)}",
                'status' => 'posted',
                'created_by' => $entry->recorded_by ?? 1,
            ]);

            // Dr. Bank
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $bankAccount->id,
                'debit_amount' => $entry->amount,
                'credit_amount' => 0,
            ]);

            // Cr. Member Deposits
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $memberDepositAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $entry->amount,
            ]);
        });
    }

    /**
     * A stable, unique-per-entry reference used in the voucher description and
     * for deduplication. Falls back to the entry id so it is never empty
     * (an empty reference made the dedup query match any voucher via LIKE '%%').
     */
    private function entryReference(SavingsEntry $entry): string
    {
        return $entry->reference
            ?: ($entry->transaction_id ?: 'SE-' . $entry->id);
    }

    private function isAlreadyJournalized(SavingsEntry $entry): bool
    {
        $reference = $this->entryReference($entry);

        return \App\Models\JournalVoucher::where('description', 'like', "%Ref: {$reference}%")
            ->exists();
    }
}
