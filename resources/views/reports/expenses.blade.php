@extends('layouts.phoenix')

@section('title', 'Expense Report | Barakah')

@section('content')
<div class="mb-6">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Expense Report</li>
        </ol>
    </nav>

    <div class="row align-items-center justify-content-between mb-3 g-2">
        <div class="col">
            <h2 class="mb-0 h4">Expense Report</h2>
            <p class="text-body-secondary mb-0 small">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('reports.expenses.pdf', request()->query()) }}" class="btn btn-sm btn-danger"><span class="fas fa-file-pdf me-1"></span>PDF</a>
            <a href="{{ route('reports.expenses.excel', request()->query()) }}" class="btn btn-sm btn-success"><span class="fas fa-file-excel me-1"></span>Excel</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.expenses') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(($filters['category_id'] ?? '') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" @selected(($filters['status'] ?? '') == $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="fas fa-filter me-1"></span>Apply</button>
                    <a href="{{ route('reports.expenses') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-6 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-danger fw-semibold" style="font-size: 0.75rem;">Total Expenses</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳{{ number_format($totalAmount, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">In range</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-6 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Records</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($count) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Expenses</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th class="fs-9 text-body-secondary">Date</th>
                            <th class="fs-9 text-body-secondary">Category</th>
                            <th class="fs-9 text-body-secondary">Title</th>
                            <th class="fs-9 text-body-secondary text-center">Status</th>
                            <th class="fs-9 text-body-secondary text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr>
                                <td class="fs-9">{{ $e->expense_date?->format('d M Y') }}</td>
                                <td class="fs-9">{{ $e->category?->name ?? 'N/A' }}</td>
                                <td class="fs-9">{{ $e->title }}</td>
                                <td class="fs-9 text-center"><span class="badge badge-phoenix badge-phoenix-{{ $e->status === 'paid' ? 'success' : ($e->status === 'approved' ? 'info' : ($e->status === 'pending' ? 'warning' : 'secondary')) }}">{{ ucfirst($e->status) }}</span></td>
                                <td class="fs-9 text-end fw-semibold">৳{{ number_format($e->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-body-secondary"><small>No expenses found for this range.</small></td></tr>
                        @endforelse
                    </tbody>
                    @if($expenses->count())
                        <tfoot>
                            <tr class="border-top">
                                <th colspan="4" class="fs-9 text-end">Total</th>
                                <th class="fs-9 text-end">৳{{ number_format($totalAmount, 2) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
