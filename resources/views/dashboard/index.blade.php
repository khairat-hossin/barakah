@extends('layouts.phoenix')

@section('title', 'Dashboard | Barakah')

@section('content')
    <div class="row gy-3 mb-6 justify-content-between">
        <div class="col-md-9 col-auto">
            <h2 class="mb-2 text-body-emphasis">Association Dashboard</h2>
            <h5 class="text-body-tertiary fw-semibold">Phoenix is now mounted on the authenticated side of the application. Next we bind these panels to real contribution, fund, and project data.</h5>
        </div>
        <div class="col-md-3 col-auto">
            <div class="d-flex justify-content-md-end">
                <a class="btn btn-primary px-5" href="{{ route('projects.index') }}">
                    <span class="fa-solid fa-plus me-2"></span>
                    View projects
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-6">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2 text-body-secondary">Active Members</h6>
                            <h3 class="text-body-emphasis mb-0">0</h3>
                        </div>
                        <div class="icon-item icon-item-sm rounded-7 shadow-none bg-primary-subtle">
                            <span class="fas fa-users text-primary"></span>
                        </div>
                    </div>
                    <p class="mb-0 fs-9 text-body-secondary mt-3">Member registry module is the next domain slice after the shell.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2 text-body-secondary">Monthly Contributions</h6>
                            <h3 class="text-body-emphasis mb-0">$0</h3>
                        </div>
                        <div class="icon-item icon-item-sm rounded-7 shadow-none bg-success-subtle">
                            <span class="fas fa-wallet text-success"></span>
                        </div>
                    </div>
                    <p class="mb-0 fs-9 text-body-secondary mt-3">This card will read from contribution dues and payment records.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2 text-body-secondary">Available Fund Balance</h6>
                            <h3 class="text-body-emphasis mb-0">$0</h3>
                        </div>
                        <div class="icon-item icon-item-sm rounded-7 shadow-none bg-info-subtle">
                            <span class="fas fa-landmark text-info"></span>
                        </div>
                    </div>
                    <p class="mb-0 fs-9 text-body-secondary mt-3">This becomes real once the ledger engine is added.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-2 text-body-secondary">Active Projects</h6>
                            <h3 class="text-body-emphasis mb-0">0</h3>
                        </div>
                        <div class="icon-item icon-item-sm rounded-7 shadow-none bg-warning-subtle">
                            <span class="fas fa-briefcase text-warning"></span>
                        </div>
                    </div>
                    <p class="mb-0 fs-9 text-body-secondary mt-3">The project management template will anchor this module.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header border-bottom border-translucent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Implementation Progress</h4>
                            <p class="text-body-secondary fs-9 mb-0">The first milestone is focused on the shell and navigation boundary.</p>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-primary">Milestone 1</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-2 p-4 h-100">
                                <h5 class="mb-3">Completed now</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex mb-2"><span class="fa-solid fa-check text-success me-2 mt-1"></span><span>Git repository initialized in the Laravel app</span></li>
                                    <li class="d-flex mb-2"><span class="fa-solid fa-check text-success me-2 mt-1"></span><span>`tyro-login` kept as the authentication boundary</span></li>
                                    <li class="d-flex mb-2"><span class="fa-solid fa-check text-success me-2 mt-1"></span><span>Phoenix assets copied into the Laravel public directory</span></li>
                                    <li class="d-flex"><span class="fa-solid fa-check text-success me-2 mt-1"></span><span>Authenticated dashboard route mounted with Phoenix layout</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-2 p-4 h-100">
                                <h5 class="mb-3">Up next</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex mb-2"><span class="fa-solid fa-arrow-right text-primary me-2 mt-1"></span><span>Convert the Phoenix project list view into a real projects index</span></li>
                                    <li class="d-flex mb-2"><span class="fa-solid fa-arrow-right text-primary me-2 mt-1"></span><span>Replace placeholder navigation items with real module routes</span></li>
                                    <li class="d-flex mb-2"><span class="fa-solid fa-arrow-right text-primary me-2 mt-1"></span><span>Add the organization and member domain foundation</span></li>
                                    <li class="d-flex"><span class="fa-solid fa-arrow-right text-primary me-2 mt-1"></span><span>Start binding metrics to actual contribution and ledger data</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header border-bottom border-translucent">
                    <h4 class="mb-0">Auth Boundary</h4>
                </div>
                <div class="card-body">
                    <p class="text-body-secondary">Guest flows remain with `tyro-login`. Authenticated users land in Phoenix after sign-in and registration.</p>
                    <div class="d-grid gap-2">
                        <div class="border rounded-2 p-3">
                            <h6 class="mb-1">Login route</h6>
                            <p class="fs-9 text-body-secondary mb-0">`/login` stays on the Tyro auth views.</p>
                        </div>
                        <div class="border rounded-2 p-3">
                            <h6 class="mb-1">App entry route</h6>
                            <p class="fs-9 text-body-secondary mb-0">`/dashboard` is now the authenticated landing page.</p>
                        </div>
                        <div class="border rounded-2 p-3">
                            <h6 class="mb-1">Next UI conversion</h6>
                            <p class="fs-9 text-body-secondary mb-0">Phoenix `Project List View` should become the first real module screen.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
