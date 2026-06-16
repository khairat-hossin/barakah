@extends('layouts.phoenix')

@section('title', $account->name . ' | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.chart-of-accounts.index') }}">Chart of Accounts</a></li>
        <li class="breadcrumb-item active">{{ $account->name }}</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">{{ $account->name }}</h2>
            <p class="text-body-secondary">
                <span class="badge bg-light text-dark font-monospace">{{ $account->code }}</span>
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
            </p>
        </div>
        <div class="col-auto">
            @can('update', $account)
                <a href="{{ route('accounting.chart-of-accounts.edit', $account) }}" class="btn btn-outline-primary">
                    <span class="fas fa-edit me-2"></span>Edit
                </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Account Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Account Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-body-secondary">Code</h6>
                            <p class="mb-0 fw-semibold font-monospace">{{ $account->code }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-body-secondary">Type</h6>
                            <p class="mb-0 fw-semibold">{{ $account->account_type }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-body-secondary">Normal Balance</h6>
                            <p class="mb-0 fw-semibold">{{ $account->normal_balance }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-body-secondary">Status</h6>
                            <p class="mb-0">
                                @if($account->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($account->description)
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-body-secondary">Description</h6>
                                <p class="mb-0">{{ $account->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Balance Summary -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card border-start border-primary border-3">
                        <div class="card-body">
                            <h6 class="card-title text-primary">Current Balance</h6>
                            <p class="card-text fs-5 fw-bold font-monospace">
                                {{ number_format($account->getBalance(), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-start border-secondary border-3">
                        <div class="card-body">
                            <h6 class="card-title text-secondary">Transactions</h6>
                            <p class="card-text fs-5 fw-bold">
                                {{ $account->journalEntries()->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            @if($account->journalEntries()->exists())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Transactions</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="bg-body-tertiary">
                                <tr>
                                    <th>Date</th>
                                    <th>Voucher</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account->journalEntries()->latest()->take(10)->get() as $entry)
                                <tr>
                                    <td>{{ $entry->created_at->format('M d, Y') }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $entry->voucher->voucher_number }}</span></td>
                                    <td>{{ Str::limit($entry->description, 40) }}</td>
                                    <td class="text-end">{{ $entry->debit_amount ? number_format($entry->debit_amount, 2) : '-' }}</td>
                                    <td class="text-end">{{ $entry->credit_amount ? number_format($entry->credit_amount, 2) : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <p class="text-body-secondary mb-0">No transactions yet for this account</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Account Metadata -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Account Metadata</h6>
                </div>
                <div class="card-body small">
                    <div class="mb-2">
                        <strong>Created:</strong><br>
                        {{ $account->created_at->format('M d, Y H:i') }}
                    </div>
                    <div class="mb-2">
                        <strong>Last Updated:</strong><br>
                        {{ $account->updated_at->format('M d, Y H:i') }}
                    </div>
                    @if($account->parent)
                        <div class="mb-2">
                            <strong>Parent Account:</strong><br>
                            <a href="{{ route('accounting.chart-of-accounts.show', $account->parent) }}">
                                {{ $account->parent->name }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sub-Accounts -->
            @if($account->children()->exists())
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Sub-Accounts</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($account->children as $child)
                            <li class="list-group-item">
                                <a href="{{ route('accounting.chart-of-accounts.show', $child) }}">
                                    {{ $child->name }}
                                    <small class="text-body-secondary">({{ $child->code }})</small>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
