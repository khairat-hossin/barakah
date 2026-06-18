<?php $__env->startSection('title', 'Users | Barakah'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-9">

        <div class="row mb-4 gx-6 gy-3 align-items-center">
            <div class="col-auto">
                <h2 class="mb-0">Users<span class="fw-normal text-body-tertiary ms-3">(<?php echo e($users->count()); ?>)</span></h2>
            </div>
            <div class="col-auto">
                <a class="btn btn-primary px-5" href="<?php echo e(route('user-management.users.create')); ?>">
                    <i class="fa-solid fa-plus me-2"></i>Add user
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">USER</th>
                                <th>ROLES</th>
                                <th>DIRECT PERMISSIONS</th>
                                <th>ACCESS LEVEL</th>
                                <th class="text-end pe-4">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isCurrentUser = auth()->id() === $user->id;
                                    $isLastSuperAdmin = $user->hasRole('Super Admin') && $superAdminCount === 1;
                                ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <p class="fw-semibold mb-0"><?php echo e($user->name); ?></p>
                                        <p class="text-body-tertiary fs-10 mb-0"><?php echo e($user->email); ?></p>
                                    </td>
                                    <td class="py-3"><?php echo e($user->roles->pluck('name')->implode(', ') ?: 'No roles'); ?></td>
                                    <td class="py-3"><?php echo e($user->permissions->count()); ?></td>
                                    <td class="py-3">
                                        <?php if($user->hasRole('Super Admin')): ?>
                                            <span class="badge badge-phoenix badge-phoenix-warning">Super Admin</span>
                                        <?php elseif($user->roles->isNotEmpty()): ?>
                                            <span class="badge badge-phoenix badge-phoenix-primary">Role Based</span>
                                        <?php else: ?>
                                            <span class="badge badge-phoenix badge-phoenix-secondary">Direct / Limited</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-inline-flex gap-2">
                                            <a class="btn btn-sm btn-phoenix-primary" href="<?php echo e(route('user-management.users.edit', $user)); ?>">Edit</a>
                                            <?php if($isCurrentUser || $isLastSuperAdmin): ?>
                                                <button class="btn btn-sm btn-phoenix-danger" type="button" disabled>Delete</button>
                                            <?php else: ?>
                                                <form method="POST" action="<?php echo e(route('user-management.users.destroy', $user)); ?>">
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

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/user-management/users/index.blade.php ENDPATH**/ ?>