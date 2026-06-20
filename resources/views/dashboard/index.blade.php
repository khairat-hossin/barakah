@extends('layouts.phoenix')

@section('title', 'Dashboard | Barakah')

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
            <h1 class="mb-1 h3">Dashboard</h1>
            <p class="text-body-secondary mb-0">Real-time organizational performance overview</p>
        </div>
        <div class="col-auto">
            <span class="text-body-secondary small">📅 As of {{ date('M d, Y') }}</span>
        </div>
    </div>

    <!-- KPI Cards - Compact Style -->
    <div class="row g-2 mb-3">
        <!-- Members -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto; display: flex; flex-direction: column; justify-content: center;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Members</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">{{ $activeMembers }} active</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0;">{{ $totalMembers }}</h6>
                        @if($memberGrowth !== 0)
                            <span class="badge" style="font-size: 0.65rem;" {{ $memberGrowth > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}>{{ $memberGrowth > 0 ? '↑' : '↓' }} {{ number_format(abs($memberGrowth), 2) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto; display: flex; flex-direction: column; justify-content: center;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Share Capital</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">{{ number_format($allocatedShares) }} allocated</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0;">{{ number_format($totalShares) }}</h6>
                        <span class="badge bg-light text-dark" style="font-size: 0.65rem; white-space: nowrap;">{{ number_format($availableShares) }} free</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto; display: flex; flex-direction: column; justify-content: center;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Monthly Deposits</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">This month</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0; color: #0d6efd;">৳{{ number_format($monthlyDeposits, 0) }}</h6>
                        @if($depositChange !== 0)
                            <span class="badge" style="font-size: 0.65rem;" {{ $depositChange > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}>{{ $depositChange > 0 ? '↑' : '↓' }} {{ number_format(abs($depositChange), 2) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto; display: flex; flex-direction: column; justify-content: center;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Investments</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">{{ $activeInvestments }} active</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0; color: #ffc107;">৳{{ number_format($totalInvested, 0) }}</h6>
                        <span class="badge bg-warning-subtle text-warning" style="font-size: 0.65rem; white-space: nowrap;">+৳{{ number_format($investmentReturns, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto; display: flex; flex-direction: column; justify-content: center;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Monthly Expenses</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">This month</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0; color: #dc3545;">৳{{ number_format($monthlyExpenses, 0) }}</h6>
                        @if($expenseChange !== 0)
                            <span class="badge" style="font-size: 0.65rem;" {{ $expenseChange > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}>{{ $expenseChange > 0 ? '↑' : '↓' }} {{ number_format(abs($expenseChange), 2) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid {{ $netPosition >= 0 ? '#198754' : '#dc3545' }} !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto; display: flex; flex-direction: column; justify-content: center;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Net Position</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">Balance</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0; color: {{ $netPosition >= 0 ? '#198754' : '#dc3545' }};">৳{{ number_format(abs($netPosition), 0) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="">
    <!-- Quick Deposit Button -->
    <div class="mb-4">
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickDepositModal">
            <i class="fas fa-plus-circle"></i> Quick Deposit
        </button>
    </div>

    <!-- Deposit Analytics Section - CRM Style -->
    <div class="row g-3 mb-5">
        <!-- Member Deposits Card -->
        <div class="col-sm-12 col-md-4">
            <a href="{{ route('deposit-status') }}" class="card h-100 text-decoration-none" style="border-left: 4px solid #6f42c1 !important; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow=''">
                @php
                    $memberTotal = $depositsPaid + $depositsUnpaid;
                    $amountRate = $totalDepositExpected > 0 ? min($monthlyDeposits / $totalDepositExpected * 100, 100) : 0;
                @endphp
                <div class="card-body p-3 d-flex flex-column">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <small class="text-body-secondary d-block fw-semibold mb-2">Member Deposits</small>
                            <h2 class="mb-1 text-primary fw-bold">{{ $depositsPaid }}/{{ $memberTotal }} <span class="fs-5 fw-normal text-body-secondary">Members</span></h2>
                            <small class="text-body-secondary">Paid this month</small>
                        </div>
                        @if($depositChange != 0)
                            <span class="badge {{ $depositChange > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                {{ $depositChange > 0 ? '↑' : '↓' }} {{ number_format(abs($depositChange), 1) }}%
                            </span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-body-secondary">Amount collected</small>
                        <small class="fw-semibold">৳{{ number_format($monthlyDeposits, 0) }} / ৳{{ number_format($totalDepositExpected, 0) }}</small>
                    </div>
                    <div class="mt-1" style="height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden;">
                        <div style="width: {{ $amountRate }}%; height: 100%; background: #0d6efd;"></div>
                    </div>

                    <div class="mt-3 d-flex gap-5">
                        <div>
                            <small class="text-success d-block">✓ Paid</small>
                            <span class="text-success fw-bold display-6">{{ $depositsPaid }}</span>
                        </div>
                        <div>
                            <small class="text-danger d-block">✗ Unpaid</small>
                            <span class="text-danger fw-bold display-6">{{ $depositsUnpaid }}</span>
                        </div>
                    </div>

                    @if($depositsUnpaid > 0)
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-body-secondary d-block fw-semibold mb-2">Pending this month</small>
                            <ul class="list-unstyled mb-0">
                                @foreach($pendingMembers as $pending)
                                    <li class="d-flex align-items-center justify-content-between py-1">
                                        <span class="fs-9 text-body text-truncate">{{ $pending->name }}</span>
                                        <span class="badge bg-danger-subtle text-danger-emphasis fs-10">Unpaid</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if($depositsUnpaid > $pendingMembers->count())
                                <small class="text-body-tertiary d-block mt-1">+ {{ $depositsUnpaid - $pendingMembers->count() }} more</small>
                            @endif
                        </div>
                    @endif

                    <div class="mt-auto pt-3 border-top text-end">
                        <small class="text-primary fw-semibold">View Details <span class="fas fa-arrow-right fa-xs ms-1"></span></small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Last 10 Deposits List -->
        <div class="col-sm-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="section-header mb-3">💵 Last 10 Deposits</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Member</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Amount</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lastDeposits as $depositor)
                                <tr>
                                    <td><small>{{ $depositor['name'] }}</small></td>
                                    <td class="text-end"><small class="fw-semibold">৳{{ number_format($depositor['amount'], 0) }}</small></td>
                                    <td class="text-end"><small class="text-body-secondary">{{ $depositor['date'] }}</small></td>
                                </tr>
                                @empty
                                <tr class="text-muted">
                                    <td colspan="3" class="text-center py-4"><small>No deposits yet</small></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-5 d-flex flex-column">
            <h3>Deposit Expected vs Received (Last 6 Months)</h3>
            <p class="text-body-tertiary mb-3">Expected deposits (members × shares × face value) vs actual amount received</p>
            <div class="flex-grow-1 position-relative" style="min-height: 250px;">
                <canvas id="depositExpectedVsReceivedChart"></canvas>
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

    <!-- Quick Actions, Organization, New Members & Top Shareholders -->
    <div class="row g-3 mb-5">
        <!-- Quick Actions -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
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

        <!-- Organization -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-primary-subtle h-100">
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

        <!-- New Members -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="section-header mb-3">✨ New Members</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Member</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Joined</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMembers as $member)
                                <tr>
                                    <td><small>{{ $member->name }}</small></td>
                                    <td class="text-end"><small class="text-body-secondary">{{ $member->created_at->format('M d, Y') }}</small></td>
                                    <td class="text-end"><span class="badge bg-success-subtle text-success-emphasis">Active</span></td>
                                </tr>
                                @empty
                                <tr class="text-muted">
                                    <td colspan="3" class="text-center py-4"><small>No new members</small></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Shareholders -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
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
                <div class="grow">
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
    </div>
</div>

<!-- Quick Deposit Modal -->
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
                    <!-- Member Select -->
                    <div class="mb-3">
                        <label for="memberSelect" class="form-label">Select Member <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="memberSelect" name="member_id" required>
                            <option value="">Choose a member...</option>
                            @foreach($activeMembersCollection as $member)
                                <option value="{{ $member->id }}" data-monthly-amount="{{ $member->getCalculatedMonthlyDepositAmount() }}">
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Select -->
                    <div class="mb-3">
                        <label for="monthSelect" class="form-label">Month <span class="text-danger">*</span></label>
                        <input type="month" class="form-control form-control-sm" id="monthSelect" name="month" value="{{ date('Y-m') }}" required>
                    </div>

                    <!-- Duplicate-month / error warning (inline) -->
                    <div id="quickDepositWarning" class="alert alert-warning py-2 px-3 d-none" role="alert" style="font-size: 0.85rem;"></div>

                    <!-- Deposit Amount (Read-only) -->
                    <div class="mb-3">
                        <label for="depositAmount" class="form-label">Deposit Amount <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">৳</span>
                            <input type="number" class="form-control" id="depositAmount" name="amount" readonly placeholder="0">
                        </div>
                        <small class="text-body-tertiary">Auto-calculated from member's shares</small>
                    </div>

                    <!-- Transaction ID -->
                    <div class="mb-3">
                        <label for="transactionId" class="form-label">Transaction ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="transactionId" name="transaction_id" placeholder="e.g., TXN-2024-001" required>
                    </div>

                    <!-- Advanced Options (Collapsible) -->
                    <div class="mb-3">
                        <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="collapse" data-bs-target="#advancedOptions">
                            <i class="fas fa-chevron-down"></i> Advanced Options
                        </button>
                    </div>

                    <div class="collapse mb-3" id="advancedOptions">
                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select form-select-sm" id="paymentMethod" name="payment_method_id">
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" @selected($method->code === 'bank_transfer')>{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="3" placeholder="Additional details..."></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-check-circle"></i> Record Deposit
                        </button>
                    </div>
                </form>
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

    const depositExpectedCtx = document.getElementById('depositExpectedVsReceivedChart').getContext('2d');
    new Chart(depositExpectedCtx, {type: 'bar', data: {labels: @json($depositExpectedVsReceived['months']), datasets: [{label: 'Expected', data: @json($depositExpectedVsReceived['expected']), backgroundColor: '#0d6efd', borderColor: '#0d6efd', borderWidth: 1, borderRadius: 4}, {label: 'Received', data: @json($depositExpectedVsReceived['received']), backgroundColor: '#198754', borderColor: '#198754', borderWidth: 1, borderRadius: 4}]}, options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {position: 'top', labels: {usePointStyle: true, padding: 15}}}, scales: {y: {beginAtZero: true}}}});

    // Quick Deposit Form Handler
    const memberSelect = document.getElementById('memberSelect');
    const monthSelect = document.getElementById('monthSelect');
    const depositAmount = document.getElementById('depositAmount');
    const quickDepositForm = document.getElementById('quickDepositForm');
    const quickDepositWarning = document.getElementById('quickDepositWarning');
    const quickSubmitBtn = quickDepositForm.querySelector('button[type="submit"]');

    function showWarning(message, type = 'warning') {
        quickDepositWarning.textContent = message;
        quickDepositWarning.classList.remove('d-none', 'alert-warning', 'alert-danger');
        quickDepositWarning.classList.add(type === 'danger' ? 'alert-danger' : 'alert-warning');
    }

    function clearWarning() {
        quickDepositWarning.classList.add('d-none');
        quickDepositWarning.textContent = '';
    }

    memberSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const monthlyAmount = selectedOption.dataset.monthlyAmount;
            depositAmount.value = monthlyAmount ? parseFloat(monthlyAmount).toFixed(2) : '0';
        } else {
            depositAmount.value = '';
        }
        checkDuplicateMonth();
    });

    monthSelect.addEventListener('change', checkDuplicateMonth);

    let checkController = null;
    async function checkDuplicateMonth() {
        const memberId = memberSelect.value;
        const month = monthSelect.value;

        clearWarning();
        quickSubmitBtn.disabled = false;

        if (!memberId || !month) {
            return;
        }

        // Cancel any in-flight check
        if (checkController) checkController.abort();
        checkController = new AbortController();

        try {
            const params = new URLSearchParams({ member_id: memberId, month: month });
            const response = await fetch(`/api/deposits/check-month?${params.toString()}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                signal: checkController.signal
            });
            const data = await response.json();

            if (response.ok && data.exists) {
                const memberName = memberSelect.options[memberSelect.selectedIndex].text.trim();
                showWarning(`${memberName} already has a deposit for ${data.month_label}. Only one deposit per month is allowed.`);
                quickSubmitBtn.disabled = true;
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                // Don't block on a failed check; server still enforces the rule
            }
        }
    }

    quickDepositForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const originalText = quickSubmitBtn.innerHTML;
        quickSubmitBtn.disabled = true;
        quickSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const formData = new FormData(this);

        try {
            const response = await fetch('/api/deposits/quick', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                clearWarning();
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = '<strong>Success!</strong> Deposit recorded successfully. <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.body.prepend(alertDiv);

                this.reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('quickDepositModal'));
                modal.hide();

                setTimeout(() => location.reload(), 1500);
            } else {
                // Show validation/duplicate errors inline instead of a JS alert
                showWarning(data.message || 'Failed to record deposit', 'danger');
            }
        } catch (error) {
            showWarning('Error: ' + error.message, 'danger');
        } finally {
            quickSubmitBtn.innerHTML = originalText;
            // Re-enable unless a duplicate warning is active
            quickSubmitBtn.disabled = !quickDepositWarning.classList.contains('d-none')
                && quickDepositWarning.classList.contains('alert-warning');
        }
    });

    // Clear state when the modal is closed
    document.getElementById('quickDepositModal').addEventListener('hidden.bs.modal', function() {
        clearWarning();
        quickSubmitBtn.disabled = false;
    });
</script>
@endsection
