@extends('layouts.phoenix')

@section('title', 'Create Permission | ' . \App\Support\Branding::name())

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Create Permission</h2>
                <a class="btn btn-phoenix-primary" href="{{ route('user-management.permissions.index') }}">Back</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('user-management.permissions.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <div class="form-floating">
                                <input class="form-control @error('name') is-invalid @enderror" id="permissionName" type="text" name="name" placeholder="Permission name" value="{{ old('name') }}" required>
                                <label for="permissionName">Permission name</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary px-5" type="submit">Create Permission</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
