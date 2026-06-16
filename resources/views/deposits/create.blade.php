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

        <div class="row g-4">
            <!-- LEFT COLUMN: Member & Month Selection -->
            <div class="col-lg-6">
                <!-- Member Selection -->
                <div class="card mb-4">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">Member & Months</h5>
                    </div>
                    <div class="card-body">
                        <!-- Member Selector -->
                        <div class="mb-4">
                            <label for="member_id" class="form-label fw-semibold">Select Member <span class="text-danger">*</span></label>
                            <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                                <option value="">Choose a member...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" data-monthly-amount="{{ $member->monthly_saving_amount }}" @selected(old('member_id') == $member->id)>
                                        {{ $member->name }} ({{ $member->member_code }}) - ৳{{ number_format($member->monthly_saving_amount, 0) }}/month
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Paid/Unpaid Months Status -->
                        <div id="member-info" class="alert alert-info d-none">
                            <div class="mb-3">
                                <strong>Monthly Amount:</strong> <span id="monthly-amount">৳0</span>
                            </div>
                            <div>
                                <p class="mb-2"><strong>Paid Months:</strong></p>
                                <div id="paid-months-display" class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-success">None yet</span>
                                </div>
                            </div>
                        </div>

                        <!-- Month Selection -->
                        <div id="months-selection" class="d-none">
                            <label class="form-label fw-semibold mb-3">Select Months to Deposit For <span class="text-danger">*</span></label>
                            <div id="months-list" class="d-grid gap-2">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deposit Details -->
                <div class="card mb-4">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">Deposit Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Amount (Auto-calculated) -->
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">Calculated Amount (Auto)</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input class="form-control form-control-lg @error('amount') is-invalid @enderror" type="number" id="amount" name="amount" readonly placeholder="0.00" value="{{ old('amount', '0.00') }}" step="0.01" />
                            </div>
                            <small class="text-body-secondary d-block mt-2">Amount = Selected Months × Monthly Amount</small>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deposit Date -->
                        <div class="mb-3">
                            <label for="deposit_date" class="form-label fw-semibold">Deposit Date <span class="text-danger">*</span></label>
                            <input class="form-control @error('deposit_date') is-invalid @enderror" type="date" id="deposit_date" name="deposit_date" value="{{ old('deposit_date', date('Y-m-d')) }}" required />
                            @error('deposit_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Select method...</option>
                                <option value="cash" @selected(old('payment_method') == 'cash')>Cash</option>
                                <option value="bank_transfer" @selected(old('payment_method') == 'bank_transfer')>Bank Transfer</option>
                                <option value="check" @selected(old('payment_method') == 'check')>Check</option>
                                <option value="mobile_banking" @selected(old('payment_method') == 'mobile_banking')>Mobile Banking</option>
                                <option value="other" @selected(old('payment_method') == 'other')>Other</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Transaction ID -->
                        <div>
                            <label for="transaction_id" class="form-label fw-semibold">Transaction ID</label>
                            <input class="form-control @error('transaction_id') is-invalid @enderror" type="text" id="transaction_id" name="transaction_id" placeholder="e.g., TXN123456" value="{{ old('transaction_id') }}" />
                            @error('transaction_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Calendar View & Summary -->
            <div class="col-lg-6">
                <!-- Month Calendar View -->
                <div class="card mb-4">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">📅 Month Calendar</h5>
                        <small class="text-body-secondary">Green = Available | Red = Already Paid</small>
                    </div>
                    <div class="card-body">
                        <div id="calendar-view" class="d-grid gap-2">
                            <div class="alert alert-info mb-0">
                                <p class="mb-0">Select a member to see the calendar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="card">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">💰 Deposit Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <small class="text-body-secondary d-block mb-1">Months Selected</small>
                                    <p class="display-6 mb-0" id="selected-count">0</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <small class="text-body-secondary d-block mb-1">Total Amount</small>
                                    <p class="display-6 mb-0" id="total-amount">৳0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="card mb-4 mt-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Additional Notes</h5>
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
let monthlyAmount = 0;
let paidMonths = [];

document.getElementById('member_id').addEventListener('change', async function() {
    const memberId = this.value;
    const option = this.options[this.selectedIndex];

    if (!memberId) {
        document.getElementById('member-info').classList.add('d-none');
        document.getElementById('months-selection').classList.add('d-none');
        document.getElementById('calendar-view').innerHTML = '<div class="alert alert-info mb-0">Select a member to see the calendar</div>';
        return;
    }

    monthlyAmount = parseFloat(option.dataset.monthlyAmount) || 0;
    document.getElementById('monthly-amount').textContent = '৳' + monthlyAmount.toLocaleString();

    try {
        const response = await fetch(`/api/member/${memberId}/paid-months`);
        const data = await response.json();
        paidMonths = data.paid_months || [];

        // Show member info
        if (paidMonths.length > 0) {
            const monthBadges = paidMonths.map(m => {
                const [month, year] = m.split('/');
                const date = new Date(year, month - 1);
                return `<span class="badge bg-success">${date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' })}</span>`;
            }).join('');
            document.getElementById('paid-months-display').innerHTML = monthBadges;
        } else {
            document.getElementById('paid-months-display').innerHTML = '<span class="badge bg-secondary">None yet</span>';
        }
        document.getElementById('member-info').classList.remove('d-none');

        // Generate month checkboxes
        let monthsHtml = '';
        for (let i = 11; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            const monthKey = `${month}/${year}`;
            const isPaid = paidMonths.includes(monthKey);

            monthsHtml += `
                <label class="btn btn-outline-${isPaid ? 'danger' : 'success'} ${isPaid ? 'disabled' : ''}" style="cursor: ${isPaid ? 'not-allowed' : 'pointer'};">
                    <input type="checkbox" name="months[]" value="${monthKey}" ${isPaid ? 'disabled' : ''} class="month-checkbox" onchange="updateCalculation()">
                    <span>${date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' })}</span>
                    ${isPaid ? ' <span class="badge bg-danger ms-1">✓ Paid</span>' : ''}
                </label>
            `;
        }
        document.getElementById('months-list').innerHTML = monthsHtml;
        document.getElementById('months-selection').classList.remove('d-none');

        // Generate calendar view
        generateCalendarView();
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('calendar-view').innerHTML = '<div class="alert alert-danger mb-0">Error loading month data</div>';
    }
});

function generateCalendarView() {
    let calendarHtml = '<div class="row g-2">';

    for (let i = 11; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        const month = date.getMonth() + 1;
        const year = date.getFullYear();
        const monthKey = `${month}/${year}`;
        const isPaid = paidMonths.includes(monthKey);
        const isSelected = document.querySelector(`input[value="${monthKey}"]`)?.checked || false;

        const bgColor = isPaid ? 'danger' : (isSelected ? 'success' : 'light');
        const textColor = isPaid ? 'white' : 'dark';

        calendarHtml += `
            <div class="col-4 col-md-3">
                <div class="btn btn-${bgColor} w-100 text-${textColor} p-2 text-center" style="border-radius: 8px; cursor: ${isPaid ? 'not-allowed' : 'pointer'};">
                    <div class="fw-bold">${date.toLocaleDateString('en-US', { month: 'short' })}</div>
                    <small>${date.toLocaleDateString('en-US', { year: '2-digit' })}</small>
                </div>
            </div>
        `;
    }

    calendarHtml += '</div>';
    document.getElementById('calendar-view').innerHTML = calendarHtml;
}

function updateCalculation() {
    const selected = document.querySelectorAll('input[name="months[]"]:checked').length;
    const totalAmount = selected * monthlyAmount;

    document.getElementById('selected-count').textContent = selected;
    document.getElementById('total-amount').textContent = '৳' + totalAmount.toLocaleString('en-US', { maximumFractionDigits: 0 });
    document.getElementById('amount').value = totalAmount.toFixed(2);

    generateCalendarView();
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const monthsChecked = document.querySelectorAll('input[name="months[]"]:checked').length;
    if (monthsChecked === 0) {
        e.preventDefault();
        alert('Please select at least one month for the deposit');
    }
});
</script>
@endsection
