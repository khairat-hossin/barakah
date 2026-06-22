@extends('layouts.phoenix')

@section('title', 'Create Journal Voucher | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.journal-vouchers.index') }}">Journal Vouchers</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Create Journal Voucher</h2>
            <p class="text-body-secondary">Record a new accounting transaction</p>
        </div>
    </div>

    <form method="POST" action="{{ route('accounting.journal-vouchers.store') }}" id="voucherForm">
        @csrf

        <div class="row">
            <div class="col-lg-9">
                <!-- Voucher Header -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="voucher_date">Voucher Date <span class="text-danger">*</span></label>
                                    <input class="form-control @error('voucher_date') is-invalid @enderror" type="date" id="voucher_date" name="voucher_date"
                                           value="{{ old('voucher_date', date('Y-m-d')) }}" required>
                                    @error('voucher_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="voucher_type">Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('voucher_type') is-invalid @enderror" id="voucher_type" name="voucher_type" required>
                                        <option value="">Select type...</option>
                                        <option value="MANUAL" {{ old('voucher_type') === 'MANUAL' ? 'selected' : '' }}>Manual</option>
                                        <option value="DEPOSIT" {{ old('voucher_type') === 'DEPOSIT' ? 'selected' : '' }}>Deposit</option>
                                        <option value="EXPENSE" {{ old('voucher_type') === 'EXPENSE' ? 'selected' : '' }}>Expense</option>
                                        <option value="INVESTMENT" {{ old('voucher_type') === 'INVESTMENT' ? 'selected' : '' }}>Investment</option>
                                    </select>
                                    @error('voucher_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                            <input class="form-control @error('description') is-invalid @enderror" type="text" id="description" name="description"
                                   placeholder="Brief description of the transaction" value="{{ old('description') }}" required>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Journal Entries -->
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Journal Entries</h5>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addEntry">
                                    <span class="fas fa-plus me-1"></span>Add Entry
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="entriesContainer">
                        <div class="entry-row mb-3">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label small">Account</label>
                                    <select class="form-select form-select-sm account-select" name="entries[0][account_id]" required>
                                        <option value="">Select account...</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Debit</label>
                                    <input type="number" class="form-control form-control-sm debit-amount" name="entries[0][debit_amount]"
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Credit</label>
                                    <input type="number" class="form-control form-control-sm credit-amount" name="entries[0][credit_amount]"
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-entry d-none" style="width: 100%;">
                                        <span class="fas fa-trash"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Summary -->
                    <div class="card-footer bg-light">
                        <div class="row text-center small">
                            <div class="col">
                                <strong>Total Debits:</strong> <span id="totalDebits">0.00</span>
                            </div>
                            <div class="col">
                                <strong>Total Credits:</strong> <span id="totalCredits">0.00</span>
                            </div>
                            <div class="col">
                                <strong>Difference:</strong> <span id="difference" class="text-danger">0.00</span>
                            </div>
                            <div class="col">
                                <strong>Status:</strong> <span id="balanceStatus" class="badge bg-danger">Not Balanced</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="col-lg-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Tips</h6>
                        <ul class="small mb-0">
                            <li>Enter each GL account once</li>
                            <li>Total Debits must equal Total Credits</li>
                            <li>Saved as Draft initially</li>
                            <li>Post to make it permanent</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">
                <span class="fas fa-save me-2"></span>Create Voucher
            </button>
            <a class="btn btn-secondary" href="{{ route('accounting.journal-vouchers.index') }}">Cancel</a>
        </div>
    </form>
</div>

<script>
let entryCount = 1;

document.getElementById('addEntry').addEventListener('click', function() {
    const container = document.getElementById('entriesContainer');
    const entryHtml = `
        <div class="entry-row mb-3">
            <div class="row g-2">
                <div class="col-md-5">
                    <select class="form-select form-select-sm account-select" name="entries[${entryCount}][account_id]" required>
                        <option value="">Select account...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control form-control-sm debit-amount" name="entries[${entryCount}][debit_amount]"
                           step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control form-control-sm credit-amount" name="entries[${entryCount}][credit_amount]"
                           step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger delete-entry" style="width: 100%;">
                        <span class="fas fa-trash"></span>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', entryHtml);
    attachEventListeners();
    entryCount++;
});

function attachEventListeners() {
    document.querySelectorAll('.debit-amount, .credit-amount').forEach(el => {
        el.addEventListener('input', updateTotals);
    });

    document.querySelectorAll('.delete-entry').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.entry-row').remove();
            updateTotals();
        });
    });
}

function updateTotals() {
    let totalDebits = 0;
    let totalCredits = 0;

    document.querySelectorAll('.entry-row').forEach(row => {
        const debit = parseFloat(row.querySelector('.debit-amount').value) || 0;
        const credit = parseFloat(row.querySelector('.credit-amount').value) || 0;
        totalDebits += debit;
        totalCredits += credit;
    });

    const difference = Math.abs(totalDebits - totalCredits);
    const isBalanced = difference < 0.01;

    document.getElementById('totalDebits').textContent = totalDebits.toFixed(2);
    document.getElementById('totalCredits').textContent = totalCredits.toFixed(2);
    document.getElementById('difference').textContent = difference.toFixed(2);

    const statusEl = document.getElementById('balanceStatus');
    if (isBalanced) {
        statusEl.className = 'badge bg-success';
        statusEl.textContent = 'Balanced ✓';
    } else {
        statusEl.className = 'badge bg-danger';
        statusEl.textContent = 'Not Balanced';
    }
}

attachEventListeners();
updateTotals();
</script>
@endsection
