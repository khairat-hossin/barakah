@extends('layouts.phoenix')

@section('title', 'Expenses | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Expenses</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Expense Management</h2>
            <p class="text-body-secondary">Track and manage organizational expenses</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-2"></span>New Expense
                </a>
                @can('manage expenses')
                    <a href="{{ route('expense-categories.index') }}" class="btn btn-outline-secondary" title="Manage categories">
                        <span class="fas fa-tags me-2"></span>Categories
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Total Expenses</p>
                            <h4 class="mb-0">৳ {{ number_format($totalExpenses, 0) }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-success rounded-pill">All Time</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">This Month</p>
                            <h4 class="mb-0">৳ {{ number_format($monthlyExpenses, 0) }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-primary rounded-pill">Current</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Pending Approval</p>
                            <h4 class="mb-0">{{ $pendingCount }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-warning rounded-pill">Action</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Total Records</p>
                            <h4 class="mb-0">{{ $expenses->count() }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-info rounded-pill">Count</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input class="form-control search-input" type="search" placeholder="Search by title, expense #..." id="searchInput" />
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="fundSourceFilter">
                        <option value="">All Fund Sources</option>
                        @foreach($fundSources as $source)
                            <option value="{{ $source }}">{{ ucfirst($source) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="table-responsive">
            <table id="expensesTable" class="table table-hover mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="fw-semibold">Exp #</th>
                        <th class="fw-semibold">Date</th>
                        <th class="fw-semibold">Category</th>
                        <th class="fw-semibold">Title</th>
                        <th class="fw-semibold">Member/Project</th>
                        <th class="fw-semibold text-end">Amount</th>
                        <th class="fw-semibold">Source</th>
                        <th class="fw-semibold">Status</th>
                        <th class="fw-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td class="fw-semibold"><a href="{{ route('expenses.show', $expense) }}">{{ $expense->expense_number }}</a></td>
                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                        <td>{{ $expense->category->name }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>{{ $expense->member?->name ?? $expense->project?->name ?? '-' }}</td>
                        <td class="text-end">৳ {{ number_format($expense->amount, 2) }}</td>
                        <td><small class="badge bg-light text-dark">{{ ucfirst($expense->fund_source) }}</small></td>
                        <td>
                            @if($expense->status === 'draft')
                                <span class="badge badge-phoenix badge-phoenix-secondary">Draft</span>
                            @elseif($expense->status === 'pending')
                                <span class="badge badge-phoenix badge-phoenix-warning">Pending</span>
                            @elseif($expense->status === 'approved')
                                <span class="badge badge-phoenix badge-phoenix-success">Approved</span>
                            @else
                                <span class="badge badge-phoenix badge-phoenix-info">Paid</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="gap-1 d-flex justify-content-center">
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <span class="fas fa-eye"></span>
                                </a>
                                @if($expense->status === 'draft')
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <span class="fas fa-edit"></span>
                                    </a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this expense?')">
                                            <span class="fas fa-trash"></span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No expenses found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
