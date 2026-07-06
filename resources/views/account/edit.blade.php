@extends('layouts.phoenix')

@section('title', 'Account Settings | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Account Settings</li>
    </ol>
</nav>

<div class="mb-4">
    <h2 class="mb-0">Account Settings</h2>
    <p class="text-body-secondary mt-2">Manage your profile details, password and account security.</p>
</div>

<div class="row g-4">
    {{-- Left: identity summary --}}
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar avatar-4xl mb-3 mx-auto">
                    <div class="avatar-name rounded-circle bg-primary-subtle">
                        <span class="fs-3 text-primary">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                    </div>
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-body-secondary mb-3">{{ $user->email }}</p>

                @foreach($user->getRoleNames() as $role)
                    <span class="badge badge-phoenix badge-phoenix-primary me-1">{{ $role }}</span>
                @endforeach

                <hr class="my-3">
                <div class="text-start small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">Email verified</span>
                        @if($user->email_verified_at)
                            <span class="text-success fw-semibold"><span class="fas fa-circle-check me-1"></span>Verified</span>
                        @else
                            <span class="text-warning fw-semibold"><span class="fas fa-circle-exclamation me-1"></span>Pending</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">Two-factor auth</span>
                        @if($user->two_factor_confirmed_at)
                            <span class="text-success fw-semibold"><span class="fas fa-shield-halved me-1"></span>Enabled</span>
                        @else
                            <span class="text-body-tertiary fw-semibold">Not set up</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-body-secondary">Member since</span>
                        <span class="fw-semibold">{{ $user->created_at?->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: forms --}}
    <div class="col-12 col-lg-8">
        {{-- Profile --}}
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h4 class="mb-0"><span class="fas fa-user text-primary me-2"></span>Profile Information</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('account.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Full Name</label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary"><span class="fas fa-floppy-disk me-2"></span>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Password --}}
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h4 class="mb-0"><span class="fas fa-key text-primary me-2"></span>Change Password</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('account.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                                   class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password">New Password</label>
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary"><span class="fas fa-key me-2"></span>Update Password</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Security --}}
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="mb-0"><span class="fas fa-shield-halved text-primary me-2"></span>Security</h4>
            </div>
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="mb-1">Two-Factor Authentication</h6>
                    <p class="text-body-secondary mb-0 small">Add an extra layer of security to your account with an authenticator app.</p>
                </div>
                <a href="{{ route('tyro-login.two-factor.setup') }}" class="btn btn-phoenix-secondary">
                    <span class="fas fa-shield-halved me-2"></span>{{ $user->two_factor_confirmed_at ? 'Manage 2FA' : 'Enable 2FA' }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
