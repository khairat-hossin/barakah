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
    <div class="row g-2 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Members</h2>
        </div>
    </div>

    <!-- Status Nav Links -->
    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">
                <span>All</span>
                <span class="text-body-tertiary fw-semibold">({{ $members->count() }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Active</span>
                <span class="text-body-tertiary fw-semibold">({{ $statusCounts['active'] }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Inactive</span>
                <span class="text-body-tertiary fw-semibold">({{ $statusCounts['inactive'] }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Suspended</span>
                <span class="text-body-tertiary fw-semibold">({{ $statusCounts['suspended'] }})</span>
            </a>
        </li>
    </ul>

    <!-- DataTable -->
    <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
        <div class="mb-4 mt-4">
            <div class="row g-3">
                <div class="col-auto">
                    <div class="search-box">
                        <form class="position-relative">
                            <input class="form-control search-input" type="search" placeholder="Search members..." aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('members.create') }}" class="btn btn-primary">
                        <span class="fas fa-plus me-2"></span>Add Member
                    </a>
                </div>
            </div>
        </div>

        <div>
            <table id="membersTable" class="table table-sm fs-9 mb-0 align-middle">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>CODE</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>STATUS</th>
                        <th>JOIN DATE</th>
                        <th class="text-end">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#membersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("members.datatable") }}',
            type: 'GET'
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
                    return `<span class="badge badge-phoenix ${row.status_class}">${data}</span>`;
                }
            },
            { data: 'joinDate' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="text-end">
                        <div class="btn-reveal-trigger position-static">
                            <button class="btn btn-sm btn-phoenix-secondary btn-icon btn-reveal" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false">
                                <span class="fas fa-ellipsis-h"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end py-2">
                                <a class="dropdown-item" href="/members/${data}">View</a>
                                <a class="dropdown-item" href="/members/${data}/edit">Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#!" onclick="if(confirm('Delete this member?')) { deleteRow(${data}); }">Delete</a>
                            </div>
                        </div>
                    </div>`;
                }
            }
        ],
        pageLength: 10,
        dom: 'lrtip',
        language: {
            lengthMenu: '_MENU_',
            info: 'Showing _START_ to _END_ of _TOTAL_ members',
            infoEmpty: 'No members found',
            zeroRecords: 'No members found',
            processing: '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });

    // Search functionality
    $('.search-input').on('keyup', function() {
        table.search($(this).val()).draw();
    });
});

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
