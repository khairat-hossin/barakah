<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Support\Collection;

class GeneralLedgerService
{
    public function getAccountLedger(ChartOfAccount $account, $fromDate = null, $toDate = null): Collection
    {
        $query = $account->journalEntries()
            ->with(['voucher', 'account'])
            ->whereHas('voucher', function ($q) {
                $q->where('status', 'POSTED');
            });

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $entries = $query->orderBy('created_at', 'asc')->get();

        return $this->calculateRunningBalances($entries, $account);
    }

    public function getLedgerWithDetails(ChartOfAccount $account, $fromDate = null, $toDate = null): array
    {
        $entries = $this->getAccountLedger($account, $fromDate, $toDate);

        return [
            'account' => [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->account_type,
                'normal_balance' => $account->normal_balance,
            ],
            'opening_balance' => $this->getOpeningBalance($account, $fromDate),
            'entries' => $entries,
            'closing_balance' => $entries->isEmpty() ? 0 : $entries->last()['balance'],
            'total_debits' => $entries->sum('debit_amount'),
            'total_credits' => $entries->sum('credit_amount'),
        ];
    }

    private function calculateRunningBalances(Collection $entries, ChartOfAccount $account): Collection
    {
        $balance = $this->getOpeningBalance($account, null);

        return $entries->map(function ($entry) use ($account, &$balance) {
            $debit = $entry->debit_amount ?? 0;
            $credit = $entry->credit_amount ?? 0;

            if ($account->isDebit()) {
                $balance += ($debit - $credit);
            } else {
                $balance += ($credit - $debit);
            }

            return [
                'id' => $entry->id,
                'voucher_number' => $entry->voucher->voucher_number,
                'voucher_date' => $entry->voucher->voucher_date->format('Y-m-d'),
                'description' => $entry->description,
                'debit_amount' => $debit > 0 ? $debit : null,
                'credit_amount' => $credit > 0 ? $credit : null,
                'balance' => $balance,
                'created_at' => $entry->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    private function getOpeningBalance(ChartOfAccount $account, $date = null): float
    {
        $query = $account->journalEntries()
            ->whereHas('voucher', function ($q) {
                $q->where('status', 'POSTED');
            });

        if ($date) {
            $query->whereDate('created_at', '<', $date);
        }

        $debits = $query->sum('debit_amount') ?? 0;
        $credits = $query->sum('credit_amount') ?? 0;

        if ($account->isDebit()) {
            return $debits - $credits;
        }

        return $credits - $debits;
    }

    public function generateGeneralLedger($fromDate = null, $toDate = null): array
    {
        $accounts = ChartOfAccount::active()->ordered()->get();

        $ledger = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $ledgerDetails = $this->getLedgerWithDetails($account, $fromDate, $toDate);

            if ($ledgerDetails['entries']->isNotEmpty()) {
                $ledger[] = $ledgerDetails;
                $totalDebits += $ledgerDetails['total_debits'];
                $totalCredits += $ledgerDetails['total_credits'];
            }
        }

        return [
            'period' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'as_of_date' => $toDate ?? now()->toDateString(),
            ],
            'accounts' => $ledger,
            'summary' => [
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'difference' => abs($totalDebits - $totalCredits),
                'is_balanced' => abs($totalDebits - $totalCredits) < 0.01,
            ],
        ];
    }

    public function exportLedgerToCsv(ChartOfAccount $account, $fromDate = null, $toDate = null): string
    {
        $details = $this->getLedgerWithDetails($account, $fromDate, $toDate);

        $csv = "Account Ledger - {$details['account']['name']}\n";
        $csv .= "Code: {$details['account']['code']}\n";
        $csv .= "Type: {$details['account']['type']}\n\n";

        $csv .= "Date,Voucher#,Description,Debit,Credit,Balance\n";

        foreach ($details['entries'] as $entry) {
            $csv .= $entry['voucher_date'] . ',';
            $csv .= $entry['voucher_number'] . ',';
            $csv .= '"' . $entry['description'] . '",';
            $csv .= ($entry['debit_amount'] ?? '') . ',';
            $csv .= ($entry['credit_amount'] ?? '') . ',';
            $csv .= $entry['balance'] . "\n";
        }

        $csv .= "\nOpening Balance: {$details['opening_balance']}\n";
        $csv .= "Closing Balance: {$details['closing_balance']}\n";
        $csv .= "Total Debits: {$details['total_debits']}\n";
        $csv .= "Total Credits: {$details['total_credits']}\n";

        return $csv;
    }

    public function getAccountAnalysis(ChartOfAccount $account, $fromDate = null, $toDate = null): array
    {
        $details = $this->getLedgerWithDetails($account, $fromDate, $toDate);

        return [
            'account' => $details['account'],
            'opening_balance' => $details['opening_balance'],
            'closing_balance' => $details['closing_balance'],
            'total_transactions' => $details['entries']->count(),
            'total_debits' => $details['total_debits'],
            'total_credits' => $details['total_credits'],
            'net_movement' => $details['closing_balance'] - $details['opening_balance'],
            'largest_debit' => $details['entries']->max('debit_amount'),
            'largest_credit' => $details['entries']->max('credit_amount'),
        ];
    }
}
