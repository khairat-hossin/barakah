@extends('layouts.phoenix')

@section('title', 'Members | Barakah')

@php
    $badgeClasses = [
        'active' => 'badge-phoenix-success',
        'inactive' => 'badge-phoenix-secondary',
        'suspended' => 'badge-phoenix-warning',
    ];
@endphp

@section('content')
    <div class="mb-9">
        @if (session('success'))
            <div class="alert alert-subtle-success border border-success-subtle mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div id="memberSummary" data-list='{"valueNames":["memberName","memberCode","phone","joinDate","monthlySaving","totalSaved","statuses"],"page":8,"pagination":true}'>
            <div class="row mb-4 gx-6 gy-3 align-items-center">
                <div class="col-auto">
                    <h2 class="mb-0">Members<span class="fw-normal text-body-tertiary ms-3">({{ $members->count() }})</span></h2>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary px-5" href="{{ route('members.create') }}">
                        <i class="fa-solid fa-plus me-2"></i>Add member
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-5">
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="mb-2 text-body-secondary">Active Members</h6>
                            <h3 class="text-body-emphasis mb-0">{{ $statusCounts['active'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="mb-2 text-body-secondary">Monthly Target</h6>
                            <h3 class="text-body-emphasis mb-0">${{ number_format((float) $members->sum('monthly_saving_amount'), 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="mb-2 text-body-secondary">Total Saved Recorded</h6>
                            <h3 class="text-body-emphasis mb-0">${{ number_format($totalSaved, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 justify-content-between align-items-end mb-4">
                <div class="col-12 col-sm-auto">
                    <ul class="nav nav-links mx-n2">
                        <li class="nav-item"><a class="nav-link px-2 py-1 active" href="#!"><span>All</span><span class="text-body-tertiary fw-semibold">({{ $members->count() }})</span></a></li>
                        <li class="nav-item"><a class="nav-link px-2 py-1" href="#!"><span>Active</span><span class="text-body-tertiary fw-semibold">({{ $statusCounts['active'] ?? 0 }})</span></a></li>
                        <li class="nav-item"><a class="nav-link px-2 py-1" href="#!"><span>Inactive</span><span class="text-body-tertiary fw-semibold">({{ $statusCounts['inactive'] ?? 0 }})</span></a></li>
                        <li class="nav-item"><a class="nav-link px-2 py-1" href="#!"><span>Suspended</span><span class="text-body-tertiary fw-semibold">({{ $statusCounts['suspended'] ?? 0 }})</span></a></li>
                    </ul>
                </div>
                <div class="col-12 col-sm-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input search" type="search" placeholder="Search members" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
            </div>

            @if ($members->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-8">
                        <div class="icon-item icon-item-xl rounded-circle bg-primary-subtle mx-auto mb-4">
                            <span class="fas fa-users text-primary fs-6"></span>
                        </div>
                        <h3 class="text-body-emphasis mb-2">No members added yet</h3>
                        <p class="text-body-secondary mb-4">Start by onboarding the existing association members so savings entries can be recorded against real profiles.</p>
                        <a class="btn btn-primary px-5" href="{{ route('members.create') }}">
                            <span class="fas fa-plus me-2"></span>Add first member
                        </a>
                    </div>
                </div>
            @else
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0 border-top border-translucent">
                        <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle ps-0" data-sort="memberName" style="width: 22%;">MEMBER</th>
                                <th class="sort align-middle ps-3" data-sort="memberCode" style="width: 10%;">CODE</th>
                                <th class="sort align-middle ps-3" data-sort="phone" style="width: 14%;">PHONE</th>
                                <th class="sort align-middle ps-3" data-sort="joinDate" style="width: 12%;">JOIN DATE</th>
                                <th class="sort align-middle ps-3" data-sort="monthlySaving" style="width: 14%;">MONTHLY SAVING</th>
                                <th class="sort align-middle ps-3" data-sort="totalSaved" style="width: 14%;">TOTAL SAVED</th>
                                <th class="sort align-middle text-end" data-sort="statuses" style="width: 14%;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach ($members as $member)
                                <tr>
                                    <td class="align-middle memberName ps-0 py-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-s">
                                                <div class="avatar-name rounded-circle">
                                                    <span>{{ $member->initials }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-body mb-0">{{ $member->name }}</p>
                                                <p class="fs-10 text-body-tertiary mb-0">{{ $member->email ?: 'No email' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle memberCode ps-3 py-4">
                                        <p class="mb-0 text-body">{{ $member->member_code ?: 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle phone ps-3 py-4">
                                        <p class="mb-0 text-body">{{ $member->phone ?: 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle joinDate ps-3 py-4">
                                        <p class="mb-0 text-body">{{ $member->join_date?->format('M d, Y') ?: 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle monthlySaving ps-3 py-4">
                                        <p class="mb-0 text-body">${{ number_format((float) ($member->monthly_saving_amount ?? 0), 2) }}</p>
                                    </td>
                                    <td class="align-middle totalSaved ps-3 py-4">
                                        <p class="mb-0 text-body">${{ number_format((float) ($member->savings_entries_sum_amount ?? 0), 2) }}</p>
                                    </td>
                                    <td class="align-middle text-end statuses py-4">
                                        <span class="badge badge-phoenix fs-10 {{ $badgeClasses[$member->status] ?? 'badge-phoenix-secondary' }}">{{ ucfirst($member->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-between py-3 pe-0 fs-9 border-bottom border-translucent">
                    <div class="d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info></p>
                        <a class="fw-semibold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                        <a class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                    </div>
                    <div class="d-flex">
                        <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                        <ul class="mb-0 pagination"></ul>
                        <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
