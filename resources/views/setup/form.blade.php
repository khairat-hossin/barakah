@extends('layouts.phoenix')

@section('title', 'Organization Setup - Barakah')

@section('content')
<div class="mb-9">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h2 class="mb-0">🏢 Organization Setup</h2>
            <p class="text-body-secondary mt-2">Configure your organization details to get started with the system</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab" aria-selected="true">
                <span class="fas fa-building me-2"></span>Basic Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-selected="false">
                <span class="fas fa-phone me-2"></span>Contact
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-selected="false">
                <span class="fas fa-map-marker me-2"></span>Address
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="mission-tab" data-bs-toggle="tab" data-bs-target="#mission" type="button" role="tab" aria-selected="false">
                <span class="fas fa-target me-2"></span>Vision & Mission
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="shares-tab" data-bs-toggle="tab" data-bs-target="#shares" type="button" role="tab" aria-selected="false">
                <span class="fas fa-share-alt me-2"></span>Share Structure
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab" aria-selected="false">
                <span class="fas fa-money-bill me-2"></span>Financial
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Basic Information Tab -->
        <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('setup.store') }}" id="setupForm">
                        @csrf

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

                        <div class="mt-4">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('contact-tab').click()">
                                Next <span class="fas fa-arrow-right ms-2"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Information Tab -->
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
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

                    <div class="mt-4 d-flex gap-2">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('basic-tab').click()">
                            <span class="fas fa-arrow-left me-2"></span> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('address-tab').click()">
                            Next <span class="fas fa-arrow-right ms-2"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Tab -->
        <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Address Information</h5>
                </div>
                <div class="card-body">
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

                    <div class="mt-4 d-flex gap-2">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('contact-tab').click()">
                            <span class="fas fa-arrow-left me-2"></span> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('mission-tab').click()">
                            Next <span class="fas fa-arrow-right ms-2"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vision & Mission Tab -->
        <div class="tab-pane fade" id="mission" role="tabpanel" aria-labelledby="mission-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Vision & Mission Statement</h5>
                </div>
                <div class="card-body">
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
                                id="vision_statement" name="vision_statement" rows="4"
                                placeholder="Describe your organization's vision">{{ old('vision_statement', $org?->vision_statement) }}</textarea>
                            @error('vision_statement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="mission_statement" class="form-label">Mission Statement</label>
                            <textarea class="form-control @error('mission_statement') is-invalid @enderror"
                                id="mission_statement" name="mission_statement" rows="4"
                                placeholder="Describe your organization's mission">{{ old('mission_statement', $org?->mission_statement) }}</textarea>
                            @error('mission_statement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('address-tab').click()">
                            <span class="fas fa-arrow-left me-2"></span> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('shares-tab').click()">
                            Next <span class="fas fa-arrow-right ms-2"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Share Structure Tab -->
        <div class="tab-pane fade" id="shares" role="tabpanel" aria-labelledby="shares-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Share Structure Configuration</h5>
                </div>
                <div class="card-body">
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

                    <div class="mt-4 d-flex gap-2">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('mission-tab').click()">
                            <span class="fas fa-arrow-left me-2"></span> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('financial-tab').click()">
                            Next <span class="fas fa-arrow-right ms-2"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Configuration Tab -->
        <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Financial Configuration</h5>
                </div>
                <div class="card-body">
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

                    <div class="mt-4 d-flex gap-2">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('shares-tab').click()">
                            <span class="fas fa-arrow-left me-2"></span> Back
                        </button>
                        <button type="submit" form="setupForm" class="btn btn-success">
                            <span class="fas fa-check me-2"></span> {{ $org ? 'Update Organization' : 'Complete Setup' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
