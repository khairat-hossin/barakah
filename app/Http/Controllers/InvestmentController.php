<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentRequest;
use App\Http\Requests\UpdateInvestmentRequest;
use App\Models\Investment;
use App\Models\InvestmentType;
use App\Models\Member;
use App\Services\InvestmentService;
use App\Services\InvestmentAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestmentController extends Controller
{
    public function __construct(
        private InvestmentService $investmentService,
        private InvestmentAnalyticsService $analyticsService,
    ) {
    }

    public function index(): View
    {
        $investments = Investment::with('investmentType', 'investor', 'creator')
            ->latest('created_at')
            ->paginate(15);

        $metrics = $this->analyticsService->getPortfolioMetrics();
        $types = InvestmentType::active()->orderBy('name')->get();

        return view('investments.index', [
            'investments' => $investments,
            'metrics' => $metrics,
            'types' => $types,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';
        $typeFilter = $request->get('type', '');
        $statusFilter = $request->get('status', '');

        $query = Investment::with('investmentType', 'investor', 'creator');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('investor', fn($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }

        if ($typeFilter) {
            $query->where('investment_type_id', $typeFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $filtered = $query->count();
        $total = Investment::count();

        $investments = $query->latest('created_at')
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'code' => $i->code,
                'name' => $i->name,
                'type' => $i->investmentType?->name ?? 'N/A',
                'investor' => $i->investor?->name ?? 'Unassigned',
                'principal' => number_format($i->getTotalInvestedAmount(), 2),
                'current_value' => number_format($i->total_invested_amount + $i->getNetProfitLoss(), 2),
                'roi' => number_format($i->getReturnPercentage(), 2),
                'status' => $i->status,
                'start_date' => $i->start_date->format('d M Y'),
            ]);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $investments,
        ]);
    }

    public function create(): View
    {
        $types = InvestmentType::active()->orderBy('name')->get();
        $members = Member::where('status', 'active')->orderBy('name')->get();

        return view('investments.create', [
            'types' => $types,
            'members' => $members,
        ]);
    }

    public function store(StoreInvestmentRequest $request): RedirectResponse
    {
        $investment = $this->investmentService->createInvestment($request->validated());

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Investment created successfully.');
    }

    public function show(Investment $investment): View
    {
        $investment->load('investmentType', 'investor', 'creator', 'transactions', 'documents', 'statusHistories');
        $performance = $this->analyticsService->getInvestmentPerformance($investment);

        return view('investments.show', [
            'investment' => $investment,
            'performance' => $performance,
        ]);
    }

    public function edit(Investment $investment): View
    {
        if ($investment->status !== 'draft') {
            return back()->with('error', 'Only draft investments can be edited.');
        }

        $types = InvestmentType::active()->orderBy('name')->get();
        $members = Member::where('status', 'active')->orderBy('name')->get();

        return view('investments.edit', [
            'investment' => $investment,
            'types' => $types,
            'members' => $members,
        ]);
    }

    public function update(UpdateInvestmentRequest $request, Investment $investment): RedirectResponse
    {
        $this->investmentService->updateInvestment($investment, $request->validated());

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Investment updated successfully.');
    }

    public function destroy(Investment $investment): RedirectResponse
    {
        if ($investment->status !== 'draft') {
            return back()->with('error', 'Only draft investments can be deleted.');
        }

        $investment->delete();

        return redirect()->route('investments.index')
            ->with('success', 'Investment deleted successfully.');
    }

    public function activate(Request $request, Investment $investment): RedirectResponse
    {
        if (!$this->investmentService->canTransitionTo($investment, 'active')) {
            return back()->with('error', 'This investment cannot be activated.');
        }

        $this->investmentService->transitionStatus($investment, 'active', 'Activated by user');

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Investment activated successfully.');
    }

    public function mature(Request $request, Investment $investment): RedirectResponse
    {
        if (!$this->investmentService->canTransitionTo($investment, 'matured')) {
            return back()->with('error', 'This investment cannot be marked as matured.');
        }

        $this->investmentService->transitionStatus($investment, 'matured', 'Marked as matured');

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Investment marked as matured.');
    }

    public function suspend(Request $request, Investment $investment): RedirectResponse
    {
        if (!$this->investmentService->canTransitionTo($investment, 'suspended')) {
            return back()->with('error', 'This investment cannot be suspended.');
        }

        $reason = $request->input('reason', 'Suspended by user');
        $notes = $request->input('notes');

        $this->investmentService->transitionStatus($investment, 'suspended', $reason, $notes);

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Investment suspended successfully.');
    }

    public function close(Request $request, Investment $investment): RedirectResponse
    {
        if (!$this->investmentService->canTransitionTo($investment, 'closed')) {
            return back()->with('error', 'This investment cannot be closed.');
        }

        $reason = $request->input('reason', 'Closed by user');
        $notes = $request->input('notes');

        $this->investmentService->transitionStatus($investment, 'closed', $reason, $notes);

        return redirect()->route('investments.show', $investment)
            ->with('success', 'Investment closed successfully.');
    }

    public function statusHistory(Investment $investment): View
    {
        $investment->load('statusHistories.changedByUser');

        return view('investments.status-history', [
            'investment' => $investment,
            'histories' => $investment->statusHistories,
        ]);
    }
}
