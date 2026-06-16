@extends('layouts.phoenix')

@section('title', 'Shares | Barakah')

@section('content')
    <div class="mb-9">
        <div class="row mb-4 gx-6 gy-3 align-items-center">
            <div class="col-auto">
                <h2 class="mb-0">Share Management</h2>
            </div>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Total Shares</h6>
                        <h3 class="text-body-emphasis mb-0">{{ $totalShares }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Allocated</h6>
                        <h3 class="text-body-emphasis mb-0">{{ $allocated }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Unallocated</h6>
                        <h3 class="text-body-emphasis mb-0">{{ $unallocated }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Allocation %</h6>
                        <h3 class="text-body-emphasis mb-0">{{ round(($allocated / $totalShares) * 100, 1) }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Shares</h5>
            </div>
            <div class="table-responsive scrollbar">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="sort align-middle ps-3">Share #</th>
                            <th class="sort align-middle ps-3">Current Owner</th>
                            <th class="sort align-middle ps-3">Status</th>
                            <th class="sort align-middle ps-3">Issue Date</th>
                            <th class="text-end align-middle ps-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shares as $share)
                            <tr>
                                <td class="align-middle ps-3">
                                    <span class="badge badge-phoenix badge-phoenix-info">Share {{ $share->share_number }}</span>
                                </td>
                                <td class="align-middle ps-3">
                                    {{ $share->current_owner_name }}
                                </td>
                                <td class="align-middle ps-3">
                                    <span class="badge badge-phoenix @if($share->status === 'active') badge-phoenix-success @else badge-phoenix-secondary @endif">
                                        {{ ucfirst($share->status) }}
                                    </span>
                                </td>
                                <td class="align-middle ps-3">
                                    {{ $share->issue_date->format('M d, Y') }}
                                </td>
                                <td class="align-middle text-end ps-3">
                                    <a href="{{ route('shares.show', $share) }}" class="btn btn-sm btn-phoenix-secondary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
