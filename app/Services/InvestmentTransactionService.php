<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\AuditLog;

class InvestmentTransactionService
{
    public function recordTransaction(Investment $investment, array $data): InvestmentTransaction
    {
        // Generate transaction number
        $year = now()->year;
        $sequence = InvestmentTransaction::whereYear('created_at', $year)->count() + 1;
        $data['transaction_number'] = sprintf('TXN-%d-%06d', $year, $sequence);
        $data['investment_id'] = $investment->id;
        $data['created_by'] = auth()->id();
        $data['status'] = $data['status'] ?? 'pending';

        $transaction = InvestmentTransaction::create($data);

        // Update investment totals
        $this->updateInvestmentTotals($investment);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_transaction_created',
            'entity_type' => 'InvestmentTransaction',
            'entity_id' => $transaction->id,
            'new_value' => $transaction->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);

        return $transaction;
    }

    public function approveTransaction(InvestmentTransaction $transaction, ?string $notes = null): void
    {
        $transaction->approve(auth()->user(), $notes);

        // Update investment totals
        $this->updateInvestmentTotals($transaction->investment);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_transaction_approved',
            'entity_type' => 'InvestmentTransaction',
            'entity_id' => $transaction->id,
            'new_value' => ['status' => 'processed'],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function reverseTransaction(InvestmentTransaction $transaction, ?string $reason = null): void
    {
        $transaction->reverse($reason);

        // Update investment totals
        $this->updateInvestmentTotals($transaction->investment);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_transaction_reversed',
            'entity_type' => 'InvestmentTransaction',
            'entity_id' => $transaction->id,
            'new_value' => ['status' => 'reversed'],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function updateInvestmentTotals(Investment $investment): void
    {
        $totalInvested = $investment->getTotalInvestedAmount();
        $totalReturned = $investment->getTotalReturnedAmount();
        $netProfit = $investment->getNetProfitLoss();

        $investment->update([
            'total_invested_amount' => $totalInvested,
            'total_returned_amount' => $totalReturned,
            'net_profit_loss' => $netProfit,
            'actual_return_percentage' => $totalInvested > 0 ? ($netProfit / $totalInvested * 100) : 0,
            'updated_by' => auth()->id(),
        ]);
    }

    public function calculateInvestmentTotals(Investment $investment): array
    {
        return [
            'total_invested' => $investment->getTotalInvestedAmount(),
            'total_returned' => $investment->getTotalReturnedAmount(),
            'net_profit_loss' => $investment->getNetProfitLoss(),
            'roi_percentage' => $investment->getReturnPercentage(),
        ];
    }

    public function validateTransactionType(Investment $investment, string $transactionType): bool
    {
        $allowedTransitions = [
            'draft' => ['INITIAL_INVESTMENT'],
            'active' => ['ADDITIONAL_INVESTMENT', 'PROFIT_DISTRIBUTION', 'LOSS_ADJUSTMENT', 'WITHDRAWAL', 'DIVIDEND_PAYMENT', 'REINVESTMENT', 'ADMINISTRATIVE_ADJUSTMENT'],
            'suspended' => ['LOSS_ADJUSTMENT', 'ADMINISTRATIVE_ADJUSTMENT'],
            'matured' => ['MATURITY_CLOSURE', 'WITHDRAWAL'],
            'closed' => [],
        ];

        return in_array($transactionType, $allowedTransitions[$investment->status] ?? []);
    }

    public function triggerAccountingEntry(InvestmentTransaction $transaction): void
    {
        // TODO: Implement when accounting module is ready
        // Create InvestmentAccountingEntry based on transaction type
        // Dispatch event to sync with external accounting system
    }
}
