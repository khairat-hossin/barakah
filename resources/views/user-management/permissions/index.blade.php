@extends('layouts.phoenix')

@section('title', 'Permissions | Barakah')

@section('content')
    <div class="mb-9">

        <div class="row mb-4 gx-6 gy-3 align-items-center">
            <div class="col-auto">
                <h2 class="mb-0">Permissions<span class="fw-normal text-body-tertiary ms-3">({{ $permissions->count() }})</span></h2>
            </div>
            <div class="col-auto">
                <a class="btn btn-primary px-5" href="{{ route('user-management.permissions.create') }}">
                    <i class="fa-solid fa-plus me-2"></i>Add permission
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
                                <th>USED BY ROLES</th>
                                <th>USED BY USERS</th>
                                <th>TYPE</th>
                                <th class="text-end pe-4">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                @php
                                    $isProtected = in_array($permission->name, $protectedPermissions, true);
                                    $isInUse = $permission->roles_count > 0 || $permission->users_count > 0;
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3 fw-semibold">{{ $permission->name }}</td>
                                    <td class="py-3">{{ $permission->guard_name }}</td>
                                    <td class="py-3">{{ $permission->roles_count }}</td>
                                    <td class="py-3">{{ $permission->users_count }}</td>
                                    <td class="py-3">
                                        @if ($isProtected)
                                            <span class="badge badge-phoenix badge-phoenix-warning">System</span>
                                        @else
                                            <span class="badge badge-phoenix badge-phoenix-secondary">Custom</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-inline-flex gap-2">
                                            <a class="btn btn-sm btn-phoenix-primary" href="{{ route('user-management.permissions.edit', $permission) }}">Edit</a>
                                            @if ($isProtected || $isInUse)
                                                <button class="btn btn-sm btn-phoenix-danger" type="button" disabled>Delete</button>
                                            @else
                                                <form method="POST" action="{{ route('user-management.permissions.destroy', $permission) }}">
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
