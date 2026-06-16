@extends('layouts.phoenix')

@section('title', 'Cash Flow | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.reports.dashboard') }}">Accounting</a></li>
        <li class="breadcrumb-item active">Cash Flow</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Cash Flow Statement</h2>
            <p class="text-body-secondary">Operating, Investing, and Financing Activities</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('accounting.reports.cash-flow') }}">
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
                        <span class="fas fa-water me-1"></span>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cash Flow Statement -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ config('app.name') }} - Cash Flow Statement</h5>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Operating Activities -->
                    <h6 class="fw-bold mb-3">OPERATING ACTIVITIES</h6>
                    <table class="table table-sm table-borderless mb-4">
                        <tbody>
                            @if($statement && isset($statement['operating']['items']) && count($statement['operating']['items']) > 0)
                                @foreach($statement['operating']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-3">No data</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Net Operating Cash Flow</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['operating']['total']))
                                        {{ number_format($statement['operating']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Investing Activities -->
                    <h6 class="fw-bold mb-3">INVESTING ACTIVITIES</h6>
                    <table class="table table-sm table-borderless mb-4">
                        <tbody>
                            @if($statement && isset($statement['investing']['items']) && count($statement['investing']['items']) > 0)
                                @foreach($statement['investing']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-3">No data</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Net Investing Cash Flow</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['investing']['total']))
                                        {{ number_format($statement['investing']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Financing Activities -->
                    <h6 class="fw-bold mb-3">FINANCING ACTIVITIES</h6>
                    <table class="table table-sm table-borderless mb-4">
                        <tbody>
                            @if($statement && isset($statement['financing']['items']) && count($statement['financing']['items']) > 0)
                                @foreach($statement['financing']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-3">No data</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Net Financing Cash Flow</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['financing']['total']))
                                        {{ number_format($statement['financing']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Net Change -->
                    <table class="table table-sm fw-bold border-top-2">
                        <tr>
                            <td>Net Change in Cash</td>
                            <td class="text-end">
                                @if($statement && isset($statement['net_change']))
                                    {{ number_format($statement['net_change'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Summary</h6>
                            <dl class="small mb-0">
                                <dt>Opening Cash:</dt>
                                <dd class="fw-bold">
                                    @if($statement && isset($statement['opening_cash']))
                                        {{ number_format($statement['opening_cash'], 2) }}
                                    @else
                                        -
                                    @endif
                                </dd>
                                <dt class="mt-2">Operating:</dt>
                                <dd class="fw-bold">
                                    @if($statement && isset($statement['operating']['total']))
                                        {{ number_format($statement['operating']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </dd>
                                <dt class="mt-2">Investing:</dt>
                                <dd class="fw-bold">
                                    @if($statement && isset($statement['investing']['total']))
                                        {{ number_format($statement['investing']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </dd>
                                <dt class="mt-2">Financing:</dt>
                                <dd class="fw-bold">
                                    @if($statement && isset($statement['financing']['total']))
                                        {{ number_format($statement['financing']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </dd>
                                <dt class="mt-2">Closing Cash:</dt>
                                <dd class="fw-bold">
                                    @if($statement && isset($statement['closing_cash']))
                                        {{ number_format($statement['closing_cash'], 2) }}
                                    @else
                                        -
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
