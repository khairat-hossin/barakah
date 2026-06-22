@extends('layouts.phoenix')

@section('title', 'Create Role | ' . \App\Support\Branding::name())

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Create Role</h2>
                <a class="btn btn-phoenix-primary" href="{{ route('user-management.roles.index') }}">Back</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('user-management.roles.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <div class="form-floating">
                                <input class="form-control @error('name') is-invalid @enderror" id="roleName" type="text" name="name" placeholder="Role name" value="{{ old('name') }}" required>
                                <label for="roleName">Role name</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <h5 class="mb-3">Permissions</h5>
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
                            <button class="btn btn-primary px-5" type="submit">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
