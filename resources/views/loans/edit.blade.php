@extends('layouts.phoenix')

@section('title', 'Edit Loan | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.index') }}">Loans</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.show', $loan) }}">{{ $loan->loan_code }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-4">Edit Loan {{ $loan->loan_code }}</h2>

    <form method="POST" action="{{ route('loans.update', $loan) }}">
        @csrf
        @method('PUT')
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary"><h5 class="mb-0">Loan Details</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5 col-sm-6">
                        <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                        <select class="form-select @error('member_id') is-invalid @enderror" name="member_id" required>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}" @selected(old('member_id', $loan->member_id) == $m->id)>{{ $m->name }} ({{ $m->member_code }})</option>
                            @endforeach
                        </select>
                        @error('member_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label fw-semibold">Purpose</label>
                        <input type="text" class="form-control" name="purpose" value="{{ old('purpose', $loan->purpose) }}" />
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Loan Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" class="form-control @error('loan_amount') is-invalid @enderror" name="loan_amount" value="{{ old('loan_amount', $loan->loan_amount) }}" required />
                        @error('loan_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Service Charge (৳)</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="service_charge" value="{{ old('service_charge', $loan->service_charge) }}" />
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Taken Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('taken_date') is-invalid @enderror" name="taken_date" value="{{ old('taken_date', $loan->taken_date?->toDateString()) }}" required />
                        @error('taken_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" value="{{ old('due_date', $loan->due_date?->toDateString()) }}" />
                        @error('due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Comment</label>
                        <textarea class="form-control" name="comment" rows="3">{{ old('comment', $loan->comment) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><span class="fas fa-save me-2"></span>Save Changes</button>
            <a href="{{ route('loans.show', $loan) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
