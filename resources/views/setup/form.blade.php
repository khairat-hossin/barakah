<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Setup - Barakah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .setup-container {
            width: 100%;
            max-width: 800px;
            padding: 20px;
        }

        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .setup-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .setup-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .setup-body {
            padding: 40px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .setup-header h1 {
                font-size: 2rem;
            }

            .row-2col {
                grid-template-columns: 1fr;
            }

            .setup-body {
                padding: 20px;
            }
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-text {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <h1>🏢 Organization Setup</h1>
                <p>Configure your organization details to get started</p>
            </div>

            <div class="setup-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('setup.store') }}" method="POST">
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">Basic Information</div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label required">Organization Name (English)</label>
                                <input type="text" class="form-control @error('organization_name_en') is-invalid @enderror"
                                    name="organization_name_en" value="{{ old('organization_name_en', $org?->organization_name_en) }}" required>
                                @error('organization_name_en')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Organization Name (Bangla)</label>
                                <input type="text" class="form-control @error('organization_name_bn') is-invalid @enderror"
                                    name="organization_name_bn" value="{{ old('organization_name_bn', $org?->organization_name_bn) }}">
                                @error('organization_name_bn')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label">Short Name</label>
                                <input type="text" class="form-control @error('short_name') is-invalid @enderror"
                                    name="short_name" value="{{ old('short_name', $org?->short_name) }}">
                                @error('short_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Organization Type</label>
                                <select class="form-select @error('organization_type') is-invalid @enderror" name="organization_type" required>
                                    <option value="">Select Type</option>
                                    @foreach ($organizationTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('organization_type', $org?->organization_type) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organization_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <div class="form-section-title">Contact Information</div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label required">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $org?->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Mobile Number</label>
                                <input type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                                    name="mobile_number" value="{{ old('mobile_number', $org?->mobile_number) }}" required>
                                @error('mobile_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Secondary Mobile</label>
                            <input type="text" class="form-control @error('secondary_mobile') is-invalid @enderror"
                                name="secondary_mobile" value="{{ old('secondary_mobile', $org?->secondary_mobile) }}">
                            @error('secondary_mobile')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="form-section">
                        <div class="form-section-title">Address</div>

                        <div class="form-group">
                            <label class="form-label">Address Line</label>
                            <input type="text" class="form-control @error('address_line') is-invalid @enderror"
                                name="address_line" value="{{ old('address_line', $org?->address_line) }}">
                            @error('address_line')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label">Village/Area</label>
                                <input type="text" class="form-control @error('village_area') is-invalid @enderror"
                                    name="village_area" value="{{ old('village_area', $org?->village_area) }}">
                                @error('village_area')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Post Office</label>
                                <input type="text" class="form-control @error('post_office') is-invalid @enderror"
                                    name="post_office" value="{{ old('post_office', $org?->post_office) }}">
                                @error('post_office')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label">Union/Ward</label>
                                <input type="text" class="form-control @error('union_ward') is-invalid @enderror"
                                    name="union_ward" value="{{ old('union_ward', $org?->union_ward) }}">
                                @error('union_ward')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Upazila</label>
                                <input type="text" class="form-control @error('upazila') is-invalid @enderror"
                                    name="upazila" value="{{ old('upazila', $org?->upazila) }}">
                                @error('upazila')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label">District</label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror"
                                    name="district" value="{{ old('district', $org?->district) }}">
                                @error('district')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Division</label>
                                <input type="text" class="form-control @error('division') is-invalid @enderror"
                                    name="division" value="{{ old('division', $org?->division) }}">
                                @error('division')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                name="postal_code" value="{{ old('postal_code', $org?->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Vision & Mission Section -->
                    <div class="form-section">
                        <div class="form-section-title">Vision & Mission</div>

                        <div class="form-group">
                            <label class="form-label">Motto</label>
                            <input type="text" class="form-control @error('motto') is-invalid @enderror"
                                name="motto" value="{{ old('motto', $org?->motto) }}">
                            @error('motto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Vision Statement</label>
                            <textarea class="form-control @error('vision_statement') is-invalid @enderror"
                                name="vision_statement" rows="3">{{ old('vision_statement', $org?->vision_statement) }}</textarea>
                            @error('vision_statement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mission Statement</label>
                            <textarea class="form-control @error('mission_statement') is-invalid @enderror"
                                name="mission_statement" rows="3">{{ old('mission_statement', $org?->mission_statement) }}</textarea>
                            @error('mission_statement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Share Structure Section -->
                    <div class="form-section">
                        <div class="form-section-title">Share Structure</div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label required">Share Face Value</label>
                                <input type="number" step="0.01" class="form-control @error('share_face_value') is-invalid @enderror"
                                    name="share_face_value" value="{{ old('share_face_value', $org?->share_face_value ?? 0) }}" required>
                                <div class="form-text">Amount per share in BDT</div>
                                @error('share_face_value')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Total Shares</label>
                                <input type="number" class="form-control @error('total_shares') is-invalid @enderror"
                                    name="total_shares" value="{{ old('total_shares', $org?->total_shares ?? 0) }}" required>
                                @error('total_shares')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Financial Configuration Section -->
                    <div class="form-section">
                        <div class="form-section-title">Financial Configuration</div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label">Membership Fee</label>
                                <input type="number" step="0.01" class="form-control @error('membership_fee') is-invalid @enderror"
                                    name="membership_fee" value="{{ old('membership_fee', $org?->membership_fee) }}">
                                @error('membership_fee')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    name="bank_name" value="{{ old('bank_name', $org?->bank_name) }}">
                                @error('bank_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row-2col">
                            <div class="form-group">
                                <label class="form-label">Account Name</label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                    name="account_name" value="{{ old('account_name', $org?->account_name) }}">
                                @error('account_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                    name="account_number" value="{{ old('account_number', $org?->account_number) }}">
                                @error('account_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        {{ $org ? '✓ Update Organization' : '→ Complete Setup' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
