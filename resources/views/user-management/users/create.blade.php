@extends('layouts.phoenix')

@section('title', 'Create User | Barakah')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Create User</h2>
                <a class="btn btn-phoenix-primary" href="{{ route('user-management.users.index') }}">Back</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('user-management.users.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('name') is-invalid @enderror" id="userName" type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
                                <label for="userName">Name</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('email') is-invalid @enderror" id="userEmail" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                                <label for="userEmail">Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('password') is-invalid @enderror" id="userPassword" type="password" name="password" placeholder="Password" required>
                                <label for="userPassword">Password</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control" id="userPasswordConfirmation" type="password" name="password_confirmation" placeholder="Confirm password" required>
                                <label for="userPasswordConfirmation">Confirm password</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <h5 class="mb-3">Roles</h5>
                            <div class="row g-3">
                                @foreach ($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" id="role-{{ $role->id }}" type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(collect(old('roles'))->contains($role->id))>
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
                                            <input class="form-check-input" id="permission-{{ $permission->id }}" type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(collect(old('permissions'))->contains($permission->id))>
                                            <label class="form-check-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary px-5" type="submit">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
