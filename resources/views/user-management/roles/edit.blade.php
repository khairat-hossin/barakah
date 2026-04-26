@extends('layouts.phoenix')

@section('title', 'Edit Role | Barakah')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Edit Role</h2>
                <a class="btn btn-phoenix-primary" href="{{ route('user-management.roles.index') }}">Back</a>
            </div>
            <div class="card">
                <div class="card-body">
                    @if ($role->name === 'Super Admin')
                        <div class="alert alert-subtle-warning border border-warning-subtle mb-4" role="alert">
                            The <strong>Super Admin</strong> role name is locked because the application uses it as the top-level access override.
                        </div>
                    @endif
                    @php $selectedPermissions = collect(old('permissions', $role->permissions->pluck('id')->all()))->map(fn ($id) => (int) $id); @endphp
                    <form method="POST" action="{{ route('user-management.roles.update', $role) }}" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-12">
                            <div class="form-floating">
                                <input class="form-control @error('name') is-invalid @enderror" id="roleName" type="text" name="name" placeholder="Role name" value="{{ old('name', $role->name) }}" @disabled($role->name === 'Super Admin') required>
                                <label for="roleName">Role name</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if ($role->name === 'Super Admin')
                            <input type="hidden" name="name" value="{{ $role->name }}">
                        @endif
                        <div class="col-12">
                            <h5 class="mb-3">Permissions</h5>
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
                            <button class="btn btn-primary px-5" type="submit">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
