@extends('layouts.phoenix')

@section('title', 'Executive Dashboard | Barakah')

@section('content')
<style>
.kpi-card { background: linear-gradient(135deg, var(--card-bg, #fff) 0%, #fafbfc 100%); }
.kpi-value { font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px; }
.trend-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; }
.trend-up { background-color: rgba(25, 135, 84, 0.15); color: #157347; }
.trend-down { background-color: rgba(220, 53, 69, 0.15); color: #842029; }
.section-header { font-size: 0.9375rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--bs-body-color); }
</style>

<div class="mb-9">
    <!-- Page Header -->
    <div class="row align-items-center justify-content-between mb-5">
        <div class="col">
            <h1 class="mb-1 h3">Executive Dashboard</h1>
            <p class="text-body-secondary mb-0">Real-time organizational performance overview</p>
        </div>
        <div class="col-auto">
            <span class="text-body-secondary small">📅 As of {{ date('M d, Y') }}</span>
        </div>
    </div>

    <!-- KPI Cards - Compact Style -->
    <div class="row g-2 mb-5">
        <!-- Members -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card" style="border-left: 4px solid #198754 !important;">
                <div class="card-body p-3">
                    <small class="text-body-secondary d-block fw-semibold mb-2">Members</small>
                    <h5 class="mb-1">{{ $totalMembers }}</h5>
                    <small class="text-body-secondary">{{ $activeMembers }} active</small>
                    @if($memberGrowth !== 0)
                        <div class="mt-1"><span class="badge {{ $memberGrowth > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $memberGrowth > 0 ? '↑' : '↓' }} {{ abs($memberGrowth) }}%</span></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body p-3">
                    <small class="text-body-secondary d-block fw-semibold mb-2">Share Capital</small>
                    <h5 class="mb-1">{{ number_format($totalShares) }}</h5>
                    <small class="text-body-secondary">{{ number_format($allocatedShares) }} allocated</small>
                    <div class="mt-1"><span class="badge bg-light text-dark" style="font-size: 0.7rem;">{{ number_format($availableShares) }} free</span></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body p-3">
                    <small class="text-body-secondary d-block fw-semibold mb-2">Monthly Deposits</small>
                    <h5 class="mb-1 text-primary">৳{{ number_format($monthlyDeposits, 0) }}</h5>
                    <small class="text-body-secondary">This month</small>
                    @if($depositChange !== 0)
                        <div class="mt-1"><span class="badge {{ $depositChange > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $depositChange > 0 ? '↑' : '↓' }} {{ abs($depositChange) }}%</span></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body p-3">
                    <small class="text-body-secondary d-block fw-semibold mb-2">Investments</small>
                    <h5 class="mb-1 text-warning">৳{{ number_format($totalInvested, 0) }}</h5>
                    <small class="text-body-secondary">{{ $activeInvestments }} active</small>
                    <div class="mt-1"><span class="badge bg-warning-subtle text-warning">+৳{{ number_format($investmentReturns, 0) }}</span></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body p-3">
                    <small class="text-body-secondary d-block fw-semibold mb-2">Monthly Expenses</small>
                    <h5 class="mb-1 text-danger">৳{{ number_format($monthlyExpenses, 0) }}</h5>
                    <small class="text-body-secondary">This month</small>
                    @if($expenseChange !== 0)
                        <div class="mt-1"><span class="badge {{ $expenseChange > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">{{ $expenseChange > 0 ? '↑' : '↓' }} {{ abs($expenseChange) }}%</span></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card" style="border-left: 4px solid {{ $netPosition >= 0 ? '#198754' : '#dc3545' }} !important;">
                <div class="card-body p-3">
                    <small class="text-body-secondary d-block fw-semibold mb-2">Net Position</small>
                    <h5 class="mb-1 {{ $netPosition >= 0 ? 'text-success' : 'text-danger' }}">৳{{ number_format(abs($netPosition), 0) }}</h5>
                    <small class="text-body-secondary">Balance</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Deposits & Deposit Count Chart -->
    <div class="row g-2 mb-5">
        <div class="col-12 col-lg-6">
            <a href="{{ route('deposit-status') }}" class="card text-decoration-none" style="border-left: 4px solid #6f42c1 !important; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow=''">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <small class="text-body-secondary d-block fw-semibold mb-2">Member Deposits</small>
                            <h5 class="mb-1 text-primary">{{ $depositsPaid }}/{{ $depositsPaid + $depositsUnpaid }} Members</h5>
                            <small class="text-body-secondary">Paid this month</small>
                        </div>
                    </div>
                    <div class="mt-3" style="height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden;">
                        <div style="width: {{ $depositsPaid + $depositsUnpaid > 0 ? ($depositsPaid / ($depositsPaid + $depositsUnpaid) * 100) : 0 }}%; height: 100%; background: #198754;"></div>
                    </div>
                    <div class="mt-3 d-flex gap-4">
                        <div>
                            <small class="text-success d-block">✓ Paid</small>
                            <strong class="text-success">{{ $depositsPaid }}</strong>
                        </div>
                        <div>
                            <small class="text-danger d-block">✗ Unpaid</small>
                            <strong class="text-danger">{{ $depositsUnpaid }}</strong>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-primary fw-semibold">View Details <span class="fas fa-arrow-right fa-xs ms-1"></span></small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column">
                    <h6 class="section-header mb-3">📊 Deposits Last 6 Months</h6>
                    <div style="flex: 1; min-height: 0;">
                        <canvas id="depositCountChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Charts -->
    <div class="row g-3 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">💰 Deposit Trend</h6>
                    <canvas id="depositChart" height="80"></canvas>
                    <small class="text-body-secondary d-block mt-2">Last 12 months</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">📊 Expense Trend</h6>
                    <canvas id="expenseChart" height="80"></canvas>
                    <small class="text-body-secondary d-block mt-2">Last 12 months</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Section -->
    <div class="row g-3 mb-5">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-4">💼 Investment Allocation</h6>
                    <canvas id="investmentChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">📈 Investment Performance</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Investment</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Capital</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Returns</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investmentPerformance as $inv)
                                <tr>
                                    <td><span class="small">{{ $inv['name'] }}</span></td>
                                    <td class="text-end"><small class="text-body-secondary">৳{{ number_format($inv['invested'], 0) }}</small></td>
                                    <td class="text-end"><span class="badge bg-success-subtle text-success-emphasis">+৳{{ number_format($inv['returns'], 0) }}</span></td>
                                    <td class="text-end"><strong>৳{{ number_format($inv['invested'] + $inv['returns'], 0) }}</strong></td>
                                </tr>
                                @empty
                                <tr class="text-muted">
                                    <td colspan="4" class="text-center py-4"><small>No active investments</small></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Analytics & Recent Members -->
    <div class="row g-3 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">👑 Top Shareholders</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Member</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Shares</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">% Ownership</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topShareholders as $sh)
                                <tr>
                                    <td><small>{{ $sh['name'] }}</small></td>
                                    <td class="text-end"><strong>{{ number_format($sh['shares']) }}</strong></td>
                                    <td class="text-end"><span class="badge bg-primary-subtle text-primary-emphasis">{{ number_format($sh['percentage'], 1) }}%</span></td>
                                </tr>
                                @empty
                                <tr class="text-muted">
                                    <td colspan="3" class="text-center py-4"><small>No shareholders</small></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">✨ New Members</h6>
                    @forelse($recentMembers as $member)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="avatar avatar-m me-2">
                            <span class="avatar-initials rounded-circle bg-primary text-white fw-bold">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <small class="d-block fw-semibold text-dark">{{ $member->name }}</small>
                            <small class="text-body-secondary">{{ $member->created_at->format('M d, Y') }}</small>
                        </div>
                        <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4"><small>No new members</small></p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Health -->
    <div class="row g-3 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-success-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">💵 Cash Available</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #0b5345;">৳{{ number_format($cashAvailable, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-primary-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">📥 Total Deposits</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #04396c;">৳{{ number_format($totalDeposits, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-warning-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">💼 Total Invested</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #664d03;">৳{{ number_format($totalInvested, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-success-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">🎁 Total Returns</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #0b5345;">৳{{ number_format($totalReturns, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body">
            <h6 class="section-header mb-4">⚡ Recent Activity</h6>
            @forelse($recentActivity as $activity)
            <div class="d-flex mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="timeline-marker me-3 flex-shrink-0" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;background-color:{{ $activity['type'] === 'deposit' ? '#d4edda' : ($activity['type'] === 'expense' ? '#f8d7da' : '#d1ecf1') }};">
                    <span class="fas fa-{{ $activity['icon'] }} fa-sm {{ $activity['type'] === 'deposit' ? 'text-success' : ($activity['type'] === 'expense' ? 'text-danger' : 'text-info') }}"></span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 small fw-semibold">{{ $activity['title'] }}</h6>
                    <small class="text-body-secondary d-block mb-1">{{ $activity['description'] }}</small>
                    <small class="text-muted">{{ $activity['date']->format('M d, Y H:i') }}</small>
                </div>
                <div class="text-end flex-shrink-0">
                    <p class="mb-0 fw-bold {{ $activity['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ $activity['amount'] > 0 ? '+' : '' }}৳{{ number_format(abs($activity['amount']), 0) }}</p>
                </div>
            </div>
            @empty
            <p class="text-muted text-center py-5"><small>No recent activity</small></p>
            @endforelse
        </div>
    </div>

    <!-- Pending Actions -->
    <div class="row g-3 mb-5">
        @if($pendingExpenses > 0)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-warning-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="section-header mb-2">Pending Expenses</h6>
                            <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #664d03;">{{ $pendingExpenses }}</p>
                        </div>
                        <span class="fas fa-receipt fa-2x opacity-25" style="color: #664d03;"></span>
                    </div>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-warning mt-3" style="font-size: 0.8125rem;">Review Now →</a>
                </div>
            </div>
        </div>
        @endif

        @if($pendingInvestments > 0)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-info-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="section-header mb-2">Pending Investments</h6>
                            <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #055160;">{{ $pendingInvestments }}</p>
                        </div>
                        <span class="fas fa-chart-line fa-2x opacity-25" style="color: #055160;"></span>
                    </div>
                    <a href="{{ route('investments.index') }}" class="btn btn-sm btn-info mt-3" style="font-size: 0.8125rem;">Review Now →</a>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-primary-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="section-header mb-2">Organization</h6>
                            <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #04396c;">{{ $totalMembers }}</p>
                            <small class="text-body-secondary">Members registered</small>
                        </div>
                        <span class="fas fa-users fa-2x opacity-25" style="color: #04396c;"></span>
                    </div>
                    <a href="{{ route('members.index') }}" class="btn btn-sm btn-primary mt-3" style="font-size: 0.8125rem;">View All →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <h6 class="section-header mb-3">⚙️ Quick Actions</h6>
            <div class="row g-2">
                <div class="col-auto"><a href="{{ route('deposit-status') }}" class="btn btn-sm btn-warning" style="font-size: 0.8125rem;"><span class="fas fa-check-double me-1"></span>Check Deposits</a></div>
                <div class="col-auto"><a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary" style="font-size: 0.8125rem;"><span class="fas fa-plus me-1"></span>Add Expense</a></div>
                <div class="col-auto"><a href="{{ route('investments.create') }}" class="btn btn-sm btn-success" style="font-size: 0.8125rem;"><span class="fas fa-plus me-1"></span>Create Investment</a></div>
                <div class="col-auto"><a href="{{ route('members.index') }}" class="btn btn-sm btn-info" style="font-size: 0.8125rem;"><span class="fas fa-users me-1"></span>View Members</a></div>
                <div class="col-auto"><a href="{{ route('accounting.reports.dashboard') }}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.8125rem;"><span class="fas fa-chart-bar me-1"></span>View Reports</a></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const depositCtx = document.getElementById('depositChart').getContext('2d');
    new Chart(depositCtx, {type: 'line', data: {labels: @json($depositTrend['months']), datasets: [{label: 'Monthly Deposits', data: @json($depositTrend['totals']), borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}});

    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {type: 'line', data: {labels: @json($expenseTrend['months']), datasets: [{label: 'Monthly Expenses', data: @json($expenseTrend['totals']), borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}});

    const investmentCtx = document.getElementById('investmentChart').getContext('2d');
    new Chart(investmentCtx, {type: 'doughnut', data: {labels: @json(array_column($investmentDistribution, 'type')), datasets: [{data: @json(array_column($investmentDistribution, 'amount')), backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#fd7e14', '#6f42c1', '#20c997'], borderColor: '#fff', borderWidth: 2}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {position: 'bottom'}}}});

    const depositCountCtx = document.getElementById('depositCountChart').getContext('2d');
    new Chart(depositCountCtx, {type: 'bar', data: {labels: @json($depositCountLabels), datasets: [{label: 'Deposit Transactions', data: @json($depositCountTrend), backgroundColor: '#198754', borderColor: '#157347', borderWidth: 1, borderRadius: 4}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true, ticks: {stepSize: 1}}}}});
</script>
@endsection
