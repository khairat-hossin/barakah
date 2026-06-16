<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChartOfAccountsService
{
    public function createAccount(array $data, User $user): ChartOfAccount
    {
        $data['created_by'] = $user->id;

        return ChartOfAccount::create($data);
    }

    public function updateAccount(ChartOfAccount $account, array $data, User $user): ChartOfAccount
    {
        $data['updated_by'] = $user->id;

        $account->update($data);

        return $account;
    }

    public function deactivateAccount(ChartOfAccount $account, User $user): ChartOfAccount
    {
        return $this->updateAccount($account, ['is_active' => false], $user);
    }

    public function activateAccount(ChartOfAccount $account, User $user): ChartOfAccount
    {
        return $this->updateAccount($account, ['is_active' => true], $user);
    }

    public function getAccountHierarchy(?string $type = null): Collection
    {
        $query = ChartOfAccount::active()
            ->whereNull('parent_id')
            ->ordered();

        if ($type) {
            $query->where('account_type', $type);
        }

        return $query->with('children')->get();
    }

    public function getAccountsByType(string $type): Collection
    {
        return ChartOfAccount::active()
            ->byType($type)
            ->ordered()
            ->get();
    }

    public function getAssetAccounts(): Collection
    {
        return $this->getAccountsByType('ASSET');
    }

    public function getLiabilityAccounts(): Collection
    {
        return $this->getAccountsByType('LIABILITY');
    }

    public function getEquityAccounts(): Collection
    {
        return $this->getAccountsByType('EQUITY');
    }

    public function getIncomeAccounts(): Collection
    {
        return $this->getAccountsByType('INCOME');
    }

    public function getExpenseAccounts(): Collection
    {
        return $this->getAccountsByType('EXPENSE');
    }

    public function findByCode(string $code): ?ChartOfAccount
    {
        return ChartOfAccount::where('code', $code)->first();
    }

    public function calculateAccountBalance(ChartOfAccount $account, $fromDate = null, $toDate = null): float
    {
        return (float) $account->getBalance($fromDate, $toDate);
    }

    public function calculateTotalAssets($date = null): float
    {
        $assets = $this->getAssetAccounts();
        $total = 0;

        foreach ($assets as $asset) {
            $total += $this->calculateAccountBalance($asset, null, $date);
        }

        return $total;
    }

    public function calculateTotalLiabilities($date = null): float
    {
        $liabilities = $this->getLiabilityAccounts();
        $total = 0;

        foreach ($liabilities as $liability) {
            $total += $this->calculateAccountBalance($liability, null, $date);
        }

        return $total;
    }

    public function calculateTotalEquity($date = null): float
    {
        $equity = $this->getEquityAccounts();
        $total = 0;

        foreach ($equity as $account) {
            $total += $this->calculateAccountBalance($account, null, $date);
        }

        return $total;
    }

    public function getInitialChartOfAccounts(User $user): array
    {
        $accountsData = [
            // Assets
            ['code' => '1100', 'name' => 'Cash', 'type' => 'ASSET', 'balance' => 'DEBIT'],
            ['code' => '1200', 'name' => 'Bank', 'type' => 'ASSET', 'balance' => 'DEBIT'],
            ['code' => '1300', 'name' => 'Investments', 'type' => 'ASSET', 'balance' => 'DEBIT'],
            ['code' => '1400', 'name' => 'Receivables', 'type' => 'ASSET', 'balance' => 'DEBIT'],

            // Liabilities
            ['code' => '2100', 'name' => 'Member Deposits', 'type' => 'LIABILITY', 'balance' => 'CREDIT'],
            ['code' => '2200', 'name' => 'Payables', 'type' => 'LIABILITY', 'balance' => 'CREDIT'],

            // Equity
            ['code' => '3100', 'name' => 'Share Capital', 'type' => 'EQUITY', 'balance' => 'CREDIT'],
            ['code' => '3200', 'name' => 'Retained Earnings', 'type' => 'EQUITY', 'balance' => 'CREDIT'],

            // Income
            ['code' => '4100', 'name' => 'Investment Income', 'type' => 'INCOME', 'balance' => 'CREDIT'],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'INCOME', 'balance' => 'CREDIT'],

            // Expenses
            ['code' => '5100', 'name' => 'Meeting Expenses', 'type' => 'EXPENSE', 'balance' => 'DEBIT'],
            ['code' => '5200', 'name' => 'Office Expenses', 'type' => 'EXPENSE', 'balance' => 'DEBIT'],
            ['code' => '5300', 'name' => 'Bank Charges', 'type' => 'EXPENSE', 'balance' => 'DEBIT'],
            ['code' => '5400', 'name' => 'Miscellaneous Expenses', 'type' => 'EXPENSE', 'balance' => 'DEBIT'],
        ];

        $accounts = [];
        foreach ($accountsData as $data) {
            $accounts[] = $this->createAccount([
                'code' => $data['code'],
                'name' => $data['name'],
                'account_type' => $data['type'],
                'normal_balance' => $data['balance'],
                'is_active' => true,
            ], $user);
        }

        return $accounts;
    }
}
