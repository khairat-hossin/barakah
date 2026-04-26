@extends('layouts.phoenix')

@section('title', 'Edit Permission | Barakah')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Edit Permission</h2>
                <a class="btn btn-phoenix-primary" href="{{ route('user-management.permissions.index') }}">Back</a>
            </div>
            <div class="card">
                <div class="card-body">
                    @if (in_array($permission->name, \App\Support\RbacDefaults::protectedPermissions(), true))
                        <div class="alert alert-subtle-warning border border-warning-subtle mb-4" role="alert">
                            This is a system permission used by the application. Its name is locked to avoid breaking access rules.
                        </div>
                    @endif
                    <form method="POST" action="{{ route('user-management.permissions.update', $permission) }}" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-12">
                            <div class="form-floating">
                                <input class="form-control @error('name') is-invalid @enderror" id="permissionName" type="text" name="name" placeholder="Permission name" value="{{ old('name', $permission->name) }}" @disabled(in_array($permission->name, \App\Support\RbacDefaults::protectedPermissions(), true)) required>
                                <label for="permissionName">Permission name</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if (in_array($permission->name, \App\Support\RbacDefaults::protectedPermissions(), true))
                            <input type="hidden" name="name" value="{{ $permission->name }}">
                        @endif
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary px-5" type="submit">Update Permission</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
