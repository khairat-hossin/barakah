@extends('layouts.phoenix')

@section('title', 'Expenses | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Expenses</li>
    </ol>
</nav>

<div class="mb-9">
    <!-- Summary Cards -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">Total Expenses</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳ {{ number_format($totalExpenses, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">All time</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">This Month</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">৳ {{ number_format($monthlyExpenses, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">{{ now()->format('M Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-warning fw-semibold" style="font-size: 0.75rem;">Pending Approval</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($pendingCount) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Needs action</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-info fw-semibold" style="font-size: 0.75rem;">Count</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($expenses->count()) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Records</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table Section -->
    <div class="card">
        <div class="card-body">
            <!-- Filters and Search -->
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input" type="search" placeholder="Search expenses..." aria-label="Search" style="width: 250px;" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" id="filterCategory" style="width: 150px;">
                        <option value="">Filter by Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" id="filterFundSource" style="width: 150px;">
                        <option value="">Filter by Source</option>
                        @foreach($fundSources as $source)
                            <option value="{{ $source }}">{{ ucfirst($source) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                        <span class="fas fa-plus me-2"></span>Record Expense
                    </a>
                    @can('manage expenses')
                        <a href="{{ route('expense-categories.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                            <span class="fas fa-tags me-2"></span>Categories
                        </a>
                    @endcan
                </div>
            </div>

            <!-- DataTable (Desktop View) -->
            <div class="table-responsive d-none d-lg-block">
                <table id="expensesTable" class="table table-hover fs-9 mb-0 align-middle">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="fw-semibold">EXP #</th>
                            <th class="fw-semibold">DATE</th>
                            <th class="fw-semibold">CATEGORY</th>
                            <th class="fw-semibold">TITLE</th>
                            <th class="fw-semibold">AMOUNT</th>
                            <th class="fw-semibold">SOURCE</th>
                            <th class="fw-semibold">STATUS</th>
                            <th class="fw-semibold text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div id="mobileExpensesContainer" class="d-lg-none"></div>

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

    /* Mobile Card View */
    .expense-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: var(--bs-body-bg);
    }
    .expense-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--bs-border-color);
    }
    .expense-card-title {
        font-weight: 600;
        color: var(--bs-body-emphasis-color);
        font-size: 0.95rem;
    }
    .expense-card-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--bs-body-emphasis-color);
    }
    .expense-card-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
    }
    .expense-card-row.full {
        grid-template-columns: 1fr;
    }
    .expense-card-field {
        display: flex;
        flex-direction: column;
    }
    .expense-card-label {
        font-size: 0.75rem;
        color: var(--bs-body-secondary);
        margin-bottom: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .expense-card-value {
        color: var(--bs-body-emphasis-color);
        font-weight: 500;
    }
    .expense-card-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--bs-border-color);
    }
    .expense-card-actions .btn {
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
    const statuses = {
        'draft': { label: 'Draft', class: 'badge-phoenix-secondary' },
        'pending': { label: 'Pending', class: 'badge-phoenix-warning' },
        'approved': { label: 'Approved', class: 'badge-phoenix-success' },
        'paid': { label: 'Paid', class: 'badge-phoenix-info' }
    };

    const fundSources = {
        'operating': { label: 'Operating', class: 'badge-phoenix-primary' },
        'reserve': { label: 'Reserve', class: 'badge-phoenix-info' },
        'emergency': { label: 'Emergency', class: 'badge-phoenix-danger' }
    };

    var table = $('#expensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("expenses.datatable") }}',
            type: 'GET',
            data: function(d) {
                d.category = $('#filterCategory').val();
                d.fund_source = $('#filterFundSource').val();
                return d;
            }
        },
        columns: [
            {
                data: 'expense_number',
                render: function(data, type, row) {
                    return `<a href="/expenses/${row.id}" class="fw-semibold text-body-emphasis">${data}</a>`;
                }
            },
            {
                data: 'expense_date',
                render: function(data) {
                    return data;
                }
            },
            {
                data: 'category',
                render: function(data) {
                    return data;
                }
            },
            {
                data: 'title',
                render: function(data) {
                    return data;
                }
            },
            {
                data: 'amount',
                render: function(data) {
                    return `<span class="fw-bold">৳ ${data}</span>`;
                }
            },
            {
                data: 'fund_source',
                render: function(data) {
                    const source = fundSources[data.toLowerCase()];
                    return source ? `<span class="badge badge-phoenix ${source.class}">${source.label}</span>` : `<span class="badge badge-phoenix badge-phoenix-secondary">${data}</span>`;
                }
            },
            {
                data: 'status',
                render: function(data) {
                    const status = statuses[data];
                    return status ? `<div class="text-center"><span class="badge badge-phoenix ${status.class}">${status.label}</span></div>` : `<span class="badge badge-phoenix badge-phoenix-secondary">${data}</span>`;
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = `<div class="text-end gap-2 d-flex justify-content-end pe-3">
                        <a href="/expenses/${data}" class="btn btn-sm btn-outline-info">View</a>`;

                    if (row.status === 'draft') {
                        actions += `<a href="/expenses/${data}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                   <button onclick="if(confirm('Delete this expense?')) { deleteExpense(${data}); }" class="btn btn-sm btn-outline-danger">Delete</button>`;
                    }

                    actions += `</div>`;
                    return actions;
                }
            }
        ],
        pageLength: 10,
        dom: 'lrti',
        language: {
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ expenses',
            infoEmpty: 'No expenses found',
            zeroRecords: 'No expenses found',
            processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[1, 'desc']],
        columnDefs: [{
            targets: '_all',
            orderable: true,
            className: 'dt-no-sort-indicator'
        }],
        drawCallback: function(settings) {
            renderMobileView();
            updatePaginationInfo();
            updatePaginationControls(table);
        }
    });

    function renderMobileView() {
        const data = table.rows({page: 'current'}).data();
        const container = $('#mobileExpensesContainer');
        container.empty();

        data.each(function(row) {
            const status = statuses[row.status];
            const source = fundSources[row.fund_source.toLowerCase()];
            const statusLabel = status ? status.label : row.status;
            const statusClass = status ? status.class : 'badge-phoenix-secondary';
            const sourceLabel = source ? source.label : row.fund_source;
            const sourceClass = source ? source.class : 'badge-phoenix-secondary';

            let actions = `<a href="/expenses/${row.id}" class="btn btn-sm btn-outline-info">View</a>`;
            if (row.status === 'draft') {
                actions += `<a href="/expenses/${row.id}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                           <button onclick="if(confirm('Delete this expense?')) { deleteExpense(${row.id}); }" class="btn btn-sm btn-outline-danger">Delete</button>`;
            }

            const card = `
                <div class="expense-card">
                    <div class="expense-card-header">
                        <div>
                            <div class="expense-card-title">
                                <a href="/expenses/${row.id}" style="text-decoration: none; color: inherit;">${row.title}</a>
                            </div>
                            <small class="text-body-secondary">${row.expense_number}</small>
                        </div>
                        <div class="text-end">
                            <div class="expense-card-amount">৳ ${row.amount}</div>
                        </div>
                    </div>
                    <div class="expense-card-row">
                        <div class="expense-card-field">
                            <span class="expense-card-label">Date</span>
                            <span class="expense-card-value">${row.expense_date}</span>
                        </div>
                        <div class="expense-card-field">
                            <span class="expense-card-label">Category</span>
                            <span class="expense-card-value">${row.category}</span>
                        </div>
                    </div>
                    <div class="expense-card-row">
                        <div class="expense-card-field">
                            <span class="expense-card-label">Source</span>
                            <span><span class="badge badge-phoenix ${sourceClass}">${sourceLabel}</span></span>
                        </div>
                        <div class="expense-card-field">
                            <span class="expense-card-label">Status</span>
                            <span><span class="badge badge-phoenix ${statusClass}">${statusLabel}</span></span>
                        </div>
                    </div>
                    <div class="expense-card-actions">
                        ${actions}
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
        const infoText = total === 0 ? 'No expenses found' : `Showing ${start} to ${end} of ${total} expenses`;
        $('[data-list-info]').text(infoText);
    }

    // Search functionality
    $('.search-input').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    // Category filter
    $('#filterCategory').on('change', function() {
        table.draw();
    });

    // Fund source filter
    $('#filterFundSource').on('change', function() {
        table.draw();
    });

    // Render mobile view on page load
    renderMobileView();
    updatePaginationInfo();
    updatePaginationControls(table);

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

function deleteExpense(expenseId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/expenses/${expenseId}`;
    form.innerHTML = '<input type="hidden" name="_method" value="DELETE">' +
                     '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

@endsection
