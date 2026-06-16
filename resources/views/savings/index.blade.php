@extends('layouts.phoenix')

@section('title', 'Savings Entries | Barakah')

@section('content')
    <div class="mb-9">

        <div id="savingsSummary" data-list='{"valueNames":["memberName","depositDate","contributionMonth","paymentMethod","amount","recordedBy"],"page":8,"pagination":true}'>
            <div class="row mb-4 gx-6 gy-3 align-items-center">
                <div class="col-auto">
                    <h2 class="mb-0">Savings Entries<span class="fw-normal text-body-tertiary ms-3">({{ $entries->count() }})</span></h2>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary px-5" href="{{ route('savings.create') }}">
                        <i class="fa-solid fa-plus me-2"></i>Record savings
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-5">
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="mb-2 text-body-secondary">Total Collected</h6>
                            <h3 class="text-body-emphasis mb-0">${{ number_format($totalCollected, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="mb-2 text-body-secondary">Collected This Month</h6>
                            <h3 class="text-body-emphasis mb-0">${{ number_format($monthlyCollected, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="mb-2 text-body-secondary">Entries Recorded</h6>
                            <h3 class="text-body-emphasis mb-0">{{ $entries->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 justify-content-between align-items-end mb-4">
                <div class="col-12 col-sm-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input search" type="search" placeholder="Search savings entries" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
            </div>

            @if ($entries->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-8">
                        <div class="icon-item icon-item-xl rounded-circle bg-success-subtle mx-auto mb-4">
                            <span class="fas fa-wallet text-success fs-6"></span>
                        </div>
                        <h3 class="text-body-emphasis mb-2">No savings entries yet</h3>
                        <p class="text-body-secondary mb-4">Once members are onboarded, use this module to record each deposit and keep the association balance traceable.</p>
                        <a class="btn btn-primary px-5" href="{{ route('savings.create') }}">
                            <span class="fas fa-plus me-2"></span>Record first entry
                        </a>
                    </div>
                </div>
            @else
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0 border-top border-translucent">
                        <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle ps-0" data-sort="memberName" style="width: 20%;">MEMBER</th>
                                <th class="sort align-middle ps-3" data-sort="depositDate" style="width: 12%;">DEPOSIT DATE</th>
                                <th class="sort align-middle ps-3" data-sort="contributionMonth" style="width: 14%;">CONTRIBUTION MONTH</th>
                                <th class="sort align-middle ps-3" data-sort="paymentMethod" style="width: 14%;">PAYMENT METHOD</th>
                                <th class="sort align-middle ps-3" data-sort="amount" style="width: 12%;">AMOUNT</th>
                                <th class="sort align-middle ps-3" data-sort="recordedBy" style="width: 16%;">RECORDED BY</th>
                                <th class="align-middle text-end" style="width: 12%;">REFERENCE</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach ($entries as $entry)
                                <tr>
                                    <td class="align-middle memberName ps-0 py-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-s">
                                                <div class="avatar-name rounded-circle">
                                                    <span>{{ $entry->member->initials }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="fw-semibold text-body mb-0">{{ $entry->member->name }}</p>
                                                <p class="fs-10 text-body-tertiary mb-0">{{ $entry->member->member_code ?: 'No code' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle depositDate ps-3 py-4">
                                        <p class="mb-0 text-body">{{ $entry->deposit_date?->format('M d, Y') }}</p>
                                    </td>
                                    <td class="align-middle contributionMonth ps-3 py-4">
                                        <p class="mb-0 text-body">{{ $entry->contribution_month?->format('M Y') ?: 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle paymentMethod ps-3 py-4">
                                        <p class="mb-0 text-body">{{ ucwords(str_replace('_', ' ', $entry->payment_method)) }}</p>
                                    </td>
                                    <td class="align-middle amount ps-3 py-4">
                                        <p class="mb-0 fw-semibold text-body">${{ number_format((float) $entry->amount, 2) }}</p>
                                    </td>
                                    <td class="align-middle recordedBy ps-3 py-4">
                                        <p class="mb-0 text-body">{{ $entry->recorder?->name ?? 'Unknown' }}</p>
                                    </td>
                                    <td class="align-middle text-end py-4">
                                        <p class="mb-0 text-body">{{ $entry->reference ?: 'N/A' }}</p>
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
