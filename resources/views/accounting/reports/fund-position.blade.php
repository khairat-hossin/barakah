@extends('layouts.phoenix')

@section('title', 'Fund Position | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.reports.dashboard') }}">Accounting</a></li>
        <li class="breadcrumb-item active">Fund Position</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Fund Position Report</h2>
            <p class="text-body-secondary">BARAKAH Fund Allocation and Balances</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('accounting.reports.fund-position') }}">
                <div class="col-md-4">
                    <label class="form-label">As of Date</label>
                    <input type="date" class="form-control" name="as_of_date" value="{{ request('as_of_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="fas fa-wallet me-1"></span>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Fund Summary Cards -->
    <div class="row mb-3">
        <div class="col-lg-6">
            <div class="card border-start border-primary border-3 h-100">
                <div class="card-body">
                    <h6 class="card-title text-primary">Operating Fund</h6>
                    <p class="card-text fs-5 fw-bold mb-2">-</p>
                    <small class="text-body-secondary">Cash on hand and in bank accounts</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-start border-info border-3 h-100">
                <div class="card-body">
                    <h6 class="card-title text-info">Investment Fund</h6>
                    <p class="card-text fs-5 fw-bold mb-2">-</p>
                    <small class="text-body-secondary">Funds invested in projects</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Fund Breakdown -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Fund Breakdown</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-body-tertiary">
                            <tr>
                                <th>Fund Type</th>
                                <th class="text-end">Balance</th>
                                <th class="text-end">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-muted">
                                <td colspan="3" class="text-center py-5">No data available</td>
                            </tr>
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Total Funds</td>
                                <td class="text-end">-</td>
                                <td class="text-end">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Details</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-body-secondary small">Operating Fund</h6>
                    <dl class="small mb-3">
                        <dt>Cash on Hand:</dt>
                        <dd class="fw-bold">-</dd>
                        <dt class="mt-2">Bank Balance:</dt>
                        <dd class="fw-bold">-</dd>
                        <dt class="mt-2">Subtotal:</dt>
                        <dd class="fw-bold">-</dd>
                    </dl>

                    <hr>

                    <h6 class="text-body-secondary small">Investment Fund</h6>
                    <dl class="small mb-0">
                        <dt>Total Investments:</dt>
                        <dd class="fw-bold">-</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
