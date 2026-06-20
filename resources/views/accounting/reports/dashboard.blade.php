@extends('layouts.phoenix')

@section('title', 'Accounting Dashboard | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Accounting</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h2 class="mb-0">Accounting Dashboard</h2>
            <p class="text-body-secondary">Financial overview and key metrics</p>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Total Assets</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($data['balance_sheet']['total_assets'], 2) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">As of {{ $data['as_of_date'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-danger fw-semibold" style="font-size: 0.75rem;">Total Liabilities</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($data['balance_sheet']['total_liabilities'], 2) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">As of {{ $data['as_of_date'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-info fw-semibold" style="font-size: 0.75rem;">Total Equity</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($data['balance_sheet']['total_equity'], 2) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">As of {{ $data['as_of_date'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid {{ $data['trial_balance']['is_balanced'] ? '#198754' : '#dc3545' }} !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="{{ $data['trial_balance']['is_balanced'] ? 'text-success' : 'text-danger' }} fw-semibold" style="font-size: 0.75rem;">Balance Status</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">
                        @if($data['trial_balance']['is_balanced'])
                            ✓ Balanced
                        @else
                            ✗ Imbalanced
                        @endif
                    </h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Dr {{ number_format($data['trial_balance']['total_debits'], 2) }} / Cr {{ number_format($data['trial_balance']['total_credits'], 2) }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Income Statement Section -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Income Statement (YTD)</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-8"><strong>Total Income</strong></div>
                        <div class="col-4 text-end"><strong class="text-success">{{ number_format($data['income_statement']['total_income'], 2) }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-8"><strong>Total Expenses</strong></div>
                        <div class="col-4 text-end"><strong class="text-danger">{{ number_format($data['income_statement']['total_expenses'], 2) }}</strong></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-8"><strong>Net {{ $data['income_statement']['net_profit'] >= 0 ? 'Profit' : 'Loss' }}</strong></div>
                        <div class="col-4 text-end">
                            <strong class="{{ $data['income_statement']['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($data['income_statement']['net_profit'], 2) }}
                            </strong>
                        </div>
                    </div>
                    <small class="text-body-secondary d-block mt-2">Margin: {{ round($data['income_statement']['net_profit_percentage'], 2) }}%</small>
                </div>
            </div>
        </div>

        <!-- Fund Position -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Fund Position</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-8"><strong>Operating Fund</strong></div>
                        <div class="col-4 text-end"><strong class="text-primary">{{ number_format($data['fund_position']['operating_fund'], 2) }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-8"><strong>Investment Fund</strong></div>
                        <div class="col-4 text-end"><strong class="text-info">{{ number_format($data['fund_position']['investment_fund'], 2) }}</strong></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-8"><strong>Total Funds</strong></div>
                        <div class="col-4 text-end"><strong class="text-success">{{ number_format($data['fund_position']['total_funds'], 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Available Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="list-group">
                                <a href="{{ route('accounting.reports.general-ledger') }}" class="list-group-item list-group-item-action">
                                    <h6 class="mb-1">General Ledger</h6>
                                    <small class="text-body-secondary">View account-level transactions with running balances</small>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="list-group">
                                <a href="{{ route('accounting.reports.trial-balance') }}" class="list-group-item list-group-item-action">
                                    <h6 class="mb-1">Trial Balance</h6>
                                    <small class="text-body-secondary">Validate double-entry accounting balance</small>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="list-group">
                                <a href="{{ route('accounting.reports.income-statement') }}" class="list-group-item list-group-item-action">
                                    <h6 class="mb-1">Income Statement</h6>
                                    <small class="text-body-secondary">Profit & Loss for selected period</small>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="list-group">
                                <a href="{{ route('accounting.reports.balance-sheet') }}" class="list-group-item list-group-item-action">
                                    <h6 class="mb-1">Balance Sheet</h6>
                                    <small class="text-body-secondary">Assets, Liabilities & Equity snapshot</small>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="list-group">
                                <a href="{{ route('accounting.reports.cash-flow') }}" class="list-group-item list-group-item-action">
                                    <h6 class="mb-1">Cash Flow</h6>
                                    <small class="text-body-secondary">Operating, Investing & Financing activities</small>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="list-group">
                                <a href="{{ route('accounting.reports.fund-position') }}" class="list-group-item list-group-item-action">
                                    <h6 class="mb-1">Fund Position</h6>
                                    <small class="text-body-secondary">Operating, Reserve & Investment fund balances</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-4">
        <div class="col">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title mb-3">Quick Actions</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('accounting.chart-of-accounts.index') }}" class="btn btn-sm btn-outline-primary">
                            <span class="fas fa-list me-1"></span>Chart of Accounts
                        </a>
                        <a href="{{ route('accounting.journal-vouchers.index') }}" class="btn btn-sm btn-outline-primary">
                            <span class="fas fa-book me-1"></span>Journal Vouchers
                        </a>
                        @can('create', \App\Models\JournalVoucher::class)
                            <a href="{{ route('accounting.journal-vouchers.create') }}" class="btn btn-sm btn-primary">
                                <span class="fas fa-plus me-1"></span>New Voucher
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
