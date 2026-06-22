@extends('layouts.phoenix')

@section('title', 'General Ledger | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.reports.dashboard') }}">Accounting</a></li>
        <li class="breadcrumb-item active">General Ledger</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">General Ledger</h2>
            <p class="text-body-secondary">Account-level transaction details with running balances</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('accounting.reports.general-ledger') }}">
                <div class="col-md-6">
                    <label class="form-label">Account</label>
                    <select class="form-select" name="account_id">
                        <option value="">All Accounts</option>
                        @foreach(\App\Models\ChartOfAccount::active()->ordered()->get() as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="fas fa-search me-1"></span>Filter
                    </button>
                    <a href="{{ route('accounting.reports.general-ledger') }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-3">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-body-secondary">Opening Balance</h6>
                    <p class="fw-bold">
                        @if($ledgerData && isset($ledgerData['opening_balance']))
                            {{ number_format($ledgerData['opening_balance'], 2) }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-body-secondary">Total Debits</h6>
                    <p class="fw-bold">
                        @if($ledgerData && isset($ledgerData['total_debits']))
                            {{ number_format($ledgerData['total_debits'], 2) }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-body-secondary">Total Credits</h6>
                    <p class="fw-bold">
                        @if($ledgerData && isset($ledgerData['total_credits']))
                            {{ number_format($ledgerData['total_credits'], 2) }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-body-secondary">Closing Balance</h6>
                    <p class="fw-bold">
                        @if($ledgerData && isset($ledgerData['closing_balance']))
                            {{ number_format($ledgerData['closing_balance'], 2) }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th>Date</th>
                        <th>Voucher</th>
                        <th>Description</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @if($ledgerData && isset($ledgerData['entries']) && $ledgerData['entries']->count() > 0)
                        @foreach($ledgerData['entries'] as $entry)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($entry['voucher_date'])->format('M d, Y') }}</td>
                            <td><code>{{ $entry['voucher_number'] }}</code></td>
                            <td>{{ $entry['description'] }}</td>
                            <td class="text-end">{{ $entry['debit_amount'] ? number_format($entry['debit_amount'], 2) : '-' }}</td>
                            <td class="text-end">{{ $entry['credit_amount'] ? number_format($entry['credit_amount'], 2) : '-' }}</td>
                            <td class="text-end"><strong>{{ number_format($entry['balance'], 2) }}</strong></td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="text-muted">
                            <td colspan="6" class="text-center py-5">
                                <p class="text-body-secondary mb-2">@if($selectedAccount) No entries found for {{ $selectedAccount->name }} @else Select an account to view its ledger @endif</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
