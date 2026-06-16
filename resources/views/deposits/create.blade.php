@extends('layouts.phoenix')

@section('title', 'Record Deposit | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('deposits.index') }}">Deposits</a></li>
        <li class="breadcrumb-item active">Record Deposit</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between g-3 mb-3">
                <div class="col-12 col-md-auto">
                    <h2 class="mb-0">Record Deposit</h2>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('deposits.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Deposit Information Section -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Deposit Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Member -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                                <option value="">Select a member...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                        {{ $member->name }} ({{ $member->member_code }})
                                    </option>
                                @endforeach
                            </select>
                            <label for="member_id">Member <span class="text-danger">*</span></label>
                            @error('member_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('amount') is-invalid @enderror" type="number" id="amount" name="amount" step="0.01" placeholder="0.00" value="{{ old('amount') }}" required />
                            <label for="amount">Amount (Taka) <span class="text-danger">*</span></label>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Deposit Date -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('deposit_date') is-invalid @enderror" type="date" id="deposit_date" name="deposit_date" value="{{ old('deposit_date', date('Y-m-d')) }}" required />
                            <label for="deposit_date">Deposit Date <span class="text-danger">*</span></label>
                            @error('deposit_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Select method...</option>
                                <option value="cash" @selected(old('payment_method') == 'cash')>Cash</option>
                                <option value="bank_transfer" @selected(old('payment_method') == 'bank_transfer')>Bank Transfer</option>
                                <option value="check" @selected(old('payment_method') == 'check')>Check</option>
                                <option value="mobile_banking" @selected(old('payment_method') == 'mobile_banking')>Mobile Banking</option>
                                <option value="other" @selected(old('payment_method') == 'other')>Other</option>
                            </select>
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Transaction ID -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('transaction_id') is-invalid @enderror" type="text" id="transaction_id" name="transaction_id" placeholder="e.g., TXN123456" value="{{ old('transaction_id') }}" />
                            <label for="transaction_id">Transaction ID</label>
                            @error('transaction_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Months Selection Section -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Select Months for Deposit <span class="text-danger">*</span></h5>
                <small class="text-body-secondary">Select which months this deposit is for. Paid months are marked with ✓</small>
            </div>
            <div class="card-body">
                <div id="months-container" class="alert alert-info">
                    <p class="mb-0">Please select a member first to see available months</p>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Additional Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Notes -->
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" placeholder="Add any additional notes..." style="min-height: 50px;">{{ old('notes') }}</textarea>
                            <label for="notes">Notes</label>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="col-12">
                        <label class="form-label" for="attachments">Attachments</label>
                        <input class="form-control @error('attachments') is-invalid @enderror" type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                        <small class="text-body-secondary">Upload receipts, bank slips, or supporting documents (PDF, JPG, PNG, DOC - Max 5MB each)</small>
                        @error('attachments')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row g-3">
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">Record Deposit</button>
            </div>
            <div class="col-auto">
                <a class="btn btn-secondary" href="{{ route('deposits.index') }}">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('member_id').addEventListener('change', async function() {
    const memberId = this.value;
    const container = document.getElementById('months-container');

    if (!memberId) {
        container.innerHTML = '<p class="mb-0">Please select a member first to see available months</p>';
        return;
    }

    try {
        // Fetch paid months for this member
        const response = await fetch(`/api/member/${memberId}/paid-months`);
        const data = await response.json();
        const paidMonths = data.paid_months || [];

        // Generate months HTML
        let html = '<div class="row g-3">';

        // Generate last 12 months
        for (let i = 11; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            const monthName = date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            const monthKey = `${month}/${year}`;
            const isPaid = paidMonths.includes(monthKey);

            html += `
                <div class="col-md-4 col-lg-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="months[]" value="${monthKey}" id="month_${monthKey.replace('/', '_')}" ${isPaid ? 'disabled' : ''} />
                        <label class="form-check-label" for="month_${monthKey.replace('/', '_')}">
                            ${monthName}
                            ${isPaid ? '<span class="badge bg-success ms-2">✓ Paid</span>' : ''}
                        </label>
                    </div>
                </div>
            `;
        }

        html += '</div>';
        container.innerHTML = html;
    } catch (error) {
        container.innerHTML = '<div class="alert alert-danger mb-0">Error loading paid months</div>';
        console.error('Error:', error);
    }
});

// Validate that at least one month is selected
document.querySelector('form').addEventListener('submit', function(e) {
    const monthsChecked = document.querySelectorAll('input[name="months[]"]:checked').length;
    if (monthsChecked === 0) {
        e.preventDefault();
        alert('Please select at least one month for the deposit');
    }
});
</script>
@endsection
