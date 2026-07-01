@extends('layouts.phoenix')

@section('title', 'Branding | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('organization-profile.index') }}">Organization Profile</a></li>
        <li class="breadcrumb-item active">Branding</li>
    </ol>
</nav>

<div class="mb-9">
    <h2 class="mb-1">Branding</h2>
    <p class="text-body-secondary mb-4">Change the application logo and the browser favicon. Changes apply across the whole app.</p>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('organization-profile.branding.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Logo -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-body-tertiary"><h5 class="mb-0">Logo</h5></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center bg-body-tertiary rounded mb-3" style="height: 120px;">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Current logo" style="max-height: 90px; max-width: 90%; object-fit: contain;">
                            @else
                                <span class="text-body-tertiary small"><span class="fas fa-image me-1"></span>No logo set</span>
                            @endif
                        </div>
                        <label class="form-label fw-semibold">Upload new logo</label>
                        <input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.webp">
                        <small class="text-body-secondary d-block mt-2">PNG, JPG or WebP · max 8 MB. A square, transparent PNG works best. It's auto-resized and used across the app, reports and PDFs.</small>
                        @if ($hasLogoOverride)
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="reset_logo" value="1" id="reset_logo">
                                <label class="form-check-label" for="reset_logo">Reset logo to the default</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Favicon -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-body-tertiary"><h5 class="mb-0">Favicon</h5></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center bg-body-tertiary rounded mb-3" style="height: 120px;">
                            @if ($faviconUrl)
                                <img src="{{ $faviconUrl }}" alt="Current favicon" style="height: 48px; width: 48px; object-fit: contain;">
                            @else
                                <span class="text-body-tertiary small"><span class="fas fa-image me-1"></span>No favicon set</span>
                            @endif
                        </div>
                        <label class="form-label fw-semibold">Upload new favicon</label>
                        <input type="file" name="favicon" class="form-control" accept=".png,.ico,.svg">
                        <small class="text-body-secondary d-block mt-2">PNG, ICO or SVG · max 1 MB. A square image (e.g. 32×32 or 64×64) is ideal. Browsers may cache the old icon — hard-refresh to see the change.</small>
                        @if ($hasFaviconOverride)
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="reset_favicon" value="1" id="reset_favicon">
                                <label class="form-check-label" for="reset_favicon">Reset favicon to the default</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><span class="fas fa-save me-2"></span>Save Branding</button>
            <a href="{{ route('organization-profile.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
