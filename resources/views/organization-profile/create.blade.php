@extends('layouts.phoenix')

@section('title', 'Setup Organization Profile | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Organization Profile</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h2 class="mb-0">Setup Organization Profile</h2>
            <p class="text-body-secondary mt-2">Configure your organization's constitutional and operational settings</p>
        </div>
    </div>

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
            <form id="generalForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>General Information</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="organization_name_bn" class="form-label">Organization Name (Bangla) *</label>
                                <input type="text" class="form-control" id="organization_name_bn" name="organization_name_bn" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="organization_name_en" class="form-label">Organization Name (English) *</label>
                                <input type="text" class="form-control" id="organization_name_en" name="organization_name_en" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="short_name" class="form-label">Short Name *</label>
                                <input type="text" class="form-control" id="short_name" name="short_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="registration_number" class="form-label">Registration Number *</label>
                                <input type="text" class="form-control" id="registration_number" name="registration_number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="registration_date" class="form-label">Registration Date *</label>
                                <input type="date" class="form-control" id="registration_date" name="registration_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="organization_type" class="form-label">Organization Type *</label>
                                <select class="form-select" id="organization_type" name="organization_type" required>
                                    <option value="">Select type...</option>
                                    <option value="coop">Cooperative</option>
                                    <option value="ngo">NGO</option>
                                    <option value="mutual">Mutual Society</option>
                                    <option value="association">Association</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="mobile_number" class="form-label">Mobile Number *</label>
                                <input type="tel" class="form-control" id="mobile_number" name="mobile_number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website">
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_mobile" class="form-label">Secondary Mobile</label>
                                <input type="tel" class="form-control" id="secondary_mobile" name="secondary_mobile">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save General Info
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-auto" onclick="document.querySelector('#address-tab').click()">
                        Next: Address →
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 2: Address -->
        <div class="tab-pane fade" id="address" role="tabpanel">
            <form id="addressForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>Organization Address</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address_line" class="form-label">Address Line *</label>
                                <textarea class="form-control" id="address_line" name="address_line" rows="2" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="village_area" class="form-label">Village/Area *</label>
                                <input type="text" class="form-control" id="village_area" name="village_area" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="post_office" class="form-label">Post Office *</label>
                                <input type="text" class="form-control" id="post_office" name="post_office" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="union_ward" class="form-label">Union/Ward *</label>
                                <input type="text" class="form-control" id="union_ward" name="union_ward" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="upazila" class="form-label">Upazila *</label>
                                <input type="text" class="form-control" id="upazila" name="upazila" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="district" class="form-label">District *</label>
                                <input type="text" class="form-control" id="district" name="district" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="division" class="form-label">Division</label>
                                <input type="text" class="form-control" id="division" name="division">
                            </div>
                            <div class="col-md-6">
                                <label for="postal_code" class="form-label">Postal Code *</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#general-tab').click()">
                        ← Back
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save Address
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-auto" onclick="document.querySelector('#mission-tab').click()">
                        Next →
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 3: Mission & Objectives -->
        <div class="tab-pane fade" id="mission" role="tabpanel">
            <form id="missionForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>Mission & Objectives</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="motto" class="form-label">Motto/Slogan *</label>
                                <input type="text" class="form-control" id="motto" name="motto" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label for="vision_statement" class="form-label">Vision Statement *</label>
                                <textarea class="form-control" id="vision_statement" name="vision_statement" rows="3" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label for="mission_statement" class="form-label">Mission Statement *</label>
                                <textarea class="form-control" id="mission_statement" name="mission_statement" rows="3" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label for="about_organization" class="form-label">About Organization</label>
                                <textarea class="form-control" id="about_organization" name="about_organization" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#address-tab').click()">
                        ← Back
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save Mission
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-auto" onclick="document.querySelector('#shares-tab').click()">
                        Next →
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 4: Share Structure -->
        <div class="tab-pane fade" id="shares" role="tabpanel">
            <form id="sharesForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>Share Structure</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="total_shares" class="form-label">Total Shares *</label>
                                <input type="number" class="form-control" id="total_shares" name="total_shares" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="share_face_value" class="form-label">Share Face Value (BDT) *</label>
                                <input type="number" step="0.01" class="form-control" id="share_face_value" name="share_face_value" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="share_ownership_model" class="form-label">Ownership Model *</label>
                                <select class="form-select" id="share_ownership_model" name="share_ownership_model" required>
                                    <option value="individual">Individual</option>
                                    <option value="collective">Collective</option>
                                    <option value="hybrid">Hybrid</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_shares_per_member" class="form-label">Minimum Shares Per Member *</label>
                                <input type="number" class="form-control" id="minimum_shares_per_member" name="minimum_shares_per_member" value="1" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="maximum_shares_per_member" class="form-label">Maximum Shares Per Member</label>
                                <input type="number" class="form-control" id="maximum_shares_per_member" name="maximum_shares_per_member">
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="share_transfer_allowed" name="share_transfer_allowed" value="1" checked>
                                    <label class="form-check-label" for="share_transfer_allowed">Allow Share Transfers</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#mission-tab').click()">
                        ← Back
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save Shares
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-auto" onclick="document.querySelector('#membership-tab').click()">
                        Next →
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 5: Membership Rules -->
        <div class="tab-pane fade" id="membership" role="tabpanel">
            <form id="membershipForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>Membership Rules</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="membership_type" class="form-label">Membership Type *</label>
                                <select class="form-select" id="membership_type" name="membership_type" required>
                                    <option value="share_based">Share-Based</option>
                                    <option value="open">Open</option>
                                    <option value="invitation_only">Invitation Only</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_share_requirement" class="form-label">Minimum Share Requirement *</label>
                                <input type="number" class="form-control" id="minimum_share_requirement" name="minimum_share_requirement" value="0" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="new_member_admission_allowed" name="new_member_admission_allowed" value="1" checked>
                                    <label class="form-check-label" for="new_member_admission_allowed">New Member Admission Allowed</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#shares-tab').click()">
                        ← Back
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save Membership
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-auto" onclick="document.querySelector('#committee-tab').click()">
                        Next →
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 6: Committee Structure -->
        <div class="tab-pane fade" id="committee" role="tabpanel">
            <form id="committeeForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>Committee Structure</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="committee_term_length" class="form-label">Committee Term Length (Years) *</label>
                                <input type="number" class="form-control" id="committee_term_length" name="committee_term_length" value="3" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="maximum_consecutive_terms" class="form-label">Maximum Consecutive Terms *</label>
                                <input type="number" class="form-control" id="maximum_consecutive_terms" name="maximum_consecutive_terms" value="2" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="election_required" name="election_required" value="1" checked>
                                    <label class="form-check-label" for="election_required">Election Required</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#membership-tab').click()">
                        ← Back
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save Committee
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-auto" onclick="document.querySelector('#financial-tab').click()">
                        Next →
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 7: Financial Configuration -->
        <div class="tab-pane fade" id="financial" role="tabpanel">
            <form id="financialForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>Financial Configuration</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="default_currency" class="form-label">Default Currency *</label>
                                <input type="text" class="form-control" id="default_currency" name="default_currency" value="BDT" maxlength="3" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="reserve_fund_percentage" class="form-label">Reserve Fund Percentage *</label>
                                <input type="number" step="0.01" class="form-control" id="reserve_fund_percentage" name="reserve_fund_percentage" value="10" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name">
                            </div>
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#committee-tab').click()">
                        ← Back
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save Financial
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-auto" onclick="document.querySelector('#meetings-tab').click()">
                        Next →
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 8: Meeting Rules -->
        <div class="tab-pane fade" id="meetings" role="tabpanel">
            <form id="meetingsForm" class="section-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-header"><h5>Meeting Rules</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="general_meeting_notice_days" class="form-label">General Meeting Notice Period (Days) *</label>
                                <input type="number" class="form-control" id="general_meeting_notice_days" name="general_meeting_notice_days" value="7" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="general_meeting_quorum_percentage" class="form-label">General Meeting Quorum (%) *</label>
                                <input type="number" class="form-control" id="general_meeting_quorum_percentage" name="general_meeting_quorum_percentage" value="50" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="committee_meeting_notice_days" class="form-label">Committee Meeting Notice Period (Days) *</label>
                                <input type="number" class="form-control" id="committee_meeting_notice_days" name="committee_meeting_notice_days" value="3" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_committee_meetings_per_year" class="form-label">Minimum Committee Meetings Per Year *</label>
                                <input type="number" class="form-control" id="minimum_committee_meetings_per_year" name="minimum_committee_meetings_per_year" value="12" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.querySelector('#financial-tab').click()">
                        ← Back
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="fas fa-save me-2"></span>Save Meetings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.section-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('{{ route("organization-profile.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                btn.innerHTML = '<span class="fas fa-check me-2"></span>Saved!';
                setTimeout(() => {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = originalText;
                }, 2000);

                if (result.profile_id) {
                    window.profileId = result.profile_id;
                    updateFormsAction(result.profile_id);
                }
            } else {
                Object.keys(result.errors || {}).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback');
                        if (feedback) feedback.textContent = result.errors[field][0];
                    }
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            alert('Error saving section: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    form.addEventListener('change', () => {
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.classList.remove('is-invalid');
        });
    });
});

function updateFormsAction(profileId) {
    document.querySelectorAll('.section-form').forEach(form => {
        form.action = `/organization-profile/${profileId}`;
        form.method = 'PATCH';
    });
}
</script>
@endsection
