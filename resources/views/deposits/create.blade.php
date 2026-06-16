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
            <!-- LEFT COLUMN: All Info in One Card -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">Deposit Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Member Selector -->
                        <div class="mb-4">
                            <label for="member_id" class="form-label fw-semibold">Select Member <span class="text-danger">*</span></label>
                            <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                                <option value="">Choose a member...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" data-monthly-amount="{{ $member->monthly_saving_amount }}" @selected(old('member_id') == $member->id)>
                                        {{ $member->name }} ({{ $member->member_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- Amount (Auto-calculated) -->
                        <div class="mb-4">
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
                        <div class="mb-4">
                            <label for="deposit_date" class="form-label fw-semibold">Deposit Date <span class="text-danger">*</span></label>
                            <input class="form-control @error('deposit_date') is-invalid @enderror" type="date" id="deposit_date" name="deposit_date" value="{{ old('deposit_date', date('Y-m-d')) }}" required />
                            @error('deposit_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
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
                        <div class="mb-4">
                            <label for="transaction_id" class="form-label fw-semibold">Transaction ID</label>
                            <input class="form-control @error('transaction_id') is-invalid @enderror" type="text" id="transaction_id" name="transaction_id" placeholder="e.g., TXN123456" value="{{ old('transaction_id') }}" />
                            @error('transaction_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="form-label fw-semibold">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" placeholder="Add any additional notes..." style="min-height: 100px;">{{ old('notes') }}</textarea>
                            @error('notes')
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
                        <h5 class="mb-0">📅 Select Months</h5>
                        <small class="text-body-secondary">Click on month to select | Green = Selected | Red = Already Paid</small>
                    </div>
                    <div class="card-body">
                        <div id="calendar-view">
                            <div class="alert alert-info mb-0">
                                <p class="mb-0">Select a member to see the calendar</p>
                            </div>
                        </div>
                        <!-- Hidden checkboxes for form submission -->
                        <div id="hidden-months"></div>
                    </div>
                </div>

                <!-- Summary & Member Info -->
                <div class="card">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">💰 Deposit Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Member Info -->
                        <div id="member-info-summary" class="alert alert-info d-none mb-4">
                            <div class="mb-3">
                                <small class="text-body-secondary d-block mb-1"><strong>Monthly Amount:</strong></small>
                                <p class="display-6 mb-0" id="monthly-amount">৳0</p>
                            </div>
                            <hr class="my-3">
                            <div>
                                <p class="mb-2"><strong>Paid Months:</strong></p>
                                <div id="paid-months-display" class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-success">None yet</span>
                                </div>
                            </div>
                        </div>

                        <!-- Deposit Summary Stats -->
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

        <!-- Attachments Section -->
        <div class="card mb-4 mt-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Attachments</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
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
let selectedMonths = new Set();

document.getElementById('member_id').addEventListener('change', async function() {
    const memberId = this.value;
    const option = this.options[this.selectedIndex];

    if (!memberId) {
        document.getElementById('member-info-summary').classList.add('d-none');
        document.getElementById('calendar-view').innerHTML = '<div class="alert alert-info mb-0">Select a member to see the calendar</div>';
        selectedMonths.clear();
        updateCalculation();
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
        document.getElementById('member-info-summary').classList.remove('d-none');

        // Clear previously selected months for new member
        selectedMonths.clear();

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
        const isSelected = selectedMonths.has(monthKey);

        let btnClass = 'btn w-100 p-3 text-center fw-bold';
        let cursor = 'pointer';

        if (isPaid) {
            btnClass += ' btn-danger text-white';
            cursor = 'not-allowed';
        } else if (isSelected) {
            btnClass += ' btn-success text-white';
        } else {
            btnClass += ' btn-light text-dark border';
        }

        const monthName = date.toLocaleDateString('en-US', { month: 'short' });
        const yearNum = date.toLocaleDateString('en-US', { year: '2-digit' });

        calendarHtml += `
            <div class="col-4 col-md-3">
                <button type="button" class="${btnClass}" style="cursor: ${cursor}; border-radius: 8px;"
                    onclick="toggleMonth('${monthKey}', ${isPaid ? 'true' : 'false'})"
                    ${isPaid ? 'disabled' : ''}>
                    <div>${monthName}</div>
                    <small>${yearNum}</small>
                    ${isPaid ? '<div style="font-size: 0.7rem; margin-top: 4px;">✓ Paid</div>' : ''}
                </button>
            </div>
        `;
    }

    calendarHtml += '</div>';
    document.getElementById('calendar-view').innerHTML = calendarHtml;
}

function toggleMonth(monthKey, isPaid) {
    if (isPaid) return; // Don't allow selecting paid months

    if (selectedMonths.has(monthKey)) {
        selectedMonths.delete(monthKey);
    } else {
        selectedMonths.add(monthKey);
    }

    generateCalendarView();
    updateCalculation();
}

function updateCalculation() {
    const selected = selectedMonths.size;
    const totalAmount = selected * monthlyAmount;

    document.getElementById('selected-count').textContent = selected;
    document.getElementById('total-amount').textContent = '৳' + totalAmount.toLocaleString('en-US', { maximumFractionDigits: 0 });
    document.getElementById('amount').value = totalAmount.toFixed(2);

    // Update hidden checkboxes for form submission
    const hiddenContainer = document.getElementById('hidden-months');
    hiddenContainer.innerHTML = '';
    selectedMonths.forEach(monthKey => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'months[]';
        input.value = monthKey;
        hiddenContainer.appendChild(input);
    });
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    if (selectedMonths.size === 0) {
        e.preventDefault();
        alert('Please select at least one month for the deposit');
    }
});
</script>
@endsection
