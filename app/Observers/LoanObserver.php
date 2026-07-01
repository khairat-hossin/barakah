<?php

namespace App\Observers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalVoucher;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class LoanObserver
{
    private function getVoucherNumber(): string
    {
        $year = now()->year;
        $prefix = 'JV-' . $year . '-';

        $lastVoucher = JournalVoucher::where('voucher_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(voucher_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $sequence = 1;
        if ($lastVoucher) {
            $sequence = (int) substr($lastVoucher->voucher_number, strlen($prefix)) + 1;
        }

        $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);

        while (JournalVoucher::where('voucher_number', $voucher_number)->exists()) {
            $sequence++;
            $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        }

        return $voucher_number;
    }

    /**
     * Post the disbursement voucher when a loan becomes active (approved):
     * Dr Loans to Members (1500) / Cr Bank (1200).
     */
    public function updated(Loan $loan): void
    {
        $originalStatus = $loan->getOriginal('status');

        if ($originalStatus !== 'active' && $loan->status === 'active') {
            $this->journalizeDisbursement($loan);
        }
    }

    private function journalizeDisbursement(Loan $loan): void
    {
        if ($this->isAlreadyJournalized($loan)) {
            return;
        }

        DB::transaction(function () use ($loan) {
            $loansAccount = ChartOfAccount::where('code', '1500')->first();
            $bankAccount = ChartOfAccount::where('code', '1200')->first();

            if (! $loansAccount || ! $bankAccount) {
                return; // Accounts not set up — skip silently, like the other observers.
            }

            $loan->loadMissing('member');

            $voucher = JournalVoucher::create([
                'voucher_number' => $this->getVoucherNumber(),
                'voucher_date' => $loan->taken_date,
                'description' => "Loan disbursed to {$loan->member?->name} - Ref: {$loan->loan_code}",
                'status' => 'posted',
                'created_by' => $loan->approved_by ?? $loan->recorded_by ?? 1,
            ]);

            // Dr. Loans to Members
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $loansAccount->id,
                'debit_amount' => $loan->loan_amount,
                'credit_amount' => 0,
            ]);

            // Cr. Bank
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $bankAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $loan->loan_amount,
            ]);
        });
    }

    private function isAlreadyJournalized(Loan $loan): bool
    {
        return JournalVoucher::where('description', 'like', "%Loan disbursed%Ref: {$loan->loan_code}%")
            ->exists();
    }
}
