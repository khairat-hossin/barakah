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
        $this->authorize('viewAny', Member::class);

        // KPI Cards Data
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
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

        // Recent Activity
        $recentActivity = $this->getRecentActivity(10);

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
        $lastDeposits = $this->getLastDeposits(10);
        $totalDepositExpected = $this->getTotalDepositExpected();
        $depositExpectedVsReceived = $this->getDepositExpectedVsReceived();
        $paymentMethods = PaymentMethod::active()->ordered()->get();

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
            'activeMembersCollection', 'paymentMethods'
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
            ->limit(10)
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
        $activities = [];

        // Recent Deposits
        $deposits = SavingsEntry::with('member')
            ->latest('deposit_date')
            ->limit($limit)
            ->get()
            ->map(fn($d) => [
                'type' => 'deposit',
                'icon' => 'wallet',
                'title' => 'Deposit Collected',
                'description' => "{$d->member->name} deposited {$d->amount}",
                'amount' => $d->amount,
                'date' => $d->deposit_date,
            ]);

        // Recent Expenses
        $expenses = Expense::with('category', 'creator')
            ->latest('expense_date')
            ->limit($limit)
            ->get()
            ->map(fn($e) => [
                'type' => 'expense',
                'icon' => 'receipt',
                'title' => 'Expense Recorded',
                'description' => "{$e->title} ({$e->category->name})",
                'amount' => -$e->amount,
                'date' => $e->expense_date,
            ]);

        // Recent Members
        $members = Member::latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn($m) => [
                'type' => 'member',
                'icon' => 'users',
                'title' => 'New Member Added',
                'description' => "{$m->name} joined",
                'amount' => 0,
                'date' => $m->created_at,
            ]);

        $activities = collect($deposits)
            ->merge($expenses)
            ->merge($members)
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->toArray();

        return $activities;
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
        $orgProfile = \App\Models\OrganizationProfile::first();
        $shareFaceValue = $orgProfile?->share_face_value ?? 0;

        return Member::active()
            ->with('shares')
            ->get()
            ->sum(fn($member) => $member->shares()->count() * $shareFaceValue);
    }

    private function getDepositExpectedVsReceived(): array
    {
        $months = [];
        $expected = [];
        $received = [];

        $orgProfile = \App\Models\OrganizationProfile::first();
        $shareFaceValue = $orgProfile?->share_face_value ?? 0;

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $totalExpected = Member::active()
                ->with('shares')
                ->get()
                ->sum(fn($member) => $member->shares()->count() * $shareFaceValue);

            $totalReceived = SavingsEntry::whereMonth('deposit_date', $date->month)
                ->whereYear('deposit_date', $date->year)
                ->sum('amount');

            $expected[] = (float)$totalExpected;
            $received[] = (float)$totalReceived;
        }

        return [
            'months' => $months,
            'expected' => $expected,
            'received' => $received,
        ];
    }
}
