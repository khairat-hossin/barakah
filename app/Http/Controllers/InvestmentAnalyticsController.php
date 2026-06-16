<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Services\InvestmentAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvestmentAnalyticsController extends Controller
{
    public function __construct(
        private InvestmentAnalyticsService $analyticsService,
    ) {
        $this->middleware('auth');
        $this->middleware('can:view investment analytics')->only(['index', 'performance']);
        $this->middleware('can:manage investments')->only(['createSnapshot']);
    }

    public function index(): View
    {
        $data = $this->analyticsService->getDashboardData();

        return view('investments.analytics', [
            'metrics' => $data['metrics'],
            'recent_investments' => $data['recent_investments'],
            'investments_by_type' => $data['investments_by_type'],
            'investments_by_status' => $data['investments_by_status'],
        ]);
    }

    public function performance(Investment $investment): View
    {
        $performance = $this->analyticsService->getInvestmentPerformance($investment);
        $trends = $this->analyticsService->getPerformanceTrends($investment, '30days');
        $snapshots = $investment->performanceSnapshots()->latest('snapshot_date')->limit(30)->get();

        return view('investments.performance', [
            'investment' => $investment,
            'performance' => $performance,
            'trends' => $trends,
            'snapshots' => $snapshots,
        ]);
    }

    public function createSnapshot(Investment $investment): RedirectResponse
    {
        $this->authorize('manage', $investment);

        $this->analyticsService->generatePerformanceSnapshot($investment);

        return redirect()->route('investments.performance', $investment)
            ->with('success', 'Performance snapshot created successfully.');
    }
}
