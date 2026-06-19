<!DOCTYPE html>
<html lang="en" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Organization Setup - Barakah</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('phoenix/assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('phoenix/assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('phoenix/assets/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('phoenix/assets/img/favicons/favicon.ico') }}">
    <meta name="theme-color" content="#ffffff">

    <script src="{{ asset('phoenix/vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('phoenix/assets/js/config.js') }}"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('phoenix/vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('phoenix/assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('phoenix/assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('phoenix/assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('phoenix/assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">

    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .setup-wrapper {
            width: 100%;
            max-width: 900px;
        }

        .setup-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .setup-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .setup-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .setup-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="setup-wrapper">
        <div class="setup-header">
            <h1>🏢 Organization Setup</h1>
            <p>Configure your organization details to get started with the system</p>
        </div>

        <div class="setup-card">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert" style="margin-bottom: 0 !important;">
                    <strong><i class="fas fa-exclamation-circle me-2"></i>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs" role="tablist" style="border-bottom: 1px solid #dee2e6; margin: 0;">
                <li class="nav-item" role="presentation" style="margin: 0;">
                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab" aria-selected="true">
                        <span class="fas fa-building me-2"></span><span class="d-none d-md-inline">Basic Info</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-selected="false">
                        <span class="fas fa-phone me-2"></span><span class="d-none d-md-inline">Contact</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-selected="false">
                        <span class="fas fa-map-marker me-2"></span><span class="d-none d-md-inline">Address</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mission-tab" data-bs-toggle="tab" data-bs-target="#mission" type="button" role="tab" aria-selected="false">
                        <span class="fas fa-target me-2"></span><span class="d-none d-md-inline">Vision & Mission</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shares-tab" data-bs-toggle="tab" data-bs-target="#shares" type="button" role="tab" aria-selected="false">
                        <span class="fas fa-share-alt me-2"></span><span class="d-none d-md-inline">Shares</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab" aria-selected="false">
                        <span class="fas fa-money-bill me-2"></span><span class="d-none d-md-inline">Financial</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" style="padding: 30px;">
                <form method="POST" action="{{ route('setup.store') }}" id="setupForm">
                    @csrf

                    <!-- Basic Information Tab -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                        <h5 class="mb-4">Basic Information</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="organization_name_en" class="form-label">Organization Name (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('organization_name_en') is-invalid @enderror"
                                    id="organization_name_en" name="organization_name_en"
                                    value="{{ old('organization_name_en', $org?->organization_name_en) }}" required>
                                @error('organization_name_en')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="organization_name_bn" class="form-label">Organization Name (Bangla)</label>
                                <input type="text" class="form-control @error('organization_name_bn') is-invalid @enderror"
                                    id="organization_name_bn" name="organization_name_bn"
                                    value="{{ old('organization_name_bn', $org?->organization_name_bn) }}">
                                @error('organization_name_bn')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="short_name" class="form-label">Short Name</label>
                                <input type="text" class="form-control @error('short_name') is-invalid @enderror"
                                    id="short_name" name="short_name"
                                    value="{{ old('short_name', $org?->short_name) }}">
                                @error('short_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="organization_type" class="form-label">Organization Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('organization_type') is-invalid @enderror"
                                    id="organization_type" name="organization_type" required>
                                    <option value="">Select Organization Type</option>
                                    <option value="coop" {{ old('organization_type', $org?->organization_type) == 'coop' ? 'selected' : '' }}>Cooperative</option>
                                    <option value="ngo" {{ old('organization_type', $org?->organization_type) == 'ngo' ? 'selected' : '' }}>NGO</option>
                                    <option value="mutual" {{ old('organization_type', $org?->organization_type) == 'mutual' ? 'selected' : '' }}>Mutual Organization</option>
                                    <option value="association" {{ old('organization_type', $org?->organization_type) == 'association' ? 'selected' : '' }}>Association</option>
                                    <option value="other" {{ old('organization_type', $org?->organization_type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('organization_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('contact-tab').click()">
                                Next <span class="fas fa-arrow-right ms-2"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Contact Information Tab -->
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <h5 class="mb-4">Contact Information</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', $org?->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="mobile_number" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                                    id="mobile_number" name="mobile_number"
                                    value="{{ old('mobile_number', $org?->mobile_number) }}" required>
                                @error('mobile_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="secondary_mobile" class="form-label">Secondary Mobile</label>
                                <input type="text" class="form-control @error('secondary_mobile') is-invalid @enderror"
                                    id="secondary_mobile" name="secondary_mobile"
                                    value="{{ old('secondary_mobile', $org?->secondary_mobile) }}">
                                @error('secondary_mobile')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('basic-tab').click()">
                                <span class="fas fa-arrow-left me-2"></span> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('address-tab').click()">
                                Next <span class="fas fa-arrow-right ms-2"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Address Tab -->
                    <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                        <h5 class="mb-4">Address Information</h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address_line" class="form-label">Address Line</label>
                                <input type="text" class="form-control @error('address_line') is-invalid @enderror"
                                    id="address_line" name="address_line"
                                    value="{{ old('address_line', $org?->address_line) }}">
                                @error('address_line')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="village_area" class="form-label">Village/Area</label>
                                <input type="text" class="form-control @error('village_area') is-invalid @enderror"
                                    id="village_area" name="village_area"
                                    value="{{ old('village_area', $org?->village_area) }}">
                                @error('village_area')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="post_office" class="form-label">Post Office</label>
                                <input type="text" class="form-control @error('post_office') is-invalid @enderror"
                                    id="post_office" name="post_office"
                                    value="{{ old('post_office', $org?->post_office) }}">
                                @error('post_office')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="union_ward" class="form-label">Union/Ward</label>
                                <input type="text" class="form-control @error('union_ward') is-invalid @enderror"
                                    id="union_ward" name="union_ward"
                                    value="{{ old('union_ward', $org?->union_ward) }}">
                                @error('union_ward')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="upazila" class="form-label">Upazila</label>
                                <input type="text" class="form-control @error('upazila') is-invalid @enderror"
                                    id="upazila" name="upazila"
                                    value="{{ old('upazila', $org?->upazila) }}">
                                @error('upazila')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="district" class="form-label">District</label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror"
                                    id="district" name="district"
                                    value="{{ old('district', $org?->district) }}">
                                @error('district')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="division" class="form-label">Division</label>
                                <input type="text" class="form-control @error('division') is-invalid @enderror"
                                    id="division" name="division"
                                    value="{{ old('division', $org?->division) }}">
                                @error('division')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                    id="postal_code" name="postal_code"
                                    value="{{ old('postal_code', $org?->postal_code) }}">
                                @error('postal_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('contact-tab').click()">
                                <span class="fas fa-arrow-left me-2"></span> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('mission-tab').click()">
                                Next <span class="fas fa-arrow-right ms-2"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Vision & Mission Tab -->
                    <div class="tab-pane fade" id="mission" role="tabpanel" aria-labelledby="mission-tab">
                        <h5 class="mb-4">Vision & Mission Statement</h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="motto" class="form-label">Motto</label>
                                <input type="text" class="form-control @error('motto') is-invalid @enderror"
                                    id="motto" name="motto" placeholder="e.g., Together We Prosper"
                                    value="{{ old('motto', $org?->motto) }}">
                                @error('motto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="vision_statement" class="form-label">Vision Statement</label>
                                <textarea class="form-control @error('vision_statement') is-invalid @enderror"
                                    id="vision_statement" name="vision_statement" rows="3"
                                    placeholder="Describe your organization's vision">{{ old('vision_statement', $org?->vision_statement) }}</textarea>
                                @error('vision_statement')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="mission_statement" class="form-label">Mission Statement</label>
                                <textarea class="form-control @error('mission_statement') is-invalid @enderror"
                                    id="mission_statement" name="mission_statement" rows="3"
                                    placeholder="Describe your organization's mission">{{ old('mission_statement', $org?->mission_statement) }}</textarea>
                                @error('mission_statement')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('address-tab').click()">
                                <span class="fas fa-arrow-left me-2"></span> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('shares-tab').click()">
                                Next <span class="fas fa-arrow-right ms-2"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Share Structure Tab -->
                    <div class="tab-pane fade" id="shares" role="tabpanel" aria-labelledby="shares-tab">
                        <h5 class="mb-4">Share Structure Configuration</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="share_face_value" class="form-label">Share Face Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" class="form-control @error('share_face_value') is-invalid @enderror"
                                        id="share_face_value" name="share_face_value"
                                        value="{{ old('share_face_value', $org?->share_face_value ?? 0) }}" required>
                                </div>
                                <small class="text-body-secondary d-block mt-2">Amount per share in BDT</small>
                                @error('share_face_value')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="total_shares" class="form-label">Total Shares <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_shares') is-invalid @enderror"
                                    id="total_shares" name="total_shares"
                                    value="{{ old('total_shares', $org?->total_shares ?? 0) }}" required>
                                @error('total_shares')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('mission-tab').click()">
                                <span class="fas fa-arrow-left me-2"></span> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('financial-tab').click()">
                                Next <span class="fas fa-arrow-right ms-2"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Financial Configuration Tab -->
                    <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                        <h5 class="mb-4">Financial Configuration</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="membership_fee" class="form-label">Membership Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" class="form-control @error('membership_fee') is-invalid @enderror"
                                        id="membership_fee" name="membership_fee"
                                        value="{{ old('membership_fee', $org?->membership_fee) }}">
                                </div>
                                @error('membership_fee')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    id="bank_name" name="bank_name"
                                    value="{{ old('bank_name', $org?->bank_name) }}">
                                @error('bank_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="account_name" class="form-label">Account Name</label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                    id="account_name" name="account_name"
                                    value="{{ old('account_name', $org?->account_name) }}">
                                @error('account_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                    id="account_number" name="account_number"
                                    value="{{ old('account_number', $org?->account_number) }}">
                                @error('account_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('shares-tab').click()">
                                <span class="fas fa-arrow-left me-2"></span> Back
                            </button>
                            <button type="submit" class="btn btn-success">
                                <span class="fas fa-check me-2"></span> {{ $org ? 'Update Organization' : 'Complete Setup' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('phoenix/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/bootstrap/bootstrap.min.js') }}"></script>
</body>
</html>
