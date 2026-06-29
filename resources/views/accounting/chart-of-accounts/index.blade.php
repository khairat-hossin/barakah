@extends('layouts.phoenix')

@section('title', 'Chart of Accounts | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Chart of Accounts</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Chart of Accounts</h2>
            <p class="text-body-secondary">Manage your general ledger account structure</p>
        </div>
        <div class="col-auto">
            @can('manage accounting')
                <a href="{{ route('accounting.chart-of-accounts.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-2"></span>New Account
                </a>
            @endcan
        </div>
    </div>

    <!-- Account Type Filters -->
    <div class="row mb-3">
        <div class="col">
            <div class="btn-group" role="group">
                <a href="{{ route('accounting.chart-of-accounts.index') }}" class="btn btn-outline-secondary {{ !request('type') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('accounting.chart-of-accounts.index', ['type' => 'ASSET']) }}" class="btn btn-outline-secondary {{ request('type') === 'ASSET' ? 'active' : '' }}">
                    Assets
                </a>
                <a href="{{ route('accounting.chart-of-accounts.index', ['type' => 'LIABILITY']) }}" class="btn btn-outline-secondary {{ request('type') === 'LIABILITY' ? 'active' : '' }}">
                    Liabilities
                </a>
                <a href="{{ route('accounting.chart-of-accounts.index', ['type' => 'EQUITY']) }}" class="btn btn-outline-secondary {{ request('type') === 'EQUITY' ? 'active' : '' }}">
                    Equity
                </a>
                <a href="{{ route('accounting.chart-of-accounts.index', ['type' => 'INCOME']) }}" class="btn btn-outline-secondary {{ request('type') === 'INCOME' ? 'active' : '' }}">
                    Income
                </a>
                <a href="{{ route('accounting.chart-of-accounts.index', ['type' => 'EXPENSE']) }}" class="btn btn-outline-secondary {{ request('type') === 'EXPENSE' ? 'active' : '' }}">
                    Expenses
                </a>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="fw-semibold">CODE</th>
                        <th class="fw-semibold">ACCOUNT NAME</th>
                        <th class="fw-semibold">TYPE</th>
                        <th class="fw-semibold">BALANCE</th>
                        <th class="fw-semibold">STATUS</th>
                        <th class="fw-semibold">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark font-monospace">{{ $account->code }}</span>
                        </td>
                        <td>
                            <strong>{{ $account->name }}</strong>
                            @if($account->parent)
                                <br><small class="text-body-secondary">Parent: {{ $account->parent->name }}</small>
                            @endif
                        </td>
                        <td>
                            @if($account->account_type === 'ASSET')
                                <span class="badge bg-primary">{{ $account->account_type }}</span>
                            @elseif($account->account_type === 'LIABILITY')
                                <span class="badge bg-danger">{{ $account->account_type }}</span>
                            @elseif($account->account_type === 'EQUITY')
                                <span class="badge bg-info">{{ $account->account_type }}</span>
                            @elseif($account->account_type === 'INCOME')
                                <span class="badge bg-success">{{ $account->account_type }}</span>
                            @else
                                <span class="badge bg-warning">{{ $account->account_type }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-monospace">
                                @php
                                    try {
                                        $balance = $account->getBalance();
                                        echo number_format($balance, 2);
                                    } catch (\Exception $e) {
                                        echo '<span class="text-body-secondary">-</span>';
                                    }
                                @endphp
                            </span>
                        </td>
                        <td>
                            @if($account->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('accounting.chart-of-accounts.show', $account) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <span class="fas fa-eye"></span>
                            </a>
                            @can('update', $account)
                                <a href="{{ route('accounting.chart-of-accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <span class="fas fa-edit"></span>
                                </a>
                            @endcan
                            @can('delete', $account)
                                <form action="{{ route('accounting.chart-of-accounts.destroy', $account) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" data-confirm="Are you sure?">
                                        <span class="fas fa-trash"></span>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <p class="text-body-secondary mb-2">No accounts found</p>
                            @can('manage accounting')
                                <a href="{{ route('accounting.chart-of-accounts.create') }}" class="btn btn-sm btn-primary">Create the first account</a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($accounts->hasPages())
        <div class="mt-4">
            {{ $accounts->links() }}
        </div>
    @endif

    <!-- Account Summary -->
    <div class="row mt-5">
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Total Assets</h6>
                    <p class="card-text fs-5 fw-bold text-primary">
                        {{ number_format($totalAssets, 2) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Total Liabilities</h6>
                    <p class="card-text fs-5 fw-bold text-danger">
                        {{ number_format($totalLiabilities, 2) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Total Equity</h6>
                    <p class="card-text fs-5 fw-bold text-info">
                        {{ number_format($totalEquity, 2) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Active Accounts</h6>
                    <p class="card-text fs-5 fw-bold text-success">
                        {{ $accounts->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
