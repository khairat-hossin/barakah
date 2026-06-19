@extends('layouts.phoenix')

@section('title', 'Import Members | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
        <li class="breadcrumb-item active">Import</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row mb-4 gx-6 gy-3 align-items-center">
        <div class="col-auto">
            <h2 class="mb-0">Import Members</h2>
        </div>
    </div>

    <div class="row g-4">
        <!-- Import Form Column -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Upload Excel File</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('members.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold">Select Excel File <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls" required />
                                <label class="input-group-text" for="file">Choose File</label>
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-body-secondary d-block mt-2">Supported formats: .xlsx, .xls (Max 5MB)</small>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <strong>ℹ️ File Format:</strong> Please use the template provided below with the following columns:
                            <ul class="mb-0 mt-2">
                                <li><strong>Name</strong> - Member's full name (required)</li>
                                <li><strong>Email</strong> - Member's username or email (optional)
                                    <small class="d-block text-muted">If only username provided, @barakah.local will be appended</small>
                                </li>
                                <li><strong>Phone</strong> - Member's phone number (optional)</li>
                                <li><strong>Number of Shares</strong> - Number of shares to assign (numeric value)</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex gap-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-upload me-2"></i>Import Members
                            </button>
                            <a href="{{ route('members.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Template Column -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">📋 Import Template</h5>
                </div>
                <div class="card-body">
                    <p class="text-body-secondary mb-3">Download the template to get started with the correct format:</p>

                    <a href="{{ route('members.template') }}" class="btn btn-outline-primary w-100 mb-3">
                        <i class="fa-solid fa-download me-2"></i>Download Template
                    </a>

                    <hr>

                    <h6 class="fw-semibold mb-2">What's Included:</h6>
                    <ul class="small list-unstyled">
                        <li>✓ Column headers with correct names</li>
                        <li>✓ Example data rows</li>
                        <li>✓ Format instructions</li>
                        <li>✓ Data validation guidelines</li>
                    </ul>

                    <hr>

                    <h6 class="fw-semibold mb-2">Tips:</h6>
                    <ul class="small list-unstyled">
                        <li>📌 Name field is required</li>
                        <li>🔄 Duplicate members will be skipped</li>
                        <li>📊 Shares will be auto-assigned</li>
                        <li>✉️ Email & phone are optional</li>
                        <li>👤 Username converts to username@barakah.local</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
