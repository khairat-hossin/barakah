@extends('layouts.phoenix')

@section('title', 'Edit Expense | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.show', $expense) }}">{{ $expense->expense_number }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-4">Edit Expense (Draft)</h2>

    <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Section 1: Expense Information -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Expense Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                    id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $expense->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="category_id">Category <span class="text-danger">*</span></label>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('title') is-invalid @enderror"
                                   type="text" id="title" name="title"
                                   value="{{ old('title', $expense->title) }}" required />
                            <label for="title">Title <span class="text-danger">*</span></label>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description"
                                      rows="3" required>{{ old('description', $expense->description) }}</textarea>
                            <label for="description">Description <span class="text-danger">*</span></label>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Linked To -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Linked To (Optional)</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="member_id" name="member_id">
                                <option value="">Select Member...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" @selected(old('member_id', $expense->member_id) == $member->id)>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="member_id">Member</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="project_id" name="project_id">
                                <option value="">Select Project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @selected(old('project_id', $expense->project_id) == $project->id)>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="project_id">Project</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Financial Details -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Financial Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input class="form-control @error('expense_date') is-invalid @enderror"
                                   type="date" id="expense_date" name="expense_date"
                                   value="{{ old('expense_date', $expense->expense_date) }}" required />
                            <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
                            @error('expense_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <input class="form-control @error('amount') is-invalid @enderror"
                                   type="number" id="amount" name="amount"
                                   step="0.01" min="0.01"
                                   value="{{ old('amount', $expense->amount) }}" required />
                            <label for="amount">Amount (৳) <span class="text-danger">*</span></label>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select @error('fund_source') is-invalid @enderror"
                                    id="fund_source" name="fund_source" required>
                                @foreach($fundSources as $source)
                                    <option value="{{ $source }}" @selected(old('fund_source', $expense->fund_source) == $source)>
                                        {{ ucfirst($source) }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="fund_source">Fund Source <span class="text-danger">*</span></label>
                            @error('fund_source')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <select class="form-select @error('payment_method') is-invalid @enderror"
                                    id="payment_method" name="payment_method" required>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method', $expense->payment_method) == $method)>
                                        {{ ucfirst(str_replace('_', ' ', $method)) }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Additional Details -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Additional Details</h5>
            </div>
            <div class="card-body">
                <div class="form-floating">
                    <textarea class="form-control @error('notes') is-invalid @enderror"
                              id="notes" name="notes"
                              rows="3">{{ old('notes', $expense->notes) }}</textarea>
                    <label for="notes">Notes</label>
                    @error('notes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row g-3">
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">
                    <span class="fas fa-save me-2"></span>Save Changes
                </button>
            </div>
            <div class="col-auto">
                <a class="btn btn-secondary" href="{{ route('expenses.show', $expense) }}">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
