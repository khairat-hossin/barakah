<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentStatusHistory;
use App\Models\InvestmentTransaction;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class InvestmentService
{
    public function createInvestment(array $data): Investment
    {
        // Generate investment code
        $year = now()->year;
        $sequence = Investment::whereYear('created_at', $year)->count() + 1;
        $data['code'] = sprintf('INV-%d-%06d', $year, $sequence);
        $data['created_by'] = auth()->id();

        $investment = Investment::create($data);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_created',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'new_value' => $investment->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);

        return $investment;
    }

    public function updateInvestment(Investment $investment, array $data): Investment
    {
        $oldData = $investment->toArray();
        $investment->update($data);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_updated',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'old_value' => $oldData,
            'new_value' => $investment->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);

        return $investment;
    }

    public function transitionStatus(Investment $investment, string $newStatus, string $reason, ?string $notes = null): InvestmentStatusHistory
    {
        if (!$investment->canTransitionTo($newStatus)) {
            throw new \Exception("Cannot transition from {$investment->status} to {$newStatus}");
        }

        $oldStatus = $investment->status;
        $investment->update([
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        $history = InvestmentStatusHistory::create([
            'investment_id' => $investment->id,
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'reason' => $reason,
            'notes' => $notes,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // Log to AuditLog
        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'investment_status_changed',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'new_value' => ['status' => $newStatus],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);

        return $history;
    }

    public function canTransitionTo(Investment $investment, string $newStatus): bool
    {
        return $investment->canTransitionTo($newStatus);
    }

    public function getRemainingTenure(Investment $investment): ?int
    {
        return $investment->getRemainingTenureDays();
    }

    public function calculatePerformance(Investment $investment): array
    {
        return [
            'total_invested' => $investment->getTotalInvestedAmount(),
            'total_returned' => $investment->getTotalReturnedAmount(),
            'net_profit_loss' => $investment->getNetProfitLoss(),
            'roi_percentage' => $investment->getReturnPercentage(),
            'current_value' => $investment->total_invested_amount + $investment->getNetProfitLoss(),
            'remaining_tenure_days' => $investment->getRemainingTenureDays(),
            'is_maturity_due' => $investment->isMaturityDue(),
        ];
    }

    public function archiveInvestment(Investment $investment): void
    {
        if (!in_array($investment->status, ['matured', 'closed'])) {
            throw new \Exception("Only matured or closed investments can be archived");
        }

        $investment->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'deleted',
            'entity_type' => 'Investment',
            'entity_id' => $investment->id,
            'old_value' => $investment->toArray(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }

    public function triggerAccountingSync(Investment $investment): void
    {
        // TODO: Implement when accounting module is ready
        // Dispatch event: InvestmentNeedsAccounting
        // This will trigger journal entry creation
    }
}
