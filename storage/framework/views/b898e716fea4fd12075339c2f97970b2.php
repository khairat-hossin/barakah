<?php $__env->startSection('title', 'Permissions | Barakah'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-9">

        <div class="row mb-4 gx-6 gy-3 align-items-center">
            <div class="col-auto">
                <h2 class="mb-0">Permissions<span class="fw-normal text-body-tertiary ms-3">(<?php echo e($permissions->count()); ?>)</span></h2>
            </div>
            <div class="col-auto">
                <a class="btn btn-primary px-5" href="<?php echo e(route('user-management.permissions.create')); ?>">
                    <i class="fa-solid fa-plus me-2"></i>Add permission
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
                                <th>GUARD</th>
                                <th>USED BY ROLES</th>
                                <th>USED BY USERS</th>
                                <th>TYPE</th>
                                <th class="text-end pe-4">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isProtected = in_array($permission->name, $protectedPermissions, true);
                                    $isInUse = $permission->roles_count > 0 || $permission->users_count > 0;
                                ?>
                                <tr>
                                    <td class="ps-4 py-3 fw-semibold"><?php echo e($permission->name); ?></td>
                                    <td class="py-3"><?php echo e($permission->guard_name); ?></td>
                                    <td class="py-3"><?php echo e($permission->roles_count); ?></td>
                                    <td class="py-3"><?php echo e($permission->users_count); ?></td>
                                    <td class="py-3">
                                        <?php if($isProtected): ?>
                                            <span class="badge badge-phoenix badge-phoenix-warning">System</span>
                                        <?php else: ?>
                                            <span class="badge badge-phoenix badge-phoenix-secondary">Custom</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-inline-flex gap-2">
                                            <a class="btn btn-sm btn-phoenix-primary" href="<?php echo e(route('user-management.permissions.edit', $permission)); ?>">Edit</a>
                                            <?php if($isProtected || $isInUse): ?>
                                                <button class="btn btn-sm btn-phoenix-danger" type="button" disabled>Delete</button>
                                            <?php else: ?>
                                                <form method="POST" action="<?php echo e(route('user-management.permissions.destroy', $permission)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button class="btn btn-sm btn-phoenix-danger" type="submit">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/user-management/permissions/index.blade.php ENDPATH**/ ?>