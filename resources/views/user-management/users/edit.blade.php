@extends('layouts.phoenix')

@section('title', 'Edit User | ' . config('app.name'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Edit User</h2>
                <a class="btn btn-phoenix-primary" href="{{ route('user-management.users.index') }}">Back</a>
            </div>
            <div class="card">
                <div class="card-body">
                    @php
                        $selectedRoles = collect(old('roles', $user->roles->pluck('id')->all()))->map(fn ($id) => (int) $id);
                        $selectedPermissions = collect(old('permissions', $user->permissions->pluck('id')->all()))->map(fn ($id) => (int) $id);
                        $isProtectedSuperAdmin = $user->hasRole('Super Admin') && \App\Models\User::role('Super Admin')->count() === 1;
                    @endphp
                    @if ($isProtectedSuperAdmin)
                        <div class="alert alert-subtle-warning border border-warning-subtle mb-4" role="alert">
                            This user is the last <strong>Super Admin</strong>. Keep that role assigned or create another Super Admin first.
                        </div>
                    @endif
                    <form method="POST" action="{{ route('user-management.users.update', $user) }}" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('name') is-invalid @enderror" id="userName" type="text" name="name" placeholder="Name" value="{{ old('name', $user->name) }}" required>
                                <label for="userName">Name</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('email') is-invalid @enderror" id="userEmail" type="email" name="email" placeholder="Email" value="{{ old('email', $user->email) }}" required>
                                <label for="userEmail">Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('password') is-invalid @enderror" id="userPassword" type="password" name="password" placeholder="Password">
                                <label for="userPassword">New password</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control" id="userPasswordConfirmation" type="password" name="password_confirmation" placeholder="Confirm password">
                                <label for="userPasswordConfirmation">Confirm new password</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <h5 class="mb-3">Roles</h5>
                            <div class="row g-3">
                                @foreach ($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" id="role-{{ $role->id }}" type="checkbox" name="roles[]" value="{{ $role->id }}" @checked($selectedRoles->contains($role->id))>
                                            <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <h5 class="mb-3">Direct Permissions</h5>
                            <div class="row g-3">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" id="permission-{{ $permission->id }}" type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked($selectedPermissions->contains($permission->id))>
                                            <label class="form-check-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary px-5" type="submit">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
