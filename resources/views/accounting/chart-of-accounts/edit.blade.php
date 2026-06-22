@extends('layouts.phoenix')

@section('title', 'Edit Account | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.chart-of-accounts.index') }}">Chart of Accounts</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.chart-of-accounts.show', $account) }}">{{ $account->name }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Edit Account</h2>
            <p class="text-body-secondary">Update account information</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('accounting.chart-of-accounts.update', $account) }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <!-- Account Code -->
                        <div class="mb-3">
                            <label class="form-label" for="code">Account Code</label>
                            <input class="form-control @error('code') is-invalid @enderror" type="text" id="code" name="code"
                                   value="{{ old('code', $account->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Name -->
                        <div class="mb-3">
                            <label class="form-label" for="name">Account Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name"
                                   value="{{ old('name', $account->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Type -->
                        <div class="mb-3">
                            <label class="form-label" for="account_type">Account Type</label>
                            <select class="form-select @error('account_type') is-invalid @enderror" id="account_type" name="account_type" required>
                                <option value="ASSET" {{ old('account_type', $account->account_type) === 'ASSET' ? 'selected' : '' }}>Asset</option>
                                <option value="LIABILITY" {{ old('account_type', $account->account_type) === 'LIABILITY' ? 'selected' : '' }}>Liability</option>
                                <option value="EQUITY" {{ old('account_type', $account->account_type) === 'EQUITY' ? 'selected' : '' }}>Equity</option>
                                <option value="INCOME" {{ old('account_type', $account->account_type) === 'INCOME' ? 'selected' : '' }}>Income</option>
                                <option value="EXPENSE" {{ old('account_type', $account->account_type) === 'EXPENSE' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('account_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Normal Balance -->
                        <div class="mb-3">
                            <label class="form-label" for="normal_balance">Normal Balance</label>
                            <select class="form-select @error('normal_balance') is-invalid @enderror" id="normal_balance" name="normal_balance" required>
                                <option value="DEBIT" {{ old('normal_balance', $account->normal_balance) === 'DEBIT' ? 'selected' : '' }}>Debit</option>
                                <option value="CREDIT" {{ old('normal_balance', $account->normal_balance) === 'CREDIT' ? 'selected' : '' }}>Credit</option>
                            </select>
                            @error('normal_balance')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                      rows="3">{{ old('description', $account->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Account</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">
                        <span class="fas fa-save me-2"></span>Update Account
                    </button>
                    <a class="btn btn-secondary" href="{{ route('accounting.chart-of-accounts.show', $account) }}">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Account Info Panel -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Account Information</h6>
                    <dl class="small mb-0">
                        <dt>Current Balance:</dt>
                        <dd class="fw-bold">{{ number_format($account->getBalance(), 2) }}</dd>
                        <dt class="mt-2">Transactions:</dt>
                        <dd>{{ $account->journalEntries()->count() }}</dd>
                        <dt class="mt-2">Created:</dt>
                        <dd>{{ $account->created_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
