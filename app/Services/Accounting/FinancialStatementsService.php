<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;

class FinancialStatementsService
{
    private ChartOfAccountsService $coaService;
    private TrialBalanceService $trialBalanceService;

    public function __construct(
        ChartOfAccountsService $coaService,
        TrialBalanceService $trialBalanceService
    ) {
        $this->coaService = $coaService;
        $this->trialBalanceService = $trialBalanceService;
    }

    public function getIncomeStatement($fromDate, $toDate): array
    {
        $incomeAccounts = $this->coaService->getIncomeAccounts();
        $expenseAccounts = $this->coaService->getExpenseAccounts();

        $income = [];
        $totalIncome = 0;

        foreach ($incomeAccounts as $account) {
            $balance = abs($account->getBalance($fromDate, $toDate));
            if ($balance > 0) {
                $income[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => $balance,
                ];
                $totalIncome += $balance;
            }
        }

        $expenses = [];
        $totalExpenses = 0;

        foreach ($expenseAccounts as $account) {
            $balance = abs($account->getBalance($fromDate, $toDate));
            if ($balance > 0) {
                $expenses[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => $balance,
                ];
                $totalExpenses += $balance;
            }
        }

        $netProfit = $totalIncome - $totalExpenses;

        return [
            'period' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            'income' => [
                'items' => $income,
                'total' => $totalIncome,
            ],
            'expenses' => [
                'items' => $expenses,
                'total' => $totalExpenses,
            ],
            'net_profit' => $netProfit,
            'net_profit_percentage' => $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0,
        ];
    }

