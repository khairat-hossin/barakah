@extends('layouts.phoenix')

@section('title', 'Bulk Import Deposits | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('deposits.index') }}">Deposits</a></li>
        <li class="breadcrumb-item active">Bulk Import</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row mb-4 gx-6 gy-3 align-items-center">
        <div class="col-auto">
            <h2 class="mb-0">Bulk Import Deposits</h2>
        </div>
    </div>

    @if (session('import_summary'))
        @php($s = session('import_summary'))
        <div class="alert {{ $s['dry_run'] ? 'alert-info' : 'alert-success' }} alert-dismissible fade show" role="alert">
            <h5 class="alert-heading mb-2">
                {{ $s['dry_run'] ? 'Preview (dry run)' : 'Import complete' }}
            </h5>
            <p class="mb-2">{{ $s['message'] }}</p>
            <ul class="mb-0">
                <li><strong>{{ $s['created'] }}</strong> deposit(s) {{ $s['dry_run'] ? 'would be created' : 'created' }} &mdash; total <strong>Tk {{ number_format($s['total_amount'], 0) }}</strong></li>
                <li>{{ $s['matched_members'] }} member column(s) matched by code</li>
                <li>{{ $s['skipped_existing'] }} skipped (member already has a deposit that month)</li>
                <li>{{ $s['skipped_empty'] }} blank / zero cell(s) skipped</li>
                @if ($s['skipped_unknown_code'] > 0)
                    <li class="text-danger">{{ $s['skipped_unknown_code'] }} cell(s) skipped under unknown member code(s): {{ implode(', ', $s['unknown_codes']) }}</li>
                @endif
            </ul>
            @if (! empty($s['warnings']))
                <hr>
                <ul class="mb-0 small">
                    @foreach ($s['warnings'] as $w)
                        <li>{{ $w }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Upload Deposit Matrix</h5>
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

                    <form action="{{ route('deposits.bulk-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold">Select Excel File <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls" required />
                                <label class="input-group-text" for="file">Choose File</label>
                            </div>
                            <small class="text-body-secondary d-block mt-2">Supported formats: .xlsx, .xls (Max 5MB)</small>
                        </div>

                        <div class="mb-4">
                            <label for="start_year" class="form-label fw-semibold">First row's year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="start_year" name="start_year" value="{{ old('start_year', 2022) }}" min="2000" max="2100" required />
                            <small class="text-body-secondary d-block mt-2">The calendar year of the first month row. The year rolls forward automatically each time the month wraps (e.g. December → January).</small>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="dry_run" name="dry_run" value="1" {{ old('dry_run', '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="dry_run">Dry run (preview only — write nothing)</label>
                            <small class="text-body-secondary d-block">Leave this on to preview the counts first. Turn it off to actually import.</small>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <strong>ℹ️ Expected layout (matrix):</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Row 1</strong> — member codes from column B onward (e.g. M0001, M0002, …)</li>
                                <li><strong>Column A</strong> — month names, one per row, contiguous (e.g. December, January, …)</li>
                                <li><strong>Each cell</strong> — the deposit amount for that member in that month</li>
                            </ul>
                            <hr class="my-2">
                            <ul class="mb-0 small">
                                <li>Blank or zero cells are skipped (no deposit that month).</li>
                                <li>Members are matched by code; cells under an unknown code are reported, not imported.</li>
                                <li>Re-running is safe — a member+month that already has a deposit is skipped (no duplicates).</li>
                                <li>Imported deposits use <strong>Bank Transfer</strong> and post to accounting like normal deposits.</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex gap-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-upload me-2"></i>Process File
                            </button>
                            <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">📋 How it works</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('deposits.bulk-import-template') }}" class="btn btn-outline-primary w-100 mb-3">
                        <i class="fa-solid fa-download me-2"></i>Download Template
                    </a>
                    <p class="text-body-secondary small">Pre-filled with your members' codes (hover a code for the name) and example months — just type the amounts.</p>
                    <hr>
                    <ul class="small list-unstyled mb-0">
                        <li class="mb-2">1️⃣ Upload the matrix file (months × member codes).</li>
                        <li class="mb-2">2️⃣ Keep <strong>Dry run</strong> on and click Process to preview the counts.</li>
                        <li class="mb-2">3️⃣ Review the summary — matched members, deposits to create, total, any unknown codes.</li>
                        <li class="mb-2">4️⃣ Turn <strong>Dry run</strong> off and Process again to commit.</li>
                    </ul>
                    <hr>
                    <h6 class="fw-semibold mb-2">Notes:</h6>
                    <ul class="small list-unstyled mb-0">
                        <li>🔁 Idempotent — safe to re-run.</li>
                        <li>🏦 Method = Bank Transfer.</li>
                        <li>📒 Posts journal vouchers to accounting.</li>
                        <li>🆔 Transaction id = IMP-YYYYMM-CODE.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
