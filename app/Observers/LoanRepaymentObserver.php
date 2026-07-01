<?php

namespace App\Observers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalVoucher;
use App\Models\LoanRepayment;
use Illuminate\Support\Facades\DB;

class LoanRepaymentObserver
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
     * Post a repayment voucher: Dr Bank (1200) / Cr Loans to Members (1500).
     */
    public function created(LoanRepayment $repayment): void
    {
        if ($this->isAlreadyJournalized($repayment)) {
            return;
        }

        DB::transaction(function () use ($repayment) {
            $loansAccount = ChartOfAccount::where('code', '1500')->first();
            $bankAccount = ChartOfAccount::where('code', '1200')->first();

            if (! $loansAccount || ! $bankAccount) {
                return;
            }

            $repayment->loadMissing('loan.member');
            $loan = $repayment->loan;

            $voucher = JournalVoucher::create([
                'voucher_number' => $this->getVoucherNumber(),
                'voucher_date' => $repayment->repaid_date,
                'description' => "Loan repayment from {$loan?->member?->name} - Ref: {$loan?->loan_code} - RPY:{$repayment->id}",
                'status' => 'posted',
                'created_by' => $repayment->recorded_by ?? 1,
            ]);

            // Dr. Bank
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $bankAccount->id,
                'debit_amount' => $repayment->amount,
                'credit_amount' => 0,
            ]);

            // Cr. Loans to Members
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $loansAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $repayment->amount,
            ]);
        });
    }

    private function isAlreadyJournalized(LoanRepayment $repayment): bool
    {
        return JournalVoucher::where('description', 'like', "%RPY:{$repayment->id}%")
            ->exists();
    }
}
