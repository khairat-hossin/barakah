<?php $__env->startSection('title', 'Member Details | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('members.index')); ?>">Members</a></li>
        <li class="breadcrumb-item active"><?php echo e($member->name); ?></li>
    </ol>
</nav>

<div class="pb-9">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between g-3 mb-3">
                <div class="col-12 col-md-auto">
                    <h2 class="mb-0">Member Details</h2>
                </div>
                <div class="col-12 col-md-auto">
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('members.edit', $member)); ?>" class="btn btn-primary">
                            <span class="fas fa-pencil me-2"></span><span>Edit</span>
                        </a>
                        <button class="btn btn-phoenix-secondary px-3 px-sm-5" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false">
                            <span class="fas fa-ellipsis"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-0">
                            <li><a class="dropdown-item" href="#!">Download Profile</a></li>
                            <li><a class="dropdown-item" href="#!">Print</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#!" onclick="if(confirm('Delete this member?')) { document.getElementById('deleteForm').submit(); }">Delete Member</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-0 g-md-4 g-xl-6">
        <!-- Left Sidebar -->
        <div class="col-md-5 col-lg-5 col-xl-4">
            <div class="sticky-leads-sidebar">
                <div class="lead-details-offcanvas bg-body scrollbar">
                    <!-- Member Avatar Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center g-3 text-center text-xxl-start">
                                <div class="col-12 col-xxl-auto">
                                    <div class="avatar avatar-5xl">
                                        <div class="avatar-name rounded-circle bg-primary-subtle">
                                            <span class="text-primary fw-bold"><?php echo e(strtoupper(substr($member->name, 0, 2))); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-auto flex-1">
                                    <h3 class="fw-bolder mb-2"><?php echo e($member->name); ?></h3>
                                    <p class="mb-1 text-body-secondary"><?php echo e($member->member_code); ?></p>
                                    <span class="badge badge-phoenix <?php if($member->status === 'active'): ?> badge-phoenix-success <?php elseif($member->status === 'inactive'): ?> badge-phoenix-secondary <?php else: ?> badge-phoenix-warning <?php endif; ?>">
                                        <?php echo e(ucfirst($member->status)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Personal Information</h3>
                                <a href="<?php echo e(route('members.edit', $member)); ?>" class="btn btn-link px-3">Edit</a>
                            </div>

                            <?php if($member->name_bn): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-signature"></span>
                                    <h5 class="text-body-highlight mb-0">Name (Bangla)</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->name_bn); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->date_of_birth): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-birthday-cake"></span>
                                    <h5 class="text-body-highlight mb-0">Date of Birth</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e(\Carbon\Carbon::parse($member->date_of_birth)->format('d M Y')); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->gender): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-venus-mars"></span>
                                    <h5 class="text-body-highlight mb-0">Gender</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e(ucfirst($member->gender)); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->nationality): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-globe"></span>
                                    <h5 class="text-body-highlight mb-0">Nationality</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->nationality); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->marital_status): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-heart"></span>
                                    <h5 class="text-body-highlight mb-0">Marital Status</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e(ucfirst($member->marital_status)); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Contact Information</h3>
                                <a href="<?php echo e(route('members.edit', $member)); ?>" class="btn btn-link px-3">Edit</a>
                            </div>

                            <?php if($member->email): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-envelope"></span>
                                    <h5 class="text-body-highlight mb-0">Email</h5>
                                </div>
                                <a href="mailto:<?php echo e($member->email); ?>"><?php echo e($member->email); ?></a>
                            </div>
                            <?php endif; ?>

                            <?php if($member->phone): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-phone"></span>
                                    <h5 class="text-body-highlight mb-0">Phone</h5>
                                </div>
                                <a href="tel:<?php echo e($member->phone); ?>"><?php echo e($member->phone); ?></a>
                            </div>
                            <?php endif; ?>

                            <?php if($member->secondary_mobile): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-mobile-alt"></span>
                                    <h5 class="text-body-highlight mb-0">Secondary Mobile</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->secondary_mobile); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->whatsapp_number): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fab fa-whatsapp"></span>
                                    <h5 class="text-body-highlight mb-0">WhatsApp</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->whatsapp_number); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Identity Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Identity Documents</h3>
                                <a href="<?php echo e(route('members.edit', $member)); ?>" class="btn btn-link px-3">Edit</a>
                            </div>

                            <?php if($member->nid_number): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-id-card"></span>
                                    <h5 class="text-body-highlight mb-0">NID</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->nid_number); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->birth_registration): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-certificate"></span>
                                    <h5 class="text-body-highlight mb-0">Birth Registration</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->birth_registration); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->passport_number): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-passport"></span>
                                    <h5 class="text-body-highlight mb-0">Passport</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->passport_number); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->tax_id): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-file-invoice"></span>
                                    <h5 class="text-body-highlight mb-0">Tax ID</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->tax_id); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Address Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Addresses</h3>
                                <a href="<?php echo e(route('members.edit', $member)); ?>" class="btn btn-link px-3">Edit</a>
                            </div>

                            <?php if($member->permanent_address_village): ?>
                            <div class="mb-4">
                                <h5 class="text-body-highlight mb-3">Permanent Address</h5>
                                <div class="mb-2">
                                    <span class="text-body-secondary"><?php echo e($member->permanent_address_village); ?>, <?php echo e($member->permanent_address_po); ?>, <?php echo e($member->permanent_address_union); ?>, <?php echo e($member->permanent_address_upazila); ?>, <?php echo e($member->permanent_address_district); ?> - <?php echo e($member->permanent_address_postal); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($member->present_address_village): ?>
                            <div class="mb-4">
                                <h5 class="text-body-highlight mb-3">Present Address</h5>
                                <div class="mb-2">
                                    <span class="text-body-secondary"><?php echo e($member->present_address_village); ?>, <?php echo e($member->present_address_po); ?>, <?php echo e($member->present_address_union); ?>, <?php echo e($member->present_address_upazila); ?>, <?php echo e($member->present_address_district); ?> - <?php echo e($member->present_address_postal); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Professional Information Card -->
                    <?php if($member->occupation || $member->business_name || $member->office_designation || $member->employer_name): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Professional Information</h3>
                                <a href="<?php echo e(route('members.edit', $member)); ?>" class="btn btn-link px-3">Edit</a>
                            </div>

                            <?php if($member->occupation): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-briefcase"></span>
                                    <h5 class="text-body-highlight mb-0">Occupation</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->occupation); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->business_name): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-store"></span>
                                    <h5 class="text-body-highlight mb-0">Business Name</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->business_name); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->office_designation): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-user-tie"></span>
                                    <h5 class="text-body-highlight mb-0">Office Designation</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->office_designation); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->employer_name): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-building"></span>
                                    <h5 class="text-body-highlight mb-0">Employer</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->employer_name); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($member->office_address): ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-map-marker-alt"></span>
                                    <h5 class="text-body-highlight mb-0">Office Address</h5>
                                </div>
                                <p class="mb-0 text-body-secondary"><?php echo e($member->office_address); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Content -->
        <div class="col-md-7 col-lg-7 col-xl-8">
            <!-- Summary Cards Section -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-success border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">Total Shares</p>
                                    <h4 class="mb-0"><?php echo e($member->shares()->count()); ?></h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-success rounded-pill"><?php echo e($member->shares()->count()); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-primary border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">EMI/Month</p>
                                    <h4 class="mb-0">৳ <?php echo e(number_format($emiPerMonth ?? 0, 0)); ?></h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-primary rounded-pill">Monthly</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-info border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">Total Deposit</p>
                                    <h4 class="mb-0">৳ <?php echo e(number_format($member->savingsEntries()->sum('amount'), 0)); ?></h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-info rounded-pill">All Time</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-warning border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">Pending EMI</p>
                                    <h4 class="mb-0">0</h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-warning rounded-pill">Due</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deposits Section -->
            <div class="card mb-4">
                <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-arrow-down-to-line me-2 text-success"></span>Recent Deposits
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                        $deposits = $member->savingsEntries()->orderByDesc('deposit_date')->get();
                    ?>
                    <?php if($deposits->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover fs-9 mb-0">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="fw-semibold">Amount</th>
                                        <th class="fw-semibold">Date</th>
                                        <th class="fw-semibold">Method</th>
                                        <th class="fw-semibold">TXN ID</th>
                                        <th class="fw-semibold">Recorded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $deposits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deposit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="fw-semibold">৳ <?php echo e(number_format($deposit->amount, 2)); ?></td>
                                        <td><?php echo e($deposit->deposit_date->format('d M Y')); ?></td>
                                        <td>
                                            <?php
                                                $badgeClass = match($deposit->payment_method) {
                                                    'cash' => 'badge-phoenix-primary',
                                                    'bank_transfer' => 'badge-phoenix-info',
                                                    'mobile_banking' => 'badge-phoenix-success',
                                                    'check' => 'badge-phoenix-warning',
                                                    'other' => 'badge-phoenix-secondary',
                                                    default => 'badge-phoenix-secondary'
                                                };
                                            ?>
                                            <span class="badge badge-phoenix <?php echo e($badgeClass); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $deposit->payment_method))); ?></span>
                                        </td>
                                        <td><code class="text-body-tertiary fs-10"><?php echo e($deposit->transaction_id ?? '-'); ?></code></td>
                                        <td><?php echo e($deposit->recorder?->name ?? 'System'); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No deposits recorded yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nominees Section - Card Grid Layout -->
            <div class="card mb-4">
                <div class="card-header bg-info-subtle d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-users me-2 text-info"></span>Nominees
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNomineeModal">
                        <span class="fas fa-plus me-2"></span>Add Nominee
                    </button>
                </div>
                <div class="card-body">
                    <?php
                        $nominees = $member->nominees()->get();
                        $totalAllocation = $nominees->sum('allocation_percentage');
                    ?>
                    <?php if($nominees->count() > 0): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $nominees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nominee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-body-tertiary-hover">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-semibold mb-0"><?php echo e($nominee->full_name); ?></h6>
                                        <?php if($nominee->is_primary): ?>
                                        <span class="badge badge-phoenix badge-phoenix-success">Primary</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-body-secondary fs-9 mb-2"><?php echo e(ucfirst($nominee->relationship)); ?></p>
                                    <div class="mb-3">
                                        <p class="text-body-tertiary fs-9 mb-1">Allocation</p>
                                        <p class="fw-bold mb-0"><?php echo e($nominee->allocation_percentage); ?>%</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-info flex-grow-1" data-bs-toggle="modal" data-bs-target="#viewNomineeModal<?php echo e($nominee->id); ?>">
                                            <span class="fas fa-eye me-1"></span>View
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNomineeModal<?php echo e($nominee->id); ?>">
                                            <span class="fas fa-edit me-1"></span>Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteNominee(<?php echo e($nominee->id); ?>)">
                                            <span class="fas fa-trash"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="text-body-secondary fs-9 mt-3 pt-3 border-top">
                            <strong>Total Allocation: <?php echo e($totalAllocation); ?>%</strong>
                            <?php if($totalAllocation < 100): ?>
                            <span class="ms-2">(<?php echo e(100 - $totalAllocation); ?>% remaining)</span>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No nominees added yet</p>
                            <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addNomineeModal">
                                <span class="fas fa-plus me-2"></span>Add First Nominee
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Documents Section - Table Layout -->
            <div class="card mb-4">
                <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-file me-2 text-warning"></span>Documents
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                        <span class="fas fa-plus me-2"></span>Upload Document
                    </button>
                </div>
                <div class="card-body">
                    <?php
                        $documents = $member->documents()->orderByDesc('upload_date')->get();
                    ?>
                    <?php if($documents->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover fs-9 mb-0">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="fw-semibold">Document</th>
                                        <th class="fw-semibold">Type</th>
                                        <th class="fw-semibold">Verified</th>
                                        <th class="fw-semibold">Uploaded</th>
                                        <th class="fw-semibold text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="fw-semibold">
                                            <span class="fas fa-file-pdf text-danger me-2"></span><?php echo e($document->file_name); ?>

                                        </td>
                                        <td><?php echo e(config('constants.document_types.' . $document->document_type, $document->document_type)); ?></td>
                                        <td>
                                            <?php if($document->verified): ?>
                                            <span class="badge badge-phoenix badge-phoenix-success">Verified</span>
                                            <?php else: ?>
                                            <span class="badge badge-phoenix badge-phoenix-secondary">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($document->upload_date->format('d M Y')); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewDocumentModal<?php echo e($document->id); ?>">
                                                    <span class="fas fa-eye"></span>
                                                </button>
                                                <a href="<?php echo e(route('documents.download', $document)); ?>" class="btn btn-sm btn-outline-primary">
                                                    <span class="fas fa-download"></span>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument(<?php echo e($document->id); ?>)">
                                                    <span class="fas fa-trash"></span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No documents uploaded yet</p>
                            <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <span class="fas fa-plus me-2"></span>Upload Document
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Nominee Modal -->
<div class="modal fade" id="addNomineeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Nominee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('nominees.store', $member)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <?php
                        $remainingAllocation = 100 - $nominees->sum('allocation_percentage');
                    ?>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="full_name" name="full_name" placeholder="Enter nominee's full name" required>
                        <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Father's name">
                        </div>
                        <div class="col-md-6">
                            <label for="mother_name" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name" placeholder="Mother's name">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                        </div>
                        <div class="col-md-6">
                            <label for="relationship" class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['relationship'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="relationship" name="relationship" required>
                                <option value="">Select relationship...</option>
                                <option value="son">Son</option>
                                <option value="daughter">Daughter</option>
                                <option value="wife">Wife</option>
                                <option value="husband">Husband</option>
                                <option value="parent">Parent</option>
                                <option value="sibling">Sibling</option>
                                <option value="other">Other</option>
                            </select>
                            <?php $__errorArgs = ['relationship'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="nid_number" class="form-label">NID Number</label>
                            <input type="text" class="form-control" id="nid_number" name="nid_number" placeholder="NID">
                        </div>
                        <div class="col-md-6">
                            <label for="birth_registration" class="form-label">Birth Registration</label>
                            <input type="text" class="form-control" id="birth_registration" name="birth_registration" placeholder="BRN">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="mobile_number" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Phone">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label for="allocation_percentage" class="form-label">Allocation Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control <?php $__errorArgs = ['allocation_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="allocation_percentage" name="allocation_percentage" min="1" max="<?php echo e($remainingAllocation); ?>" placeholder="Enter percentage" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-body-secondary">Maximum available: <?php echo e($remainingAllocation); ?>%</small>
                        <?php $__errorArgs = ['allocation_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter address"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary">
                            Set as Primary Nominee
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Nominee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Nominee Modal (dynamic for each nominee) -->
<?php
    $nominees = $member->nominees()->get();
?>
<?php $__currentLoopData = $nominees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nominee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="viewNomineeModal<?php echo e($nominee->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nominee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-body-tertiary rounded">
                            <h6 class="fw-semibold mb-2"><?php echo e($nominee->full_name); ?></h6>
                            <div class="d-flex gap-2">
                                <span class="badge badge-phoenix badge-phoenix-info"><?php echo e(ucfirst($nominee->relationship)); ?></span>
                                <?php if($nominee->is_primary): ?>
                                <span class="badge badge-phoenix badge-phoenix-success">Primary Nominee</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Father's Name</label>
                        <p class="mb-0"><?php echo e($nominee->father_name ?? '-'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Mother's Name</label>
                        <p class="mb-0"><?php echo e($nominee->mother_name ?? '-'); ?></p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Date of Birth</label>
                        <p class="mb-0"><?php echo e($nominee->date_of_birth?->format('d M Y') ?? '-'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Relationship</label>
                        <p class="mb-0"><?php echo e(ucfirst($nominee->relationship)); ?></p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">NID Number</label>
                        <p class="mb-0"><?php echo e($nominee->nid_number ?? '-'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Birth Registration</label>
                        <p class="mb-0"><?php echo e($nominee->birth_registration ?? '-'); ?></p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Mobile Number</label>
                        <p class="mb-0">
                            <?php if($nominee->mobile_number): ?>
                                <a href="tel:<?php echo e($nominee->mobile_number); ?>"><?php echo e($nominee->mobile_number); ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Email</label>
                        <p class="mb-0">
                            <?php if($nominee->email): ?>
                                <a href="mailto:<?php echo e($nominee->email); ?>"><?php echo e($nominee->email); ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-body-secondary fs-9">Address</label>
                        <p class="mb-0"><?php echo e($nominee->address ?? '-'); ?></p>
                    </div>

                    <div class="col-12">
                        <div class="p-3 bg-info-subtle rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-1">Allocation Percentage</p>
                                    <h5 class="mb-0"><?php echo e($nominee->allocation_percentage); ?>%</h5>
                                </div>
                                <div class="text-end">
                                    <p class="text-body-secondary fs-9 mb-1">Created</p>
                                    <p class="text-body-secondary fs-9 mb-0"><?php echo e($nominee->created_at->format('d M Y')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editNomineeModal<?php echo e($nominee->id); ?>">
                    <span class="fas fa-edit me-2"></span>Edit
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Edit Nominee Modal (dynamic for each nominee) -->
<?php $__currentLoopData = $nominees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nominee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editNomineeModal<?php echo e($nominee->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Nominee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('nominees.update', [$member, $nominee])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <?php
                        $otherAllocation = $member->nominees()
                            ->where('id', '!=', $nominee->id)
                            ->sum('allocation_percentage');
                        $remainingForEdit = 100 - $otherAllocation;
                    ?>

                    <div class="mb-3">
                        <label for="edit_full_name<?php echo e($nominee->id); ?>" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_full_name<?php echo e($nominee->id); ?>" name="full_name" value="<?php echo e(old('full_name', $nominee->full_name)); ?>" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="edit_father_name<?php echo e($nominee->id); ?>" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="edit_father_name<?php echo e($nominee->id); ?>" name="father_name" value="<?php echo e(old('father_name', $nominee->father_name)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_mother_name<?php echo e($nominee->id); ?>" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" id="edit_mother_name<?php echo e($nominee->id); ?>" name="mother_name" value="<?php echo e(old('mother_name', $nominee->mother_name)); ?>">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="edit_date_of_birth<?php echo e($nominee->id); ?>" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="edit_date_of_birth<?php echo e($nominee->id); ?>" name="date_of_birth" value="<?php echo e(old('date_of_birth', $nominee->date_of_birth?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_relationship<?php echo e($nominee->id); ?>" class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_relationship<?php echo e($nominee->id); ?>" name="relationship" required>
                                <option value="">Select relationship...</option>
                                <option value="son" <?php if(old('relationship', $nominee->relationship) === 'son'): echo 'selected'; endif; ?>>Son</option>
                                <option value="daughter" <?php if(old('relationship', $nominee->relationship) === 'daughter'): echo 'selected'; endif; ?>>Daughter</option>
                                <option value="wife" <?php if(old('relationship', $nominee->relationship) === 'wife'): echo 'selected'; endif; ?>>Wife</option>
                                <option value="husband" <?php if(old('relationship', $nominee->relationship) === 'husband'): echo 'selected'; endif; ?>>Husband</option>
                                <option value="parent" <?php if(old('relationship', $nominee->relationship) === 'parent'): echo 'selected'; endif; ?>>Parent</option>
                                <option value="sibling" <?php if(old('relationship', $nominee->relationship) === 'sibling'): echo 'selected'; endif; ?>>Sibling</option>
                                <option value="other" <?php if(old('relationship', $nominee->relationship) === 'other'): echo 'selected'; endif; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="edit_nid_number<?php echo e($nominee->id); ?>" class="form-label">NID Number</label>
                            <input type="text" class="form-control" id="edit_nid_number<?php echo e($nominee->id); ?>" name="nid_number" value="<?php echo e(old('nid_number', $nominee->nid_number)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_birth_registration<?php echo e($nominee->id); ?>" class="form-label">Birth Registration</label>
                            <input type="text" class="form-control" id="edit_birth_registration<?php echo e($nominee->id); ?>" name="birth_registration" value="<?php echo e(old('birth_registration', $nominee->birth_registration)); ?>">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="edit_mobile_number<?php echo e($nominee->id); ?>" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="edit_mobile_number<?php echo e($nominee->id); ?>" name="mobile_number" value="<?php echo e(old('mobile_number', $nominee->mobile_number)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email<?php echo e($nominee->id); ?>" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email<?php echo e($nominee->id); ?>" name="email" value="<?php echo e(old('email', $nominee->email)); ?>">
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label for="edit_allocation_percentage<?php echo e($nominee->id); ?>" class="form-label">Allocation Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="edit_allocation_percentage<?php echo e($nominee->id); ?>" name="allocation_percentage" min="1" max="<?php echo e($remainingForEdit); ?>" value="<?php echo e(old('allocation_percentage', $nominee->allocation_percentage)); ?>" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-body-secondary">Maximum available: <?php echo e($remainingForEdit); ?>%</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_address<?php echo e($nominee->id); ?>" class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address<?php echo e($nominee->id); ?>" name="address" rows="2"><?php echo e(old('address', $nominee->address)); ?></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_primary<?php echo e($nominee->id); ?>" name="is_primary" value="1" <?php if(old('is_primary', $nominee->is_primary)): echo 'checked'; endif; ?>>
                        <label class="form-check-label" for="edit_is_primary<?php echo e($nominee->id); ?>">
                            Set as Primary Nominee
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Nominee</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('documents.store', $member)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="document_type" name="document_type" required>
                            <option value="">Select document type...</option>
                            <option value="nid_copy">NID Copy</option>
                            <option value="birth_registration_copy">Birth Registration Copy</option>
                            <option value="trade_license">Trade License</option>
                            <option value="passport_copy">Passport Copy</option>
                            <option value="membership_agreement">Membership Agreement</option>
                            <option value="nominee_form">Nominee Form</option>
                            <option value="bank_account_proof">Bank Account Proof</option>
                            <option value="other_attachment">Other Attachment</option>
                        </select>
                        <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <small class="text-body-secondary">Allowed: PDF, JPG, PNG (Max 5MB)</small>
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Add any remarks about this document"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Document Modal (dynamic for each document) -->
<?php
    $documents = $member->documents()->orderByDesc('upload_date')->get();
?>
<?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="viewDocumentModal<?php echo e($document->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-body-tertiary rounded">
                            <p class="text-body-secondary fs-9 mb-2">File Name</p>
                            <p class="fw-semibold mb-0"><?php echo e($document->file_name); ?></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Document Type</label>
                        <p class="mb-0"><?php echo e(config('constants.document_types.' . $document->document_type, $document->document_type)); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">File Size</label>
                        <p class="mb-0"><?php echo e(number_format($document->file_size / 1024, 2)); ?> KB</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Upload Date</label>
                        <p class="mb-0"><?php echo e($document->upload_date->format('d M Y, g:i A')); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Uploaded By</label>
                        <p class="mb-0"><?php echo e($document->uploader?->name ?? 'System'); ?></p>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-body-secondary fs-9">Status</label>
                        <p class="mb-0">
                            <?php if($document->verified): ?>
                                <span class="badge badge-phoenix badge-phoenix-success">
                                    <span class="fas fa-check-circle me-1"></span>Verified
                                </span>
                                <?php if($document->verification_date): ?>
                                    <small class="text-body-secondary ms-2">on <?php echo e($document->verification_date->format('d M Y')); ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-phoenix badge-phoenix-warning">
                                    <span class="fas fa-clock me-1"></span>Pending Verification
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if($document->remarks): ?>
                    <div class="col-12">
                        <label class="form-label text-body-secondary fs-9">Remarks</label>
                        <p class="mb-0"><?php echo e($document->remarks); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="<?php echo e(route('documents.download', $document)); ?>" class="btn btn-primary">
                    <span class="fas fa-download me-2"></span>Download
                </a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Hidden Delete Form -->
<form id="deleteNomineeForm" action="" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<!-- Hidden Delete Form for Document -->
<form id="deleteDocumentForm" action="" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<!-- Hidden Delete Form for Member -->
<form id="deleteForm" action="<?php echo e(route('members.destroy', $member)); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<script>
function deleteNominee(nomineeId) {
    if (confirm('Are you sure you want to delete this nominee?')) {
        const form = document.getElementById('deleteNomineeForm');
        form.action = `/members/<?php echo e($member->id); ?>/nominees/${nomineeId}`;
        form.submit();
    }
}

function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        const form = document.getElementById('deleteDocumentForm');
        form.action = `/documents/${documentId}`;
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/members/show.blade.php ENDPATH**/ ?>