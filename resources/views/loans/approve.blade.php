@extends('layouts.phoenix')

@section('title', 'Approve Loan ' . $loan->loan_code . ' | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.index') }}">Loans</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.show', $loan) }}">{{ $loan->loan_code }}</a></li>
        <li class="breadcrumb-item active">Approve</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-body-tertiary"><h5 class="mb-0">Approve Loan {{ $loan->loan_code }}</h5></div>
                <div class="card-body">
                    <dl class="row fs-9">
                        <dt class="col-sm-4 text-body-secondary">Member</dt>
                        <dd class="col-sm-8">{{ $loan->member?->name }} ({{ $loan->member?->member_code }})</dd>
                        <dt class="col-sm-4 text-body-secondary">Loan Amount</dt>
                        <dd class="col-sm-8 fw-bold">৳ {{ number_format($loan->loan_amount, 2) }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Service Charge</dt>
                        <dd class="col-sm-8">৳ {{ number_format($loan->service_charge, 2) }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Total Payable</dt>
                        <dd class="col-sm-8">৳ {{ number_format($loan->total_payable, 2) }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Taken Date</dt>
                        <dd class="col-sm-8">{{ $loan->taken_date?->format('d M Y') }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Purpose</dt>
                        <dd class="col-sm-8">{{ $loan->purpose ?: '—' }}</dd>
                    </dl>

                    <div class="alert alert-info fs-9">Approving will mark the loan <strong>Active</strong>, set you as approver, and post a journal voucher: <strong>Dr Loans to Members / Cr Bank</strong> for ৳ {{ number_format($loan->loan_amount, 0) }}.</div>

                    <form method="POST" action="{{ route('loans.approve.store', $loan) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Approval Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional">{{ old('notes') }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><span class="fas fa-check me-2"></span>Approve & Disburse</button>
                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
