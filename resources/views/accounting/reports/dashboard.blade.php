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
    <div class="row mb-4">
        <div class="col-lg-3">
            <div class="card border-start border-primary border-3">
                <div class="card-body">
                    <h6 class="card-title text-primary fw-semibold">Total Assets</h6>
                    <p class="card-text fs-5 fw-bold">{{ number_format($data['balance_sheet']['total_assets'], 2) }}</p>
                    <small class="text-body-secondary">As of {{ $data['as_of_date'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card border-start border-danger border-3">
                <div class="card-body">
                    <h6 class="card-title text-danger fw-semibold">Total Liabilities</h6>
                    <p class="card-text fs-5 fw-bold">{{ number_format($data['balance_sheet']['total_liabilities'], 2) }}</p>
                    <small class="text-body-secondary">As of {{ $data['as_of_date'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card border-start border-info border-3">
                <div class="card-body">
                    <h6 class="card-title text-info fw-semibold">Total Equity</h6>
                    <p class="card-text fs-5 fw-bold">{{ number_format($data['balance_sheet']['total_equity'], 2) }}</p>
                    <small class="text-body-secondary">As of {{ $data['as_of_date'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card {{ $data['trial_balance']['is_balanced'] ? 'border-success' : 'border-danger' }} border-start border-3">
                <div class="card-body">
                    <h6 class="card-title {{ $data['trial_balance']['is_balanced'] ? 'text-success' : 'text-danger' }} fw-semibold">Balance Status</h6>
                    <p class="card-text fs-5 fw-bold">
                        @if($data['trial_balance']['is_balanced'])
                            ✓ Balanced
                        @else
                            ✗ Imbalanced
                        @endif
                    </p>
                    <small class="text-body-secondary">Dr {{ number_format($data['trial_balance']['total_debits'], 2) }} / Cr {{ number_format($data['trial_balance']['total_credits'], 2) }}</small>
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
