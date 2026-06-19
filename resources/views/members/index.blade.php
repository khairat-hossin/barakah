@extends('layouts.phoenix')

@section('title', 'Members | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Members</li>
    </ol>
</nav>

<div class="mb-9">
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Total Members</p>
                            <h4 class="mb-0">{{ $members->count() }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-success rounded-pill">{{ number_format($members->count()) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Active</p>
                            <h4 class="mb-0">{{ $statusCounts['active'] }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-primary rounded-pill">Now</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Inactive</p>
                            <h4 class="mb-0">{{ $statusCounts['inactive'] }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-info rounded-pill">Hold</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Suspended</p>
                            <h4 class="mb-0">{{ $statusCounts['suspended'] }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-warning rounded-pill">Blocked</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table Section -->
    <div class="card">
        <div class="card-body">
            <!-- Filters and Search -->
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input" type="search" placeholder="Search members..." aria-label="Search" style="width: 250px;" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" id="filterStatus" style="width: 150px;">
                        <option value="">Filter by Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-auto ms-auto">
                    <div class="d-flex gap-2">
                        <a href="{{ route('members.import-form') }}" class="btn btn-outline-primary btn-sm">
                            <span class="fas fa-file-import me-2"></span>Import
                        </a>
                        <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                            <span class="fas fa-plus me-2"></span>Add Member
                        </a>
                    </div>
                </div>
            </div>

            <!-- DataTable (Desktop View) -->
            <div class="table-responsive d-none d-lg-block">
                <table id="membersTable" class="table table-hover fs-9 mb-0 align-middle">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="fw-semibold">NAME</th>
                            <th class="fw-semibold">CODE</th>
                            <th class="fw-semibold">EMAIL</th>
                            <th class="fw-semibold">PHONE</th>
                            <th class="fw-semibold">STATUS</th>
                            <th class="fw-semibold">JOIN DATE</th>
                            <th class="fw-semibold text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div id="mobileMembersContainer" class="d-lg-none"></div>

            <!-- Pagination Info and Controls -->
            <div class="d-flex justify-content-between align-items-center mt-4 fs-9">
                <span class="text-body-secondary" data-list-info></span>
                <nav aria-label="Pagination navigation">
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

@push('styles')
<style>
    .dataTables_wrapper thead th:before,
    .dataTables_wrapper thead th:after {
        display: none !important;
    }
    table.dataTable thead th {
        background-image: none !important;
    }

    /* Compact summary cards */
    .summary-card {
        padding: 0.5rem !important;
    }
    .summary-card .card-body {
        padding: 0.5rem !important;
    }
    .summary-card h4 {
        font-size: 0.95rem !important;
        margin-bottom: 0 !important;
    }
    .summary-card p {
        font-size: 0.65rem !important;
        margin-bottom: 0.1rem !important;
    }
    .summary-card .badge {
        font-size: 0.55rem !important;
        padding: 0.15rem 0.35rem !important;
    }

    /* Mobile Card View */
    .member-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: var(--bs-body-bg);
    }
    .member-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--bs-border-color);
    }
    .member-card-avatar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .member-card-name {
        font-weight: 600;
        color: var(--bs-body-emphasis-color);
        font-size: 1rem;
        text-decoration: none;
    }
    .member-card-code {
        font-size: 0.75rem;
        color: var(--bs-body-tertiary);
    }
    .member-card-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
    }
    .member-card-row.full {
        grid-template-columns: 1fr;
    }
    .member-card-field {
        display: flex;
        flex-direction: column;
    }
    .member-card-label {
        font-size: 0.75rem;
        color: var(--bs-body-secondary);
        margin-bottom: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .member-card-value {
        color: var(--bs-body-emphasis-color);
        font-weight: 500;
    }
    .member-card-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--bs-border-color);
    }
    .member-card-actions .btn {
        flex: 1;
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }

    /* Pagination styling */
    .pagination-sm .page-link {
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
    }
    .pagination .page-item.disabled .page-link {
        pointer-events: none;
        opacity: 0.5;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const statusClasses = {
        'active': 'badge-phoenix-success',
        'inactive': 'badge-phoenix-warning',
        'suspended': 'badge-phoenix-danger'
    };

    var table = $('#membersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("members.datatable") }}',
            type: 'GET',
            data: function(d) {
                d.status = $('#filterStatus').val();
                return d;
            }
        },
        columns: [
            {
                data: 'name',
                render: function(data, type, row) {
                    var initials = data.substring(0, 2).toUpperCase();
                    return `<a class="d-flex align-items-center text-body-emphasis" href="/members/${row.id}">
                        <div class="avatar avatar-s me-3">
                            <div class="avatar-name rounded-circle bg-primary-subtle">
                                <span class="text-primary fw-semibold">${initials}</span>
                            </div>
                        </div>
                        <p class="mb-0 fw-semibold">${data}</p>
                    </a>`;
                }
            },
            { data: 'code' },
            {
                data: 'email',
                render: function(data) {
                    return data !== 'N/A' ? `<a href="mailto:${data}" class="fw-semibold">${data}</a>` : data;
                }
            },
            { data: 'phone' },
            {
                data: 'status',
                render: function(data, type, row) {
                    const statusClass = statusClasses[data] || 'badge-phoenix-secondary';
                    return `<span class="badge badge-phoenix ${statusClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { data: 'joinDate' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="text-end gap-2 d-flex justify-content-end pe-3">
                        <a href="/members/${data}" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="/members/${data}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button onclick="if(confirm('Delete this member?')) { deleteRow(${data}); }" class="btn btn-sm btn-outline-danger">Delete</button>
                    </div>`;
                }
            }
        ],
        pageLength: 10,
        dom: 'lrti',
        language: {
            lengthMenu: '_MENU_',
            info: 'Showing _START_ to _END_ of _TOTAL_ members',
            infoEmpty: 'No members found',
            zeroRecords: 'No members found',
            processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        drawCallback: function(settings) {
            renderMobileView();
            updatePaginationInfo();
            updatePaginationControls(table);
        }
    });

    function renderMobileView() {
        const data = table.rows({page: 'current'}).data();
        const container = $('#mobileMembersContainer');
        container.empty();

        data.each(function(row) {
            const initials = row.name.substring(0, 2).toUpperCase();
            const statusClass = statusClasses[row.status] || 'badge-phoenix-secondary';
            const statusLabel = row.status.charAt(0).toUpperCase() + row.status.slice(1);

            const card = `
                <div class="member-card">
                    <div class="member-card-header">
                        <div>
                            <div class="member-card-avatar">
                                <div class="avatar avatar-s">
                                    <div class="avatar-name rounded-circle bg-primary-subtle">
                                        <span class="text-primary fw-semibold">${initials}</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="/members/${row.id}" class="member-card-name">${row.name}</a>
                                    <div class="member-card-code">${row.code}</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-phoenix ${statusClass}">${statusLabel}</span>
                        </div>
                    </div>
                    <div class="member-card-row">
                        <div class="member-card-field">
                            <span class="member-card-label">Email</span>
                            ${row.email !== 'N/A' ? `<a href="mailto:${row.email}" class="member-card-value">${row.email}</a>` : `<span class="member-card-value text-body-tertiary">${row.email}</span>`}
                        </div>
                        <div class="member-card-field">
                            <span class="member-card-label">Phone</span>
                            <span class="member-card-value">${row.phone}</span>
                        </div>
                    </div>
                    <div class="member-card-row full">
                        <div class="member-card-field">
                            <span class="member-card-label">Join Date</span>
                            <span class="member-card-value">${row.joinDate}</span>
                        </div>
                    </div>
                    <div class="member-card-actions">
                        <a href="/members/${row.id}" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="/members/${row.id}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button onclick="if(confirm('Delete this member?')) { deleteRow(${row.id}); }" class="btn btn-sm btn-outline-danger">Delete</button>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function updatePaginationInfo() {
        const info = table.page.info();
        const start = info.start + 1;
        const end = Math.min(info.end, info.recordsTotal);
        const total = info.recordsTotal;
        const infoText = total === 0 ? 'No members found' : `Showing ${start} to ${end} of ${total} members`;
        $('[data-list-info]').text(infoText);
    }

    // Search functionality
    $('.search-input').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    // Status filter
    $('#filterStatus').on('change', function() {
        table.draw();
    });

    // Bind pagination click handlers
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

    // Render mobile view on page load
    renderMobileView();
    updatePaginationInfo();
    updatePaginationControls(table);
});

function updatePaginationControls(table) {
    const info = table.page.info();
    const currentPage = info.page + 1;
    const totalPages = Math.max(1, info.pages);

    // Update page indicator
    $('#pageIndicator span').text(currentPage);

    // Update Previous button
    const prevBtn = $('#prevBtn');
    if (info.page === 0) {
        prevBtn.addClass('disabled');
    } else {
        prevBtn.removeClass('disabled');
    }

    // Update Next button
    const nextBtn = $('#nextBtn');
    if (info.page >= totalPages - 1) {
        nextBtn.addClass('disabled');
    } else {
        nextBtn.removeClass('disabled');
    }
}

function deleteRow(memberId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/members/${memberId}`;
    form.innerHTML = '<input type="hidden" name="_method" value="DELETE">' +
                     '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
