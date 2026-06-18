@extends('layouts.phoenix')

@section('title', 'Edit Deposit | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('deposits.index') }}">Deposits</a></li>
        <li class="breadcrumb-item"><a href="{{ route('deposits.show', $deposit) }}">Deposit #{{ $deposit->id }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between g-3 mb-3">
                <div class="col-12 col-md-auto">
                    <h2 class="mb-0">Edit Deposit</h2>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('deposits.update', $deposit) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                                    <option value="{{ $member->id }}" @selected($deposit->member_id == $member->id)>
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
                            <input class="form-control @error('amount') is-invalid @enderror" type="number" id="amount" name="amount" step="0.01" placeholder="0.00" value="{{ old('amount', $deposit->amount) }}" required />
                            <label for="amount">Amount (Taka) <span class="text-danger">*</span></label>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Deposit Date -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('deposit_date') is-invalid @enderror" type="date" id="deposit_date" name="deposit_date" value="{{ old('deposit_date', $deposit->deposit_date->format('Y-m-d')) }}" required />
                            <label for="deposit_date">Deposit Date <span class="text-danger">*</span></label>
                            @error('deposit_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('payment_method_id') is-invalid @enderror" id="payment_method_id" name="payment_method_id" required>
                                <option value="">Select method...</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" @selected(old('payment_method_id', $deposit->payment_method_id) == $method->id)>{{ $method->name }}</option>
                                @endforeach
                            </select>
                            <label for="payment_method_id">Payment Method <span class="text-danger">*</span></label>
                            @error('payment_method_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Transaction ID -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('transaction_id') is-invalid @enderror" type="text" id="transaction_id" name="transaction_id" placeholder="e.g., TXN123456" value="{{ old('transaction_id', $deposit->transaction_id) }}" />
                            <label for="transaction_id">Transaction ID</label>
                            @error('transaction_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-md-9 col-sm-6">
                        <div class="form-floating">
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" placeholder="Add any additional notes..." style="min-height: 50px;">{{ old('notes', $deposit->notes) }}</textarea>
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
                <button class="btn btn-primary" type="submit">Update Deposit</button>
            </div>
            <div class="col-auto">
                <a class="btn btn-secondary" href="{{ route('deposits.show', $deposit) }}">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
