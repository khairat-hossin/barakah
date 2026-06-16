@extends('layouts.phoenix')

@section('title', 'Trial Balance | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.reports.dashboard') }}">Accounting</a></li>
        <li class="breadcrumb-item active">Trial Balance</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Trial Balance</h2>
            <p class="text-body-secondary">Verify that total debits equal total credits</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('accounting.reports.trial-balance') }}">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <span class="fas fa-search me-1"></span>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Trial Balance Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th>Code</th>
                        <th>Account Name</th>
                        <th>Type</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-muted">
                        <td colspan="5" class="text-center py-5">
                            <p class="text-body-secondary mb-0">Generate trial balance by clicking the button above</p>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="fw-bold border-top-2">
                    <tr>
                        <td colspan="3">TOTALS</td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Balance Status -->
    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-body-secondary">Total Debits</h6>
                    <p class="fw-bold">-</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-body-secondary">Total Credits</h6>
                    <p class="fw-bold">-</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-body-secondary">Status</h6>
                    <p><span class="badge bg-secondary">Not Generated</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
