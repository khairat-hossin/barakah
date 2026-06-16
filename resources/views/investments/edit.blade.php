@extends('layouts.phoenix')
@section('title', 'Edit Investment | Barakah')
@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('investments.index') }}">Investments</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-3">Edit Investment (Draft)</h2>

    <form method="POST" action="{{ route('investments.update', $investment) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input class="form-control" type="text" value="{{ $investment->name }}" required />
                            <label>Investment Name *</label>
                        </div>
                    </div>
                    <!-- Additional form fields similar to create -->
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Save Changes</button>
            <a class="btn btn-secondary" href="{{ route('investments.show', $investment) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
