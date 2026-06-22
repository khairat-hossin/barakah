@extends('layouts.phoenix')

@section('title', 'Share Transfers | ' . config('app.name'))

@section('content')
    <div class="mb-9">

        <div class="row mb-4 gx-6 gy-3 align-items-center">
            <div class="col-auto">
                <h2 class="mb-0">Share Transfers</h2>
            </div>
            <div class="col-auto">
                @can('create share transfers')
                    <a class="btn btn-primary px-5" href="{{ route('share-transfers.create') }}">
                        <i class="fa-solid fa-plus me-2"></i>Initiate Transfer
                    </a>
                @endcan
            </div>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Pending</h6>
                        <h3 class="text-body-emphasis mb-0">{{ $statusCounts['pending'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Approved</h6>
                        <h3 class="text-body-emphasis mb-0">{{ $statusCounts['approved'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Rejected</h6>
                        <h3 class="text-body-emphasis mb-0">{{ $statusCounts['rejected'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive scrollbar">
                <table class="table table-sm fs-9 mb-0">
                    <thead class="bg-body-secondary">
                        <tr>
                            <th class="align-middle ps-3">From Member</th>
                            <th class="align-middle ps-3">To Member</th>
                            <th class="align-middle ps-3">Shares</th>
                            <th class="align-middle ps-3">Date</th>
                            <th class="align-middle ps-3">Status</th>
                            <th class="align-middle ps-3">Approved By</th>
                            <th class="text-end align-middle ps-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transfers as $transfer)
                            <tr>
                                <td class="align-middle ps-3">
                                    <strong>{{ $transfer->fromMember->name }}</strong>
                                </td>
                                <td class="align-middle ps-3">
                                    {{ $transfer->toMember->name }}
                                </td>
                                <td class="align-middle ps-3">
                                    <span class="badge badge-phoenix badge-phoenix-info">{{ $transfer->share_count }}</span>
                                </td>
                                <td class="align-middle ps-3">
                                    {{ $transfer->transfer_date->format('M d, Y') }}
                                </td>
                                <td class="align-middle ps-3">
                                    <span class="badge badge-phoenix @if($transfer->approval_status === 'pending') badge-phoenix-warning @elseif($transfer->approval_status === 'approved') badge-phoenix-success @else badge-phoenix-danger @endif">
                                        {{ ucfirst($transfer->approval_status) }}
                                    </span>
                                </td>
                                <td class="align-middle ps-3">
                                    {{ $transfer->approver?->name ?? '-' }}
                                </td>
                                <td class="align-middle text-end ps-3">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('share-transfers.show', $transfer) }}" class="btn btn-sm btn-phoenix-secondary">View</a>
                                        @can('approve share transfers')
                                            @if ($transfer->approval_status === 'pending')
                                                <a href="{{ route('share-transfers.approve', $transfer) }}" class="btn btn-sm btn-phoenix-success">Approve</a>
                                                <a href="{{ route('share-transfers.reject', $transfer) }}" class="btn btn-sm btn-phoenix-danger">Reject</a>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No transfers found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $transfers->links() }}
            </div>
        </div>
    </div>
@endsection
