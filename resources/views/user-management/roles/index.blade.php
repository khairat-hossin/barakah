@extends('layouts.phoenix')

@section('title', 'Roles | Barakah')

@section('content')
    <div class="mb-9">

        <div class="row mb-4 gx-6 gy-3 align-items-center">
            <div class="col-auto">
                <h2 class="mb-0">Roles<span class="fw-normal text-body-tertiary ms-3">({{ $roles->count() }})</span></h2>
            </div>
            <div class="col-auto">
                <a class="btn btn-primary px-5" href="{{ route('user-management.roles.create') }}">
                    <i class="fa-solid fa-plus me-2"></i>Add role
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">NAME</th>
                                <th>GUARD</th>
                                <th>PERMISSIONS</th>
                                <th>USERS</th>
                                <th>TYPE</th>
                                <th class="text-end pe-4">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                @php
                                    $isProtected = in_array($role->name, $protectedRoles, true);
                                    $isInUse = $role->users_count > 0;
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3 fw-semibold">{{ $role->name }}</td>
                                    <td class="py-3">{{ $role->guard_name }}</td>
                                    <td class="py-3">{{ $role->permissions_count }}</td>
                                    <td class="py-3">{{ $role->users_count }}</td>
                                    <td class="py-3">
                                        @if ($isProtected)
                                            <span class="badge badge-phoenix badge-phoenix-warning">Protected</span>
                                        @else
                                            <span class="badge badge-phoenix badge-phoenix-secondary">Custom</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-inline-flex gap-2">
                                            <a class="btn btn-sm btn-phoenix-primary" href="{{ route('user-management.roles.edit', $role) }}">Edit</a>
                                            @if ($isProtected || $isInUse)
                                                <button class="btn btn-sm btn-phoenix-danger" type="button" disabled>Delete</button>
                                            @else
                                                <form method="POST" action="{{ route('user-management.roles.destroy', $role) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-phoenix-danger" type="submit">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
