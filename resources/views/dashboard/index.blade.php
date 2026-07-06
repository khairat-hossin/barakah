@extends('layouts.phoenix')

@section('title', 'Dashboard | ' . \App\Support\Branding::name())

@push('styles')
<style>
    /* ---- Dashboard (financial operations) ---- */
    .dash-section { margin-bottom: 1.5rem; }
    .dash-title { font-size: 0.95rem; font-weight: 700; }
    .card-quiet { background: var(--bs-body-bg); }

    /* KPI cards */
    .kpi-card .kpi-label { font-size: 0.68rem; letter-spacing: 0.03em; line-height: 1.2; }
    .kpi-card .kpi-icon { opacity: 0.4; font-size: 0.85rem; }
    .kpi-card .kpi-value { font-size: 1.4rem; line-height: 1.15; word-break: break-word; }
    .kpi-card .kpi-sub { font-size: 0.7rem; }

    /* Collection radial */
    .collection-ring {
        width: 128px; height: 128px; border-radius: 50%;
        background: conic-gradient(var(--ring-color, #198754) calc(var(--pct, 0) * 1%), var(--bs-secondary-bg, #e9ecef) 0);
        display: flex; align-items: center; justify-content: center; margin: 0 auto;
    }
    [data-bs-theme="dark"] .collection-ring { background: conic-gradient(var(--ring-color, #198754) calc(var(--pct, 0) * 1%), #313749 0); }
    .collection-ring-inner {
        width: 96px; height: 96px; border-radius: 50%; background: var(--bs-card-bg);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .collection-ring-inner .rv { font-size: 1.5rem; font-weight: 700; line-height: 1; }
    .collection-ring-inner .rl { font-size: 0.65rem; text-transform: uppercase; letter-spacing: .04em; }

    .stat-line { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; }
    .stat-line + .stat-line { border-top: 1px solid var(--bs-border-color-translucent); }

    .dash-chart-wrap { position: relative; width: 100%; }

    @media (max-width: 575.98px) {
        .kpi-card .kpi-value { font-size: 1.1rem; }
        .kpi-card .card-body { padding: 0.65rem 0.7rem !important; }
        .dash-section { margin-bottom: 1rem; }
    }
</style>
@endpush

@section('content')
{{-- ============================ SECTION 1 — HEADER ============================ --}}
<div class="row align-items-center g-2 mb-4">
    <div class="col">
        <h2 class="mb-0 h4">Dashboard</h2>
        <p class="text-body-secondary mb-0 small">Financial operations · {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="col-auto">
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            @can('create deposits')
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickDepositModal">
                    <i class="fas fa-plus-circle me-1"></i><span class="d-none d-sm-inline"> Quick</span> Deposit
                </button>
            @endcan
            @can('create expenses')
                <a href="{{ route('expenses.create') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Expense</a>
            @endcan
            @can('create investments')
                <a href="{{ route('investments.create') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus me-1"></i>Investment</a>
            @endcan
            @canany(['view deposits', 'view expenses', 'view investments'])
                <a href="{{ route('reports.deposits') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-chart-bar me-1"></i>Reports</a>
            @endcanany

            @canany(['view members', 'create deposits', 'view accounting'])
            <div class="dropdown">
                <button class="btn btn-phoenix-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis"></i><span class="d-none d-md-inline ms-1">More</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('view members')<li><a class="dropdown-item" href="{{ route('members.index') }}"><i class="fas fa-users me-2 text-body-tertiary"></i>View Members</a></li>@endcan
                    @can('view members')<li><a class="dropdown-item" href="{{ route('deposit-status') }}"><i class="fas fa-check-double me-2 text-body-tertiary"></i>Check Deposit Status</a></li>@endcan
                    @can('view accounting')<li><a class="dropdown-item" href="{{ route('accounting.reports.dashboard') }}"><i class="fas fa-book me-2 text-body-tertiary"></i>Accounting</a></li>@endcan
                </ul>
            </div>
            @endcanany
        </div>
    </div>
</div>

{{-- ============================ SECTION 2 — KPI ROW ============================ --}}
<div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 g-2 g-md-3 dash-section">
    <div class="col">
        <x-dashboard.kpi-card color="blue" icon="calculator" label="Expected This Month"
            :value="'৳ ' . number_format($expectedThisMonth, 0)"
            sub="members × shares × face value" />
    </div>
    <div class="col">
        <x-dashboard.kpi-card color="green" icon="hand-holding-dollar" label="Collected This Month"
            :value="'৳ ' . number_format($collectedThisMonth, 0)"
            :sub="$depositsPaid . ' of ' . ($depositsPaid + $depositsUnpaid) . ' members paid'">
            <x-slot:trend><x-dashboard.trend :value="$depositChange" suffix="" /></x-slot:trend>
        </x-dashboard.kpi-card>
    </div>
    <div class="col">
        <x-dashboard.kpi-card color="red" icon="triangle-exclamation" label="Outstanding Due"
            :value="'৳ ' . number_format($outstandingDue, 0)"
            :sub="$depositsUnpaid . ' unpaid member' . ($depositsUnpaid === 1 ? '' : 's')" />
    </div>
    <div class="col">
        <x-dashboard.kpi-card color="info" icon="percent" label="Collection Rate"
            :value="number_format($collectionRate, 0) . '%'"
            sub="collected of expected" />
    </div>
    <div class="col">
        <x-dashboard.kpi-card color="dark" icon="wallet" label="Net Fund Position"
            :value="'৳ ' . number_format($netPosition, 0)"
            sub="deposits − expenses" />
    </div>
    <div class="col">
        <x-dashboard.kpi-card color="orange" icon="chart-line" label="Total Invested"
            :value="'৳ ' . number_format($totalInvested, 0)"
            :sub="$activeInvestments . ' active investment' . ($activeInvestments === 1 ? '' : 's')" />
    </div>
</div>

{{-- ===================== SECTION 3 — COLLECTION CONTROL CENTER ===================== --}}
<x-dashboard.section-title title="Monthly Collection — {{ now()->format('F Y') }}" subtitle="Track, act on, and forecast this month's deposit collection" />
<div class="row g-3 dash-section">
    {{-- 3A. Collection Status --}}
    <div class="col-lg-4">
        <div class="card h-100" style="border-top: 3px solid #198754 !important;">
            <div class="card-body">
                <h6 class="dash-title mb-3">Collection Status</h6>
                @php $ringColor = $collectionRate >= 75 ? '#198754' : ($collectionRate >= 40 ? '#fd7e14' : '#dc3545'); @endphp
                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-3">
                    {{-- Left (top on mobile): radial chart + amount --}}
                    <div class="text-center flex-shrink-0">
                        <div class="collection-ring" style="--pct: {{ round($collectionRate) }}; --ring-color: {{ $ringColor }};">
                            <div class="collection-ring-inner">
                                <span class="rv" style="color: {{ $ringColor }};">{{ number_format($collectionRate, 0) }}%</span>
                                <span class="rl text-body-secondary">collected</span>
                            </div>
                        </div>
                        <div class="mt-2 lh-sm">
                            <div class="fw-bold">৳ {{ number_format($collectedThisMonth, 0) }}</div>
                            <div class="text-body-secondary small">of ৳ {{ number_format($expectedThisMonth, 0) }}</div>
                        </div>
                    </div>
                    {{-- Right: summary --}}
                    <div class="flex-fill">
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-circle-check text-success me-1"></span>Paid members</span>
                            <span class="fw-bold">{{ $depositsPaid }}</span>
                        </div>
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-circle-xmark {{ $depositsUnpaid > 0 ? 'text-danger' : 'text-body-tertiary' }} me-1"></span>Unpaid members</span>
                            <span class="fw-bold {{ $depositsUnpaid > 0 ? 'text-danger' : '' }}">{{ $depositsUnpaid }}</span>
                        </div>
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-triangle-exclamation text-warning me-1"></span>Outstanding due</span>
                            <span class="fw-bold">৳ {{ number_format($outstandingDue, 0) }}</span>
                        </div>
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-arrow-trend-up text-body-tertiary me-1"></span>vs last month</span>
                            <x-dashboard.trend :value="$depositChange" suffix="" />
                        </div>
                    </div>
                </div>
                @canany(['create deposits', 'view members'])
                <div class="d-flex gap-2 mt-3">
                    @can('create deposits')
                        <a href="{{ route('deposits.create') }}" class="btn btn-success btn-sm flex-fill"><i class="fas fa-plus me-1"></i>Record</a>
                    @endcan
                    @can('view members')
                        <a href="{{ route('deposit-status') }}" class="btn btn-outline-secondary btn-sm flex-fill">Unpaid list</a>
                    @endcan
                </div>
                @endcanany
            </div>
        </div>
    </div>

    {{-- 3B. Unpaid Members --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center py-2">
                <h6 class="dash-title mb-0">Unpaid Members</h6>
                @can('view members')<a href="{{ route('deposit-status') }}" class="small text-decoration-none">View all →</a>@endcan
            </div>
            <div class="card-body p-0">
                @if(count($unpaidMembers))
                    <div class="table-responsive">
                        <table class="table table-sm table-hover fs-9 mb-0 align-middle">
                            <tbody>
                                @foreach($unpaidMembers as $um)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-truncate" style="max-width: 140px;">{{ $um['name'] }}</div>
                                        <div class="text-body-tertiary" style="font-size: .7rem;">
                                            {{ $um['last_paid'] ? 'Last paid ' . $um['last_paid'] : 'Never paid' }}
                                        </div>
                                    </td>
                                    <td class="text-end">৳ {{ number_format($um['due'], 0) }}</td>
                                    <td class="text-end pe-3">
                                        <span class="badge badge-phoenix {{ $um['overdue'] ? 'badge-phoenix-danger' : 'badge-phoenix-warning' }}">
                                            {{ $um['overdue'] ? 'Overdue' : 'Unpaid' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-dashboard.empty-state variant="success" icon="circle-check"
                        title="All members paid this month" message="Nothing outstanding right now." />
                @endif
            </div>
        </div>
    </div>

    {{-- 3C. Expected vs Received chart --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-body-tertiary py-2">
                <h6 class="dash-title mb-0">Expected vs Received</h6>
                <small class="text-body-secondary">Last 6 months</small>
            </div>
            <div class="card-body">
                <div class="dash-chart-wrap" style="height: 240px;">
                    <canvas id="expectedVsReceivedChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================ SECTION 4 — FINANCIAL OVERVIEW ============================ --}}
<x-dashboard.section-title title="Financial Overview" subtitle="Cash flow and where the money currently sits" />
<div class="row g-3 dash-section">
    {{-- 4A. Deposit / Cash-flow trend --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-body-tertiary py-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="dash-title mb-0">Cash Flow Trend</h6>
                    <small class="text-body-secondary">Deposits vs expenses · last 12 months</small>
                </div>
            </div>
            <div class="card-body">
                <div class="dash-chart-wrap" style="height: 280px;">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 4B. Fund breakdown --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-body-tertiary py-2">
                <h6 class="dash-title mb-0">Fund Breakdown</h6>
                <small class="text-body-secondary">Where the organization's money sits</small>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-6 col-lg-5">
                        @if(count($investmentDistribution))
                            <div class="dash-chart-wrap mx-auto" style="max-width: 160px; height: 160px;">
                                <canvas id="fundAllocationChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-body-tertiary py-3">
                                <span class="fas fa-chart-pie fs-1 opacity-25 d-block mb-1"></span>
                                <small>No investments yet</small>
                            </div>
                        @endif
                    </div>
                    <div class="col-6 col-lg-7">
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-circle text-success me-1" style="font-size:.5rem;"></span>Cash available</span>
                            <span class="fw-bold">৳ {{ number_format($cashAvailable, 0) }}</span>
                        </div>
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-circle text-primary me-1" style="font-size:.5rem;"></span>Total deposits</span>
                            <span class="fw-bold">৳ {{ number_format($totalDeposits, 0) }}</span>
                        </div>
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-circle text-warning me-1" style="font-size:.5rem;"></span>Total invested</span>
                            <span class="fw-bold">৳ {{ number_format($totalInvested, 0) }}</span>
                        </div>
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-circle text-info me-1" style="font-size:.5rem;"></span>Total returns</span>
                            <span class="fw-bold text-success">৳ {{ number_format($totalReturns, 0) }}</span>
                        </div>
                        <div class="stat-line">
                            <span class="text-body-secondary small"><span class="fas fa-circle text-danger me-1" style="font-size:.5rem;"></span>Total expenses</span>
                            <span class="fw-bold">৳ {{ number_format($totalExpenses, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== SECTION 5 — ACTIVITY + INVESTMENT SUMMARY ===================== --}}
<div class="row g-3 dash-section">
    {{-- 5A. Recent activity --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-body-tertiary py-2 d-flex justify-content-between align-items-center">
                <h6 class="dash-title mb-0">Recent Activity</h6>
            </div>
            <div class="card-body p-0">
                @if(count($recentActivity))
                    <div class="table-responsive">
                        <table class="table table-sm table-hover fs-9 mb-0 align-middle">
                            <tbody>
                                @foreach($recentActivity as $a)
                                <tr>
                                    <td class="ps-3" style="width: 34px;">
                                        <span class="fas fa-{{ $a['icon'] }} {{ $a['type'] === 'deposit' ? 'text-success' : ($a['type'] === 'expense' ? 'text-danger' : ($a['type'] === 'investment' ? 'text-warning' : 'text-info')) }}"></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $a['title'] }}</div>
                                        <div class="text-body-tertiary text-truncate" style="max-width: 260px; font-size:.72rem;">{{ $a['description'] }}</div>
                                    </td>
                                    <td class="text-end text-body-secondary" style="font-size:.72rem;">{{ \Illuminate\Support\Carbon::parse($a['date'])->format('d M') }}</td>
                                    <td class="text-end pe-3">
                                        @if($a['amount'] != 0)
                                            <span class="fw-bold {{ $a['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ $a['amount'] > 0 ? '+' : '−' }}৳{{ number_format(abs($a['amount']), 0) }}</span>
                                        @else
                                            <span class="text-body-tertiary">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-dashboard.empty-state icon="clock-rotate-left" title="No recent activity" />
                @endif
            </div>
        </div>
    </div>

    {{-- 5B. Investment summary --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-body-tertiary py-2 d-flex justify-content-between align-items-center">
                <h6 class="dash-title mb-0">Investment Summary</h6>
                @can('view investments')<a href="{{ route('investments.index') }}" class="small text-decoration-none">View all →</a>@endcan
            </div>
            <div class="card-body p-0">
                @if(count($investmentPerformance))
                    <div class="d-flex justify-content-around text-center py-3 border-bottom">
                        <div><div class="fw-bold">{{ $activeInvestments }}</div><small class="text-body-secondary">Active</small></div>
                        <div><div class="fw-bold">৳{{ number_format($totalInvested, 0) }}</div><small class="text-body-secondary">Invested</small></div>
                        <div><div class="fw-bold text-success">৳{{ number_format($totalReturns, 0) }}</div><small class="text-body-secondary">Returns</small></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover fs-9 mb-0 align-middle">
                            <tbody>
                                @foreach($investmentPerformance as $inv)
                                <tr>
                                    <td class="ps-3 text-truncate" style="max-width: 160px;">{{ $inv['name'] }}</td>
                                    <td class="text-end text-body-secondary">৳{{ number_format($inv['invested'], 0) }}</td>
                                    <td class="text-end pe-3"><span class="badge badge-phoenix badge-phoenix-success">+৳{{ number_format($inv['returns'], 0) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-dashboard.empty-state icon="chart-line" title="No investments yet"
                        message="Active investments will appear here." />
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================ SECTION 6 — ADDITIONAL INSIGHTS ============================ --}}
<div class="row g-4 dash-section">
    {{-- ===================== Additional Insights (left) ===================== --}}
    <div class="col-md-6 col-sm-12 d-flex flex-column">
        <x-dashboard.section-title title="Additional Insights" subtitle="Members, shareholders and recent deposits" />
        <div class="card card-quiet flex-fill">
            <div class="card-header bg-transparent border-0 pb-0">
                <ul class="nav nav-underline" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-members" type="button">Members</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-shareholders" type="button">Top Shareholders</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-deposits" type="button">Recent Deposits</button></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    {{-- New Members --}}
                    <div class="tab-pane fade show active" id="tab-members" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <div class="text-center p-3 rounded bg-body-tertiary h-100 d-flex flex-column justify-content-center">
                                    <div class="h3 mb-0 fw-bold text-primary">{{ $totalMembers }}</div>
                                    <small class="text-body-secondary">Active members</small>
                                </div>
                            </div>
                            <div class="col-md-7">
                                @if($recentMembers->isNotEmpty())
                                    <table class="table table-sm fs-9 mb-0 align-middle">
                                        <thead><tr class="text-body-secondary"><th>New member</th><th class="text-end">Joined</th></tr></thead>
                                        <tbody>
                                            @foreach($recentMembers as $m)
                                            <tr><td>{{ $m->name }}</td><td class="text-end text-body-secondary">{{ $m->created_at->format('d M Y') }}</td></tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <x-dashboard.empty-state icon="users" title="No members yet" />
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Top Shareholders --}}
                    <div class="tab-pane fade" id="tab-shareholders" role="tabpanel">
                        @if(count($topShareholders))
                            <table class="table table-sm fs-9 mb-0 align-middle">
                                <thead><tr class="text-body-secondary"><th>Member</th><th class="text-end">Shares</th><th class="text-end">Ownership</th></tr></thead>
                                <tbody>
                                    @foreach($topShareholders as $sh)
                                    <tr>
                                        <td>{{ $sh['name'] }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($sh['shares']) }}</td>
                                        <td class="text-end"><span class="badge badge-phoenix badge-phoenix-primary">{{ number_format($sh['percentage'], 1) }}%</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <x-dashboard.empty-state icon="crown" title="No shareholders yet" />
                        @endif
                    </div>
                    {{-- Recent Deposits --}}
                    <div class="tab-pane fade" id="tab-deposits" role="tabpanel">
                        @if(count($lastDeposits))
                            <table class="table table-sm fs-9 mb-0 align-middle">
                                <thead><tr class="text-body-secondary"><th>Member</th><th class="text-end">Amount</th><th class="text-end">Date</th></tr></thead>
                                <tbody>
                                    @foreach($lastDeposits as $d)
                                    <tr><td>{{ $d['name'] }}</td><td class="text-end fw-semibold">৳{{ number_format($d['amount'], 0) }}</td><td class="text-end text-body-secondary">{{ $d['date'] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <x-dashboard.empty-state icon="wallet" title="No deposits yet" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Loans & Repayments (right) ===================== --}}
    <div class="col-md-6 col-sm-12 d-flex flex-column">
        <x-dashboard.section-title title="Loans & Repayments" subtitle="Money lent out and what's owed back">
            @can('view loans')<a href="{{ route('loans.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none">View all →</a>@endcan
        </x-dashboard.section-title>
        <div class="card card-quiet flex-fill">
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="text-center p-2 rounded bg-body-tertiary h-100">
                            <div class="h4 mb-0 fw-bold text-primary">{{ $loanStats['active'] }}</div>
                            <small class="text-body-secondary">Active loans</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 rounded bg-body-tertiary h-100">
                            <div class="fw-bold text-body-emphasis" style="font-size:1.05rem;">৳{{ number_format($loanStats['outstanding'], 0) }}</div>
                            <small class="text-body-secondary">Outstanding</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 rounded bg-body-tertiary h-100">
                            <div class="h4 mb-0 fw-bold {{ $loanStats['overdue'] > 0 ? 'text-danger' : 'text-body-tertiary' }}">{{ $loanStats['overdue'] }}</div>
                            <small class="text-body-secondary">Overdue</small>
                        </div>
                    </div>
                </div>

                @if($loanWatch->isNotEmpty())
                    <table class="table table-sm fs-9 mb-0 align-middle">
                        <thead><tr class="text-body-secondary"><th>Borrower</th><th class="text-end">Outstanding</th><th class="text-end">Due</th></tr></thead>
                        <tbody>
                            @foreach($loanWatch as $l)
                            <tr>
                                <td class="text-truncate" style="max-width:150px;">{{ $l['name'] }}@if($l['overdue'])<span class="badge badge-phoenix badge-phoenix-danger ms-1">Overdue</span>@endif</td>
                                <td class="text-end fw-semibold">৳{{ number_format($l['outstanding'], 0) }}</td>
                                <td class="text-end text-body-secondary">{{ $l['due'] ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-body-secondary fs-9 mt-2 pt-2 border-top">
                        Disbursed ৳{{ number_format($loanStats['disbursed'], 0) }} &middot; Repaid ৳{{ number_format($loanStats['repaid'], 0) }}
                    </div>
                @else
                    <x-dashboard.empty-state icon="hand-holding-dollar" title="No active loans" message="Outstanding loans and repayments will appear here." />
                @endif
            </div>
        </div>
    </div>
</div>

@can('create deposits')
{{-- ============================ QUICK DEPOSIT MODAL ============================ --}}
<div class="modal fade" id="quickDepositModal" tabindex="-1" role="dialog" aria-labelledby="quickDepositLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="quickDepositLabel">Quick Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form id="quickDepositForm">
                    @csrf
                    <div class="mb-3">
                        <label for="memberSelect" class="form-label">Select Member <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="memberSelect" name="member_id" required>
                            <option value="">Choose a member...</option>
                            @foreach($activeMembersCollection as $qdMember)
                                <option value="{{ $qdMember->id }}" data-monthly-amount="{{ $qdMember->getCalculatedMonthlyDepositAmount() }}">{{ $qdMember->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monthSelect" class="form-label">Month <span class="text-danger">*</span></label>
                        <input type="month" class="form-control form-control-sm" id="monthSelect" name="month" value="{{ date('Y-m') }}" required>
                    </div>
                    <div id="quickDepositWarning" class="alert alert-warning py-2 px-3 d-none" role="alert" style="font-size: 0.85rem;"></div>
                    <div class="mb-3">
                        <label for="depositAmount" class="form-label">Deposit Amount <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">৳</span>
                            <input type="number" class="form-control" id="depositAmount" name="amount" readonly placeholder="0">
                        </div>
                        <small class="text-body-tertiary">Auto-calculated from member's shares</small>
                    </div>
                    <div class="mb-3">
                        <label for="transactionId" class="form-label">Transaction ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="transactionId" name="transaction_id" placeholder="e.g., TXN-2024-001" required>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="collapse" data-bs-target="#advancedOptions">
                            <i class="fas fa-chevron-down"></i> Advanced Options
                        </button>
                    </div>
                    <div class="collapse mb-3" id="advancedOptions">
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select form-select-sm" id="paymentMethod" name="payment_method_id">
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" @selected($method->code === 'bank_transfer')>{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="3" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check-circle"></i> Record Deposit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
(function () {
    const money = (v) => '৳' + Number(v).toLocaleString('en-BD');
    const gridColor = 'rgba(128,128,128,0.12)';
    Chart.defaults.font.size = 11;

    // Expected vs Received (bar)
    new Chart(document.getElementById('expectedVsReceivedChart'), {
        type: 'bar',
        data: {
            labels: @json($depositExpectedVsReceived['months']),
            datasets: [
                { label: 'Expected', data: @json($depositExpectedVsReceived['expected']), backgroundColor: '#0d6efd', borderRadius: 4, maxBarThickness: 22 },
                { label: 'Received', data: @json($depositExpectedVsReceived['received']), backgroundColor: '#198754', borderRadius: 4, maxBarThickness: 22 },
            ]
        },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, padding: 12 } },
                tooltip: { callbacks: { label: (c) => c.dataset.label + ': ' + money(c.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: (v) => v >= 1000 ? (v/1000)+'k' : v } }, x: { grid: { display: false } } } }
    });

    // Cash flow (deposits vs expenses, line)
    new Chart(document.getElementById('cashFlowChart'), {
        type: 'line',
        data: {
            labels: @json(array_map(fn($m) => \Illuminate\Support\Str::of($m)->before(' '), $depositTrend['months'])),
            datasets: [
                { label: 'Deposits', data: @json($depositTrend['totals']), borderColor: '#198754', backgroundColor: 'rgba(25,135,84,0.10)', borderWidth: 2, fill: true, tension: 0.35, pointRadius: 2 },
                { label: 'Expenses', data: @json($expenseTrend['totals']), borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.06)', borderWidth: 2, fill: true, tension: 0.35, pointRadius: 2 },
            ]
        },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, padding: 12 } },
                tooltip: { callbacks: { label: (c) => c.dataset.label + ': ' + money(c.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: (v) => v >= 1000 ? (v/1000)+'k' : v } }, x: { grid: { display: false } } } }
    });

    // Fund allocation (doughnut) — only if investments exist
    const fundEl = document.getElementById('fundAllocationChart');
    if (fundEl) {
        new Chart(fundEl, {
            type: 'doughnut',
            data: {
                labels: @json(array_column($investmentDistribution, 'type')),
                datasets: [{ data: @json(array_column($investmentDistribution, 'amount')),
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#fd7e14', '#6f42c1', '#20c997'], borderColor: 'var(--bs-card-bg)', borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '62%',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => c.label + ': ' + money(c.raw) } } } }
        });
    }
})();
</script>

@can('create deposits')
<script>
(function () {
    const memberSelect = document.getElementById('memberSelect');
    const monthSelect = document.getElementById('monthSelect');
    const depositAmount = document.getElementById('depositAmount');
    const quickDepositForm = document.getElementById('quickDepositForm');
    const quickDepositWarning = document.getElementById('quickDepositWarning');
    if (!quickDepositForm) return;
    const quickSubmitBtn = quickDepositForm.querySelector('button[type="submit"]');

    function showWarning(message, type = 'warning') {
        quickDepositWarning.textContent = message;
        quickDepositWarning.classList.remove('d-none', 'alert-warning', 'alert-danger');
        quickDepositWarning.classList.add(type === 'danger' ? 'alert-danger' : 'alert-warning');
    }
    function clearWarning() { quickDepositWarning.classList.add('d-none'); quickDepositWarning.textContent = ''; }

    memberSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const amt = this.value ? opt.dataset.monthlyAmount : '';
        depositAmount.value = amt ? parseFloat(amt).toFixed(2) : '';
        checkDuplicateMonth();
    });
    monthSelect.addEventListener('change', checkDuplicateMonth);

    let checkController = null;
    async function checkDuplicateMonth() {
        const memberId = memberSelect.value, month = monthSelect.value;
        clearWarning(); quickSubmitBtn.disabled = false;
        if (!memberId || !month) return;
        if (checkController) checkController.abort();
        checkController = new AbortController();
        try {
            const params = new URLSearchParams({ member_id: memberId, month });
            const res = await fetch(`/api/deposits/check-month?${params}`, { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, signal: checkController.signal });
            const data = await res.json();
            if (res.ok && data.exists) {
                showWarning(`${memberSelect.options[memberSelect.selectedIndex].text.trim()} already has a deposit for ${data.month_label}.`);
                quickSubmitBtn.disabled = true;
            }
        } catch (e) { /* server still enforces */ }
    }

    quickDepositForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const original = quickSubmitBtn.innerHTML;
        quickSubmitBtn.disabled = true; quickSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        try {
            const res = await fetch('/api/deposits/quick', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: new FormData(this) });
            const data = await res.json();
            if (res.ok) {
                clearWarning();
                const a = document.createElement('div');
                a.className = 'alert alert-success alert-dismissible fade show';
                a.innerHTML = '<strong>Success!</strong> Deposit recorded. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.body.prepend(a);
                this.reset();
                bootstrap.Modal.getInstance(document.getElementById('quickDepositModal')).hide();
                setTimeout(() => location.reload(), 1200);
            } else { showWarning(data.message || 'Failed to record deposit', 'danger'); }
        } catch (err) { showWarning('Error: ' + err.message, 'danger'); }
        finally { quickSubmitBtn.innerHTML = original; quickSubmitBtn.disabled = !quickDepositWarning.classList.contains('d-none') && quickDepositWarning.classList.contains('alert-warning'); }
    });

    document.getElementById('quickDepositModal').addEventListener('hidden.bs.modal', function () { clearWarning(); quickSubmitBtn.disabled = false; });
})();
</script>
@endcan
@endsection
