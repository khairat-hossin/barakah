<?php $__env->startSection('title', 'Payment Methods | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-9">
    <div class="row mb-4 gx-6 gy-3 align-items-center">
        <div class="col-auto">
            <h2 class="mb-0">Payment Methods<span class="fw-normal text-body-tertiary ms-3">(<?php echo e($paymentMethods->total()); ?>)</span></h2>
        </div>
        <div class="col-auto">
            <a class="btn btn-primary px-5" href="<?php echo e(route('payment-methods.create')); ?>">
                <i class="fa-solid fa-plus me-2"></i>Add Method
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
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <p class="fw-semibold mb-0"><?php echo e($method->name); ?></p>
                            </td>
                            <td class="py-3"><code><?php echo e($method->code); ?></code></td>
                            <td class="py-3 text-body-secondary text-truncate" style="max-width: 300px;"><?php echo e($method->description ?? '-'); ?></td>
                            <td class="py-3">
                                <?php if($method->is_active): ?>
                                    <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-phoenix badge-phoenix-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-sm btn-phoenix-primary" href="<?php echo e(route('payment-methods.edit', $method)); ?>">Edit</a>
                                    <form action="<?php echo e(route('payment-methods.destroy', $method)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-phoenix-danger" onclick="return confirm('Delete this payment method?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                                <p class="text-body-secondary">No payment methods found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-body-tertiary">
            <?php echo e($paymentMethods->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/payment-methods/index.blade.php ENDPATH**/ ?>