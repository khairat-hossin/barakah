@extends('layouts.phoenix')

@section('title', 'Executive Dashboard | Barakah')

@section('content')
<div class="mb-9">
    <!-- Page Header -->
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h1 class="mb-0 h2">Executive Dashboard</h1>
            <p class="text-body-secondary">Real-time visibility into BARAKAH's organizational performance</p>
        </div>
        <div class="col-auto">
            <div class="input-group">
                <input class="form-control form-control-sm" type="date" value="{{ date('Y-m-d') }}" disabled>
            </div>
        </div>
    </div>

    <!-- Section 1: Executive Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Members Card -->
        <div class="col-md-6 col-lg-4 col-xl-2">
            <div class="card h-100 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="text-body-secondary small fw-semibold">Total Members</h6>
                        <span class="fas fa-users text-primary"></span>
                    </div>
                    <p class="card-text fs-4 fw-bold mb-1">{{ $totalMembers }}</p>
                    <small class="text-body-secondary">{{ $activeMembers }} Active</small>
                    <p class="mb-0 text-success small mt-2"><span class="fas fa-arrow-up"></span> {{ number_format($memberGrowth, 1) }}% this month</p>
                </div>
            </div>
        </div>

        <!-- Total Shares Card -->
        <div class="col-md-6 col-lg-4 col-xl-2">
            <div class="card h-100 border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="text-body-secondary small fw-semibold">Total Shares</h6>
                        <span class="fas fa-chart-pie text-info"></span>
                    </div>
                    <p class="card-text fs-4 fw-bold mb-1">{{ number_format($totalShares) }}</p>
                    <small class="text-body-secondary">{{ number_format($allocatedShares) }} Allocated</small>
                    <p class="mb-0 text-warning small mt-2">{{ number_format($availableShares) }} Available</p>
                </div>
            </div>
        </div>

        <!-- Monthly Deposits Card -->
        <div class="col-md-6 col-lg-4 col-xl-2">
            <div class="card h-100 border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="text-body-secondary small fw-semibold">Monthly Deposits</h6>
                        <span class="fas fa-wallet text-success"></span>
                    </div>
                    <p class="card-text fs-4 fw-bold mb-1">{{ number_format($monthlyDeposits, 2) }}</p>
                    <small class="text-body-secondary">This month</small>
                    <p class="mb-0 text-success small mt-2"><span class="fas fa-arrow-up"></span> {{ number_format($depositChange, 1) }}%</p>
                </div>
            </div>
        </div>

        <!-- Investments Card -->
        <div class="col-md-6 col-lg-4 col-xl-2">
            <div class="card h-100 border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="text-body-secondary small fw-semibold">Investments</h6>
                        <span class="fas fa-chart-line text-warning"></span>
                    </div>
                    <p class="card-text fs-4 fw-bold mb-1">{{ number_format($totalInvested, 2) }}</p>
                    <small class="text-body-secondary">{{ $activeInvestments }} Active</small>
                    <p class="mb-0 text-success small mt-2">₱{{ number_format($investmentReturns, 2) }} Returns</p>
                </div>
            </div>
        </div>

        <!-- Monthly Expenses Card -->
        <div class="col-md-6 col-lg-4 col-xl-2">
            <div class="card h-100 border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="text-body-secondary small fw-semibold">Monthly Expenses</h6>
                        <span class="fas fa-receipt text-danger"></span>
                    </div>
                    <p class="card-text fs-4 fw-bold mb-1">{{ number_format($monthlyExpenses, 2) }}</p>
                    <small class="text-body-secondary">This month</small>
                    <p class="mb-0 text-danger small mt-2"><span class="fas fa-arrow-up"></span> {{ number_format($expenseChange, 1) }}%</p>
                </div>
            </div>
        </div>

        <!-- Net Position Card -->
        <div class="col-md-6 col-lg-4 col-xl-2">
            <div class="card h-100 border-start border-secondary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="text-body-secondary small fw-semibold">Net Position</h6>
                        <span class="fas fa-chart-bar text-secondary"></span>
                    </div>
                    <p class="card-text fs-4 fw-bold mb-1 {{ $netPosition >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netPosition, 2) }}</p>
                    <small class="text-body-secondary">Assets - Liabilities</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Deposit Trend (12 Months)</h5></div>
                <div class="card-body"><canvas id="depositChart" height="80"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Expense Trend (12 Months)</h5></div>
                <div class="card-body"><canvas id="expenseChart" height="80"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Investment Section -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Investment Distribution</h5></div>
                <div class="card-body"><canvas id="investmentChart" height="100"></canvas></div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Investment Performance</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-body-tertiary"><tr><th>Investment</th><th class="text-end">Capital</th><th class="text-end">Returns</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @forelse($investmentPerformance as $inv)
                            <tr><td>{{ $inv['name'] }}</td><td class="text-end">{{ number_format($inv['invested'], 2) }}</td><td class="text-end text-success">{{ number_format($inv['returns'], 2) }}</td><td class="text-end fw-bold">{{ number_format($inv['invested'] + $inv['returns'], 2) }}</td></tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-5">No active investments</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Analytics & Recent Members -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Top Shareholders</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-body-tertiary"><tr><th>Member</th><th class="text-end">Shares</th><th class="text-end">%</th><th>Joined</th></tr></thead>
                        <tbody>
                            @forelse($topShareholders as $sh)
                            <tr><td>{{ $sh['name'] }}</td><td class="text-end">{{ number_format($sh['shares']) }}</td><td class="text-end">{{ number_format($sh['percentage'], 1) }}%</td><td class="small text-body-secondary">{{ $sh['joinedAt'] }}</td></tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-5">No shareholders</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Recent Members</h5></div>
                <div class="card-body">
                    @forelse($recentMembers as $member)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="avatar avatar-l me-2"><span class="avatar-initials rounded-circle bg-primary text-white">{{ strtoupper(substr($member->name, 0, 2)) }}</span></div>
                        <div class="flex-grow-1"><h6 class="mb-1">{{ $member->name }}</h6><small class="text-body-secondary">Joined {{ $member->created_at->format('M d, Y') }}</small></div>
                        <span class="badge bg-success">Active</span>
                    </div>
                    @empty
                    <p class="text-muted text-center py-5">No recent members</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Health -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3"><div class="card bg-light"><div class="card-body"><h6 class="card-title small text-body-secondary">Cash Available</h6><p class="card-text fs-5 fw-bold text-success">{{ number_format($cashAvailable, 2) }}</p></div></div></div>
        <div class="col-md-6 col-lg-3"><div class="card bg-light"><div class="card-body"><h6 class="card-title small text-body-secondary">Total Deposits</h6><p class="card-text fs-5 fw-bold text-primary">{{ number_format($totalDeposits, 2) }}</p></div></div></div>
        <div class="col-md-6 col-lg-3"><div class="card bg-light"><div class="card-body"><h6 class="card-title small text-body-secondary">Total Investments</h6><p class="card-text fs-5 fw-bold text-warning">{{ number_format($totalInvested, 2) }}</p></div></div></div>
        <div class="col-md-6 col-lg-3"><div class="card bg-light"><div class="card-body"><h6 class="card-title small text-body-secondary">Total Returns</h6><p class="card-text fs-5 fw-bold text-success">{{ number_format($totalReturns, 2) }}</p></div></div></div>
    </div>

    <!-- Recent Activity -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Recent Activity</h5></div>
        <div class="card-body">
            @forelse($recentActivity as $activity)
            <div class="d-flex mb-3 pb-3 border-bottom">
                <div class="timeline-marker bg-{{ $activity['type'] === 'deposit' ? 'success' : ($activity['type'] === 'expense' ? 'danger' : 'primary') }} me-3" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><span class="fas fa-{{ $activity['icon'] }} text-white"></span></div>
                <div class="flex-grow-1"><h6 class="mb-1">{{ $activity['title'] }}</h6><small class="text-body-secondary">{{ $activity['description'] }}</small><br><small class="text-muted">{{ $activity['date']->format('M d, Y H:i') }}</small></div>
                <div class="text-end"><p class="mb-0 fw-bold {{ $activity['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ $activity['amount'] > 0 ? '+' : '' }}{{ number_format($activity['amount'], 2) }}</p></div>
            </div>
            @empty
            <p class="text-muted text-center py-5">No recent activity</p>
            @endforelse
        </div>
    </div>

    <!-- Pending Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4"><div class="card border-start border-warning border-3"><div class="card-body"><div class="d-flex align-items-center justify-content-between"><div><h6 class="text-body-secondary small mb-1">Pending Expenses</h6><p class="card-text fs-5 fw-bold">{{ $pendingExpenses }}</p></div><span class="fas fa-receipt fa-2x text-warning opacity-50"></span></div><a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-warning mt-2">Review</a></div></div></div>
        <div class="col-md-6 col-lg-4"><div class="card border-start border-info border-3"><div class="card-body"><div class="d-flex align-items-center justify-content-between"><div><h6 class="text-body-secondary small mb-1">Pending Investments</h6><p class="card-text fs-5 fw-bold">{{ $pendingInvestments }}</p></div><span class="fas fa-chart-line fa-2x text-info opacity-50"></span></div><a href="{{ route('investments.index') }}" class="btn btn-sm btn-outline-info mt-2">Review</a></div></div></div>
        <div class="col-md-6 col-lg-4"><div class="card border-start border-primary border-3"><div class="card-body"><div class="d-flex align-items-center justify-content-between"><div><h6 class="text-body-secondary small mb-1">Total Members</h6><p class="card-text fs-5 fw-bold">{{ $totalMembers }}</p></div><span class="fas fa-users fa-2x text-primary opacity-50"></span></div><a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-primary mt-2">View All</a></div></div></div>
    </div>

    <!-- Quick Actions -->
    <div class="card bg-light border-0"><div class="card-body"><h5 class="mb-3">Quick Actions</h5><div class="row g-2"><div class="col-auto"><a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary"><span class="fas fa-plus me-1"></span>Add Expense</a></div><div class="col-auto"><a href="{{ route('investments.create') }}" class="btn btn-sm btn-success"><span class="fas fa-plus me-1"></span>Create Investment</a></div><div class="col-auto"><a href="{{ route('members.index') }}" class="btn btn-sm btn-info"><span class="fas fa-users me-1"></span>View Members</a></div><div class="col-auto"><a href="{{ route('accounting.reports.dashboard') }}" class="btn btn-sm btn-warning"><span class="fas fa-chart-bar me-1"></span>View Reports</a></div></div></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const depositCtx = document.getElementById('depositChart').getContext('2d');
    new Chart(depositCtx, {type: 'line', data: {labels: @json($depositTrend['months']), datasets: [{label: 'Monthly Deposits', data: @json($depositTrend['totals']), borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}});

    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {type: 'line', data: {labels: @json($expenseTrend['months']), datasets: [{label: 'Monthly Expenses', data: @json($expenseTrend['totals']), borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}});

    const investmentCtx = document.getElementById('investmentChart').getContext('2d');
    new Chart(investmentCtx, {type: 'doughnut', data: {labels: @json(array_column($investmentDistribution, 'type')), datasets: [{data: @json(array_column($investmentDistribution, 'amount')), backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#fd7e14', '#6f42c1', '#20c997'], borderColor: '#fff', borderWidth: 2}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {position: 'bottom'}}}});
</script>
@endsection
