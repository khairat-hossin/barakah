@extends('layouts.phoenix')

@section('title', 'Income Statement | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.reports.dashboard') }}">Accounting</a></li>
        <li class="breadcrumb-item active">Income Statement</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Income Statement</h2>
            <p class="text-body-secondary">Profit & Loss for selected period</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('accounting.reports.income-statement') }}">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="from_date" value="{{ request('from_date', date('Y-01-01')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <span class="fas fa-chart-line me-1"></span>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Income Statement -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ config('app.name') }} - Income Statement</h5>

            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>INCOME</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($statement && isset($statement['income']['items']) && count($statement['income']['items']) > 0)
                                @foreach($statement['income']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-5">No income recorded</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Total Income</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['income']['total']))
                                        {{ number_format($statement['income']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table table-borderless mt-4">
                        <thead>
                            <tr>
                                <th>EXPENSES</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($statement && isset($statement['expenses']['items']) && count($statement['expenses']['items']) > 0)
                                @foreach($statement['expenses']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-5">No expenses recorded</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Total Expenses</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['expenses']['total']))
                                        {{ number_format($statement['expenses']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table table-borderless mt-4 border-top-2">
                        <tfoot class="fw-bold">
                            <tr>
                                <td>NET PROFIT / (LOSS)</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['net_profit']))
                                        <span class="{{ $statement['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($statement['net_profit'], 2) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Summary</h6>
                            <dl class="small mb-0">
                                <dt>Total Income:</dt>
                                <dd class="text-success fw-bold">
                                    @if($statement && isset($statement['income']['total']))
                                        {{ number_format($statement['income']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </dd>
                                <dt class="mt-2">Total Expenses:</dt>
                                <dd class="text-danger fw-bold">
                                    @if($statement && isset($statement['expenses']['total']))
                                        {{ number_format($statement['expenses']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </dd>
                                <dt class="mt-2">Net Profit/Loss:</dt>
                                <dd class="fw-bold">
                                    @if($statement && isset($statement['net_profit']))
                                        <span class="{{ $statement['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($statement['net_profit'], 2) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </dd>
                                <dt class="mt-2">Margin:</dt>
                                <dd>
                                    @if($statement && isset($statement['net_profit_percentage']))
                                        {{ number_format($statement['net_profit_percentage'], 2) }}%
                                    @else
                                        -%
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
