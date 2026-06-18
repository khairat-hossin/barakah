<?php $__env->startSection('title', 'Deposits | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Deposits</li>
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
                            <p class="text-body-secondary fs-9 mb-2">Recorded</p>
                            <h4 class="mb-0"><?php echo e($entries->count()); ?></h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-success rounded-pill"><?php echo e(number_format($entries->count())); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">This Month</p>
                            <h4 class="mb-0">৳ <?php echo e(number_format($monthlyCollected, 0)); ?></h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-primary rounded-pill"><?php echo e(now()->format('M Y')); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Average</p>
                            <h4 class="mb-0">৳ <?php echo e(number_format($entries->count() > 0 ? $totalCollected / $entries->count() : 0, 0)); ?></h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-info rounded-pill">Per Deposit</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Total Collected</p>
                            <h4 class="mb-0">৳ <?php echo e(number_format($totalCollected, 0)); ?></h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-warning rounded-pill">All Time</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deposits Table Section -->
    <div class="card">
        

        <div class="card-body">
            <!-- Filters and Search -->
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input" type="search" placeholder="Search deposits..." aria-label="Search" style="width: 250px;" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" id="filterMethod" style="width: 150px;">
                        <option value="">Filter by Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_banking">Mobile Banking</option>
                        <option value="check">Check</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-auto ms-auto">
                    
                    <a href="<?php echo e(route('deposits.create')); ?>" class="btn btn-primary btn-sm">
                        <span class="fas fa-plus me-2"></span>Record Deposit
                    </a>
                </div>
            </div>

            <!-- DataTable (Desktop View) -->
            <div class="table-responsive d-none d-lg-block">
                <table id="depositsTable" class="table table-hover fs-9 mb-0 align-middle">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="fw-semibold">MEMBER</th>
                            <th class="fw-semibold">AMOUNT</th>
                            <th class="fw-semibold">METHOD</th>
                            <th class="fw-semibold">DATE</th>
                            <th class="fw-semibold">TXN ID</th>
                            <th class="fw-semibold">RECORDED BY</th>
                            <th class="fw-semibold text-center">STATUS</th>
                            <th class="fw-semibold text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div id="mobileDepositsContainer" class="d-lg-none"></div>

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

<?php $__env->startPush('styles'); ?>
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
    .deposit-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: var(--bs-body-bg);
    }
    .deposit-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--bs-border-color);
    }
    .deposit-card-member {
        font-weight: 600;
        color: var(--bs-body-emphasis-color);
        font-size: 0.95rem;
    }
    .deposit-card-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--bs-body-emphasis-color);
    }
    .deposit-card-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
    }
    .deposit-card-row.full {
        grid-template-columns: 1fr;
    }
    .deposit-card-field {
        display: flex;
        flex-direction: column;
    }
    .deposit-card-label {
        font-size: 0.75rem;
        color: var(--bs-body-secondary);
        margin-bottom: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .deposit-card-value {
        color: var(--bs-body-emphasis-color);
        font-weight: 500;
    }
    .deposit-card-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--bs-border-color);
    }
    .deposit-card-actions .btn {
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    const methods = {
        'cash': { label: 'Cash', class: 'badge-phoenix-primary' },
        'bank_transfer': { label: 'Bank Transfer', class: 'badge-phoenix-info' },
        'mobile_banking': { label: 'Mobile Banking', class: 'badge-phoenix-success' },
        'check': { label: 'Check', class: 'badge-phoenix-warning' },
        'other': { label: 'Other', class: 'badge-phoenix-secondary' }
    };

    var table = $('#depositsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("deposits.datatable")); ?>',
            type: 'GET',
            data: function(d) {
                d.payment_method = $('#filterMethod').val();
                return d;
            }
        },
        columns: [
            {
                data: 'member_name',
                render: function(data, type, row) {
                    return `<a href="/members/${row.member_id}" class="fw-semibold text-body-emphasis">${data}</a>`;
                }
            },
            {
                data: 'amount',
                render: function(data) {
                    return `<span class="fw-bold">৳ ${parseFloat(data).toLocaleString('en-BD', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                }
            },
            {
                data: 'payment_method',
                render: function(data) {
                    const method = methods[data];
                    return method ? `<span class="badge badge-phoenix ${method.class}">${method.label}</span>` : `<span class="badge badge-phoenix badge-phoenix-secondary">${data}</span>`;
                }
            },
            {
                data: 'deposit_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('en-BD', {year: 'numeric', month: 'short', day: 'numeric'});
                }
            },
            {
                data: 'transaction_id',
                render: function(data) {
                    return data ? `<code class="text-body-tertiary fs-10">${data}</code>` : '<span class="text-body-tertiary">-</span>';
                }
            },
            {
                data: 'recorder_name',
                render: function(data) {
                    return `<small class="text-body-secondary">${data}</small>`;
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<div class="text-center"><span class="badge badge-phoenix badge-phoenix-success">Recorded</span></div>`;
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="text-end gap-2 d-flex justify-content-end pe-3">
                        <a href="/deposits/${data}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button onclick="if(confirm('Delete this deposit?')) { deleteDeposit(${data}); }" class="btn btn-sm btn-outline-danger">Delete</button>
                    </div>`;
                }
            }
        ],
        pageLength: 10,
        dom: 'lrti',
        language: {
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ deposits',
            infoEmpty: 'No deposits found',
            zeroRecords: 'No deposits found',
            processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        order: [[3, 'desc']],
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
        const container = $('#mobileDepositsContainer');
        container.empty();

        data.each(function(row) {
            const method = methods[row.payment_method];
            const methodLabel = method ? method.label : row.payment_method;
            const methodClass = method ? method.class : 'badge-phoenix-secondary';
            const amount = parseFloat(row.amount).toLocaleString('en-BD', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            const date = new Date(row.deposit_date).toLocaleDateString('en-BD', {year: 'numeric', month: 'short', day: 'numeric'});

            const card = `
                <div class="deposit-card">
                    <div class="deposit-card-header">
                        <div>
                            <div class="deposit-card-member">
                                <a href="/members/${row.member_id}" style="text-decoration: none; color: inherit;">${row.member_name}</a>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="deposit-card-amount">৳ ${amount}</div>
                        </div>
                    </div>
                    <div class="deposit-card-row">
                        <div class="deposit-card-field">
                            <span class="deposit-card-label">Method</span>
                            <span><span class="badge badge-phoenix ${methodClass}">${methodLabel}</span></span>
                        </div>
                        <div class="deposit-card-field">
                            <span class="deposit-card-label">Date</span>
                            <span class="deposit-card-value">${date}</span>
                        </div>
                    </div>
                    ${row.transaction_id ? `
                    <div class="deposit-card-row">
                        <div class="deposit-card-field">
                            <span class="deposit-card-label">TXN ID</span>
                            <code class="deposit-card-value">${row.transaction_id}</code>
                        </div>
                    </div>
                    ` : ''}
                    <div class="deposit-card-row full">
                        <div class="deposit-card-field">
                            <span class="deposit-card-label">Recorded By</span>
                            <span class="deposit-card-value text-body-secondary">${row.recorder_name}</span>
                        </div>
                    </div>
                    <div class="deposit-card-actions">
                        <a href="/deposits/${row.id}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button onclick="if(confirm('Delete this deposit?')) { deleteDeposit(${row.id}); }" class="btn btn-sm btn-outline-danger">Delete</button>
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
        const infoText = total === 0 ? 'No deposits found' : `Showing ${start} to ${end} of ${total} deposits`;
        $('[data-list-info]').text(infoText);
    }

    // Search functionality
    $('.search-input').on('keyup', function() {
        table.search($(this).val()).draw();
    });

    // Method filter
    $('#filterMethod').on('change', function() {
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

function deleteDeposit(depositId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/deposits/${depositId}`;
    form.innerHTML = '<input type="hidden" name="_method" value="DELETE">' +
                     '<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">';
    document.body.appendChild(form);
    form.submit();
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/deposits/index.blade.php ENDPATH**/ ?>