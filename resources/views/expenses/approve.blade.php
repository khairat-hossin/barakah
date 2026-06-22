@extends('layouts.phoenix')

@section('title', 'Approve Expense | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.show', $expense) }}">{{ $expense->expense_number }}</a></li>
        <li class="breadcrumb-item active">Approve</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Approve Expense</h2>

            <!-- Expense Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Expense Number</small>
                            <h6 class="mb-0">{{ $expense->expense_number }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Amount</small>
                            <h6 class="mb-0">৳ {{ number_format($expense->amount, 2) }}</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Category</small>
                            <p class="mb-0">{{ $expense->category->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Expense Date</small>
                            <p class="mb-0">{{ $expense->expense_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-12">
                            <small class="text-body-secondary d-block mb-1">Title</small>
                            <p class="mb-0">{{ $expense->title }}</p>
                        </div>
                        <div class="col-12">
                            <small class="text-body-secondary d-block mb-1">Description</small>
                            <p class="mb-0">{{ $expense->description }}</p>
                        </div>
                        @if($expense->member)
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Member</small>
                            <p class="mb-0">{{ $expense->member->name }}</p>
                        </div>
                        @endif
                        @if($expense->project)
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Project</small>
                            <p class="mb-0">{{ $expense->project->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval Form -->
            <form method="POST" action="{{ route('expenses.approve-store', $expense) }}">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">Approval Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Approval Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      name="notes" rows="4"
                                      placeholder="Add any notes related to this approval...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Approval Confirmation -->
                <div class="alert alert-info mb-4">
                    <span class="fas fa-info-circle me-2"></span>
                    <strong>Confirm Approval:</strong> You are about to approve this expense for <strong>৳ {{ number_format($expense->amount, 2) }}</strong>.
                    Once approved, the expense can be marked as paid.
                </div>

                <div class="row g-3">
                    <div class="col-auto">
                        <button class="btn btn-success btn-lg" type="submit">
                            <span class="fas fa-check me-2"></span>Approve Expense
                        </button>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-secondary btn-lg" href="{{ route('expenses.show', $expense) }}">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
