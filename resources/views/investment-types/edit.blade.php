@extends('layouts.phoenix')

@section('title', 'Edit Investment Type | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('investment-types.index') }}">Investment Types</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Edit Investment Type</h2>
            <p class="text-body-secondary">Update investment type details</p>
        </div>
    </div>

    <form method="POST" action="{{ route('investment-types.update', $type) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Code (Read-only) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control" type="text" value="{{ $type->code }}" disabled />
                            <label>Code (Cannot be changed)</label>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('name') is-invalid @enderror"
                                   type="text" id="name" name="name"
                                   placeholder="Investment type name"
                                   value="{{ old('name', $type->name) }}" required />
                            <label for="name">Name <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('category') is-invalid @enderror"
                                   type="text" id="category" name="category"
                                   placeholder="Category (e.g., Savings, Bonds)"
                                   value="{{ old('category', $type->category) }}" />
                            <label for="category">Category</label>
                            @error('category')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Default Return Type -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('default_return_type') is-invalid @enderror"
                                    id="default_return_type" name="default_return_type" required>
                                <option value="">Select return type...</option>
                                <option value="fixed" @selected(old('default_return_type', $type->default_return_type) === 'fixed')>Fixed</option>
                                <option value="variable" @selected(old('default_return_type', $type->default_return_type) === 'variable')>Variable</option>
                                <option value="dividend" @selected(old('default_return_type', $type->default_return_type) === 'dividend')>Dividend</option>
                            </select>
                            <label for="default_return_type">Default Return Type <span class="text-danger">*</span></label>
                            @error('default_return_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Default Tenure Months -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('default_tenure_months') is-invalid @enderror"
                                   type="number" id="default_tenure_months" name="default_tenure_months"
                                   placeholder="Months"
                                   value="{{ old('default_tenure_months', $type->default_tenure_months) }}" min="0" />
                            <label for="default_tenure_months">Default Tenure (Months)</label>
                            @error('default_tenure_months')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Min Investment Amount -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('min_investment_amount') is-invalid @enderror"
                                   type="number" id="min_investment_amount" name="min_investment_amount"
                                   placeholder="0.00"
                                   value="{{ old('min_investment_amount', $type->min_investment_amount) }}" step="0.01" min="0" />
                            <label for="min_investment_amount">Minimum Investment Amount (৳)</label>
                            @error('min_investment_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Max Investment Amount -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('max_investment_amount') is-invalid @enderror"
                                   type="number" id="max_investment_amount" name="max_investment_amount"
                                   placeholder="0.00"
                                   value="{{ old('max_investment_amount', $type->max_investment_amount) }}" step="0.01" min="0" />
                            <label for="max_investment_amount">Maximum Investment Amount (৳)</label>
                            @error('max_investment_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description"
                                      placeholder="Description of this investment type"
                                      style="min-height: 100px;">{{ old('description', $type->description) }}</textarea>
                            <label for="description">Description</label>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input @error('requires_approval') is-invalid @enderror"
                                   type="checkbox" id="requires_approval" name="requires_approval"
                                   value="1" @checked(old('requires_approval', $type->requires_approval)) />
                            <label class="form-check-label" for="requires_approval">
                                Requires Approval for New Investments
                            </label>
                            @error('requires_approval')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input @error('is_active') is-invalid @enderror"
                                   type="checkbox" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active', $type->is_active)) />
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                            @error('is_active')
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
            <form action="{{ route('investment-types.destroy', $type) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this investment type?')">
                    <span class="fas fa-trash me-2"></span>Delete
                </button>
            </form>
            <a class="btn btn-secondary" href="{{ route('investment-types.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
