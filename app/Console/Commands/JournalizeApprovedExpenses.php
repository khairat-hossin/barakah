<?php

namespace App\Console\Commands;

use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\JournalVoucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JournalizeApprovedExpenses extends Command
{
    protected $signature = 'accounting:journalize-expenses {--dry-run}';
    protected $description = 'Journalize all approved expenses that haven\'t been journalized yet';

    private array $expenseAccountMap = [
        'Office Supplies' => 5200,
        'Meeting Expenses' => 5100,
        'Bank Charges' => 5300,
        'Office Expenses' => 5200,
    ];

    public function handle()
    {
        DB::beginTransaction();

        try {
            $this->info('Finding approved expenses without journal vouchers...');

            // Get all approved expenses
            $approvedExpenses = Expense::where('status', 'approved')->get();

            $count = 0;
            foreach ($approvedExpenses as $expense) {
                // Check if already journalized
                if ($this->isAlreadyJournalized($expense)) {
                    $this->line("  ⊘ {$expense->expense_number} - Already journalized");
                    continue;
                }

                $this->journalizeExpense($expense);
                $this->line("  ✓ {$expense->expense_number} - Journalized");
                $count++;
            }

            $this->info("Created $count journal vouchers for approved expenses");

            if ($this->option('dry-run')) {
                $this->warn('DRY RUN: Rolling back all changes');
                DB::rollBack();
            } else {
                DB::commit();
                $this->info('✓ All approved expenses journalized successfully!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function journalizeExpense(Expense $expense): void
    {
        $bankAccount = ChartOfAccount::where('code', '1200')->first();
        $expenseAccountCode = $this->expenseAccountMap[$expense->category->name] ?? 5400;
        $expenseAccount = ChartOfAccount::where('code', (string)$expenseAccountCode)->first();

        if (!$bankAccount || !$expenseAccount) {
            throw new \Exception("Required GL accounts not found for expense {$expense->expense_number}");
        }

        $voucher = JournalVoucher::create([
            'voucher_number' => $this->getVoucherNumber(),
            'voucher_date' => $expense->expense_date,
            'description' => "{$expense->category->name} - {$expense->title} ({$expense->expense_number})",
            'status' => 'posted',
            'created_by' => $expense->approved_by ?? 1,
        ]);

        // Dr. Expense
        JournalEntry::create([
            'voucher_id' => $voucher->id,
            'account_id' => $expenseAccount->id,
            'debit_amount' => $expense->amount,
            'credit_amount' => 0,
        ]);

        // Cr. Bank
        JournalEntry::create([
            'voucher_id' => $voucher->id,
            'account_id' => $bankAccount->id,
            'debit_amount' => 0,
            'credit_amount' => $expense->amount,
        ]);
    }

    private function getVoucherNumber(): string
    {
        $year = now()->year;
        $prefix = 'JV-' . $year . '-';

        $lastVoucher = JournalVoucher::where('voucher_number', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(voucher_number, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $sequence = 1;
        if ($lastVoucher) {
            $lastSequence = (int)substr($lastVoucher->voucher_number, strlen($prefix));
            $sequence = $lastSequence + 1;
        }

        $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);

        while (JournalVoucher::where('voucher_number', $voucher_number)->exists()) {
            $sequence++;
            $voucher_number = $prefix . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        }

        return $voucher_number;
    }

    private function isAlreadyJournalized(Expense $expense): bool
    {
        return JournalVoucher::where('description', 'like', "%{$expense->expense_number}%")
            ->exists();
    }
}
