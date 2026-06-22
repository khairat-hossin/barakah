<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Organization Setup - {{ \App\Support\Branding::name() }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            max-width: 650px;
            width: 100%;
        }
        .setup-header {
            padding: 24px 40px 18px;
            border-bottom: 1px solid #e9ecef;
        }
        .setup-title {
            font-size: 26px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 4px;
        }
        .setup-subtitle {
            font-size: 15px;
            color: #6c757d;
            margin-bottom: 16px;
        }
        .setup-body {
            padding: 24px 40px 28px;
        }
        .setup-progress {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }
        .progress-step {
            flex: 1;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .progress-step.active {
            background: #0d6efd;
        }
        .progress-step.completed {
            background: #198754;
        }
        .step-counter {
            font-size: 13px;
            color: #6c757d;
        }
        .step-pane {
            display: none;
        }
        .step-pane.active {
            display: block;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: #e7f1ff;
            color: #0d6efd;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .step-title {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 4px;
        }
        .step-subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 18px;
        }
        .step-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            justify-content: space-between;
        }
        .btn {
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            border-radius: 8px;
        }
        /* Fallback CSS - ensure inputs display even if Bootstrap fails to load */
        .form-control,
        .form-select,
        input,
        textarea,
        select {
            display: block !important;
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            color: #212529 !important;
            background-color: #fff !important;
            background-clip: padding-box !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.375rem !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            box-sizing: border-box !important;
            visibility: visible !important;
            opacity: 1 !important;
            height: auto !important;
            min-height: 2.5rem !important;
        }

        textarea {
            resize: vertical !important;
            min-height: 5rem !important;
        }

        .form-label {
            display: block !important;
            margin-bottom: 0.5rem !important;
            font-weight: 600 !important;
            color: #212529 !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .row {
            display: flex !important;
            flex-wrap: wrap !important;
            margin-right: -0.5rem !important;
            margin-left: -0.5rem !important;
        }

        .col-md-6 {
            flex: 0 0 50% !important;
            max-width: 50% !important;
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
        }

        @media (max-width: 576px) {
            .col-md-6 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
            .setup-header, .setup-body {
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
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="setup-card">
        <!-- Header -->
        <div class="setup-header">
            <img src="{{ \App\Support\Branding::url('logo-name.png') }}" alt="Logo" style="height: 44px; margin-bottom: 16px;">
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
            <div class="step-counter">Step <span id="current-step">1</span> of 6</div>
        </div>

        <!-- Body -->
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
                    <div class="step-badge">1</div>
                    <div class="step-title">Basic Information</div>
                    <div class="step-subtitle">Tell us about your organization</div>

                    <div class="mb-3">
                        <label class="form-label">Organization Name (English) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="organization_name_en" value="{{ old('organization_name_en', $org?->organization_name_en) }}" required>
                        @error('organization_name_en')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Organization Name (Bangla)</label>
                        <input type="text" class="form-control" name="organization_name_bn" value="{{ old('organization_name_bn', $org?->organization_name_bn) }}">
                        @error('organization_name_bn')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Short Name</label>
                                <input type="text" class="form-control" name="short_name" value="{{ old('short_name', $org?->short_name) }}">
                                @error('short_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Organization Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="organization_type" required>
                                    <option value="">Select Type</option>
                                    <option value="coop" {{ old('organization_type', $org?->organization_type) == 'coop' ? 'selected' : '' }}>Cooperative</option>
                                    <option value="ngo" {{ old('organization_type', $org?->organization_type) == 'ngo' ? 'selected' : '' }}>NGO</option>
                                    <option value="mutual" {{ old('organization_type', $org?->organization_type) == 'mutual' ? 'selected' : '' }}>Mutual Organization</option>
                                    <option value="association" {{ old('organization_type', $org?->organization_type) == 'association' ? 'selected' : '' }}>Association</option>
                                    <option value="other" {{ old('organization_type', $org?->organization_type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('organization_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn btn-primary" onclick="goToStep(2)">Next →</button>
                    </div>
                </div>

                <!-- Step 2: Contact Information -->
                <div class="step-pane" data-step="2">
                    <div class="step-badge">2</div>
                    <div class="step-title">Contact Information</div>
                    <div class="step-subtitle">How can we reach your organization?</div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $org?->email) }}" required>
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile_number" value="{{ old('mobile_number', $org?->mobile_number) }}" required>
                        @error('mobile_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Secondary Mobile</label>
                        <input type="text" class="form-control" name="secondary_mobile" value="{{ old('secondary_mobile', $org?->secondary_mobile) }}">
                        @error('secondary_mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(1)">← Back</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(3)">Next →</button>
                    </div>
                </div>

                <!-- Step 3: Address -->
                <div class="step-pane" data-step="3">
                    <div class="step-badge">3</div>
                    <div class="step-title">Address</div>
                    <div class="step-subtitle">Where is your organization located?</div>

                    <div class="mb-3">
                        <label class="form-label">Address Line</label>
                        <input type="text" class="form-control" name="address_line" value="{{ old('address_line', $org?->address_line) }}">
                        @error('address_line')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Village/Area</label>
                                <input type="text" class="form-control" name="village_area" value="{{ old('village_area', $org?->village_area) }}">
                                @error('village_area')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Post Office</label>
                                <input type="text" class="form-control" name="post_office" value="{{ old('post_office', $org?->post_office) }}">
                                @error('post_office')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Union/Ward</label>
                                <input type="text" class="form-control" name="union_ward" value="{{ old('union_ward', $org?->union_ward) }}">
                                @error('union_ward')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Upazila</label>
                                <input type="text" class="form-control" name="upazila" value="{{ old('upazila', $org?->upazila) }}">
                                @error('upazila')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">District</label>
                                <input type="text" class="form-control" name="district" value="{{ old('district', $org?->district) }}">
                                @error('district')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Division</label>
                                <input type="text" class="form-control" name="division" value="{{ old('division', $org?->division) }}">
                                @error('division')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code', $org?->postal_code) }}">
                        @error('postal_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(2)">← Back</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(4)">Next →</button>
                    </div>
                </div>

                <!-- Step 4: Vision & Mission -->
                <div class="step-pane" data-step="4">
                    <div class="step-badge">4</div>
                    <div class="step-title">Vision & Mission</div>
                    <div class="step-subtitle">What is your organization's purpose?</div>

                    <div class="mb-3">
                        <label class="form-label">Motto</label>
                        <input type="text" class="form-control" name="motto" placeholder="e.g., Together We Prosper" value="{{ old('motto', $org?->motto) }}">
                        @error('motto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vision Statement</label>
                        <textarea class="form-control" name="vision_statement" rows="3" placeholder="Describe your organization's vision">{{ old('vision_statement', $org?->vision_statement) }}</textarea>
                        @error('vision_statement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mission Statement</label>
                        <textarea class="form-control" name="mission_statement" rows="3" placeholder="Describe your organization's mission">{{ old('mission_statement', $org?->mission_statement) }}</textarea>
                        @error('mission_statement')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(3)">← Back</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(5)">Next →</button>
                    </div>
                </div>

                <!-- Step 5: Share Structure -->
                <div class="step-pane" data-step="5">
                    <div class="step-badge">5</div>
                    <div class="step-title">Share Structure</div>
                    <div class="step-subtitle">Configure your organization's shares</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Share Face Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" class="form-control" name="share_face_value" value="{{ old('share_face_value', $org?->share_face_value ?? 0) }}" required>
                                </div>
                                <small class="text-muted d-block mt-1">Amount per share in BDT</small>
                                @error('share_face_value')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Shares <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="total_shares" value="{{ old('total_shares', $org?->total_shares ?? 0) }}" required>
                                @error('total_shares')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(4)">← Back</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(6)">Next →</button>
                    </div>
                </div>

                <!-- Step 6: Financial Configuration -->
                <div class="step-pane" data-step="6">
                    <div class="step-badge">6</div>
                    <div class="step-title">Financial Setup</div>
                    <div class="step-subtitle">Complete your organization setup</div>

                    <div class="mb-3">
                        <label class="form-label">Membership Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" step="0.01" class="form-control" name="membership_fee" value="{{ old('membership_fee', $org?->membership_fee) }}">
                        </div>
                        @error('membership_fee')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name', $org?->bank_name) }}">
                                @error('bank_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Account Name</label>
                                <input type="text" class="form-control" name="account_name" value="{{ old('account_name', $org?->account_name) }}">
                                @error('account_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" class="form-control" name="account_number" value="{{ old('account_number', $org?->account_number) }}">
                        @error('account_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(5)">← Back</button>
                        <button type="submit" class="btn btn-success">✓ Complete Setup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function goToStep(step) {
            document.querySelectorAll('.step-pane').forEach(pane => pane.classList.remove('active'));
            document.querySelector(`.step-pane[data-step="${step}"]`).classList.add('active');

            document.querySelectorAll('.progress-step').forEach((el, index) => {
                const stepNum = index + 1;
                el.classList.remove('active', 'completed');
                if (stepNum < step) el.classList.add('completed');
                else if (stepNum === step) el.classList.add('active');
            });

            document.getElementById('current-step').textContent = step;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        goToStep(1);
    </script>
</body>
</html>
