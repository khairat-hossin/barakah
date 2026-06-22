@extends('layouts.phoenix')

@section('title', 'Investments | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Investments</li>
    </ol>
</nav>

<div class="mb-9">
    <!-- Summary Cards -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">Total Capital</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳ {{ number_format($metrics['total_invested'], 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">{{ number_format($metrics['total_investments_count']) }} investments</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Active</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($metrics['active_count']) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Running</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-info fw-semibold" style="font-size: 0.75rem;">Current Value</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳ {{ number_format($metrics['current_portfolio_value'], 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Portfolio</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-warning fw-semibold" style="font-size: 0.75rem;">ROI %</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($metrics['roi_percentage'], 2) }}%</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Return</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Investments Table -->
    <div class="card">
        <div class="card-body">
            <!-- Filters -->
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input" type="search" placeholder="Search investments..." style="width: 250px;" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" id="filterType" style="width: 150px;">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto ms-auto">
                    @can('create investments')
                        <a href="{{ route('investments.create') }}" class="btn btn-primary btn-sm">
                            <span class="fas fa-plus me-2"></span>New Investment
                        </a>
                    @endcan
                </div>
            </div>

            <!-- DataTable -->
            <div class="table-responsive">
                <table id="investmentsTable" class="table table-hover fs-9 mb-0 align-middle">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="fw-semibold">CODE</th>
                            <th class="fw-semibold">NAME</th>
                            <th class="fw-semibold">TYPE</th>
                            <th class="fw-semibold">PRINCIPAL</th>
                            <th class="fw-semibold">CURRENT VALUE</th>
                            <th class="fw-semibold">ROI %</th>
                            <th class="fw-semibold">STATUS</th>
                            <th class="fw-semibold text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4 fs-9">
                <span class="text-body-secondary" data-list-info></span>
                <nav aria-label="Pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" id="prevBtn">
                            <a class="page-link" href="#" id="prevLink">Previous</a>
                        </li>
                        <li class="page-item active" id="pageIndicator">
                            <span class="page-link">1</span>
                        </li>
                        <li class="page-item" id="nextBtn">
                            <a class="page-link" href="#" id="nextLink">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const statuses = {
        'draft': { label: 'Draft', class: 'badge-phoenix-secondary' },
        'active': { label: 'Active', class: 'badge-phoenix-success' },
        'matured': { label: 'Matured', class: 'badge-phoenix-warning' },
        'closed': { label: 'Closed', class: 'badge-phoenix-info' },
        'suspended': { label: 'Suspended', class: 'badge-phoenix-danger' },
    };

    var table = $('#investmentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("investments.datatable") }}',
            type: 'GET',
            data: function(d) {
                d.type = $('#filterType').val();
                return d;
            }
        },
        columns: [
            {
                data: 'code',
                render: function(data, type, row) {
                    return `<a href="/investments/${row.id}" class="fw-semibold text-body-emphasis">${data}</a>`;
                }
            },
            { data: 'name' },
            { data: 'type' },
            {
                data: 'principal',
                render: function(data) {
                    return `৳ ${data}`;
                }
            },
            {
                data: 'current_value',
                render: function(data) {
                    return `৳ ${data}`;
                }
            },
            {
                data: 'roi',
                render: function(data) {
                    return `${data}%`;
                }
            },
            {
                data: 'status',
                render: function(data) {
                    const status = statuses[data];
                    return status ? `<span class="badge badge-phoenix ${status.class}">${status.label}</span>` : data;
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<div class="text-center">
                        <a href="/investments/${data}" class="btn btn-sm btn-outline-info">View</a>
                    </div>`;
                }
            }
        ],
        pageLength: 10,
        dom: 'lrti',
        language: {
            info: 'Showing _START_ to _END_ of _TOTAL_ investments',
            infoEmpty: 'No investments found',
            zeroRecords: 'No investments found',
            processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[1, 'desc']],
        columnDefs: [{
            targets: '_all',
            className: 'dt-no-sort-indicator'
        }],
        drawCallback: function() {
            updatePaginationInfo();
            updatePaginationControls(table);
        }
    });

    function updatePaginationInfo() {
        const info = table.page.info();
        const start = info.start + 1;
        const end = Math.min(info.end, info.recordsTotal);
        const total = info.recordsTotal;
        const text = total === 0 ? 'No investments found' : `Showing ${start} to ${end} of ${total} investments`;
        $('[data-list-info]').text(text);
    }

    $('.search-input').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    $('#filterType').on('change', function() {
        table.draw();
    });

    $('#prevLink').on('click', function(e) {
        e.preventDefault();
        if (table.page() > 0) {
            table.page(table.page() - 1).draw(false);
        }
    });

    $('#nextLink').on('click', function(e) {
        e.preventDefault();
        const info = table.page.info();
        if (table.page() < info.pages - 1) {
            table.page(table.page() + 1).draw(false);
        }
    });

    function updatePaginationControls(table) {
        const info = table.page.info();
        $('#pageIndicator span').text(info.page + 1);
        if (info.page === 0) {
            $('#prevBtn').addClass('disabled');
        } else {
            $('#prevBtn').removeClass('disabled');
        }
        if (info.page >= info.pages - 1) {
            $('#nextBtn').addClass('disabled');
        } else {
            $('#nextBtn').removeClass('disabled');
        }
    }
});
</script>
@endpush
@endsection
