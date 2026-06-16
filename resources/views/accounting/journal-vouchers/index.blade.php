@extends('layouts.phoenix')

@section('title', 'Journal Vouchers | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Journal Vouchers</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Journal Vouchers</h2>
            <p class="text-body-secondary">Create and manage accounting transactions</p>
        </div>
        <div class="col-auto">
            @can('create', \App\Models\JournalVoucher::class)
                <a href="{{ route('accounting.journal-vouchers.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-2"></span>New Voucher
                </a>
            @endcan
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Total Vouchers</h6>
                    <p class="card-text fs-5 fw-bold text-primary">
                        {{ $totalVouchers }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Draft</h6>
                    <p class="card-text fs-5 fw-bold text-warning">
                        {{ $draftCount }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Posted</h6>
                    <p class="card-text fs-5 fw-bold text-success">
                        {{ $postedCount }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Reversed</h6>
                    <p class="card-text fs-5 fw-bold text-secondary">
                        {{ $reversedCount }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="row mb-3">
        <div class="col">
            <div class="btn-group" role="group">
                <a href="{{ route('accounting.journal-vouchers.index') }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('accounting.journal-vouchers.index', ['status' => 'DRAFT']) }}" class="btn btn-outline-secondary {{ request('status') === 'DRAFT' ? 'active' : '' }}">
                    Draft
                </a>
                <a href="{{ route('accounting.journal-vouchers.index', ['status' => 'POSTED']) }}" class="btn btn-outline-secondary {{ request('status') === 'POSTED' ? 'active' : '' }}">
                    Posted
                </a>
                <a href="{{ route('accounting.journal-vouchers.index', ['status' => 'REVERSED']) }}" class="btn btn-outline-secondary {{ request('status') === 'REVERSED' ? 'active' : '' }}">
                    Reversed
                </a>
            </div>
        </div>
    </div>

    <!-- Vouchers Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="fw-semibold">VOUCHER #</th>
                        <th class="fw-semibold">DATE</th>
                        <th class="fw-semibold">DESCRIPTION</th>
                        <th class="fw-semibold">AMOUNT</th>
                        <th class="fw-semibold">TYPE</th>
                        <th class="fw-semibold">STATUS</th>
                        <th class="fw-semibold">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark font-monospace">{{ $voucher->voucher_number }}</span>
                        </td>
                        <td>
                            {{ $voucher->voucher_date->format('M d, Y') }}
                        </td>
                        <td>
                            {{ Str::limit($voucher->description, 40) }}
                        </td>
                        <td>
                            <span class="font-monospace">
                                {{ number_format($voucher->entries->sum(fn($e) => $e->debit_amount ?? 0), 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $voucher->voucher_type }}</span>
                        </td>
                        <td>
                            @if($voucher->status === 'DRAFT')
                                <span class="badge bg-warning text-dark">Draft</span>
                            @elseif($voucher->status === 'POSTED')
                                <span class="badge bg-success">Posted</span>
                            @else
                                <span class="badge bg-secondary">Reversed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('accounting.journal-vouchers.show', $voucher) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <span class="fas fa-eye"></span>
                            </a>
                            @can('update', $voucher)
                                <a href="{{ route('accounting.journal-vouchers.edit', $voucher) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <span class="fas fa-edit"></span>
                                </a>
                            @endcan
                            @can('delete', $voucher)
                                <form action="{{ route('accounting.journal-vouchers.destroy', $voucher) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                        <span class="fas fa-trash"></span>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <p class="text-body-secondary mb-2">No vouchers found</p>
                            @can('create', \App\Models\JournalVoucher::class)
                                <a href="{{ route('accounting.journal-vouchers.create') }}" class="btn btn-sm btn-primary">Create the first voucher</a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($vouchers->hasPages())
        <div class="mt-4">
            {{ $vouchers->links() }}
        </div>
    @endif
</div>
@endsection
