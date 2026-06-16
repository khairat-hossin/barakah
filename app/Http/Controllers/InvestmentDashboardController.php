<?php

namespace App\Http\Controllers;

use App\Services\InvestmentAnalyticsService;
use Illuminate\View\View;

class InvestmentDashboardController extends Controller
{
    public function __construct(
        private InvestmentAnalyticsService $analyticsService,
    ) {
    }

    public function show(): View
    {
        $data = $this->analyticsService->getDashboardData();
        $maturingAlerts = $this->analyticsService->getMaturityAlerts();
        $riskDistribution = $this->analyticsService->getRiskDistribution();

        return view('investments.dashboard', [
            'metrics' => $data['metrics'],
            'recent_investments' => $data['recent_investments'],
            'maturing_investments' => $maturingAlerts,
            'investments_by_type' => $data['investments_by_type'],
            'investments_by_status' => $data['investments_by_status'],
            'risk_distribution' => $riskDistribution,
        ]);
    }
}
