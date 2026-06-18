<?php $__env->startSection('title', 'Organization Profile | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Organization Profile</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0"><?php echo e($profile->organization_name_en); ?></h2>
            <p class="text-body-secondary mt-2"><?php echo e($profile->organization_name_bn); ?></p>
        </div>
        <div class="col-auto">
            <a href="<?php echo e(route('organization-profile.edit', $profile)); ?>" class="btn btn-primary btn-sm">
                <span class="fas fa-edit me-2"></span>Edit Profile
            </a>
            <a href="<?php echo e(route('organization-profile.audit-logs', $profile)); ?>" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-history me-2"></span>Audit Logs
            </a>
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
            <div class="card mb-4">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="mb-0">General Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Organization Name (English)</label>
                            <p class="mb-0"><?php echo e($profile->organization_name_en); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Organization Name (Bangla)</label>
                            <p class="mb-0"><?php echo e($profile->organization_name_bn); ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-body-secondary">Short Name</label>
                            <p class="mb-0"><?php echo e($profile->short_name); ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-body-secondary">Registration Number</label>
                            <p class="mb-0"><?php echo e($profile->registration_number); ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-body-secondary">Registration Date</label>
                            <p class="mb-0"><?php echo e($profile->registration_date->format('d M, Y')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Organization Type</label>
                            <p class="mb-0"><span class="badge bg-secondary"><?php echo e(ucfirst($profile->organization_type)); ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Status</label>
                            <p class="mb-0">
                                <span class="badge <?php if($profile->status === 'active'): ?> bg-success <?php elseif($profile->status === 'inactive'): ?> bg-secondary <?php else: ?> bg-danger <?php endif; ?>">
                                    <?php echo e(ucfirst($profile->status)); ?>

                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Mobile Number</label>
                            <p class="mb-0"><a href="tel:<?php echo e($profile->mobile_number); ?>"><?php echo e($profile->mobile_number); ?></a></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Email</label>
                            <p class="mb-0"><a href="mailto:<?php echo e($profile->email); ?>"><?php echo e($profile->email); ?></a></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Website</label>
                            <p class="mb-0">
                                <?php if($profile->website): ?>
                                    <a href="<?php echo e($profile->website); ?>" target="_blank"><?php echo e($profile->website); ?></a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Secondary Mobile</label>
                            <p class="mb-0">
                                <?php if($profile->secondary_mobile): ?>
                                    <a href="tel:<?php echo e($profile->secondary_mobile); ?>"><?php echo e($profile->secondary_mobile); ?></a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Address -->
        <div class="tab-pane fade" id="address" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="mb-0">Organization Address</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-body-secondary">Full Address</label>
                            <p class="mb-0"><?php echo e($profile->address_line); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Village/Area</label>
                            <p class="mb-0"><?php echo e($profile->village_area); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Post Office</label>
                            <p class="mb-0"><?php echo e($profile->post_office); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Union/Ward</label>
                            <p class="mb-0"><?php echo e($profile->union_ward); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Upazila</label>
                            <p class="mb-0"><?php echo e($profile->upazila); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">District</label>
                            <p class="mb-0"><?php echo e($profile->district); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Division</label>
                            <p class="mb-0"><?php echo e($profile->division ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Postal Code</label>
                            <p class="mb-0"><?php echo e($profile->postal_code); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Mission & Objectives -->
        <div class="tab-pane fade" id="mission" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="mb-0">Mission & Objectives</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-body-secondary">Motto/Slogan</label>
                            <p class="mb-0 fw-bold"><?php echo e($profile->motto); ?></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-body-secondary">Vision Statement</label>
                            <p class="mb-0"><?php echo e($profile->vision_statement); ?></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-body-secondary">Mission Statement</label>
                            <p class="mb-0"><?php echo e($profile->mission_statement); ?></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-body-secondary">About Organization</label>
                            <p class="mb-0"><?php echo e($profile->about_organization ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Share Structure -->
        <div class="tab-pane fade" id="shares" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header bg-info bg-opacity-10">
                    <h5 class="mb-0">Share Structure</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Total Shares</label>
                            <p class="mb-0 fw-bold"><?php echo e(number_format($profile->total_shares)); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Share Face Value (BDT)</label>
                            <p class="mb-0 fw-bold">৳ <?php echo e(number_format($profile->share_face_value, 2)); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Ownership Model</label>
                            <p class="mb-0"><span class="badge bg-secondary"><?php echo e(ucfirst($profile->share_ownership_model)); ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Share Transfers</label>
                            <p class="mb-0">
                                <?php if($profile->share_transfer_allowed): ?>
                                    <span class="badge bg-success">Allowed</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Not Allowed</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Minimum Shares Per Member</label>
                            <p class="mb-0"><?php echo e($profile->minimum_shares_per_member); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Maximum Shares Per Member</label>
                            <p class="mb-0"><?php echo e($profile->maximum_shares_per_member ?? 'Unlimited'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Membership Rules -->
        <div class="tab-pane fade" id="membership" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header bg-danger bg-opacity-10">
                    <h5 class="mb-0">Membership Rules</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Membership Type</label>
                            <p class="mb-0"><span class="badge bg-secondary"><?php echo e(str_replace('_', ' ', ucfirst($profile->membership_type))); ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Minimum Share Requirement</label>
                            <p class="mb-0"><?php echo e($profile->minimum_share_requirement); ?></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-body-secondary">New Member Admission</label>
                            <p class="mb-0">
                                <?php if($profile->new_member_admission_allowed): ?>
                                    <span class="badge bg-success">Allowed</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Not Allowed</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Committee Structure -->
        <div class="tab-pane fade" id="committee" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header bg-dark bg-opacity-10">
                    <h5 class="mb-0">Committee Structure</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Committee Term Length</label>
                            <p class="mb-0"><?php echo e($profile->committee_term_length); ?> Years</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Maximum Consecutive Terms</label>
                            <p class="mb-0"><?php echo e($profile->maximum_consecutive_terms); ?></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-body-secondary">Election Required</label>
                            <p class="mb-0">
                                <?php if($profile->election_required): ?>
                                    <span class="badge bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 7: Financial Configuration -->
        <div class="tab-pane fade" id="financial" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="mb-0">Financial Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Default Currency</label>
                            <p class="mb-0"><strong><?php echo e($profile->default_currency); ?></strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Reserve Fund Percentage</label>
                            <p class="mb-0"><?php echo e($profile->reserve_fund_percentage); ?>%</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Bank Name</label>
                            <p class="mb-0"><?php echo e($profile->bank_name ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Account Number</label>
                            <p class="mb-0"><?php echo e($profile->account_number ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 8: Meeting Rules -->
        <div class="tab-pane fade" id="meetings" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="mb-0">Meeting Rules</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">General Meeting Notice Period</label>
                            <p class="mb-0"><?php echo e($profile->general_meeting_notice_days); ?> days</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">General Meeting Quorum</label>
                            <p class="mb-0"><?php echo e($profile->general_meeting_quorum_percentage); ?>%</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Committee Meeting Notice Period</label>
                            <p class="mb-0"><?php echo e($profile->committee_meeting_notice_days); ?> days</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-body-secondary">Minimum Committee Meetings / Year</label>
                            <p class="mb-0"><?php echo e($profile->minimum_committee_meetings_per_year); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/organization-profile/show.blade.php ENDPATH**/ ?>