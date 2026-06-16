@extends('layouts.phoenix')
@section('title', $investment->name . ' | Barakah')
@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('investments.index') }}">Investments</a></li>
        <li class="breadcrumb-item active">{{ $investment->code }}</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">{{ $investment->name }}</h2>
            <p class="text-body-secondary">{{ $investment->code }} • {{ $investment->investmentType?->name }}</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-phoenix badge-phoenix-{{ $investment->status === 'draft' ? 'secondary' : ($investment->status === 'active' ? 'success' : 'warning') }}">
                {{ ucfirst($investment->status) }}
            </span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">Principal Amount</p>
                    <h5 class="mb-0">৳ {{ number_format($performance['total_invested'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">Current Value</p>
                    <h5 class="mb-0">৳ {{ number_format($performance['current_value'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">Net Profit/Loss</p>
                    <h5 class="mb-0">৳ {{ number_format($performance['net_profit_loss'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">ROI %</p>
                    <h5 class="mb-0">{{ number_format($performance['roi_percentage'], 2) }}%</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#information" data-bs-toggle="tab">Information</a></li>
                <li class="nav-item"><a class="nav-link" href="#transactions" data-bs-toggle="tab">Transactions</a></li>
                <li class="nav-item"><a class="nav-link" href="#documents" data-bs-toggle="tab">Documents</a></li>
                <li class="nav-item"><a class="nav-link" href="#history" data-bs-toggle="tab">History</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="information" role="tabpanel">
                    <p><strong>Description:</strong> {{ $investment->description }}</p>
                    <p><strong>Type:</strong> {{ $investment->investmentType?->name }}</p>
                    <p><strong>Start Date:</strong> {{ $investment->start_date->format('d M Y') }}</p>
                    <p><strong>Maturity Date:</strong> {{ $investment->maturity_date?->format('d M Y') ?? 'N/A' }}</p>
                    <p><strong>Risk Level:</strong> {{ ucfirst($investment->risk_level) }}</p>
                    <p><strong>Expected Return:</strong> {{ $investment->expected_return_percentage }}%</p>
                </div>
                <div class="tab-pane fade" id="transactions" role="tabpanel">
                    <p>Transaction history will display here</p>
                </div>
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <p>Document attachments will display here</p>
                </div>
                <div class="tab-pane fade" id="history" role="tabpanel">
                    <p>Status history will display here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-3">
        @if($investment->status === 'draft')
            <form action="{{ route('investments.destroy', $investment) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this investment?')">Delete</button>
            </form>
            <a href="{{ route('investments.edit', $investment) }}" class="btn btn-primary btn-sm">Edit</a>
        @endif
        @if($investment->canTransitionTo('active'))
            <form action="{{ route('investments.activate', $investment) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Activate</button>
            </form>
        @endif
    </div>
</div>
@endsection
