<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentPerformanceSnapshot;
use Illuminate\Support\Collection;

class InvestmentAnalyticsService
{
    public function getPortfolioMetrics(): array
    {
        $investments = Investment::where('status', '!=', 'draft')->get();

        $totalInvested = $investments->sum(fn($i) => $i->getTotalInvestedAmount());
        $totalReturned = $investments->sum(fn($i) => $i->getTotalReturnedAmount());
        $netProfit = $investments->sum(fn($i) => $i->getNetProfitLoss());
        $roiPercentage = $totalInvested > 0 ? ($netProfit / $totalInvested * 100) : 0;

        return [
            'total_investments_count' => $investments->count(),
            'active_count' => Investment::active()->count(),
            'matured_count' => Investment::matured()->count(),
            'closed_count' => Investment::closed()->count(),
            'total_invested' => $totalInvested,
            'total_returned' => $totalReturned,
            'net_profit_loss' => $netProfit,
            'roi_percentage' => $roiPercentage,
            'current_portfolio_value' => $totalInvested + $netProfit,
        ];
    }

    public function getInvestmentPerformance(Investment $investment): array
    {
        return [
            'investment_id' => $investment->id,
            'code' => $investment->code,
            'name' => $investment->name,
            'status' => $investment->status,
            'total_invested' => $investment->getTotalInvestedAmount(),
            'total_returned' => $investment->getTotalReturnedAmount(),
            'net_profit_loss' => $investment->getNetProfitLoss(),
            'roi_percentage' => $investment->getReturnPercentage(),
            'current_value' => $investment->total_invested_amount + $investment->getNetProfitLoss(),
            'remaining_tenure_days' => $investment->getRemainingTenureDays(),
            'is_maturity_due' => $investment->isMaturityDue(),
            'transaction_count' => $investment->transactions()->count(),
            'document_count' => $investment->documents()->count(),
        ];
    }

    public function getDashboardData(): array
    {
        $portfolioMetrics = $this->getPortfolioMetrics();
        $recentInvestments = Investment::latest('created_at')->limit(10)->get();
        $maturingInvestments = Investment::active()
            ->whereNotNull('maturity_date')
            ->where('maturity_date', '<=', now()->addDays(30))
            ->get();

        $investmentsByType = Investment::active()
            ->with('investmentType')
            ->get()
            ->groupBy(fn($i) => $i->investmentType?->name)
            ->map(fn($group) => $group->count());

        $investmentsByStatus = Investment::where('status', '!=', 'draft')
            ->get()
            ->groupBy('status')
            ->map(fn($group) => [
                'count' => $group->count(),
                'amount' => $group->sum(fn($i) => $i->getTotalInvestedAmount()),
            ]);

        return [
            'metrics' => $portfolioMetrics,
            'recent_investments' => $recentInvestments,
            'maturing_investments' => $maturingInvestments,
            'investments_by_type' => $investmentsByType,
            'investments_by_status' => $investmentsByStatus,
        ];
    }

    public function generatePerformanceSnapshot(Investment $investment): InvestmentPerformanceSnapshot
    {
        $performance = $this->getInvestmentPerformance($investment);

        return InvestmentPerformanceSnapshot::updateOrCreate(
            [
                'investment_id' => $investment->id,
                'snapshot_date' => now()->toDateString(),
            ],
            [
                'total_invested' => $performance['total_invested'],
                'current_value' => $performance['current_value'],
                'unrealized_gain_loss' => max(0, $performance['current_value'] - $performance['total_invested']),
                'realized_gain_loss' => $performance['net_profit_loss'],
                'return_percentage' => $performance['roi_percentage'],
                'transaction_count' => $performance['transaction_count'],
            ]
        );
    }

    public function getMaturityAlerts(): Collection
    {
        return Investment::active()
            ->whereNotNull('maturity_date')
            ->where('maturity_date', '<=', now()->addDays(30))
            ->orderBy('maturity_date')
            ->get();
    }

    public function getRiskDistribution(): array
    {
        return Investment::active()
            ->get()
            ->groupBy('risk_level')
            ->map(fn($group) => [
                'count' => $group->count(),
                'percentage' => ($group->count() / Investment::active()->count() * 100),
                'amount' => $group->sum(fn($i) => $i->getTotalInvestedAmount()),
            ])
            ->toArray();
    }

    public function getPerformanceTrends(Investment $investment, string $period = '30days'): array
    {
        $from = match($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            '1year' => now()->subYear(),
            default => now()->subDays(30),
        };

        $snapshots = InvestmentPerformanceSnapshot::where('investment_id', $investment->id)
            ->where('snapshot_date', '>=', $from->toDateString())
            ->orderBy('snapshot_date')
            ->get();

        return [
            'period' => $period,
            'snapshots' => $snapshots->map(fn($s) => [
                'date' => $s->snapshot_date->format('Y-m-d'),
                'value' => $s->current_value,
                'roi_percentage' => $s->return_percentage,
            ])->toArray(),
        ];
    }
}
