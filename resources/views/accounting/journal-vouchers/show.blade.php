@extends('layouts.phoenix')

@section('title', $voucher->voucher_number . ' | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.journal-vouchers.index') }}">Journal Vouchers</a></li>
        <li class="breadcrumb-item active">{{ $voucher->voucher_number }}</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">{{ $voucher->voucher_number }}</h2>
            <p class="text-body-secondary">
                {{ $voucher->voucher_date->format('M d, Y') }}
                @if($voucher->status === 'DRAFT')
                    <span class="badge bg-warning text-dark">Draft</span>
                @elseif($voucher->status === 'POSTED')
                    <span class="badge bg-success">Posted</span>
                @else
                    <span class="badge bg-secondary">Reversed</span>
                @endif
            </p>
        </div>
        <div class="col-auto">
            @can('update', $voucher)
                <a href="{{ route('accounting.journal-vouchers.edit', $voucher) }}" class="btn btn-outline-primary">
                    <span class="fas fa-edit me-2"></span>Edit
                </a>
            @endcan
            @can('post', $voucher)
                <form action="{{ route('accounting.journal-vouchers.post', $voucher) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <span class="fas fa-check me-2"></span>Post
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Voucher Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Voucher Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-body-secondary">Type</h6>
                            <p class="mb-0 fw-semibold">{{ $voucher->voucher_type }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-body-secondary">Date</h6>
                            <p class="mb-0 fw-semibold">{{ $voucher->voucher_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-body-secondary">Description</h6>
                        <p class="mb-0">{{ $voucher->description }}</p>
                    </div>
                    @if($voucher->source_module)
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-body-secondary">Source Module</h6>
                                <p class="mb-0">{{ $voucher->source_module }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Journal Entries -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Journal Entries</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-body-tertiary">
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDebits = 0;
                                $totalCredits = 0;
                            @endphp
                            @foreach($voucher->entries as $entry)
                            <tr>
                                <td>
                                    {{ $entry->account->code }} - {{ $entry->account->name }}
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $entry->debit_amount ? number_format($entry->debit_amount, 2) : '-' }}
                                    @php $totalDebits += $entry->debit_amount ?? 0; @endphp
                                </td>
                                <td class="text-end font-monospace">
                                    {{ $entry->credit_amount ? number_format($entry->credit_amount, 2) : '-' }}
                                    @php $totalCredits += $entry->credit_amount ?? 0; @endphp
                                </td>
                                <td>{{ $entry->description }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr>
                                <td>TOTALS</td>
                                <td class="text-end font-monospace">{{ number_format($totalDebits, 2) }}</td>
                                <td class="text-end font-monospace">{{ number_format($totalCredits, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Posting Information -->
            @if($voucher->isPosted())
                <div class="card bg-light">
                    <div class="card-body small">
                        <h6 class="card-title">Posting Information</h6>
                        <p class="mb-1">
                            <strong>Posted by:</strong> {{ $voucher->postedBy->name ?? 'N/A' }}<br>
                            <strong>Posted on:</strong> {{ $voucher->posted_date?->format('M d, Y H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Status</h6>
                </div>
                <div class="card-body">
                    @if($voucher->isDraft())
                        <div class="alert alert-warning mb-0">This voucher is in Draft status and has not been posted yet.</div>
                    @elseif($voucher->isPosted())
                        <div class="alert alert-success mb-0">This voucher has been posted and is permanent in the GL.</div>
                    @else
                        <div class="alert alert-secondary mb-0">This voucher has been reversed.</div>
                    @endif
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-2">
                        <h6 class="text-body-secondary small">Total Debit</h6>
                        <p class="fw-bold">{{ number_format($totalDebits, 2) }}</p>
                    </div>
                    <div class="mb-2">
                        <h6 class="text-body-secondary small">Total Credit</h6>
                        <p class="fw-bold">{{ number_format($totalCredits, 2) }}</p>
                    </div>
                    <div>
                        <h6 class="text-body-secondary small">Balance Status</h6>
                        <p>
                            @if(abs($totalDebits - $totalCredits) < 0.01)
                                <span class="badge bg-success">Balanced ✓</span>
                            @else
                                <span class="badge bg-danger">Not Balanced</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Information</h6>
                </div>
                <div class="card-body small">
                    <div class="mb-2">
                        <strong>Created by:</strong><br>
                        {{ $voucher->createdBy->name }}
                    </div>
                    <div class="mb-2">
                        <strong>Created on:</strong><br>
                        {{ $voucher->created_at->format('M d, Y H:i') }}
                    </div>
                    <div>
                        <strong>Entries:</strong><br>
                        {{ $voucher->entries->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
