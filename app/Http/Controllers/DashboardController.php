<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Share;
use App\Models\MemberShareOwnership;
use App\Models\SavingsEntry;
use App\Models\Expense;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function depositStatus(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Member::class);

        // Selected month (defaults to current). Accepts ?month=Y-m
        $selectedMonth = $request->query('month');
        try {
            $monthDate = $selectedMonth
                ? \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()
                : now()->startOfMonth();
        } catch (\Exception $e) {
            $monthDate = now()->startOfMonth();
        }

        $month = $monthDate->month;
        $year = $monthDate->year;

        // Get paid months for each member
        $paidMonths = \App\Models\MemberDepositMonth::all()
            ->groupBy('member_id')
            ->map(fn($months) => $months->map(fn($m) => "{$m->month}/{$m->year}")->toArray());

        $members = Member::active()
            ->orderBy('name')
            ->get()
            ->map(function ($member) use ($month, $year, $paidMonths) {
                $monthKey = "{$month}/{$year}";
                $hasPaidThisMonth = in_array($monthKey, $paidMonths[$member->id] ?? []);

                // Get amount deposited the selected month
                $amountDepositedThisMonth = SavingsEntry::where('member_id', $member->id)
                    ->whereMonth('deposit_date', $month)
                    ->whereYear('deposit_date', $year)
                    ->sum('amount');

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'code' => $member->member_code ?? 'N/A',
                    'status' => $hasPaidThisMonth ? 'deposited' : 'pending',
                    'has_deposited' => $hasPaidThisMonth,
                    'phone' => $member->phone ?? 'N/A',
                    'email' => $member->email ?? 'N/A',
                    'shares' => MemberShareOwnership::where('member_id', $member->id)->current()->count(),
                    'amount_deposited' => (float) $amountDepositedThisMonth,
                    'monthly_amount' => $member->getCalculatedMonthlyDepositAmount(),
                ];
            });

        $deposited = $members->where('has_deposited', true)->count();
        $pending = $members->where('has_deposited', false)->count();

        return view('dashboard.deposit-status', [
            'members' => $members,
            'deposited' => $deposited,
            'pending' => $pending,
            'month' => $month,
            'year' => $year,
            'selectedMonth' => $monthDate->format('Y-m'),
            'monthLabel' => $monthDate->format('F Y'),
        ]);
    }

    public function index()
    {
        // Any user with "view dashboard" (including the Member role) may see the
        // dashboard. It is read-only for them — action buttons are hidden in the
        // view via @can checks, so no authorize() gate is needed here.

        // KPI Cards Data — counts reflect active members only (deactivated
        // members are kept for evidence but excluded from headline counts).
        $totalMembers = Member::where('status', 'active')->count();
        $activeMembers = $totalMembers;
        $memberGrowth = $this->calculateMemberGrowth();

        // Deposit Status This Month
        $month = now()->month;
        $year = now()->year;
        $activeMembersCollection = Member::active()->get();

        $paidMemberIds = \App\Models\MemberDepositMonth::where('month', $month)
            ->where('year', $year)
            ->pluck('member_id')
            ->unique();
        $depositsPaid = $paidMemberIds->count();
        $depositsUnpaid = $activeMembersCollection->count() - $depositsPaid;
        $pendingMembers = $activeMembersCollection
            ->whereNotIn('id', $paidMemberIds)
            ->sortBy('name')
            ->take(5)
            ->values();

        $totalShares = Share::count();
        $allocatedShares = MemberShareOwnership::current()->count();
        $availableShares = $totalShares - $allocatedShares;

        $monthlyDeposits = SavingsEntry::whereMonth('deposit_date', now()->month)
            ->whereYear('deposit_date', now()->year)
            ->sum('amount');
        $previousMonthDeposits = SavingsEntry::whereMonth('deposit_date', now()->subMonth()->month)
            ->whereYear('deposit_date', now()->subMonth()->year)
            ->sum('amount');
        $depositChange = $previousMonthDeposits > 0
            ? (($monthlyDeposits - $previousMonthDeposits) / $previousMonthDeposits * 100)
            : 0;

        $totalInvested = Investment::sum('total_invested_amount');
        $activeInvestments = Investment::where('status', 'active')->count();
        $investmentReturns = Investment::sum('total_returned_amount');

        $monthlyExpenses = Expense::whereNull('deleted_at')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $previousMonthExpenses = Expense::whereNull('deleted_at')
            ->whereMonth('expense_date', now()->subMonth()->month)
            ->whereYear('expense_date', now()->subMonth()->year)
            ->sum('amount');
        $expenseChange = $previousMonthExpenses > 0
            ? (($monthlyExpenses - $previousMonthExpenses) / $previousMonthExpenses * 100)
            : 0;

        // Financial Position
        $totalDeposits = SavingsEntry::sum('amount');
        $totalExpenses = Expense::whereNull('deleted_at')->sum('amount');
        $netPosition = $totalDeposits - $totalExpenses;

        // Charts Data
        $depositTrend = $this->getDepositTrend();
        $expenseTrend = $this->getExpenseTrend();
        $investmentDistribution = $this->getInvestmentDistribution();
        $investmentPerformance = $this->getInvestmentPerformance();

        // Share Analytics
        $topShareholders = $this->getTopShareholders(5);
        $shareDistribution = $this->getShareDistribution();

        // Recent Activity (compact — dashboard shows a short feed)
        $recentActivity = $this->getRecentActivity(8);

        // Pending Actions
        $pendingExpenses = Expense::where('status', 'pending')->count();
        $pendingInvestments = Investment::where('status', 'pending')->count();

        // Recent Members
        $recentMembers = Member::latest('created_at')->limit(5)->get();

        // Last 6 Months Deposit Count
        $depositCountTrend = [];
        $depositCountLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $depositCountLabels[] = $date->format('M');
            $count = SavingsEntry::whereMonth('deposit_date', $date->month)
                ->whereYear('deposit_date', $date->year)
                ->count();
            $depositCountTrend[] = $count;
        }

        // Organization Health
        $cashAvailable = SavingsEntry::sum('amount') - Expense::whereNull('deleted_at')->sum('amount');
        $totalReturns = InvestmentTransaction::where('transaction_type', 'return')->sum('amount');

        // Deposit Analytics
        $lastDeposits = $this->getLastDeposits(5);
        $totalDepositExpected = $this->getTotalDepositExpected();
        $depositExpectedVsReceived = $this->getDepositExpectedVsReceived($totalDepositExpected);
        $paymentMethods = PaymentMethod::active()->ordered()->get();

        // ---- Derived KPIs for the financial-operations dashboard ----
        $expectedThisMonth = $totalDepositExpected;
        $collectedThisMonth = (float) $monthlyDeposits;
        $outstandingDue = max(0, $expectedThisMonth - $collectedThisMonth);
        $collectionRate = $expectedThisMonth > 0
            ? min(100, $collectedThisMonth / $expectedThisMonth * 100)
            : 0;
        // Top unpaid members for the current month (name, due, last paid, overdue).
        $unpaidMembers = $this->getUnpaidMembers($paidMemberIds, 5);

        return view('dashboard.index', compact(
            'totalMembers', 'activeMembers', 'memberGrowth',
            'totalShares', 'allocatedShares', 'availableShares',
            'monthlyDeposits', 'depositChange', 'depositsPaid', 'depositsUnpaid', 'pendingMembers',
            'totalInvested', 'activeInvestments', 'investmentReturns',
            'monthlyExpenses', 'expenseChange',
            'netPosition', 'totalDeposits', 'totalExpenses',
            'depositTrend', 'expenseTrend',
            'investmentDistribution', 'investmentPerformance',
            'topShareholders', 'shareDistribution',
            'recentActivity', 'pendingExpenses', 'pendingInvestments',
            'recentMembers', 'cashAvailable', 'totalReturns',
            'depositCountTrend', 'depositCountLabels',
            'lastDeposits', 'totalDepositExpected', 'depositExpectedVsReceived',
            'activeMembersCollection', 'paymentMethods',
            'expectedThisMonth', 'collectedThisMonth', 'outstandingDue', 'collectionRate', 'unpaidMembers'
        ));
    }

    private function calculateMemberGrowth(): float
    {
        $currentMonth = Member::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $previousMonth = Member::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        return $previousMonth > 0 ? (($currentMonth - $previousMonth) / $previousMonth * 100) : 0;
    }

    private function getDepositTrend(): array
    {
        $months = [];
        $totals = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $total = SavingsEntry::whereMonth('deposit_date', $date->month)
                ->whereYear('deposit_date', $date->year)
                ->sum('amount');
            $totals[] = (float)$total;
        }

        return [
            'months' => $months,
            'totals' => $totals,
            'average' => count($totals) > 0 ? array_sum($totals) / count($totals) : 0,
        ];
    }

    private function getExpenseTrend(): array
    {
        $months = [];
        $totals = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $total = Expense::whereNull('deleted_at')
                ->whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year)
                ->sum('amount');
            $totals[] = (float)$total;
        }

        return [
            'months' => $months,
            'totals' => $totals,
        ];
    }

    private function getInvestmentDistribution(): array
    {
        $distribution = Investment::select('investment_type_id', DB::raw('COUNT(*) as count, SUM(total_invested_amount) as total'))
            ->with('investmentType')
            ->groupBy('investment_type_id')
            ->get()
            ->map(fn($inv) => [
                'type' => $inv->investmentType?->name ?? 'Unknown',
                'count' => $inv->count,
                'amount' => (float)($inv->total ?? 0),
            ])
            ->toArray();

        return $distribution;
    }

    private function getInvestmentPerformance(): array
    {
        return Investment::select('id', 'name', 'total_invested_amount', 'total_returned_amount')
            ->where('status', 'active')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(fn($inv) => [
                'name' => $inv->name,
                'invested' => (float)$inv->total_invested_amount,
                'returns' => (float)($inv->total_returned_amount ?? 0),
            ])
            ->toArray();
    }

    private function getTopShareholders(int $limit = 10): array
    {
        $totalOwned = MemberShareOwnership::current()->count();

        return MemberShareOwnership::with('member')
            ->current()
            ->select('member_id', DB::raw('COUNT(*) as share_count'))
            ->groupBy('member_id')
            ->orderByDesc('share_count')
            ->limit($limit)
            ->get()
            ->map(fn($ownership) => [
                'name' => $ownership->member->name,
                'shares' => $ownership->share_count,
                'percentage' => ($totalOwned > 0 ? ($ownership->share_count / $totalOwned * 100) : 0),
                'joinedAt' => $ownership->member->created_at->format('M d, Y'),
            ])
            ->toArray();
    }

    private function getShareDistribution(): array
    {
        return MemberShareOwnership::with('member')
            ->current()
            ->select('member_id', DB::raw('COUNT(*) as share_count'))
            ->groupBy('member_id')
            ->orderByDesc('share_count')
            ->limit(5)
            ->get()
            ->map(fn($ownership) => [
                'name' => $ownership->member->name,
                'shares' => (float)$ownership->share_count,
            ])
            ->toArray();
    }

    private function getRecentActivity(int $limit = 10): array
    {
        // Financial events (higher priority): deposits, expenses, investments.
        $deposits = SavingsEntry::with('member')
            ->latest('deposit_date')->limit($limit)->get()
            ->map(fn ($d) => [
                'type' => 'deposit',
                'icon' => 'arrow-down',
                'title' => 'Deposit collected',
                'description' => $d->member->name ?? 'Member',
                'amount' => (float) $d->amount,
                'date' => $d->deposit_date,
            ]);

        $expenses = Expense::with('category')
            ->latest('expense_date')->limit($limit)->get()
            ->map(fn ($e) => [
                'type' => 'expense',
                'icon' => 'arrow-up',
                'title' => 'Expense recorded',
                'description' => $e->title . ($e->category ? ' · ' . $e->category->name : ''),
                'amount' => -(float) $e->amount,
                'date' => $e->expense_date,
            ]);

        $investments = Investment::latest('created_at')->limit($limit)->get()
            ->map(fn ($i) => [
                'type' => 'investment',
                'icon' => 'chart-line',
                'title' => 'Investment created',
                'description' => $i->name,
                'amount' => -(float) $i->total_invested_amount,
                'date' => $i->created_at,
            ]);

        $financial = collect($deposits)->merge($expenses)->merge($investments)
            ->sortByDesc('date')
            ->take($limit);

        // Fill any remaining slots with new-member events (lower priority).
        if ($financial->count() < $limit) {
            $members = Member::latest('created_at')
                ->limit($limit - $financial->count())->get()
                ->map(fn ($m) => [
                    'type' => 'member',
                    'icon' => 'user-plus',
                    'title' => 'New member added',
                    'description' => $m->name,
                    'amount' => 0,
                    'date' => $m->created_at,
                ]);
            $financial = $financial->merge($members);
        }

        return $financial->sortByDesc('date')->take($limit)->values()->toArray();
    }

    private function getLastDeposits(int $limit = 10): array
    {
        return SavingsEntry::with('member')
            ->latest('deposit_date')
            ->limit($limit)
            ->get()
            ->map(fn($entry) => [
                'name' => $entry->member->name,
                'amount' => (float)$entry->amount,
                'date' => $entry->deposit_date->format('M d'),
            ])
            ->toArray();
    }

    private function getTotalDepositExpected(): float
    {
        $shareFaceValue = \App\Models\OrganizationProfile::first()?->share_face_value ?? 0;

        if ($shareFaceValue <= 0) {
            return 0.0;
        }

        // withCount() computes each active member's current share count in a
        // single query (the shares() relation already excludes ended ownerships),
        // avoiding the previous N+1 of calling $member->shares()->count() per row.
        return (float) Member::active()
            ->withCount('shares')
            ->get()
            ->sum(fn ($member) => $member->shares_count * $shareFaceValue);
    }

    private function getDepositExpectedVsReceived(?float $totalExpected = null): array
    {
        $months = [];
        $expected = [];
        $received = [];

        // Expected is based on current shareholding, so it's the same each month —
        // compute it once instead of recalculating inside the loop.
        $totalExpected = $totalExpected ?? $this->getTotalDepositExpected();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            $expected[] = $totalExpected;
            $received[] = (float) SavingsEntry::whereMonth('deposit_date', $date->month)
                ->whereYear('deposit_date', $date->year)
                ->sum('amount');
        }

        return [
            'months' => $months,
            'expected' => $expected,
            'received' => $received,
        ];
    }

    /**
     * Top unpaid members for the current month, with their monthly due amount
     * and last paid month. Reuses the paid-member-ids already computed in index().
     */
    private function getUnpaidMembers($paidMemberIds, int $limit = 5): array
    {
        $faceValue = \App\Models\OrganizationProfile::first()?->share_face_value ?? 0;

        $unpaid = Member::active()
            ->whereNotIn('id', $paidMemberIds)
            ->withCount('shares')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        // Latest paid month per member in one query (avoids N+1).
        $lastPaidByMember = \App\Models\MemberDepositMonth::whereIn('member_id', $unpaid->pluck('id'))
            ->get()
            ->groupBy('member_id')
            ->map(fn ($rows) => $rows->sortByDesc(fn ($r) => $r->year * 100 + $r->month)->first());

        $previousMonthStart = now()->subMonth()->startOfMonth();

        return $unpaid->map(function ($m) use ($faceValue, $lastPaidByMember, $previousMonthStart) {
            $lp = $lastPaidByMember[$m->id] ?? null;
            $lpDate = $lp ? \Carbon\Carbon::createFromDate($lp->year, $lp->month, 1) : null;

            return [
                'id' => $m->id,
                'name' => $m->name,
                'code' => $m->member_code ?? '—',
                'due' => (float) ($m->shares_count * $faceValue),
                'last_paid' => $lpDate?->format('M Y'),
                // Overdue = never paid, or last payment predates the previous month.
                'overdue' => $lpDate ? $lpDate->lt($previousMonthStart) : true,
            ];
        })->toArray();
    }
}
