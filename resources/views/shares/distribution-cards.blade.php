@extends('layouts.phoenix')

@section('title', 'Share Distribution | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('shares.index') }}">Shares</a></li>
        <li class="breadcrumb-item active">Distribution</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">Share Distribution</h2>
            <p class="text-body-secondary">Member share allocation overview</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('shares.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-2"></span>Back
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-primary fw-semibold">Total Shares</small>
                <h6 class="mb-0">{{ number_format($totalShares) }}</h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-success fw-semibold">Assigned</small>
                <h6 class="mb-0" id="assignedTotal">{{ number_format($assignedShares) }}</h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-warning fw-semibold">Available</small>
                <h6 class="mb-0" id="availableTotal">{{ number_format($availableShares) }}</h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-info fw-semibold">Members</small>
                <h6 class="mb-0">{{ $memberShares->count() }}</h6>
            </div>
        </div>
    </div>

    <!-- Member Boxes Grid -->
    @if($memberShares->count())
        <div class="row g-2">
            @foreach($memberShares as $member)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm member-box" style="min-height: 140px;">
                        <div class="card-body p-3">
                            <!-- Member Name -->
                            <h6 class="card-title mb-3 text-truncate" title="{{ $member->name }}">
                                {{ $member->name }}
                            </h6>

                            <!-- Share Count -->
                            <div class="mb-3">
                                <small class="text-muted d-block">Shares</small>
                                <div class="display-6 fw-bold text-primary" style="font-size: 2rem; line-height: 1;">
                                    {{ $member->shares_count }}
                                </div>
                            </div>

                            <!-- EMI Per Month -->
                            <div>
                                <small class="text-muted d-block">EMI/Month</small>
                                <h6 class="mb-0 text-success">
                                    ৳ <span class="emi-amount">{{ number_format($member->emi_per_month ?? 0, 2) }}</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info mb-0">
            <span class="fas fa-info-circle me-2"></span>No members found.
        </div>
    @endif
</div>

<style>
    .member-box {
        transition: all 0.2s ease;
        cursor: pointer;
        border-radius: 8px;
    }

    .member-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    @media (max-width: 768px) {
        .member-box .display-6 {
            font-size: 1.5rem !important;
        }
    }
</style>
@endsection
