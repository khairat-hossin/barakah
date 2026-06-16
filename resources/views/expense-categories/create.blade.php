@extends('layouts.phoenix')

@section('title', 'Create Expense Category | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">Expense Categories</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-4">Create Expense Category</h2>

    <form method="POST" action="{{ route('expense-categories.store') }}">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Category Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('name') is-invalid @enderror"
                                   type="text" id="name" name="name"
                                   placeholder="Category Name"
                                   value="{{ old('name') }}" required />
                            <label for="name">Category Name <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('code') is-invalid @enderror"
                                   type="text" id="code" name="code"
                                   placeholder="e.g., OFF_SUP"
                                   value="{{ old('code') }}" required />
                            <label for="code">Code <span class="text-danger">*</span></label>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description"
                                      placeholder="Description"
                                      rows="3">{{ old('description') }}</textarea>
                            <label for="description">Description</label>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input @error('is_active') is-invalid @enderror"
                                   type="checkbox" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active', true)) />
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                            @error('is_active')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">Create Category</button>
            </div>
            <div class="col-auto">
                <a class="btn btn-secondary" href="{{ route('expense-categories.index') }}">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
