@extends('layouts.phoenix')

@section('title', 'Loan Report | ' . \App\Support\Branding::name())

@php
    $badges = [
        'pending' => 'warning', 'active' => 'primary', 'repaid' => 'success',
        'rejected' => 'secondary', 'written_off' => 'secondary',
    ];
@endphp

@section('content')
<div class="mb-6">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Loan Report</li>
        </ol>
    </nav>

    <div class="row align-items-center justify-content-between mb-3 g-2">
        <div class="col">
            <h2 class="mb-0 h4">Loan Report</h2>
            <p class="text-body-secondary mb-0 small">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('reports.loans.pdf', request()->query()) }}" class="btn btn-sm btn-danger" target="_blank" rel="noopener"><span class="fas fa-file-pdf me-1"></span>PDF</a>
            <a href="{{ route('reports.loans.excel', request()->query()) }}" class="btn btn-sm btn-success"><span class="fas fa-file-excel me-1"></span>Excel</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.loans') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">From Month</label>
                    <input type="month" name="from_month" value="{{ $fromMonth }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">To Month</label>
                    <input type="month" name="to_month" value="{{ $toMonth }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
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
                    <a href="{{ route('reports.loans') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">Total Lent</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem;">৳{{ number_format($totalLent, 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Total Repaid</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem;">৳{{ number_format($totalRepaid, 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #fd7e14 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-warning fw-semibold" style="font-size: 0.75rem;">Outstanding</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem;">৳{{ number_format($totalOutstanding, 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #6c757d !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Loans</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem;">{{ number_format($count) }}</h6>
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
                            <th class="fs-9 text-body-secondary">Code</th>
                            <th class="fs-9 text-body-secondary">Member</th>
                            <th class="fs-9 text-body-secondary">Taken</th>
                            <th class="fs-9 text-body-secondary">Due</th>
                            <th class="fs-9 text-body-secondary">Status</th>
                            <th class="fs-9 text-body-secondary text-end">Loan</th>
                            <th class="fs-9 text-body-secondary text-end">Repaid</th>
                            <th class="fs-9 text-body-secondary text-end">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $l)
                            <tr>
                                <td class="fs-9"><a href="{{ route('loans.show', $l) }}">{{ $l->loan_code }}</a></td>
                                <td class="fs-9">{{ $l->member?->name ?? 'N/A' }}</td>
                                <td class="fs-9">{{ $l->taken_date?->format('d M Y') }}</td>
                                <td class="fs-9">{{ $l->due_date?->format('d M Y') ?? '—' }}</td>
                                <td class="fs-9"><span class="badge badge-phoenix badge-phoenix-{{ $badges[$l->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$l->status)) }}</span></td>
                                <td class="fs-9 text-end">৳{{ number_format($l->loan_amount, 2) }}</td>
                                <td class="fs-9 text-end text-success">৳{{ number_format($l->total_repaid, 2) }}</td>
                                <td class="fs-9 text-end fw-semibold">৳{{ number_format($l->outstanding_balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-body-secondary"><small>No loans found for this range.</small></td></tr>
                        @endforelse
                    </tbody>
                    @if($loans->count())
                        <tfoot>
                            <tr class="border-top">
                                <th colspan="5" class="fs-9 text-end">Totals</th>
                                <th class="fs-9 text-end">৳{{ number_format($totalLent, 2) }}</th>
                                <th class="fs-9 text-end">৳{{ number_format($totalRepaid, 2) }}</th>
                                <th class="fs-9 text-end">৳{{ number_format($totalOutstanding, 2) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
