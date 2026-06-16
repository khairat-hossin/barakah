@extends('layouts.phoenix')
@section('title', 'Create Investment | Barakah')
@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('investments.index') }}">Investments</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-3">Create Investment</h2>

    <form method="POST" action="{{ route('investments.store') }}">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name') }}" required />
                            <label for="name">Investment Name *</label>
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('investment_type_id') is-invalid @enderror" id="investment_type_id" name="investment_type_id" required>
                                <option value="">Select Type...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" @selected(old('investment_type_id') == $type->id)>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <label for="investment_type_id">Investment Type *</label>
                            @error('investment_type_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('start_date') is-invalid @enderror" type="date" id="start_date" name="start_date" value="{{ old('start_date', today()) }}" required />
                            <label for="start_date">Start Date *</label>
                            @error('start_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('maturity_date') is-invalid @enderror" type="date" id="maturity_date" name="maturity_date" value="{{ old('maturity_date') }}" />
                            <label for="maturity_date">Maturity Date</label>
                            @error('maturity_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input class="form-control @error('tenure_months') is-invalid @enderror" type="number" id="tenure_months" name="tenure_months" value="{{ old('tenure_months') }}" required />
                            <label for="tenure_months">Tenure (Months) *</label>
                            @error('tenure_months')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select @error('risk_level') is-invalid @enderror" id="risk_level" name="risk_level" required>
                                <option value="">Select Risk...</option>
                                <option value="low" @selected(old('risk_level') == 'low')>Low</option>
                                <option value="medium" @selected(old('risk_level') == 'medium')>Medium</option>
                                <option value="high" @selected(old('risk_level') == 'high')>High</option>
                            </select>
                            <label for="risk_level">Risk Level *</label>
                            @error('risk_level')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input class="form-control @error('expected_return_percentage') is-invalid @enderror" type="number" step="0.01" id="expected_return_percentage" name="expected_return_percentage" value="{{ old('expected_return_percentage') }}" />
                            <label for="expected_return_percentage">Expected Return %</label>
                            @error('expected_return_percentage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('return_type') is-invalid @enderror" id="return_type" name="return_type" required>
                                <option value="">Select Return Type...</option>
                                <option value="fixed" @selected(old('return_type') == 'fixed')>Fixed</option>
                                <option value="variable" @selected(old('return_type') == 'variable')>Variable</option>
                                <option value="dividend" @selected(old('return_type') == 'dividend')>Dividend</option>
                            </select>
                            <label for="return_type">Return Type *</label>
                            @error('return_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="investor_id" name="investor_id">
                                <option value="">Select Investor...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" @selected(old('investor_id') == $member->id)>{{ $member->name }}</option>
                                @endforeach
                            </select>
                            <label for="investor_id">Investor (Optional)</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" style="min-height: 100px;" required>{{ old('description') }}</textarea>
                            <label for="description">Description *</label>
                            @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control" id="notes" name="notes" style="min-height: 80px;">{{ old('notes') }}</textarea>
                            <label for="notes">Notes</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">
                <span class="fas fa-save me-2"></span>Create Investment
            </button>
            <a class="btn btn-secondary" href="{{ route('investments.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
