<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;

class TrialBalanceService
{
    private GeneralLedgerService $ledgerService;

    public function __construct(GeneralLedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function generateTrialBalance($date = null, $fromDate = null, $toDate = null): array
    {
        $accounts = ChartOfAccount::active()->ordered()->get();

        $trialBalance = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $balance = $account->getBalance($fromDate, $toDate);

            if ($balance != 0) {
                if ($account->isDebit()) {
                    $debit = abs($balance);
                    $credit = 0;
                } else {
                    $debit = 0;
                    $credit = abs($balance);
                }

                $trialBalance[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->account_type,
                    'normal_balance' => $account->normal_balance,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                ];

                $totalDebits += $debit;
                $totalCredits += $credit;
            }
        }

        return [
            'period' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'as_of_date' => $date ?? $toDate ?? now()->toDateString(),
            ],
            'accounts' => $trialBalance,
            'summary' => [
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'difference' => abs($totalDebits - $totalCredits),
                'is_balanced' => abs($totalDebits - $totalCredits) < 0.01,
            ],
        ];
    }

    public function isBalanced($date = null, $fromDate = null, $toDate = null): bool
    {
        $trialBalance = $this->generateTrialBalance($date, $fromDate, $toDate);

        return $trialBalance['summary']['is_balanced'];
    }

    public function getTrialBalanceByType(string $type, $date = null, $fromDate = null, $toDate = null): array
    {
        $accounts = ChartOfAccount::active()
            ->byType($type)
            ->ordered()
            ->get();

        $balance = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $balance_amount = $account->getBalance($fromDate, $toDate);

            if ($balance_amount != 0) {
                if ($account->isDebit()) {
                    $debit = abs($balance_amount);
                    $credit = 0;
                } else {
                    $debit = 0;
                    $credit = abs($balance_amount);
                }

                $balance[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'debit' => $debit,
                    'credit' => $credit,
                ];

                $totalDebits += $debit;
                $totalCredits += $credit;
            }
        }

        return [
            'type' => $type,
            'accounts' => $balance,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
        ];
    }

    public function validateTrialBalance($date = null, $fromDate = null, $toDate = null): array
    {
        $trialBalance = $this->generateTrialBalance($date, $fromDate, $toDate);
        $errors = [];

        if (!$trialBalance['summary']['is_balanced']) {
            $errors[] = [
                'type' => 'UNBALANCED',
                'message' => 'Trial balance is not balanced',
                'total_debits' => $trialBalance['summary']['total_debits'],
                'total_credits' => $trialBalance['summary']['total_credits'],
                'difference' => $trialBalance['summary']['difference'],
            ];
        }

        return $errors;
    }

    public function exportTrialBalanceToCsv($date = null, $fromDate = null, $toDate = null): string
    {
        $trialBalance = $this->generateTrialBalance($date, $fromDate, $toDate);

        $csv = "Trial Balance Report\n";
        $csv .= "As of: {$trialBalance['period']['as_of_date']}\n\n";

        $csv .= "Code,Account Name,Type,Debit,Credit\n";

        foreach ($trialBalance['accounts'] as $account) {
            $csv .= $account['code'] . ',';
            $csv .= '"' . $account['name'] . '",';
            $csv .= $account['type'] . ',';
            $csv .= $account['debit'] . ',';
            $csv .= $account['credit'] . "\n";
        }

        $csv .= "\nTOTALS,,,";
        $csv .= $trialBalance['summary']['total_debits'] . ',';
        $csv .= $trialBalance['summary']['total_credits'] . "\n";

        $csv .= "Balanced: " . ($trialBalance['summary']['is_balanced'] ? 'YES' : 'NO') . "\n";
        $csv .= "Difference: " . $trialBalance['summary']['difference'] . "\n";

        return $csv;
    }
}