    public function getBalanceSheet($date = null): array
    {
        $assetAccounts = $this->coaService->getAssetAccounts();
        $liabilityAccounts = $this->coaService->getLiabilityAccounts();
        $equityAccounts = $this->coaService->getEquityAccounts();

        $assets = [];
        $totalAssets = 0;

        foreach ($assetAccounts as $account) {
            $balance = abs($account->getBalance(null, $date));
            if ($balance > 0) {
                $assets[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => $balance,
                ];
                $totalAssets += $balance;
            }
        }

        $liabilities = [];
        $totalLiabilities = 0;

        foreach ($liabilityAccounts as $account) {
            $balance = abs($account->getBalance(null, $date));
            if ($balance > 0) {
                $liabilities[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => $balance,
                ];
                $totalLiabilities += $balance;
            }
        }

        $equity = [];
        $totalEquity = 0;

        foreach ($equityAccounts as $account) {
            $balance = abs($account->getBalance(null, $date));
            if ($balance > 0) {
                $equity[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => $balance,
                ];
                $totalEquity += $balance;
            }
        }

        return [
            'as_of_date' => $date ?? now()->toDateString(),
            'assets' => [
                'items' => $assets,
                'total' => $totalAssets,
            ],
            'liabilities' => [
                'items' => $liabilities,
                'total' => $totalLiabilities,
            ],
            'equity' => [
                'items' => $equity,
                'total' => $totalEquity,
            ],
            'total_liabilities_and_equity' => $totalLiabilities + $totalEquity,
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }

    public function getCashFlowStatement($fromDate, $toDate): array
    {
        $cashAccount = ChartOfAccount::where('code', '1100')->first() ?? ChartOfAccount::where('code', '1200')->first();

        if (!$cashAccount) {
            return [
                'error' => 'Cash account not found',
            ];
        }

        $openingBalance = $this->getOpeningBalance($cashAccount, $fromDate);

        $operatingActivities = [];
        $investingActivities = [];
        $financingActivities = [];

        $totalOperating = 0;
        $totalInvesting = 0;
        $totalFinancing = 0;

        // Operating activities (Deposits, Expenses)
        $depositAccount = ChartOfAccount::where('code', '2100')->first();
        if ($depositAccount) {
            $amount = abs($depositAccount->getBalance($fromDate, $toDate));
            if ($amount > 0) {
                $operatingActivities[] = [
                    'description' => 'Member Deposits',
                    'amount' => $amount,
                ];
                $totalOperating += $amount;
            }
        }

        $expenseAccounts = $this->coaService->getExpenseAccounts();
        foreach ($expenseAccounts as $account) {
            $amount = abs($account->getBalance($fromDate, $toDate));
            if ($amount > 0) {
                $operatingActivities[] = [
                    'description' => $account->name,
                    'amount' => -$amount,
                ];
                $totalOperating -= $amount;
            }
        }

        // Investing activities (Investments)
        $investmentAccount = ChartOfAccount::where('code', '1300')->first();
        if ($investmentAccount) {
            $amount = abs($investmentAccount->getBalance($fromDate, $toDate));
            if ($amount > 0) {
                $investingActivities[] = [
                    'description' => 'Investment Purchases',
                    'amount' => -$amount,
                ];
                $totalInvesting -= $amount;
            }
        }

        // Financing activities (Share Capital)
        $shareAccount = ChartOfAccount::where('code', '3100')->first();
        if ($shareAccount) {
            $amount = abs($shareAccount->getBalance($fromDate, $toDate));
            if ($amount > 0) {
                $financingActivities[] = [
                    'description' => 'Share Capital Issued',
                    'amount' => $amount,
                ];
                $totalFinancing += $amount;
            }
        }

        $closingBalance = $openingBalance + $totalOperating + $totalInvesting + $totalFinancing;

        return [
            'period' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            'opening_balance' => $openingBalance,
            'operating_activities' => [
                'items' => $operatingActivities,
                'total' => $totalOperating,
            ],
            'investing_activities' => [
                'items' => $investingActivities,
                'total' => $totalInvesting,
            ],
            'financing_activities' => [
                'items' => $financingActivities,
                'total' => $totalFinancing,
            ],
            'net_cash_flow' => $totalOperating + $totalInvesting + $totalFinancing,
            'closing_balance' => $closingBalance,
        ];
    }

    public function getFundPositionReport($date = null): array
    {
        $cashAccount = ChartOfAccount::where('code', '1100')->first();
        $bankAccount = ChartOfAccount::where('code', '1200')->first();
        $investmentAccount = ChartOfAccount::where('code', '1300')->first();

        $cashBalance = $cashAccount ? $cashAccount->getBalance(null, $date) : 0;
        $bankBalance = $bankAccount ? $bankAccount->getBalance(null, $date) : 0;
        $investmentBalance = $investmentAccount ? $investmentAccount->getBalance(null, $date) : 0;

        $totalAssets = $cashBalance + $bankBalance + $investmentBalance;

        return [
            'as_of_date' => $date ?? now()->toDateString(),
            'operating_fund' => [
                'cash' => max(0, $cashBalance),
                'bank' => max(0, $bankBalance),
                'total' => max(0, $cashBalance + $bankBalance),
            ],
            'investment_fund' => [
                'amount' => max(0, $investmentBalance),
            ],
            'total_funds' => $totalAssets,
            'fund_percentage' => [
                'operating' => $totalAssets > 0 ? ((max(0, $cashBalance + $bankBalance) / $totalAssets) * 100) : 0,
                'investment' => $totalAssets > 0 ? ((max(0, $investmentBalance) / $totalAssets) * 100) : 0,
            ],
        ];
    }

    private function getOpeningBalance(ChartOfAccount $account, $fromDate)
    {
        if (!$fromDate) {
            return 0;
        }

        return $account->getBalance(null, date('Y-m-d', strtotime($fromDate . ' -1 day')));
    }

    public function exportIncomeStatementToCsv($fromDate, $toDate): string
    {
        $statement = $this->getIncomeStatement($fromDate, $toDate);

        $csv = "Income Statement\n";
        $csv .= "Period: {$statement['period']['from_date']} to {$statement['period']['to_date']}\n\n";

        $csv .= "INCOME\n";
        foreach ($statement['income']['items'] as $item) {
            $csv .= "{$item['code']}," . '"' . $item['name'] . '",' . $item['amount'] . "\n";
        }
        $csv .= "Total Income,," . $statement['income']['total'] . "\n\n";

        $csv .= "EXPENSES\n";
        foreach ($statement['expenses']['items'] as $item) {
            $csv .= "{$item['code']}," . '"' . $item['name'] . '",' . $item['amount'] . "\n";
        }
        $csv .= "Total Expenses,," . $statement['expenses']['total'] . "\n\n";

        $csv .= "NET PROFIT,," . $statement['net_profit'] . "\n";

        return $csv;
    }

    public function exportBalanceSheetToCsv($date = null): string
    {
        $statement = $this->getBalanceSheet($date);

        $csv = "Balance Sheet\n";
        $csv .= "As of: {$statement['as_of_date']}\n\n";

        $csv .= "ASSETS\n";
        foreach ($statement['assets']['items'] as $item) {
            $csv .= "{$item['code']}," . '"' . $item['name'] . '",' . $item['amount'] . "\n";
        }
        $csv .= "Total Assets,," . $statement['assets']['total'] . "\n\n";

        $csv .= "LIABILITIES\n";
        foreach ($statement['liabilities']['items'] as $item) {
            $csv .= "{$item['code']}," . '"' . $item['name'] . '",' . $item['amount'] . "\n";
        }
        $csv .= "Total Liabilities,," . $statement['liabilities']['total'] . "\n\n";

        $csv .= "EQUITY\n";
        foreach ($statement['equity']['items'] as $item) {
            $csv .= "{$item['code']}," . '"' . $item['name'] . '",' . $item['amount'] . "\n";
        }
        $csv .= "Total Equity,," . $statement['equity']['total'] . "\n\n";

        $csv .= "TOTAL LIABILITIES AND EQUITY,," . $statement['total_liabilities_and_equity'] . "\n";
        $csv .= "Balanced: " . ($statement['is_balanced'] ? 'YES' : 'NO') . "\n";

        return $csv;
    }
}
