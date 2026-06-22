@extends('layouts.phoenix')

@section('title', 'Investment Report | ' . \App\Support\Branding::name())

@section('content')
<div class="mb-6">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Investment Report</li>
        </ol>
    </nav>

    <div class="row align-items-center justify-content-between mb-3 g-2">
        <div class="col">
            <h2 class="mb-0 h4">Investment Report</h2>
            <p class="text-body-secondary mb-0 small">Started {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('reports.investments.pdf', request()->query()) }}" class="btn btn-sm btn-danger" target="_blank" rel="noopener"><span class="fas fa-file-pdf me-1"></span>PDF</a>
            <a href="{{ route('reports.investments.excel', request()->query()) }}" class="btn btn-sm btn-success"><span class="fas fa-file-excel me-1"></span>Excel</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.investments') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">From (start date)</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">To (start date)</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Type</label>
                    <select name="investment_type_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}" @selected(($filters['investment_type_id'] ?? '') == $t->id)>{{ $t->name }}</option>
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
                    <a href="{{ route('reports.investments') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">Total Invested</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳{{ number_format($totalInvested, 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-info fw-semibold" style="font-size: 0.75rem;">Total Returned</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳{{ number_format($totalReturned, 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid {{ $netProfit >= 0 ? '#198754' : '#dc3545' }} !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="fw-semibold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.75rem;">Net P/L</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0; color: {{ $netProfit >= 0 ? '#198754' : '#dc3545' }};">৳{{ number_format($netProfit, 0) }}</h6>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Count</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($count) }}</h6>
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
                            <th class="fs-9 text-body-secondary">Name</th>
                            <th class="fs-9 text-body-secondary">Type</th>
                            <th class="fs-9 text-body-secondary text-center">Status</th>
                            <th class="fs-9 text-body-secondary text-end">Invested</th>
                            <th class="fs-9 text-body-secondary text-end">Returned</th>
                            <th class="fs-9 text-body-secondary text-end">Net P/L</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($investments as $i)
                            <tr>
                                <td class="fs-9 text-body-tertiary">{{ $i->code }}</td>
                                <td class="fs-9">{{ $i->name }}</td>
                                <td class="fs-9">{{ $i->investmentType?->name ?? 'N/A' }}</td>
                                <td class="fs-9 text-center"><span class="badge badge-phoenix badge-phoenix-{{ $i->status === 'active' ? 'success' : ($i->status === 'matured' ? 'info' : 'secondary') }}">{{ ucfirst($i->status) }}</span></td>
                                <td class="fs-9 text-end">৳{{ number_format($i->total_invested_amount, 0) }}</td>
                                <td class="fs-9 text-end">৳{{ number_format($i->total_returned_amount, 0) }}</td>
                                <td class="fs-9 text-end fw-semibold {{ $i->net_profit_loss >= 0 ? 'text-success' : 'text-danger' }}">৳{{ number_format($i->net_profit_loss, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-body-secondary"><small>No investments found for this range.</small></td></tr>
                        @endforelse
                    </tbody>
                    @if($investments->count())
                        <tfoot>
                            <tr class="border-top">
                                <th colspan="4" class="fs-9 text-end">Total</th>
                                <th class="fs-9 text-end">৳{{ number_format($totalInvested, 0) }}</th>
                                <th class="fs-9 text-end">৳{{ number_format($totalReturned, 0) }}</th>
                                <th class="fs-9 text-end">৳{{ number_format($netProfit, 0) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
