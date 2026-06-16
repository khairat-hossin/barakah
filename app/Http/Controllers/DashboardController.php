<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Share;
use App\Models\MemberShareOwnership;
use App\Models\SavingsEntry;
use App\Models\Expense;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function depositStatus()
    {
        $this->authorize('viewAny', Member::class);

        $month = now()->month;
        $year = now()->year;

        $members = Member::active()
            ->with(['savingsEntries' => function ($q) use ($month, $year) {
                $q->whereMonth('deposit_date', $month)
                  ->whereYear('deposit_date', $year);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($member) use ($month, $year) {
                $deposits = $member->savingsEntries;
                $hasDeposited = $deposits->isNotEmpty();
                $totalDeposited = $deposits->sum('amount');
                $lastDeposit = $member->savingsEntries()
                    ->orderByDesc('deposit_date')
                    ->first();

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'code' => $member->code ?? 'N/A',
                    'status' => $hasDeposited ? 'deposited' : 'pending',
                    'has_deposited' => $hasDeposited,
                    'amount_deposited' => $totalDeposited,
                    'last_deposit_date' => $lastDeposit?->deposit_date->format('M d, Y') ?? 'Never',
                    'phone' => $member->phone ?? 'N/A',
                    'email' => $member->email ?? 'N/A',
                    'shares' => MemberShareOwnership::where('member_id', $member->id)->current()->count(),
                ];
            });

        $deposited = $members->where('has_deposited', true)->count();
        $pending = $members->where('has_deposited', false)->count();

        return view('dashboard.deposit-status', compact('members', 'deposited', 'pending', 'month', 'year'));
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
        $membersWithDeposits = Member::active()
            ->with(['savingsEntries' => function ($q) use ($month, $year) {
                $q->whereMonth('deposit_date', $month)
                  ->whereYear('deposit_date', $year);
            }])
            ->get();
        $depositsPaid = $membersWithDeposits->filter(fn($m) => $m->savingsEntries->isNotEmpty())->count();
        $depositsUnpaid = $membersWithDeposits->filter(fn($m) => $m->savingsEntries->isEmpty())->count();

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

        $monthlyExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $previousMonthExpenses = Expense::whereMonth('expense_date', now()->subMonth()->month)
            ->whereYear('expense_date', now()->subMonth()->year)
            ->sum('amount');
        $expenseChange = $previousMonthExpenses > 0
            ? (($monthlyExpenses - $previousMonthExpenses) / $previousMonthExpenses * 100)
            : 0;

        // Financial Position
        $totalDeposits = SavingsEntry::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $netPosition = $totalDeposits - $totalExpenses;

        // Charts Data
        $depositTrend = $this->getDepositTrend();
        $expenseTrend = $this->getExpenseTrend();
        $investmentDistribution = $this->getInvestmentDistribution();
        $investmentPerformance = $this->getInvestmentPerformance();

        // Share Analytics
        $topShareholders = $this->getTopShareholders(10);
        $shareDistribution = $this->getShareDistribution();

        // Recent Activity
        $recentActivity = $this->getRecentActivity(10);

        // Pending Actions
        $pendingExpenses = Expense::where('status', 'pending')->count();
        $pendingInvestments = Investment::where('status', 'pending')->count();

        // Recent Members
        $recentMembers = Member::latest('created_at')->limit(5)->get();

        // Organization Health
        $cashAvailable = SavingsEntry::sum('amount') - Expense::sum('amount');
        $totalReturns = InvestmentTransaction::where('transaction_type', 'return')->sum('amount');

        return view('dashboard.index', compact(
            'totalMembers', 'activeMembers', 'memberGrowth',
            'totalShares', 'allocatedShares', 'availableShares',
            'monthlyDeposits', 'depositChange', 'depositsPaid', 'depositsUnpaid',
            'totalInvested', 'activeInvestments', 'investmentReturns',
            'monthlyExpenses', 'expenseChange',
            'netPosition', 'totalDeposits', 'totalExpenses',
            'depositTrend', 'expenseTrend',
            'investmentDistribution', 'investmentPerformance',
            'topShareholders', 'shareDistribution',
            'recentActivity', 'pendingExpenses', 'pendingInvestments',
            'recentMembers', 'cashAvailable', 'totalReturns'
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
            $total = Expense::whereMonth('expense_date', $date->month)
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
}
