<?php $__env->startSection('title', 'Share Distribution | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('shares.index')); ?>">Shares</a></li>
        <li class="breadcrumb-item active">Distribution</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">Share Distribution</h2>
            <p class="text-body-secondary">Member share allocation overview</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo e(route('shares.index')); ?>" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-2"></span>Back
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-primary fw-semibold">Total Shares</small>
                <h6 class="mb-0"><?php echo e(number_format($totalShares)); ?></h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-success fw-semibold">Assigned</small>
                <h6 class="mb-0" id="assignedTotal"><?php echo e(number_format($assignedShares)); ?></h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-warning fw-semibold">Available</small>
                <h6 class="mb-0" id="availableTotal"><?php echo e(number_format($availableShares)); ?></h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-info fw-semibold">Members</small>
                <h6 class="mb-0"><?php echo e($memberShares->count()); ?></h6>
            </div>
        </div>
    </div>

    <!-- Member Boxes Grid -->
    <?php if($memberShares->count()): ?>
        <div class="row g-2">
            <?php $__currentLoopData = $memberShares; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-4 col-lg-2-4" style="width: 20%;">
                    <div class="card h-100 border-0 shadow-sm member-box" style="padding: 10px; display: flex; flex-direction: column;">
                        <!-- Member Code - Name & Status -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; gap: 4px;">
                            <div style="font-weight: 500; font-size: 12px; color: #6c757d; flex: 0 0 auto;">
                                <?php echo e($member->member_id); ?>

                            </div>
                            <div style="font-weight: 500; font-size: 13px; line-height: 1.2; flex: 1; min-width: 0;" class="text-truncate" title="<?php echo e($member->name); ?>">
                                <?php echo e($member->name); ?>

                            </div>
                            <div style="flex: 0 0 auto;">
                                <span class="badge <?php if($member->status === 'active'): ?> bg-success <?php elseif($member->status === 'inactive'): ?> bg-secondary <?php else: ?> bg-danger <?php endif; ?>" style="font-size: 10px; padding: 2px 6px;">
                                    <?php echo e(ucfirst(substr($member->status, 0, 1))); ?>

                                </span>
                            </div>
                        </div>

                        <!-- Share Count & EMI Row -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 8px;">
                            <!-- Share Count (Left) -->
                            <div>
                                <div style="color: #6c757d; font-size: 11px; margin-bottom: 2px;">Shares</div>
                                <div style="font-size: 28px; font-weight: bold; color: #0d6efd; line-height: 1;">
                                    <?php echo e($member->shares_count); ?>

                                </div>
                            </div>

                            <!-- EMI Per Month (Right) -->
                            <div style="text-align: right;">
                                <div style="color: #6c757d; font-size: 11px; margin-bottom: 2px;">EMI/Month</div>
                                <div style="font-size: 13px; font-weight: 500; color: #198754; line-height: 1.2;">
                                    ৳ <span class="emi-amount"><?php echo e(number_format($member->emi_per_month ?? 0, 0)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mb-0">
            <span class="fas fa-info-circle me-2"></span>No members found.
        </div>
    <?php endif; ?>
</div>

<style>
    .member-box {
        transition: all 0.2s ease;
        cursor: pointer;
        border-radius: 8px;
    }

    .member-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    @media (max-width: 768px) {
        .member-box .display-6 {
            font-size: 1.5rem !important;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/shares/distribution-cards.blade.php ENDPATH**/ ?>