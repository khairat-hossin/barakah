@extends('layouts.phoenix')

@section('title', 'Create Expense | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between g-3 mb-3">
                <div class="col-12 col-md-auto">
                    <h2 class="mb-0">Record Expense</h2>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
        @csrf

        <!-- Expense Information -->
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Expense Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Category -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                    id="category_id" name="category_id" required>
                                <option value="">Select Category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
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

                    <!-- Title -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('title') is-invalid @enderror"
                                   type="text" id="title" name="title"
                                   placeholder="Expense Title"
                                   value="{{ old('title') }}" required />
                            <label for="title">Title <span class="text-danger">*</span></label>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('amount') is-invalid @enderror"
                                   type="number" id="amount" name="amount"
                                   step="0.01" min="0.01" placeholder="0.00"
                                   value="{{ old('amount') }}" required />
                            <label for="amount">Amount (৳) <span class="text-danger">*</span></label>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Expense Date -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('expense_date') is-invalid @enderror"
                                   type="date" id="expense_date" name="expense_date"
                                   value="{{ old('expense_date', today()) }}" required />
                            <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
                            @error('expense_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Fund Source -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('fund_source') is-invalid @enderror"
                                    id="fund_source" name="fund_source" required>
                                <option value="">Select Fund Source...</option>
                                @foreach($fundSources as $source)
                                    <option value="{{ $source }}" @selected(old('fund_source') == $source)>
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

                    <!-- Payment Method -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('payment_method') is-invalid @enderror"
                                    id="payment_method" name="payment_method" required>
                                <option value="">Select Method...</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method') == $method)>
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

                    <!-- Member (Optional) -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('member_id') is-invalid @enderror"
                                    id="member_id" name="member_id">
                                <option value="">Select Member...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="member_id">Member (Optional)</label>
                            @error('member_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Project (Optional) -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('project_id') is-invalid @enderror"
                                    id="project_id" name="project_id">
                                <option value="">Select Project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="project_id">Project (Optional)</label>
                            @error('project_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-md-9 col-sm-6">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description"
                                      placeholder="Detailed description of the expense"
                                      style="min-height: 50px;" required>{{ old('description') }}</textarea>
                            <label for="description">Description <span class="text-danger">*</span></label>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-md-9 col-sm-6">
                        <div class="form-floating">
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes"
                                      placeholder="Internal notes or remarks"
                                      style="min-height: 50px;">{{ old('notes') }}</textarea>
                            <label for="notes">Notes</label>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="col-12">
                        <label class="form-label" for="attachments">Attachments</label>
                        <input class="form-control @error('attachments') is-invalid @enderror"
                               type="file" id="attachments" name="attachments[]"
                               multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" />
                        <small class="text-body-secondary">Upload receipt, invoice, voucher, or supporting documents (PDF, JPG, PNG, DOC, DOCX, XLS, XLSX - Max 5MB each)</small>
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
                <button class="btn btn-primary" type="submit" name="action" value="save_draft">
                    <span class="fas fa-save me-2"></span>Save as Draft
                </button>
            </div>
            <div class="col-auto">
                <button class="btn btn-success" type="submit" name="action" value="submit">
                    <span class="fas fa-check me-2"></span>Submit for Approval
                </button>
            </div>
            <div class="col-auto">
                <a class="btn btn-secondary" href="{{ route('expenses.index') }}">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
