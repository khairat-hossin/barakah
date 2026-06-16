@extends('layouts.phoenix')

@section('title', 'Deposit Details | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('deposits.index') }}">Deposits</a></li>
        <li class="breadcrumb-item active">Deposit #{{ $deposit->id }}</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between g-3 mb-3">
                <div class="col-12 col-md-auto">
                    <h2 class="mb-0">Deposit Details</h2>
                </div>
                <div class="col-12 col-md-auto">
                    <div class="d-flex gap-2">
                        <a href="{{ route('deposits.edit', $deposit) }}" class="btn btn-primary">
                            <span class="fas fa-pencil me-2"></span><span>Edit</span>
                        </a>
                        <button class="btn btn-phoenix-secondary px-3 px-sm-5" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false">
                            <span class="fas fa-ellipsis"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-0">
                            <li><a class="dropdown-item" href="#">Download Receipt</a></li>
                            <li><a class="dropdown-item" href="#">Print</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#!" onclick="if(confirm('Delete this deposit?')) { document.getElementById('deleteForm').submit(); }">Delete Deposit</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Deposit Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <p class="text-body-secondary fs-9 mb-2"><strong>Member</strong></p>
                            <p class="mb-0">
                                <a href="{{ route('members.show', $deposit->member) }}" class="fw-semibold text-body-emphasis">
                                    {{ $deposit->member->name }}
                                </a>
                            </p>
                            <p class="text-body-tertiary fs-9 mb-0">{{ $deposit->member->member_code }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-body-secondary fs-9 mb-2"><strong>Amount</strong></p>
                            <p class="mb-0 fw-bold fs-5">৳ {{ number_format($deposit->amount, 2) }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-body-secondary fs-9 mb-2"><strong>Deposit Date</strong></p>
                            <p class="mb-0">{{ $deposit->deposit_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-body-secondary fs-9 mb-2"><strong>Payment Method</strong></p>
                            <p class="mb-0">
                                @php
                                    $methods = [
                                        'cash' => 'Cash',
                                        'bank_transfer' => 'Bank Transfer',
                                        'mobile_banking' => 'Mobile Banking',
                                        'check' => 'Check',
                                        'other' => 'Other'
                                    ];
                                @endphp
                                <span class="badge badge-phoenix badge-phoenix-info">
                                    {{ $methods[$deposit->payment_method] ?? ucfirst($deposit->payment_method) }}
                                </span>
                            </p>
                        </div>

                        @if($deposit->transaction_id)
                        <div class="col-sm-6">
                            <p class="text-body-secondary fs-9 mb-2"><strong>Transaction ID</strong></p>
                            <p class="mb-0"><code>{{ $deposit->transaction_id }}</code></p>
                        </div>
                        @endif

                        @if($deposit->notes)
                        <div class="col-12">
                            <p class="text-body-secondary fs-9 mb-2"><strong>Notes</strong></p>
                            <p class="mb-0 text-body-secondary">{{ $deposit->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($deposit->attachments && count($deposit->attachments) > 0)
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Attachments</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($deposit->attachments as $attachment)
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 text-center">
                                <p class="text-body-tertiary fs-9 mb-2">
                                    <span class="fas fa-file"></span>
                                </p>
                                <p class="text-body-emphasis fs-9 mb-2 text-truncate">{{ $attachment }}</p>
                                <a href="#!" class="btn btn-sm btn-phoenix-secondary">Download</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-2"><strong>Recorded By</strong></p>
                    <p class="mb-3">{{ $deposit->recorder->name ?? 'System' }}</p>

                    <p class="text-body-secondary fs-9 mb-2"><strong>Created</strong></p>
                    <p class="mb-3 text-body-tertiary">{{ $deposit->created_at->format('d M Y, g:i A') }}</p>

                    <p class="text-body-secondary fs-9 mb-2"><strong>Last Updated</strong></p>
                    <p class="text-body-tertiary">{{ $deposit->updated_at->format('d M Y, g:i A') }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Member Info</h6>
                    <div class="mb-3">
                        <p class="text-body-secondary fs-9 mb-2"><strong>Email</strong></p>
                        <p class="mb-0 text-body-tertiary">{{ $deposit->member->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-body-secondary fs-9 mb-2"><strong>Phone</strong></p>
                        <p class="mb-0 text-body-tertiary">{{ $deposit->member->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" action="{{ route('deposits.destroy', $deposit) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection
