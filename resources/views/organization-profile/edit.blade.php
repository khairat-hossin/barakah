@extends('layouts.phoenix')

@section('title', 'Edit Organization Profile | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('organization-profile.show', $profile) }}">Organization Profile</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h2 class="mb-0">Edit Organization Profile</h2>
            <p class="text-body-secondary mt-2">Update your organization's constitutional and operational settings</p>
        </div>
    </div>

    <form action="{{ route('organization-profile.update', $profile) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-selected="true">
                    <span class="fas fa-info-circle me-2"></span>General
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-selected="false">
                    <span class="fas fa-map-marker me-2"></span>Address
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mission-tab" data-bs-toggle="tab" data-bs-target="#mission" type="button" role="tab" aria-selected="false">
                    <span class="fas fa-target me-2"></span>Mission
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shares-tab" data-bs-toggle="tab" data-bs-target="#shares" type="button" role="tab" aria-selected="false">
                    <span class="fas fa-share me-2"></span>Shares
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="membership-tab" data-bs-toggle="tab" data-bs-target="#membership" type="button" role="tab" aria-selected="false">
                    <span class="fas fa-users me-2"></span>Membership
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="committee-tab" data-bs-toggle="tab" data-bs-target="#committee" type="button" role="tab" aria-selected="false">
                    <span class="fas fa-chair me-2"></span>Committee
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab" aria-selected="false">
                    <span class="fas fa-money-bill me-2"></span>Financial
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="meetings-tab" data-bs-toggle="tab" data-bs-target="#meetings" type="button" role="tab" aria-selected="false">
                    <span class="fas fa-calendar me-2"></span>Meetings
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Section 1: General Information -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>General Information</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="organization_name_bn" class="form-label">Organization Name (Bangla) *</label>
                                <input type="text" class="form-control @error('organization_name_bn') is-invalid @enderror" id="organization_name_bn" name="organization_name_bn" value="{{ old('organization_name_bn', $profile->organization_name_bn) }}" required>
                                @error('organization_name_bn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="organization_name_en" class="form-label">Organization Name (English) *</label>
                                <input type="text" class="form-control @error('organization_name_en') is-invalid @enderror" id="organization_name_en" name="organization_name_en" value="{{ old('organization_name_en', $profile->organization_name_en) }}" required>
                                @error('organization_name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="short_name" class="form-label">Short Name *</label>
                                <input type="text" class="form-control @error('short_name') is-invalid @enderror" id="short_name" name="short_name" value="{{ old('short_name', $profile->short_name) }}" required>
                                @error('short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="registration_number" class="form-label">Registration Number *</label>
                                <input type="text" class="form-control @error('registration_number') is-invalid @enderror" id="registration_number" name="registration_number" value="{{ old('registration_number', $profile->registration_number) }}" required>
                                @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="registration_date" class="form-label">Registration Date *</label>
                                <input type="date" class="form-control @error('registration_date') is-invalid @enderror" id="registration_date" name="registration_date" value="{{ old('registration_date', $profile->registration_date->format('Y-m-d')) }}" required>
                                @error('registration_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="organization_type" class="form-label">Organization Type *</label>
                                <select class="form-select @error('organization_type') is-invalid @enderror" id="organization_type" name="organization_type" required>
                                    <option value="">Select type...</option>
                                    <option value="coop" @selected(old('organization_type', $profile->organization_type) == 'coop')>Cooperative</option>
                                    <option value="ngo" @selected(old('organization_type', $profile->organization_type) == 'ngo')>NGO</option>
                                    <option value="mutual" @selected(old('organization_type', $profile->organization_type) == 'mutual')>Mutual Society</option>
                                    <option value="association" @selected(old('organization_type', $profile->organization_type) == 'association')>Association</option>
                                    <option value="other" @selected(old('organization_type', $profile->organization_type) == 'other')>Other</option>
                                </select>
                                @error('organization_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" @selected(old('status', $profile->status) == 'active')>Active</option>
                                    <option value="inactive" @selected(old('status', $profile->status) == 'inactive')>Inactive</option>
                                    <option value="suspended" @selected(old('status', $profile->status) == 'suspended')>Suspended</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mobile_number" class="form-label">Mobile Number *</label>
                                <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $profile->mobile_number) }}" required>
                                @error('mobile_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $profile->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $profile->website) }}">
                                @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_mobile" class="form-label">Secondary Mobile</label>
                                <input type="tel" class="form-control @error('secondary_mobile') is-invalid @enderror" id="secondary_mobile" name="secondary_mobile" value="{{ old('secondary_mobile', $profile->secondary_mobile) }}">
                                @error('secondary_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary float-end" onclick="document.querySelector('#address-tab').click()">Next: Address →</button>
            </div>

            <!-- Section 2: Address (abbreviated for brevity) -->
            <div class="tab-pane fade" id="address" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>Organization Address</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address_line" class="form-label">Address Line *</label>
                                <textarea class="form-control @error('address_line') is-invalid @enderror" id="address_line" name="address_line" rows="2" required>{{ old('address_line', $profile->address_line) }}</textarea>
                                @error('address_line') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="village_area" class="form-label">Village/Area *</label>
                                <input type="text" class="form-control @error('village_area') is-invalid @enderror" id="village_area" name="village_area" value="{{ old('village_area', $profile->village_area) }}" required>
                                @error('village_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="post_office" class="form-label">Post Office *</label>
                                <input type="text" class="form-control @error('post_office') is-invalid @enderror" id="post_office" name="post_office" value="{{ old('post_office', $profile->post_office) }}" required>
                                @error('post_office') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="union_ward" class="form-label">Union/Ward *</label>
                                <input type="text" class="form-control @error('union_ward') is-invalid @enderror" id="union_ward" name="union_ward" value="{{ old('union_ward', $profile->union_ward) }}" required>
                                @error('union_ward') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="upazila" class="form-label">Upazila *</label>
                                <input type="text" class="form-control @error('upazila') is-invalid @enderror" id="upazila" name="upazila" value="{{ old('upazila', $profile->upazila) }}" required>
                                @error('upazila') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="district" class="form-label">District *</label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" name="district" value="{{ old('district', $profile->district) }}" required>
                                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="division" class="form-label">Division</label>
                                <input type="text" class="form-control @error('division') is-invalid @enderror" id="division" name="division" value="{{ old('division', $profile->division) }}">
                                @error('division') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="postal_code" class="form-label">Postal Code *</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}" required>
                                @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#general-tab').click()">← Back</button>
                <button type="button" class="btn btn-outline-primary float-end" onclick="document.querySelector('#mission-tab').click()">Next →</button>
            </div>

            <!-- Section 3: Mission & Objectives -->
            <div class="tab-pane fade" id="mission" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>Mission & Objectives</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="motto" class="form-label">Motto/Slogan *</label>
                                <input type="text" class="form-control @error('motto') is-invalid @enderror" id="motto" name="motto" value="{{ old('motto', $profile->motto) }}" required>
                                @error('motto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="vision_statement" class="form-label">Vision Statement *</label>
                                <textarea class="form-control @error('vision_statement') is-invalid @enderror" id="vision_statement" name="vision_statement" rows="3" required>{{ old('vision_statement', $profile->vision_statement) }}</textarea>
                                @error('vision_statement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="mission_statement" class="form-label">Mission Statement *</label>
                                <textarea class="form-control @error('mission_statement') is-invalid @enderror" id="mission_statement" name="mission_statement" rows="3" required>{{ old('mission_statement', $profile->mission_statement) }}</textarea>
                                @error('mission_statement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="about_organization" class="form-label">About Organization</label>
                                <textarea class="form-control @error('about_organization') is-invalid @enderror" id="about_organization" name="about_organization" rows="3">{{ old('about_organization', $profile->about_organization) }}</textarea>
                                @error('about_organization') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#address-tab').click()">← Back</button>
                <button type="button" class="btn btn-outline-primary float-end" onclick="document.querySelector('#shares-tab').click()">Next →</button>
            </div>

            <!-- Section 4: Share Structure -->
            <div class="tab-pane fade" id="shares" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>Share Structure</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="total_shares" class="form-label">Total Shares *</label>
                                <input type="number" class="form-control @error('total_shares') is-invalid @enderror" id="total_shares" name="total_shares" value="{{ old('total_shares', $profile->total_shares) }}" required>
                                @error('total_shares') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="share_face_value" class="form-label">Share Face Value (BDT) *</label>
                                <input type="number" step="0.01" class="form-control @error('share_face_value') is-invalid @enderror" id="share_face_value" name="share_face_value" value="{{ old('share_face_value', $profile->share_face_value) }}" required>
                                @error('share_face_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="share_ownership_model" class="form-label">Ownership Model *</label>
                                <select class="form-select @error('share_ownership_model') is-invalid @enderror" id="share_ownership_model" name="share_ownership_model" required>
                                    <option value="individual" @selected(old('share_ownership_model', $profile->share_ownership_model) == 'individual')>Individual</option>
                                    <option value="collective" @selected(old('share_ownership_model', $profile->share_ownership_model) == 'collective')>Collective</option>
                                    <option value="hybrid" @selected(old('share_ownership_model', $profile->share_ownership_model) == 'hybrid')>Hybrid</option>
                                </select>
                                @error('share_ownership_model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_shares_per_member" class="form-label">Minimum Shares Per Member *</label>
                                <input type="number" class="form-control @error('minimum_shares_per_member') is-invalid @enderror" id="minimum_shares_per_member" name="minimum_shares_per_member" value="{{ old('minimum_shares_per_member', $profile->minimum_shares_per_member) }}" required>
                                @error('minimum_shares_per_member') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="maximum_shares_per_member" class="form-label">Maximum Shares Per Member</label>
                                <input type="number" class="form-control @error('maximum_shares_per_member') is-invalid @enderror" id="maximum_shares_per_member" name="maximum_shares_per_member" value="{{ old('maximum_shares_per_member', $profile->maximum_shares_per_member) }}">
                                @error('maximum_shares_per_member') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="share_transfer_allowed" name="share_transfer_allowed" value="1" @checked(old('share_transfer_allowed', $profile->share_transfer_allowed))>
                                    <label class="form-check-label" for="share_transfer_allowed">Allow Share Transfers</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#mission-tab').click()">← Back</button>
                <button type="button" class="btn btn-outline-primary float-end" onclick="document.querySelector('#membership-tab').click()">Next →</button>
            </div>

            <!-- Remaining sections (abbreviated) -->
            <div class="tab-pane fade" id="membership" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>Membership Rules</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="membership_type" class="form-label">Membership Type *</label>
                                <select class="form-select" id="membership_type" name="membership_type" required>
                                    <option value="share_based" @selected(old('membership_type', $profile->membership_type) == 'share_based')>Share-Based</option>
                                    <option value="open" @selected(old('membership_type', $profile->membership_type) == 'open')>Open</option>
                                    <option value="invitation_only" @selected(old('membership_type', $profile->membership_type) == 'invitation_only')>Invitation Only</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_share_requirement" class="form-label">Minimum Share Requirement *</label>
                                <input type="number" class="form-control" id="minimum_share_requirement" name="minimum_share_requirement" value="{{ old('minimum_share_requirement', $profile->minimum_share_requirement) }}" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="new_member_admission_allowed" name="new_member_admission_allowed" value="1" @checked(old('new_member_admission_allowed', $profile->new_member_admission_allowed))>
                                    <label class="form-check-label" for="new_member_admission_allowed">New Member Admission Allowed</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#shares-tab').click()">← Back</button>
                <button type="button" class="btn btn-outline-primary float-end" onclick="document.querySelector('#committee-tab').click()">Next →</button>
            </div>

            <div class="tab-pane fade" id="committee" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>Committee Structure</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="committee_term_length" class="form-label">Committee Term Length (Years) *</label>
                                <input type="number" class="form-control" id="committee_term_length" name="committee_term_length" value="{{ old('committee_term_length', $profile->committee_term_length) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="maximum_consecutive_terms" class="form-label">Maximum Consecutive Terms *</label>
                                <input type="number" class="form-control" id="maximum_consecutive_terms" name="maximum_consecutive_terms" value="{{ old('maximum_consecutive_terms', $profile->maximum_consecutive_terms) }}" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="election_required" name="election_required" value="1" @checked(old('election_required', $profile->election_required))>
                                    <label class="form-check-label" for="election_required">Election Required</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#membership-tab').click()">← Back</button>
                <button type="button" class="btn btn-outline-primary float-end" onclick="document.querySelector('#financial-tab').click()">Next →</button>
            </div>

            <div class="tab-pane fade" id="financial" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>Financial Configuration</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="default_currency" class="form-label">Default Currency *</label>
                                <input type="text" class="form-control" id="default_currency" name="default_currency" value="{{ old('default_currency', $profile->default_currency) }}" maxlength="3" required>
                            </div>
                            <div class="col-md-6">
                                <label for="reserve_fund_percentage" class="form-label">Reserve Fund Percentage *</label>
                                <input type="number" step="0.01" class="form-control" id="reserve_fund_percentage" name="reserve_fund_percentage" value="{{ old('reserve_fund_percentage', $profile->reserve_fund_percentage) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number', $profile->account_number) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#committee-tab').click()">← Back</button>
                <button type="button" class="btn btn-outline-primary float-end" onclick="document.querySelector('#meetings-tab').click()">Next →</button>
            </div>

            <div class="tab-pane fade" id="meetings" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header"><h5>Meeting Rules</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="general_meeting_notice_days" class="form-label">General Meeting Notice Period (Days) *</label>
                                <input type="number" class="form-control" id="general_meeting_notice_days" name="general_meeting_notice_days" value="{{ old('general_meeting_notice_days', $profile->general_meeting_notice_days) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="general_meeting_quorum_percentage" class="form-label">General Meeting Quorum (%) *</label>
                                <input type="number" class="form-control" id="general_meeting_quorum_percentage" name="general_meeting_quorum_percentage" value="{{ old('general_meeting_quorum_percentage', $profile->general_meeting_quorum_percentage) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="committee_meeting_notice_days" class="form-label">Committee Meeting Notice Period (Days) *</label>
                                <input type="number" class="form-control" id="committee_meeting_notice_days" name="committee_meeting_notice_days" value="{{ old('committee_meeting_notice_days', $profile->committee_meeting_notice_days) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_committee_meetings_per_year" class="form-label">Minimum Committee Meetings Per Year *</label>
                                <input type="number" class="form-control" id="minimum_committee_meetings_per_year" name="minimum_committee_meetings_per_year" value="{{ old('minimum_committee_meetings_per_year', $profile->minimum_committee_meetings_per_year) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#financial-tab').click()">← Back</button>
                <button type="submit" class="btn btn-success float-end">✓ Save Changes</button>
            </div>
        </div>
    </form>
</div>
@endsection
