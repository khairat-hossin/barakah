@extends('layouts.phoenix')

@section('title', 'Users | ' . \App\Support\Branding::name())

@section('content')
    <div class="mb-9">

        <div class="row mb-4 gx-6 gy-3 align-items-center">
            <div class="col-auto">
                <h2 class="mb-0">Users<span class="fw-normal text-body-tertiary ms-3">({{ $users->count() }})</span></h2>
            </div>
            <div class="col-auto">
                <a class="btn btn-primary px-5" href="{{ route('user-management.users.create') }}">
                    <i class="fa-solid fa-plus me-2"></i>Add user
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">USER</th>
                                <th>ROLES</th>
                                <th>DIRECT PERMISSIONS</th>
                                <th>ACCESS LEVEL</th>
                                <th>STATUS</th>
                                <th class="text-end pe-4">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                @php
                                    $isCurrentUser = auth()->id() === $user->id;
                                    $isLastSuperAdmin = $user->hasRole('Super Admin') && $superAdminCount === 1;
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <p class="fw-semibold mb-0">{{ $user->name }}</p>
                                        <p class="text-body-tertiary fs-10 mb-0">{{ $user->email }}</p>
                                    </td>
                                    <td class="py-3">{{ $user->roles->pluck('name')->implode(', ') ?: 'No roles' }}</td>
                                    <td class="py-3">{{ $user->permissions->count() }}</td>
                                    <td class="py-3">
                                        @if ($user->hasRole('Super Admin'))
                                            <span class="badge badge-phoenix badge-phoenix-warning">Super Admin</span>
                                        @elseif ($user->roles->isNotEmpty())
                                            <span class="badge badge-phoenix badge-phoenix-primary">Role Based</span>
                                        @else
                                            <span class="badge badge-phoenix badge-phoenix-secondary">Direct / Limited</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if ($user->is_active)
                                            <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                        @else
                                            <span class="badge badge-phoenix badge-phoenix-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-inline-flex gap-2">
                                            @if (! $isCurrentUser && ! ($user->is_active && $isLastSuperAdmin))
                                                <form method="POST" action="{{ route('user-management.users.toggle-status', $user) }}"
                                                      onsubmit="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} this user?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="btn btn-sm {{ $user->is_active ? 'btn-phoenix-warning' : 'btn-phoenix-success' }}" type="submit">
                                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            @endif
                                            <a class="btn btn-sm btn-phoenix-primary" href="{{ route('user-management.users.edit', $user) }}">Edit</a>
                                            @if ($isCurrentUser || $isLastSuperAdmin)
                                                <button class="btn btn-sm btn-phoenix-danger" type="button" disabled>Delete</button>
                                            @else
                                                <form method="POST" action="{{ route('user-management.users.destroy', $user) }}">
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
