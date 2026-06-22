@extends('layouts.phoenix')

@section('title', 'Balance Sheet | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.reports.dashboard') }}">Accounting</a></li>
        <li class="breadcrumb-item active">Balance Sheet</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Balance Sheet</h2>
            <p class="text-body-secondary">Assets, Liabilities, and Equity as of a specific date</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('accounting.reports.balance-sheet') }}">
                <div class="col-md-4">
                    <label class="form-label">As of Date</label>
                    <input type="date" class="form-control" name="as_of_date" value="{{ request('as_of_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="fas fa-chart-bar me-1"></span>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Balance Sheet -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ \App\Support\Branding::name() }} - Balance Sheet</h5>

            <div class="row">
                <div class="col-md-6">
                    <!-- Assets -->
                    <h6 class="fw-bold mb-3">ASSETS</h6>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            @if($statement && isset($statement['assets']['items']) && count($statement['assets']['items']) > 0)
                                @foreach($statement['assets']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-3">No assets recorded</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Total Assets</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['assets']['total']))
                                        {{ number_format($statement['assets']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-md-6">
                    <!-- Liabilities & Equity -->
                    <h6 class="fw-bold mb-3">LIABILITIES</h6>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            @if($statement && isset($statement['liabilities']['items']) && count($statement['liabilities']['items']) > 0)
                                @foreach($statement['liabilities']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-3">No liabilities recorded</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Total Liabilities</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['liabilities']['total']))
                                        {{ number_format($statement['liabilities']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <h6 class="fw-bold mb-3 mt-4">EQUITY</h6>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            @if($statement && isset($statement['equity']['items']) && count($statement['equity']['items']) > 0)
                                @foreach($statement['equity']['items'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="text-muted">
                                    <td colspan="2" class="text-center py-3">No equity recorded</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="fw-bold border-top">
                            <tr>
                                <td>Total Equity</td>
                                <td class="text-end">
                                    @if($statement && isset($statement['equity']['total']))
                                        {{ number_format($statement['equity']['total'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table table-sm fw-bold border-top-2 mt-3">
                        <tr>
                            <td>Total Liabilities & Equity</td>
                            <td class="text-end">
                                @if($statement && isset($statement['liabilities']['total']) && isset($statement['equity']['total']))
                                    {{ number_format($statement['liabilities']['total'] + $statement['equity']['total'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Balance Status -->
            <div class="mt-4 p-3 bg-light rounded">
                <p class="mb-0">
                    <strong>Balance Status:</strong>
                    @if($statement && isset($statement['is_balanced']))
                        @if($statement['is_balanced'])
                            <span class="badge bg-success">Balanced ✓</span>
                        @else
                            <span class="badge bg-danger">Not Balanced</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Not Generated</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
