<?php $__env->startSection('title', 'Investment Types | Barakah'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-9">
    <div class="row mb-4 gx-6 gy-3 align-items-center">
        <div class="col-auto">
            <h2 class="mb-0">Investment Types<span class="fw-normal text-body-tertiary ms-3">(<?php echo e($types->total()); ?>)</span></h2>
        </div>
        <div class="col-auto">
            <a class="btn btn-primary px-5" href="<?php echo e(route('investment-types.create')); ?>">
                <i class="fa-solid fa-plus me-2"></i>Add Type
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive scrollbar">
                <table class="table fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">NAME</th>
                            <th>CODE</th>
                            <th>CATEGORY</th>
                            <th>RETURN TYPE</th>
                            <th>TENURE</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <p class="fw-semibold mb-0"><?php echo e($type->name); ?></p>
                            </td>
                            <td class="py-3">
                                <span class="badge badge-phoenix badge-phoenix-secondary"><?php echo e($type->code); ?></span>
                            </td>
                            <td class="py-3"><?php echo e($type->category ?? '-'); ?></td>
                            <td class="py-3">
                                <span class="badge badge-phoenix badge-phoenix-info"><?php echo e(ucfirst($type->default_return_type)); ?></span>
                            </td>
                            <td class="py-3"><?php echo e($type->default_tenure_months ?? '-'); ?> months</td>
                            <td class="py-3">
                                <?php if($type->is_active): ?>
                                    <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-phoenix badge-phoenix-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-sm btn-phoenix-primary" href="<?php echo e(route('investment-types.edit', $type)); ?>">Edit</a>
                                    <form action="<?php echo e(route('investment-types.destroy', $type)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-phoenix-danger" onclick="return confirm('Delete this investment type?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                                <p class="text-body-secondary">No investment types found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-body-tertiary">
            <?php echo e($types->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/investment-types/index.blade.php ENDPATH**/ ?>