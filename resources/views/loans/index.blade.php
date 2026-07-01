@extends('layouts.phoenix')

@section('title', 'Loans | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Loans</li>
    </ol>
</nav>

<div class="mb-9">
    <!-- Summary Cards -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">Total Lent</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem; line-height: 1.2; margin: 0.25rem 0;">৳ {{ number_format($totalLent, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Approved & repaid loans</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #fd7e14 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-warning fw-semibold" style="font-size: 0.75rem;">Outstanding</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem; line-height: 1.2; margin: 0.25rem 0;">৳ {{ number_format($outstanding, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Still owed</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Total Repaid</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem; line-height: 1.2; margin: 0.25rem 0;">৳ {{ number_format($totalRepaid, 0) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Collected back</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-danger fw-semibold" style="font-size: 0.75rem;">Pending / Overdue</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.4rem; line-height: 1.2; margin: 0.25rem 0;">{{ $pendingCount }} / {{ $overdueCount }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Awaiting approval / past due</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Filters -->
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input" type="search" placeholder="Search loans..." aria-label="Search" style="width: 220px;" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" id="filterStatus" style="width: 150px;">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="repaid">Repaid</option>
                        <option value="rejected">Rejected</option>
                        <option value="written_off">Written Off</option>
                    </select>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2">
                        <label for="fromMonth" class="form-label mb-0 fs-9 text-body-secondary">From</label>
                        <input type="month" class="form-control form-control-sm" id="fromMonth" style="width: 140px;" />
                        <label for="toMonth" class="form-label mb-0 fs-9 text-body-secondary">To</label>
                        <input type="month" class="form-control form-control-sm" id="toMonth" style="width: 140px;" />
                        <button type="button" class="btn btn-phoenix-secondary btn-sm" id="clearMonthFilter" title="Clear month filter"><span class="fas fa-times"></span></button>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    @can('create loans')
                        <a href="{{ route('loans.create') }}" class="btn btn-primary btn-sm">
                            <span class="fas fa-plus me-2"></span>New Loan
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Desktop Table -->
            <div class="table-responsive d-none d-lg-block">
                <table id="loansTable" class="table table-hover fs-9 mb-0 align-middle">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="fw-semibold">CODE</th>
                            <th class="fw-semibold">MEMBER</th>
                            <th class="fw-semibold">LOAN</th>
                            <th class="fw-semibold">PAYABLE</th>
                            <th class="fw-semibold">OUTSTANDING</th>
                            <th class="fw-semibold">TAKEN</th>
                            <th class="fw-semibold">DUE</th>
                            <th class="fw-semibold text-center">STATUS</th>
                            <th class="fw-semibold text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div id="mobileLoansContainer" class="d-lg-none"></div>

            <div class="d-flex justify-content-between align-items-center mt-4 fs-9">
                <span class="text-body-secondary" data-list-info></span>
                <nav aria-label="Pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" id="prevBtn"><a class="page-link" href="#" id="prevLink">Previous</a></li>
                        <li class="page-item active" id="pageIndicator"><span class="page-link">1</span></li>
                        <li class="page-item" id="nextBtn"><a class="page-link" href="#" id="nextLink">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dataTables_wrapper thead th:before, .dataTables_wrapper thead th:after { display: none !important; }
    table.dataTable thead th { background-image: none !important; }
    .loan-card { border: 1px solid var(--bs-border-color); border-radius: 0.375rem; padding: 1rem; margin-bottom: 1rem; background-color: var(--bs-body-bg); }
    .loan-card-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.5rem; font-size: 0.85rem; }
    .loan-card-label { font-size: 0.7rem; color: var(--bs-body-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .pagination-sm .page-link { padding: 0.35rem 0.65rem; font-size: 0.85rem; }
    .pagination .page-item.disabled .page-link { pointer-events: none; opacity: 0.5; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    const statusBadge = {
        pending: { label: 'Pending', class: 'badge-phoenix-warning' },
        active: { label: 'Active', class: 'badge-phoenix-primary' },
        partially_repaid: { label: 'Partially Repaid', class: 'badge-phoenix-info' },
        overdue: { label: 'Overdue', class: 'badge-phoenix-danger' },
        repaid: { label: 'Repaid', class: 'badge-phoenix-success' },
        rejected: { label: 'Rejected', class: 'badge-phoenix-secondary' },
        written_off: { label: 'Written Off', class: 'badge-phoenix-secondary' },
    };
    const badge = (s) => {
        const b = statusBadge[s] || { label: s, class: 'badge-phoenix-secondary' };
        return `<span class="badge badge-phoenix ${b.class}">${b.label}</span>`;
    };

    var table = $('#loansTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("loans.datatable") }}',
            data: function (d) {
                d.status = $('#filterStatus').val();
                d.from_month = $('#fromMonth').val();
                d.to_month = $('#toMonth').val();
            }
        },
        columns: [
            { data: 'loan_code', render: (d) => `<code class="text-body-tertiary">${d}</code>` },
            { data: 'member_name', render: (d, t, r) => `<a href="/members/${r.member_id}" class="fw-semibold text-body-emphasis">${d}</a>${r.member_status && r.member_status !== 'active' ? ' <span class="badge badge-phoenix badge-phoenix-secondary fs-10">Inactive</span>' : ''}` },
            { data: 'loan_amount', render: (d) => `৳ ${d}` },
            { data: 'total_payable', render: (d) => `৳ ${d}` },
            { data: 'outstanding', render: (d) => `<span class="fw-bold">৳ ${d}</span>` },
            { data: 'taken_date' },
            { data: 'due_date' },
            { data: 'status', className: 'text-center', orderable: false, render: (d) => badge(d) },
            { data: 'id', orderable: false, searchable: false, render: (d) => `<div class="text-end"><a href="/loans/${d}" class="btn btn-sm btn-outline-secondary">View</a></div>` },
        ],
        pageLength: 10,
        dom: 'lrti',
        order: [[5, 'desc']],
        language: {
            info: 'Showing _START_ to _END_ of _TOTAL_ loans',
            infoEmpty: 'No loans found', zeroRecords: 'No loans found',
            processing: '<div class="spinner-border spinner-border-sm" role="status"></div>'
        },
        drawCallback: function () { renderMobile(); updateInfo(); updateControls(table); }
    });

    function renderMobile() {
        const data = table.rows({ page: 'current' }).data();
        const c = $('#mobileLoansContainer'); c.empty();
        data.each(function (r) {
            c.append(`
                <div class="loan-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div><a href="/members/${r.member_id}" class="fw-semibold" style="text-decoration:none;color:inherit;">${r.member_name}</a>${r.member_status && r.member_status !== 'active' ? ' <span class="badge badge-phoenix badge-phoenix-secondary fs-10">Inactive</span>' : ''}
                            <div><code class="text-body-tertiary fs-10">${r.loan_code}</code></div></div>
                        <div>${badge(r.status)}</div>
                    </div>
                    <div class="loan-card-row">
                        <div><span class="loan-card-label">Loan</span><div>৳ ${r.loan_amount}</div></div>
                        <div><span class="loan-card-label">Outstanding</span><div class="fw-bold">৳ ${r.outstanding}</div></div>
                        <div><span class="loan-card-label">Taken</span><div>${r.taken_date}</div></div>
                        <div><span class="loan-card-label">Due</span><div>${r.due_date}</div></div>
                    </div>
                    <a href="/loans/${r.id}" class="btn btn-sm btn-outline-secondary w-100 mt-2">View</a>
                </div>`);
        });
    }
    function updateInfo() {
        const i = table.page.info();
        $('[data-list-info]').text(i.recordsTotal === 0 ? 'No loans found' : `Showing ${i.start + 1} to ${Math.min(i.end, i.recordsTotal)} of ${i.recordsTotal} loans`);
    }
    $('.search-input').on('keyup', function () { table.search($(this).val()).draw(); });
    $('#filterStatus, #fromMonth, #toMonth').on('change', function () { table.draw(); });
    $('#clearMonthFilter').on('click', function () { $('#fromMonth, #toMonth').val(''); table.draw(); });
    $('#prevLink').on('click', function (e) { e.preventDefault(); if (table.page() > 0) table.page(table.page() - 1).draw(false); });
    $('#nextLink').on('click', function (e) { e.preventDefault(); const i = table.page.info(); if (table.page() < i.pages - 1) table.page(table.page() + 1).draw(false); });
    renderMobile(); updateInfo(); updateControls(table);
});
function updateControls(table) {
    const i = table.page.info();
    $('#pageIndicator span').text(i.page + 1);
    $('#prevBtn').toggleClass('disabled', i.page === 0);
    $('#nextBtn').toggleClass('disabled', i.page >= Math.max(1, i.pages) - 1);
}
</script>
@endpush
@endsection
