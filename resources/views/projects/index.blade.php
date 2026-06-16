@extends('layouts.phoenix')

@section('title', 'Projects | Barakah')

@php
    $badgeClasses = [
        'draft' => 'badge-phoenix-secondary',
        'proposed' => 'badge-phoenix-info',
        'approved' => 'badge-phoenix-warning',
        'active' => 'badge-phoenix-primary',
        'completed' => 'badge-phoenix-success',
        'cancelled' => 'badge-phoenix-danger',
    ];
@endphp

@section('content')
    <div class="mb-9">

        <div id="projectSummary" data-list='{"valueNames":["projectName","lead","category","capital","return","start","deadline","statuses"],"page":6,"pagination":true}'>
            <div class="row mb-4 gx-6 gy-3 align-items-center">
                <div class="col-auto">
                    <h2 class="mb-0">Investment Projects<span class="fw-normal text-body-tertiary ms-3">({{ $projects->count() }})</span></h2>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary px-5" href="{{ route('projects.create') }}">
                        <i class="fa-solid fa-plus me-2"></i>Add new project
                    </a>
                </div>
            </div>

            <div class="row g-3 justify-content-between align-items-end mb-4">
                <div class="col-12 col-sm-auto">
                    <ul class="nav nav-links mx-n2">
                        <li class="nav-item"><a class="nav-link px-2 py-1 active" aria-current="page" href="#!"><span>All</span><span class="text-body-tertiary fw-semibold">({{ $projects->count() }})</span></a></li>
                        <li class="nav-item"><a class="nav-link px-2 py-1" href="#!"><span>Active</span><span class="text-body-tertiary fw-semibold">({{ $statusCounts['active'] ?? 0 }})</span></a></li>
                        <li class="nav-item"><a class="nav-link px-2 py-1" href="#!"><span>Completed</span><span class="text-body-tertiary fw-semibold">({{ $statusCounts['completed'] ?? 0 }})</span></a></li>
                        <li class="nav-item"><a class="nav-link px-2 py-1" href="#!"><span>Proposed</span><span class="text-body-tertiary fw-semibold">({{ $statusCounts['proposed'] ?? 0 }})</span></a></li>
                        <li class="nav-item"><a class="nav-link px-2 py-1" href="#!"><span>Cancelled</span><span class="text-body-tertiary fw-semibold">({{ $statusCounts['cancelled'] ?? 0 }})</span></a></li>
                    </ul>
                </div>
                <div class="col-12 col-sm-auto">
                    <div class="d-flex align-items-center">
                        <div class="search-box me-3">
                            <form class="position-relative">
                                <input class="form-control search-input search" type="search" placeholder="Search projects" aria-label="Search" />
                                <span class="fas fa-search search-box-icon"></span>
                            </form>
                        </div>
                        <a class="btn btn-phoenix-primary px-3 me-1 border-0 text-body" href="{{ route('projects.index') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="List view">
                            <span class="fa-solid fa-list fs-10"></span>
                        </a>
                    </div>
                </div>
            </div>

            @if ($projects->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-8">
                        <div class="icon-item icon-item-xl rounded-circle bg-primary-subtle mx-auto mb-4">
                            <span class="fas fa-briefcase text-primary fs-6"></span>
                        </div>
                        <h3 class="text-body-emphasis mb-2">No projects yet</h3>
                        <p class="text-body-secondary mb-4">Create the first investment project to start tracking capital, timelines, and expected returns.</p>
                        <a class="btn btn-primary px-5" href="{{ route('projects.create') }}">
                            <span class="fas fa-plus me-2"></span>Create first project
                        </a>
                    </div>
                </div>
            @else
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0 border-top border-translucent">
                        <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle ps-0" scope="col" data-sort="projectName" style="width: 24%;">PROJECT NAME</th>
                                <th class="sort align-middle ps-3" scope="col" data-sort="lead" style="width: 16%;">LEAD</th>
                                <th class="sort align-middle ps-3" scope="col" data-sort="category" style="width: 12%;">CATEGORY</th>
                                <th class="sort align-middle ps-3" scope="col" data-sort="capital" style="width: 12%;">CAPITAL</th>
                                <th class="sort align-middle ps-3" scope="col" data-sort="return" style="width: 12%;">EXPECTED RETURN</th>
                                <th class="sort align-middle ps-3" scope="col" data-sort="start" style="width: 10%;">START DATE</th>
                                <th class="sort align-middle ps-3" scope="col" data-sort="deadline" style="width: 10%;">DEADLINE</th>
                                <th class="sort align-middle text-end" scope="col" data-sort="statuses" style="width: 10%;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="list" id="project-list-table-body">
                            @foreach ($projects as $project)
                                <tr class="position-static">
                                    <td class="align-middle white-space-nowrap ps-0 projectName py-4">
                                        <span class="fw-bold fs-8 text-body-emphasis">{{ $project->name }}</span>
                                    </td>
                                    <td class="align-middle white-space-nowrap lead ps-3 py-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-s">
                                                <div class="avatar-name rounded-circle">
                                                    <span>{{ $project->lead_initials }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold text-body">{{ $project->owner?->name ?? 'Unknown' }}</p>
                                                <p class="mb-0 fs-10 text-body-tertiary">{{ $project->owner?->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle white-space-nowrap category ps-3 py-4">
                                        <p class="mb-0 fs-9 text-body">{{ $project->category ?: 'Uncategorized' }}</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap capital ps-3 py-4">
                                        <p class="mb-0 fs-9 text-body">${{ number_format((float) ($project->budget_requested ?? 0), 2) }}</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap return ps-3 py-4">
                                        <p class="mb-0 fs-9 text-body">{{ $project->expected_return_percentage !== null ? number_format((float) $project->expected_return_percentage, 2).'%' : 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap start ps-3 py-4">
                                        <p class="mb-0 fs-9 text-body">{{ $project->start_date?->format('M d, Y') ?? 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap deadline ps-3 py-4">
                                        <p class="mb-0 fs-9 text-body">{{ $project->deadline?->format('M d, Y') ?? 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap text-end statuses py-4">
                                        <span class="badge badge-phoenix fs-10 {{ $badgeClasses[$project->status] ?? 'badge-phoenix-secondary' }}">{{ ucfirst($project->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between py-3 pe-0 fs-9 border-bottom border-translucent">
                    <div class="d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info></p>
                        <a class="fw-semibold" href="#!" data-list-view="*">
                            View all
                            <span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span>
                        </a>
                        <a class="fw-semibold d-none" href="#!" data-list-view="less">
                            View Less
                            <span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span>
                        </a>
                    </div>
                    <div class="d-flex">
                        <button class="page-link" data-list-pagination="prev">
                            <span class="fas fa-chevron-left"></span>
                        </button>
                        <ul class="mb-0 pagination"></ul>
                        <button class="page-link pe-0" data-list-pagination="next">
                            <span class="fas fa-chevron-right"></span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
