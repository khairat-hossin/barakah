@extends('layouts.phoenix')

@section('title', 'Edit Payment Method | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('payment-methods.index') }}">Payment Methods</a></li>
        <li class="breadcrumb-item active">{{ $paymentMethod->name }}</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-4">Edit Payment Method</h2>

    <form method="POST" action="{{ route('payment-methods.update', $paymentMethod) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                <h5 class="mb-0">Payment Method Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('name') is-invalid @enderror"
                                   type="text" id="name" name="name"
                                   placeholder="Payment Method Name"
                                   value="{{ old('name', $paymentMethod->name) }}" required />
                            <label for="name">Payment Method Name <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control @error('code') is-invalid @enderror"
                                   type="text" id="code" name="code"
                                   placeholder="e.g., BANK_TRANSFER"
                                   value="{{ old('code', $paymentMethod->code) }}" required />
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
                                      rows="3">{{ old('description', $paymentMethod->description) }}</textarea>
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
                                   value="1" @checked(old('is_active', $paymentMethod->is_active)) />
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
                <button class="btn btn-primary" type="submit">Update Method</button>
            </div>
            <div class="col-auto">
                <a class="btn btn-secondary" href="{{ route('payment-methods.index') }}">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
