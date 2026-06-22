@extends('layouts.phoenix')

@section('title', 'Edit Investment | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('investments.index') }}">Investments</a></li>
        <li class="breadcrumb-item"><a href="{{ route('investments.show', $investment) }}">{{ $investment->code }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Edit Investment (Draft)</h2>
            <p class="text-body-secondary">Update investment details - only available for draft investments</p>
        </div>
    </div>

    <form method="POST" action="{{ route('investments.update', $investment) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Code (Read-only) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control" type="text" value="{{ $investment->code }}" disabled />
                            <label>Investment Code (Auto-generated)</label>
                        </div>
                    </div>

                    <!-- Status (Read-only) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control" type="text"
                                   value="{{ ucfirst($investment->status) }}" disabled />
                            <label>Status</label>
                        </div>
                    </div>

                    <!-- Investment Name -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('name') is-invalid @enderror"
                                   type="text" id="name" name="name"
                                   placeholder="Investment name"
                                   value="{{ old('name', $investment->name) }}" required />
                            <label for="name">Investment Name <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Investment Type -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('investment_type_id') is-invalid @enderror"
                                    id="investment_type_id" name="investment_type_id" required>
                                <option value="">Select investment type...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}"
                                            @selected(old('investment_type_id', $investment->investment_type_id) == $type->id)>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="investment_type_id">Investment Type <span class="text-danger">*</span></label>
                            @error('investment_type_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Investor (Member) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('investor_id') is-invalid @enderror"
                                    id="investor_id" name="investor_id">
                                <option value="">Select member...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}"
                                            @selected(old('investor_id', $investment->investor_id) == $member->id)>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="investor_id">Investor (Member)</label>
                            @error('investor_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('start_date') is-invalid @enderror"
                                   type="date" id="start_date" name="start_date"
                                   value="{{ old('start_date', $investment->start_date->format('Y-m-d')) }}" required />
                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tenure (Months) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('tenure_months') is-invalid @enderror"
                                   type="number" id="tenure_months" name="tenure_months"
                                   placeholder="Number of months"
                                   value="{{ old('tenure_months', $investment->tenure_months) }}"
                                   min="1" />
                            <label for="tenure_months">Tenure (Months)</label>
                            @error('tenure_months')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Maturity Date -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('maturity_date') is-invalid @enderror"
                                   type="date" id="maturity_date" name="maturity_date"
                                   value="{{ old('maturity_date', $investment->maturity_date?->format('Y-m-d')) }}" />
                            <label for="maturity_date">Maturity Date</label>
                            @error('maturity_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Risk Level -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('risk_level') is-invalid @enderror"
                                    id="risk_level" name="risk_level" required>
                                <option value="">Select risk level...</option>
                                <option value="low" @selected(old('risk_level', $investment->risk_level) === 'low')>Low</option>
                                <option value="medium" @selected(old('risk_level', $investment->risk_level) === 'medium')>Medium</option>
                                <option value="high" @selected(old('risk_level', $investment->risk_level) === 'high')>High</option>
                            </select>
                            <label for="risk_level">Risk Level <span class="text-danger">*</span></label>
                            @error('risk_level')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Return Type -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('return_type') is-invalid @enderror"
                                    id="return_type" name="return_type" required>
                                <option value="">Select return type...</option>
                                <option value="fixed" @selected(old('return_type', $investment->return_type) === 'fixed')>Fixed</option>
                                <option value="variable" @selected(old('return_type', $investment->return_type) === 'variable')>Variable</option>
                                <option value="dividend" @selected(old('return_type', $investment->return_type) === 'dividend')>Dividend</option>
                            </select>
                            <label for="return_type">Return Type <span class="text-danger">*</span></label>
                            @error('return_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Expected Return Percentage -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('expected_return_percentage') is-invalid @enderror"
                                   type="number" id="expected_return_percentage" name="expected_return_percentage"
                                   placeholder="0.00"
                                   value="{{ old('expected_return_percentage', $investment->expected_return_percentage) }}"
                                   step="0.01" min="0" max="100" />
                            <label for="expected_return_percentage">Expected Return (%)</label>
                            @error('expected_return_percentage')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description"
                                      placeholder="Investment description"
                                      style="min-height: 100px;">{{ old('description', $investment->description) }}</textarea>
                            <label for="description">Description</label>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes"
                                      placeholder="Additional notes"
                                      style="min-height: 80px;">{{ old('notes', $investment->notes) }}</textarea>
                            <label for="notes">Notes</label>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">
                <span class="fas fa-save me-2"></span>Save Changes
            </button>
            <a class="btn btn-secondary" href="{{ route('investments.show', $investment) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
