<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['member']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['member']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $completeness = $member->getProfileCompleteness();
    $percentage = $completeness['percentage'];
    $status = $completeness['status'];
    $color = $completeness['color'];
    $details = $completeness['details'];
    $missingFields = $completeness['missing_fields'];
?>

<!-- Profile Completeness Progress Bar -->
<style>
    #profileCompletenessModal<?php echo e($member->id); ?> .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }
</style>
<div style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#profileCompletenessModal<?php echo e($member->id); ?>">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <small class="text-body-secondary fw-semibold">Profile Completeness</small>
        <small class="badge badge-phoenix badge-phoenix-<?php echo e($color); ?>"><?php echo e($percentage); ?>%</small>
    </div>
    <div class="progress" style="height: 8px; cursor: pointer;">
        <div class="progress-bar bg-<?php echo e($color); ?>" role="progressbar"
             style="width: <?php echo e($percentage); ?>%"
             aria-valuenow="<?php echo e($percentage); ?>"
             aria-valuemin="0"
             aria-valuemax="100">
        </div>
    </div>
</div>

<!-- Profile Completeness Modal -->
<div class="modal fade" id="profileCompletenessModal<?php echo e($member->id); ?>" tabindex="-1"
     aria-labelledby="profileCompletenessLabel<?php echo e($member->id); ?>" aria-hidden="true"
     style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 10000;">
        <div class="modal-content" style="box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: none;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="profileCompletenessLabel<?php echo e($member->id); ?>">
                    Profile Completeness - <?php echo e($member->name); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Overall Progress -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Overall Progress</strong>
                        <span class="badge badge-phoenix badge-phoenix-<?php echo e($color); ?>"><?php echo e($percentage); ?>%</span>
                    </div>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-<?php echo e($color); ?>" role="progressbar"
                             style="width: <?php echo e($percentage); ?>%"
                             aria-valuenow="<?php echo e($percentage); ?>"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <small class="fw-bold text-white"><?php echo e($percentage); ?>%</small>
                        </div>
                    </div>
                    <small class="text-body-secondary d-block mt-2">Status: <strong><?php echo e($status); ?></strong></small>
                </div>

                <hr>

                <!-- Parameters Status -->
                <div>
                    <h6 class="fw-semibold mb-3">Profile Parameters (20% each)</h6>

                    <!-- Personal Information -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <?php if($details['personal_information']): ?>
                                        <span class="text-success">✓</span>
                                    <?php else: ?>
                                        <span class="text-danger">✗</span>
                                    <?php endif; ?>
                                    Personal Information
                                </h6>
                                <?php if(!$details['personal_information'] && isset($missingFields['personal_information'])): ?>
                                    <small class="text-body-secondary">
                                        Missing: <strong><?php echo e(implode(', ', $missingFields['personal_information'])); ?></strong>
                                    </small>
                                <?php else: ?>
                                    <small class="text-success">All fields completed</small>
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-<?php echo e($details['personal_information'] ? 'success' : 'danger'); ?>">20%</span>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <?php if($details['contact_information']): ?>
                                        <span class="text-success">✓</span>
                                    <?php else: ?>
                                        <span class="text-danger">✗</span>
                                    <?php endif; ?>
                                    Contact Information
                                </h6>
                                <?php if(!$details['contact_information'] && isset($missingFields['contact_information'])): ?>
                                    <small class="text-body-secondary">
                                        Missing: <strong><?php echo e(implode(', ', $missingFields['contact_information'])); ?></strong>
                                    </small>
                                <?php else: ?>
                                    <small class="text-success">All fields completed</small>
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-<?php echo e($details['contact_information'] ? 'success' : 'danger'); ?>">20%</span>
                        </div>
                    </div>

                    <!-- Nominee -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <?php if($details['nominee']): ?>
                                        <span class="text-success">✓</span>
                                    <?php else: ?>
                                        <span class="text-danger">✗</span>
                                    <?php endif; ?>
                                    Nominee
                                </h6>
                                <?php if(!$details['nominee'] && isset($missingFields['nominee'])): ?>
                                    <small class="text-body-secondary">
                                        Missing: <strong><?php echo e(implode(', ', $missingFields['nominee'])); ?></strong>
                                    </small>
                                <?php else: ?>
                                    <small class="text-success">All fields completed</small>
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-<?php echo e($details['nominee'] ? 'success' : 'danger'); ?>">20%</span>
                        </div>
                    </div>

                    <!-- Share Ownership -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <?php if($details['share_ownership']): ?>
                                        <span class="text-success">✓</span>
                                    <?php else: ?>
                                        <span class="text-danger">✗</span>
                                    <?php endif; ?>
                                    Share Ownership
                                </h6>
                                <?php if(!$details['share_ownership'] && isset($missingFields['share_ownership'])): ?>
                                    <small class="text-body-secondary">
                                        <strong><?php echo e(implode(', ', $missingFields['share_ownership'])); ?></strong>
                                    </small>
                                <?php else: ?>
                                    <small class="text-success">Shares assigned</small>
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-<?php echo e($details['share_ownership'] ? 'success' : 'danger'); ?>">20%</span>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    <?php if($details['documents']): ?>
                                        <span class="text-success">✓</span>
                                    <?php else: ?>
                                        <span class="text-danger">✗</span>
                                    <?php endif; ?>
                                    Documents
                                </h6>
                                <?php if(!$details['documents'] && isset($missingFields['documents'])): ?>
                                    <small class="text-body-secondary">
                                        <strong><?php echo e(implode(', ', $missingFields['documents'])); ?></strong>
                                    </small>
                                <?php else: ?>
                                    <small class="text-success">Documents uploaded</small>
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-<?php echo e($details['documents'] ? 'success' : 'danger'); ?>">20%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="<?php echo e(route('members.edit', $member)); ?>" class="btn btn-primary">
                    <span class="fas fa-pencil me-2"></span>Complete Profile
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/components/profile-completeness.blade.php ENDPATH**/ ?>