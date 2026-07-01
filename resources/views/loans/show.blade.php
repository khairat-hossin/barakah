@extends('layouts.phoenix')

@section('title', 'Loan ' . $loan->loan_code . ' | ' . \App\Support\Branding::name())

@php
    $badges = [
        'pending' => ['Pending', 'warning'], 'active' => ['Active', 'primary'],
        'partially_repaid' => ['Partially Repaid', 'info'], 'overdue' => ['Overdue', 'danger'],
        'repaid' => ['Repaid', 'success'], 'rejected' => ['Rejected', 'secondary'],
        'written_off' => ['Written Off', 'secondary'],
    ];
    [$badgeLabel, $badgeColor] = $badges[$loan->display_status] ?? [$loan->display_status, 'secondary'];
@endphp

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.index') }}">Loans</a></li>
        <li class="breadcrumb-item active">{{ $loan->loan_code }}</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between g-2 mb-3">
        <div class="col-auto">
            <h2 class="mb-0 d-inline-block me-2">Loan {{ $loan->loan_code }}</h2>
            <span class="badge badge-phoenix badge-phoenix-{{ $badgeColor }} fs-9 align-middle">{{ $badgeLabel }}</span>
        </div>
        <div class="col-auto d-flex gap-2">
            @can('approve', $loan)
                @if($loan->status === 'pending')
                    <a href="{{ route('loans.approve', $loan) }}" class="btn btn-sm btn-success"><span class="fas fa-check me-1"></span>Approve</a>
                    <form method="POST" action="{{ route('loans.reject', $loan) }}" data-confirm="Reject this loan request?">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger"><span class="fas fa-times me-1"></span>Reject</button>
                    </form>
                @endif
            @endcan
            @can('update', $loan)
                @if($loan->status === 'pending')
                    <a href="{{ route('loans.edit', $loan) }}" class="btn btn-sm btn-outline-primary"><span class="fas fa-edit me-1"></span>Edit</a>
                @endif
            @endcan
            @can('manage loans')
                @if($loan->status === 'active')
                    <form method="POST" action="{{ route('loans.write-off', $loan) }}" data-confirm="Write off the remaining balance of this loan?">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning"><span class="fas fa-ban me-1"></span>Write Off</button>
                    </form>
                @endif
            @endcan
            @can('delete', $loan)
                @if(in_array($loan->status, ['pending', 'rejected']))
                    <form method="POST" action="{{ route('loans.destroy', $loan) }}" data-confirm="Delete this loan?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><span class="fas fa-trash me-1"></span>Delete</button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: details + repayments -->
        <div class="col-lg-8">
            <!-- Summary numbers -->
            <div class="row g-2 mb-4">
                <div class="col-4">
                    <div class="card h-100"><div class="card-body py-2 px-3">
                        <small class="text-body-secondary">Total Payable</small>
                        <h5 class="mb-0">৳ {{ number_format($loan->total_payable, 0) }}</h5>
                    </div></div>
                </div>
                <div class="col-4">
                    <div class="card h-100"><div class="card-body py-2 px-3">
                        <small class="text-body-secondary">Repaid</small>
                        <h5 class="mb-0 text-success">৳ {{ number_format($loan->total_repaid, 0) }}</h5>
                    </div></div>
                </div>
                <div class="col-4">
                    <div class="card h-100"><div class="card-body py-2 px-3">
                        <small class="text-body-secondary">Outstanding</small>
                        <h5 class="mb-0 text-warning">৳ {{ number_format($loan->outstanding_balance, 0) }}</h5>
                    </div></div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-body-tertiary"><h5 class="mb-0">Loan Details</h5></div>
                <div class="card-body">
                    <dl class="row mb-0 fs-9">
                        <dt class="col-sm-4 text-body-secondary">Member</dt>
                        <dd class="col-sm-8"><a href="{{ route('members.show', $loan->member) }}">{{ $loan->member?->name }}</a> ({{ $loan->member?->member_code }})</dd>
                        <dt class="col-sm-4 text-body-secondary">Loan Amount</dt>
                        <dd class="col-sm-8">৳ {{ number_format($loan->loan_amount, 2) }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Service Charge</dt>
                        <dd class="col-sm-8">৳ {{ number_format($loan->service_charge, 2) }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Taken Date</dt>
                        <dd class="col-sm-8">{{ $loan->taken_date?->format('d M Y') }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Due Date</dt>
                        <dd class="col-sm-8">{{ $loan->due_date?->format('d M Y') ?? '—' }} @if($loan->is_overdue)<span class="badge badge-phoenix badge-phoenix-danger ms-1">Overdue</span>@endif</dd>
                        <dt class="col-sm-4 text-body-secondary">Purpose</dt>
                        <dd class="col-sm-8">{{ $loan->purpose ?: '—' }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Comment</dt>
                        <dd class="col-sm-8">{{ $loan->comment ?: '—' }}</dd>
                        <dt class="col-sm-4 text-body-secondary">Recorded By</dt>
                        <dd class="col-sm-8">{{ $loan->recorder?->name ?? '—' }}</dd>
                        @if($loan->approved_by)
                            <dt class="col-sm-4 text-body-secondary">Approved By</dt>
                            <dd class="col-sm-8">{{ $loan->approver?->name }} on {{ $loan->approved_at?->format('d M Y') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Repayments -->
            <div class="card mb-4">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Repayments</h5>
                    <span class="text-body-secondary fs-9">{{ $loan->repayments->count() }} payment(s)</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover fs-9 mb-0">
                            <thead><tr>
                                <th>Date</th><th>Amount</th><th>Method</th><th>Txn ID</th><th>Comment</th><th>By</th><th></th>
                            </tr></thead>
                            <tbody>
                                @forelse($loan->repayments->sortBy('repaid_date') as $r)
                                    <tr>
                                        <td>{{ $r->repaid_date?->format('d M Y') }}</td>
                                        <td class="fw-semibold">৳ {{ number_format($r->amount, 2) }}</td>
                                        <td>{{ $r->paymentMethod?->name ?? '—' }}</td>
                                        <td class="text-body-tertiary">{{ $r->transaction_id ?: '—' }}</td>
                                        <td>{{ $r->comment ?: '—' }}</td>
                                        <td class="text-body-secondary">{{ $r->recorder?->name ?? '—' }}</td>
                                        <td class="text-end">
                                            @can('update', $loan)
                                                <form method="POST" action="{{ route('loans.repayments.destroy', $r) }}" data-confirm="Remove this repayment?">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"><span class="fas fa-trash"></span></button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-body-secondary py-3">No repayments recorded yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @can('update', $loan)
                        @if($loan->status === 'active')
                            <hr>
                            <h6 class="fw-semibold mb-3">Record a Repayment</h6>
                            <form method="POST" action="{{ route('loans.repayments.store', $loan) }}" class="row g-2 align-items-end">
                                @csrf
                                <div class="col-md-3 col-6">
                                    <label class="form-label fs-9 mb-1">Amount (৳) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" max="{{ $loan->outstanding_balance }}" name="amount" class="form-control form-control-sm" value="{{ old('amount') }}" required />
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label fs-9 mb-1">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="repaid_date" class="form-control form-control-sm" value="{{ old('repaid_date', today()->toDateString()) }}" required />
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label fs-9 mb-1">Method</label>
                                    <select name="payment_method_id" class="form-select form-select-sm">
                                        <option value="">—</option>
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label fs-9 mb-1">Txn ID</label>
                                    <input type="text" name="transaction_id" class="form-control form-control-sm" value="{{ old('transaction_id') }}" />
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label fs-9 mb-1">Comment</label>
                                    <input type="text" name="comment" class="form-control form-control-sm" value="{{ old('comment') }}" />
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary btn-sm w-100"><span class="fas fa-plus me-1"></span>Add Repayment</button>
                                </div>
                            </form>
                            <small class="text-body-secondary d-block mt-2">Outstanding: ৳ {{ number_format($loan->outstanding_balance, 2) }}</small>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <!-- Right: status history -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-body-tertiary"><h5 class="mb-0">Status History</h5></div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($loan->statusHistories->sortByDesc('changed_at') as $h)
                            <li class="mb-3 pb-3 border-bottom">
                                <div class="fw-semibold">
                                    {{ $h->status_from ? ucfirst(str_replace('_',' ',$h->status_from)) . ' → ' : '' }}{{ ucfirst(str_replace('_',' ',$h->status_to)) }}
                                </div>
                                <small class="text-body-secondary d-block">{{ $h->changed_at?->format('d M Y, h:i A') }} · {{ $h->changedBy?->name ?? 'System' }}</small>
                                @if($h->notes)<small class="d-block mt-1">{{ $h->notes }}</small>@endif
                            </li>
                        @empty
                            <li class="text-body-secondary">No history.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
