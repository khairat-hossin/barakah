@extends('layouts.phoenix')

@section('title', 'New Loan | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.index') }}">Loans</a></li>
        <li class="breadcrumb-item active">New</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-4">New Loan</h2>

    <form method="POST" action="{{ route('loans.store') }}">
        @csrf
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary"><h5 class="mb-0">Loan Details</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Loan Code</label>
                        <input type="text" class="form-control" value="{{ $nextLoanCode }}" disabled />
                        <small class="text-body-secondary">Auto-generated</small>
                    </div>
                    <div class="col-md-5 col-sm-6">
                        <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                        <select class="form-select @error('member_id') is-invalid @enderror" name="member_id" required>
                            <option value="">Select Member...</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}" @selected(old('member_id') == $m->id)>{{ $m->name }} ({{ $m->member_code }})</option>
                            @endforeach
                        </select>
                        @error('member_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label fw-semibold">Purpose</label>
                        <input type="text" class="form-control @error('purpose') is-invalid @enderror" name="purpose" value="{{ old('purpose') }}" placeholder="e.g. Medical, Business" />
                        @error('purpose')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Loan Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" class="form-control @error('loan_amount') is-invalid @enderror" name="loan_amount" value="{{ old('loan_amount') }}" required />
                        @error('loan_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Service Charge (৳)</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('service_charge') is-invalid @enderror" name="service_charge" value="{{ old('service_charge', 0) }}" />
                        <small class="text-body-secondary">Optional. Total payable = loan + charge</small>
                        @error('service_charge')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Taken Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('taken_date') is-invalid @enderror" name="taken_date" value="{{ old('taken_date', today()->toDateString()) }}" required />
                        @error('taken_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" value="{{ old('due_date') }}" />
                        <small class="text-body-secondary">Expected return date</small>
                        @error('due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Comment</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" name="comment" rows="3" placeholder="Any notes about this loan">{{ old('comment') }}</textarea>
                        @error('comment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">This loan will be created as <strong>Pending</strong> and must be approved before it is disbursed and posted to accounting.</div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><span class="fas fa-check me-2"></span>Submit for Approval</button>
            <a href="{{ route('loans.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
