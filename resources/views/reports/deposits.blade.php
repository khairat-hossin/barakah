@extends('layouts.phoenix')

@section('title', 'Deposit Report | Barakah')

@section('content')
<div class="mb-6">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Deposit Report</li>
        </ol>
    </nav>

    <div class="row align-items-center justify-content-between mb-3 g-2">
        <div class="col">
            <h2 class="mb-0 h4">Deposit Report</h2>
            <p class="text-body-secondary mb-0 small">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('reports.deposits.pdf', request()->query()) }}" class="btn btn-sm btn-danger"><span class="fas fa-file-pdf me-1"></span>PDF</a>
            <a href="{{ route('reports.deposits.excel', request()->query()) }}" class="btn btn-sm btn-success"><span class="fas fa-file-excel me-1"></span>Excel</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.deposits') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Payment Method</label>
                    <select name="payment_method_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" @selected(($filters['payment_method_id'] ?? '') == $pm->id)>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Member</label>
                    <select name="member_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" @selected(($filters['member_id'] ?? '') == $m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="fas fa-filter me-1"></span>Apply</button>
                    <a href="{{ route('reports.deposits') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-4 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">Total Collected</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳{{ number_format($totalAmount, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">In range</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Deposits</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($count) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Records</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-info fw-semibold" style="font-size: 0.75rem;">Average</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳{{ number_format($average, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Per deposit</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th class="fs-9 text-body-secondary">Date</th>
                            <th class="fs-9 text-body-secondary">Member</th>
                            <th class="fs-9 text-body-secondary">Transaction ID</th>
                            <th class="fs-9 text-body-secondary">Method</th>
                            <th class="fs-9 text-body-secondary text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $e)
                            <tr>
                                <td class="fs-9">{{ $e->deposit_date?->format('d M Y') }}</td>
                                <td class="fs-9">{{ $e->member?->name ?? 'N/A' }}</td>
                                <td class="fs-9 text-body-tertiary">{{ $e->transaction_id }}</td>
                                <td class="fs-9">{{ $e->paymentMethod?->name ?? ucfirst($e->payment_method) }}</td>
                                <td class="fs-9 text-end fw-semibold">৳{{ number_format($e->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-body-secondary"><small>No deposits found for this range.</small></td></tr>
                        @endforelse
                    </tbody>
                    @if($entries->count())
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
