<?php

namespace App\Console\Commands;

use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\JournalVoucher;
use App\Models\SavingsEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JournalizeTransactions extends Command
{
    protected $signature = 'accounting:journalize {--dry-run}';
    protected $description = 'Create journal vouchers for existing deposits and expenses';

    public function handle()
    {
        DB::beginTransaction();

        try {
            $this->info('Starting journalization of deposits and expenses...');

            $bankAccount = ChartOfAccount::where('code', '1200')->firstOrFail();
            $memberDepositAccount = ChartOfAccount::where('code', '2100')->firstOrFail();

            // Journalize Deposits (SavingsEntry)
            $this->info(PHP_EOL . 'Journalizing Deposits:');
            $depositCount = $this->journalizeSavingsEntries($bankAccount, $memberDepositAccount);
            $this->info("Created $depositCount journal vouchers for deposits");

            // Journalize Expenses
            $this->info(PHP_EOL . 'Journalizing Expenses:');
            $expenseCount = $this->journalizeExpenses($bankAccount);
            $this->info("Created $expenseCount journal vouchers for expenses");

            if ($this->option('dry-run')) {
                $this->warn('DRY RUN: Rolling back all changes');
                DB::rollBack();
            } else {
                DB::commit();
                $this->info(PHP_EOL . '✓ All transactions journalized successfully!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function journalizeSavingsEntries($bankAccount, $memberDepositAccount)
    {
        $count = 0;
        $entries = SavingsEntry::orderBy('deposit_date')
            ->get();

        foreach ($entries as $entry) {
            $voucher = JournalVoucher::create([
                'voucher_number' => $this->generateVoucherNumber(),
                'voucher_date' => $entry->deposit_date,
                'description' => "Member deposit from {$entry->member->name} - Ref: {$entry->reference}",
                'status' => 'posted',
                'created_by' => 1, // System user
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

            $this->line("  ✓ {$entry->reference} - {$entry->amount}");
            $count++;
        }

        return $count;
    }

    private function journalizeExpenses($bankAccount)
    {
        $count = 0;
        $entries = Expense::where('status', '!=', 'draft')
            ->orderBy('expense_date')
            ->get();

        // Map expense categories to GL accounts
        $expenseAccountMap = [
            'Office Supplies' => 5200,
            'Meeting Expenses' => 5100,
            'Bank Charges' => 5300,
        ];

        foreach ($entries as $entry) {
            $expenseAccountCode = $expenseAccountMap[$entry->category->name] ?? 5400;
            $expenseAccount = ChartOfAccount::where('code', (string)$expenseAccountCode)->firstOrFail();

            $voucher = JournalVoucher::create([
                'voucher_number' => $this->generateVoucherNumber(),
                'voucher_date' => $entry->expense_date,
                'description' => "{$entry->category->name} - {$entry->title}",
                'status' => 'posted',
                'created_by' => 1, // System user
            ]);

            // Dr. Expense
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $expenseAccount->id,
                'debit_amount' => $entry->amount,
                'credit_amount' => 0,
            ]);

            // Cr. Bank
            JournalEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $bankAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $entry->amount,
            ]);

            $this->line("  ✓ {$entry->expense_number} - {$entry->amount}");
            $count++;
        }

        return $count;
    }

    private function generateVoucherNumber()
    {
        $year = now()->year;
        $prefix = 'JV-' . $year . '-';

        // Get the highest sequence number for this year
        $lastVoucher = JournalVoucher::where('voucher_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(voucher_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $sequence = 1;
        if ($lastVoucher) {
            $lastSequence = (int)substr($lastVoucher->voucher_number, strlen($prefix));
            $sequence = $lastSequence + 1;
        }

        $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);

        // Make sure it doesn't exist
        while (JournalVoucher::where('voucher_number', $voucher_number)->exists()) {
            $sequence++;
            $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        }

        return $voucher_number;
    }
}
