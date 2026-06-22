@extends('layouts.phoenix')

@section('title', 'Create Account | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.chart-of-accounts.index') }}">Chart of Accounts</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Create Account</h2>
            <p class="text-body-secondary">Add a new account to your chart of accounts</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('accounting.chart-of-accounts.store') }}">
                @csrf

                <div class="card">
                    <div class="card-body">
                        <!-- Account Code -->
                        <div class="mb-3">
                            <label class="form-label" for="code">Account Code <span class="text-danger">*</span></label>
                            <input class="form-control @error('code') is-invalid @enderror" type="text" id="code" name="code"
                                   placeholder="e.g., 1500" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Name -->
                        <div class="mb-3">
                            <label class="form-label" for="name">Account Name <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name"
                                   placeholder="e.g., Cash on Hand" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Type -->
                        <div class="mb-3">
                            <label class="form-label" for="account_type">Account Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('account_type') is-invalid @enderror" id="account_type" name="account_type" required>
                                <option value="">Select account type...</option>
                                <option value="ASSET" {{ old('account_type') === 'ASSET' ? 'selected' : '' }}>Asset</option>
                                <option value="LIABILITY" {{ old('account_type') === 'LIABILITY' ? 'selected' : '' }}>Liability</option>
                                <option value="EQUITY" {{ old('account_type') === 'EQUITY' ? 'selected' : '' }}>Equity</option>
                                <option value="INCOME" {{ old('account_type') === 'INCOME' ? 'selected' : '' }}>Income</option>
                                <option value="EXPENSE" {{ old('account_type') === 'EXPENSE' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('account_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Normal Balance -->
                        <div class="mb-3">
                            <label class="form-label" for="normal_balance">Normal Balance <span class="text-danger">*</span></label>
                            <select class="form-select @error('normal_balance') is-invalid @enderror" id="normal_balance" name="normal_balance" required>
                                <option value="">Select normal balance...</option>
                                <option value="DEBIT" {{ old('normal_balance') === 'DEBIT' ? 'selected' : '' }}>Debit</option>
                                <option value="CREDIT" {{ old('normal_balance') === 'CREDIT' ? 'selected' : '' }}>Credit</option>
                            </select>
                            @error('normal_balance')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Parent Account -->
                        <div class="mb-3">
                            <label class="form-label" for="parent_id">Parent Account (Optional)</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">No parent account</option>
                                <option value="">-- Assets --</option>
                                <option value="">-- Liabilities --</option>
                                <option value="">-- Equity --</option>
                                <option value="">-- Income --</option>
                                <option value="">-- Expenses --</option>
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                      placeholder="Account description..." rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Account</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">
                        <span class="fas fa-save me-2"></span>Create Account
                    </button>
                    <a class="btn btn-secondary" href="{{ route('accounting.chart-of-accounts.index') }}">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Account Types</h6>
                    <ul class="small mb-0">
                        <li><strong>Asset:</strong> Resources owned</li>
                        <li><strong>Liability:</strong> Amounts owed</li>
                        <li><strong>Equity:</strong> Owner's stake</li>
                        <li><strong>Income:</strong> Revenue earned</li>
                        <li><strong>Expense:</strong> Costs incurred</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
