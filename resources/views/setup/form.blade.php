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
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .setup-container {
            width: 100%;
            max-width: 700px;
        }

        .setup-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .setup-header {
            padding: 40px 40px 30px;
            border-bottom: 1px solid #f1f3f5;
        }

        .setup-title {
            font-size: 28px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 8px;
        }

        .setup-subtitle {
            font-size: 15px;
            color: #6c757d;
            margin: 0;
        }

        .setup-progress {
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-step {
            flex: 1;
            height: 4px;
            background-color: #e9ecef;
            border-radius: 2px;
            transition: background-color 0.3s ease;
        }

        .progress-step.active {
            background-color: #0d6efd;
        }

        .progress-step.completed {
            background-color: #198754;
        }

        .step-info {
            font-size: 13px;
            color: #6c757d;
            margin-top: 12px;
        }

        .setup-body {
            padding: 40px;
        }

        .step-pane {
            display: none;
        }

        .step-pane.active {
            display: block;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background-color: #e7f1ff;
            color: #0d6efd;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .step-title {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 24px;
        }

        .step-description {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 24px;
        }

        .form-group-wrapper {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #212529;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        .form-label .text-danger {
            margin-left: 4px;
        }

        .form-control,
        .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            font-size: 13px;
            color: #dc3545;
            margin-top: 6px;
        }

        .input-group .input-group-text {
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            font-size: 14px;
            border-radius: 8px 0 0 8px;
        }

        .input-group .form-control {
            border-radius: 0 8px 8px 0;
            border-left: none;
        }

        .form-helpers {
            font-size: 13px;
            color: #6c757d;
            margin-top: 6px;
        }

        .step-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            justify-content: space-between;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: #0d6efd;
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background-color: #0b5ed7;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover:not(:disabled) {
            background-color: #5c636a;
        }

        .btn-success {
            background-color: #198754;
            color: white;
        }

        .btn-success:hover:not(:disabled) {
            background-color: #157347;
            transform: translateY(-1px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 8px;
            border: 1px solid;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #842029;
        }

        .alert ul {
            margin: 8px 0 0 20px;
        }

        .alert li {
            margin-bottom: 4px;
        }

        @media (max-width: 576px) {
            .setup-header,
            .setup-body {
                padding: 24px;
            }

            .setup-title {
                font-size: 22px;
            }

            .step-title {
                font-size: 18px;
            }

            .step-actions {
                flex-direction: column-reverse;
            }

            .btn {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <div class="setup-title">Organization Setup</div>
                <div class="setup-subtitle">Configure your organization to get started</div>

                <div class="setup-progress">
                    <div class="progress-step active" data-step="1"></div>
                    <div class="progress-step" data-step="2"></div>
                    <div class="progress-step" data-step="3"></div>
                    <div class="progress-step" data-step="4"></div>
                    <div class="progress-step" data-step="5"></div>
                    <div class="progress-step" data-step="6"></div>
                </div>
                <div class="step-info">Step <span id="current-step">1</span> of 6</div>
            </div>

            <div class="setup-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('setup.store') }}" id="setupForm">
                    @csrf

                    <!-- Step 1: Basic Information -->
                    <div class="step-pane active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-title">Basic Information</div>
                        <div class="step-description">Tell us about your organization</div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Organization Name (English) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('organization_name_en') is-invalid @enderror"
                                name="organization_name_en" value="{{ old('organization_name_en', $org?->organization_name_en) }}" required>
                            @error('organization_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Organization Name (Bangla)</label>
                            <input type="text" class="form-control @error('organization_name_bn') is-invalid @enderror"
                                name="organization_name_bn" value="{{ old('organization_name_bn', $org?->organization_name_bn) }}">
                            @error('organization_name_bn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Short Name</label>
                                    <input type="text" class="form-control @error('short_name') is-invalid @enderror"
                                        name="short_name" value="{{ old('short_name', $org?->short_name) }}">
                                    @error('short_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Organization Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('organization_type') is-invalid @enderror"
                                        name="organization_type" required>
                                        <option value="">Select Type</option>
                                        <option value="coop" {{ old('organization_type', $org?->organization_type) == 'coop' ? 'selected' : '' }}>Cooperative</option>
                                        <option value="ngo" {{ old('organization_type', $org?->organization_type) == 'ngo' ? 'selected' : '' }}>NGO</option>
                                        <option value="mutual" {{ old('organization_type', $org?->organization_type) == 'mutual' ? 'selected' : '' }}>Mutual Organization</option>
                                        <option value="association" {{ old('organization_type', $org?->organization_type) == 'association' ? 'selected' : '' }}>Association</option>
                                        <option value="other" {{ old('organization_type', $org?->organization_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('organization_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-primary" onclick="goToStep(2)">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Contact Information -->
                    <div class="step-pane" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-title">Contact Information</div>
                        <div class="step-description">How can we reach your organization?</div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email', $org?->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                                name="mobile_number" value="{{ old('mobile_number', $org?->mobile_number) }}" required>
                            @error('mobile_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Secondary Mobile</label>
                            <input type="text" class="form-control @error('secondary_mobile') is-invalid @enderror"
                                name="secondary_mobile" value="{{ old('secondary_mobile', $org?->secondary_mobile) }}">
                            @error('secondary_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(1)">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="goToStep(3)">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Address -->
                    <div class="step-pane" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-title">Address</div>
                        <div class="step-description">Where is your organization located?</div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Address Line</label>
                            <input type="text" class="form-control @error('address_line') is-invalid @enderror"
                                name="address_line" value="{{ old('address_line', $org?->address_line) }}">
                            @error('address_line')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Village/Area</label>
                                    <input type="text" class="form-control @error('village_area') is-invalid @enderror"
                                        name="village_area" value="{{ old('village_area', $org?->village_area) }}">
                                    @error('village_area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Post Office</label>
                                    <input type="text" class="form-control @error('post_office') is-invalid @enderror"
                                        name="post_office" value="{{ old('post_office', $org?->post_office) }}">
                                    @error('post_office')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Union/Ward</label>
                                    <input type="text" class="form-control @error('union_ward') is-invalid @enderror"
                                        name="union_ward" value="{{ old('union_ward', $org?->union_ward) }}">
                                    @error('union_ward')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Upazila</label>
                                    <input type="text" class="form-control @error('upazila') is-invalid @enderror"
                                        name="upazila" value="{{ old('upazila', $org?->upazila) }}">
                                    @error('upazila')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">District</label>
                                    <input type="text" class="form-control @error('district') is-invalid @enderror"
                                        name="district" value="{{ old('district', $org?->district) }}">
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Division</label>
                                    <input type="text" class="form-control @error('division') is-invalid @enderror"
                                        name="division" value="{{ old('division', $org?->division) }}">
                                    @error('division')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                        name="postal_code" value="{{ old('postal_code', $org?->postal_code) }}">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(2)">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="goToStep(4)">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Vision & Mission -->
                    <div class="step-pane" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-title">Vision & Mission</div>
                        <div class="step-description">What is your organization's purpose?</div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Motto</label>
                            <input type="text" class="form-control @error('motto') is-invalid @enderror"
                                name="motto" placeholder="e.g., Together We Prosper"
                                value="{{ old('motto', $org?->motto) }}">
                            @error('motto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Vision Statement</label>
                            <textarea class="form-control @error('vision_statement') is-invalid @enderror"
                                name="vision_statement" rows="3" placeholder="Describe your organization's vision">{{ old('vision_statement', $org?->vision_statement) }}</textarea>
                            @error('vision_statement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Mission Statement</label>
                            <textarea class="form-control @error('mission_statement') is-invalid @enderror"
                                name="mission_statement" rows="3" placeholder="Describe your organization's mission">{{ old('mission_statement', $org?->mission_statement) }}</textarea>
                            @error('mission_statement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(3)">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="goToStep(5)">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 5: Share Structure -->
                    <div class="step-pane" data-step="5">
                        <div class="step-number">5</div>
                        <div class="step-title">Share Structure</div>
                        <div class="step-description">Configure your organization's shares</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Share Face Value <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" class="form-control @error('share_face_value') is-invalid @enderror"
                                            name="share_face_value" value="{{ old('share_face_value', $org?->share_face_value ?? 0) }}" required>
                                    </div>
                                    <div class="form-helpers">Amount per share in BDT</div>
                                    @error('share_face_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Total Shares <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_shares') is-invalid @enderror"
                                        name="total_shares" value="{{ old('total_shares', $org?->total_shares ?? 0) }}" required>
                                    @error('total_shares')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(4)">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="goToStep(6)">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 6: Financial Configuration -->
                    <div class="step-pane" data-step="6">
                        <div class="step-number">6</div>
                        <div class="step-title">Financial Setup</div>
                        <div class="step-description">Complete your organization setup</div>

                        <div class="form-group-wrapper">
                            <label class="form-label">Membership Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" class="form-control @error('membership_fee') is-invalid @enderror"
                                    name="membership_fee" value="{{ old('membership_fee', $org?->membership_fee) }}">
                            </div>
                            @error('membership_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                        name="bank_name" value="{{ old('bank_name', $org?->bank_name) }}">
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Account Name</label>
                                    <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                        name="account_name" value="{{ old('account_name', $org?->account_name) }}">
                                    @error('account_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group-wrapper">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                        name="account_number" value="{{ old('account_number', $org?->account_number) }}">
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(5)">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Complete Setup
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('phoenix/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/bootstrap/bootstrap.min.js') }}"></script>

    <script>
        const maxSteps = 6;

        function goToStep(step) {
            if (step < 1 || step > maxSteps) return;

            // Hide all steps
            document.querySelectorAll('.step-pane').forEach(pane => {
                pane.classList.remove('active');
            });

            // Show current step
            document.querySelector(`[data-step="${step}"]`).classList.add('active');

            // Update progress bar
            document.querySelectorAll('.progress-step').forEach((el, index) => {
                const stepNum = index + 1;
                if (stepNum < step) {
                    el.classList.add('completed');
                    el.classList.remove('active');
                } else if (stepNum === step) {
                    el.classList.add('active');
                    el.classList.remove('completed');
                } else {
                    el.classList.remove('active', 'completed');
                }
            });

            // Update step counter
            document.getElementById('current-step').textContent = step;

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Initialize
        goToStep(1);
    </script>
</body>
</html>
