<?php $__env->startSection('title', 'Import Members | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('members.index')); ?>">Members</a></li>
        <li class="breadcrumb-item active">Import</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row mb-4 gx-6 gy-3 align-items-center">
        <div class="col-auto">
            <h2 class="mb-0">Import Members</h2>
        </div>
    </div>

    <div class="row g-4">
        <!-- Import Form Column -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Upload Excel File</h5>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong>
                            <ul class="mb-0 mt-2">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('members.import')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold">Select Excel File <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <input type="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="file" name="file" accept=".xlsx,.xls" required />
                                <label class="input-group-text" for="file">Choose File</label>
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
                            <small class="text-body-secondary d-block mt-2">Supported formats: .xlsx, .xls (Max 5MB)</small>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <strong>ℹ️ File Format:</strong> Please use the template provided below with the following columns:
                            <ul class="mb-0 mt-2">
                                <li><strong>Name</strong> - Member's full name (required)</li>
                                <li><strong>Email</strong> - Member's email (optional)</li>
                                <li><strong>Phone</strong> - Member's phone number (optional)</li>
                                <li><strong>Join Date</strong> - Date in YYYY-MM-DD format (optional)</li>
                                <li><strong>Status</strong> - active, inactive, or suspended (optional, defaults to active)</li>
                                <li><strong>Monthly Saving Amount</strong> - Numeric value (optional)</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex gap-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-upload me-2"></i>Import Members
                            </button>
                            <a href="<?php echo e(route('members.index')); ?>" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Template Column -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">📋 Import Template</h5>
                </div>
                <div class="card-body">
                    <p class="text-body-secondary mb-3">Download the template to get started with the correct format:</p>

                    <a href="<?php echo e(route('members.template')); ?>" class="btn btn-outline-primary w-100 mb-3">
                        <i class="fa-solid fa-download me-2"></i>Download Template
                    </a>

                    <hr>

                    <h6 class="fw-semibold mb-2">What's Included:</h6>
                    <ul class="small list-unstyled">
                        <li>✓ Column headers with correct names</li>
                        <li>✓ Example data rows</li>
                        <li>✓ Format instructions</li>
                        <li>✓ Data validation guidelines</li>
                    </ul>

                    <hr>

                    <h6 class="fw-semibold mb-2">Tips:</h6>
                    <ul class="small list-unstyled">
                        <li>📌 Name field is required</li>
                        <li>📅 Use YYYY-MM-DD for dates</li>
                        <li>🔄 Duplicate members will be skipped</li>
                        <li>✉️ Email must be valid format</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-body-tertiary">
                    <h5 class="mb-0">Status Options</h5>
                </div>
                <div class="card-body">
                    <div class="badge badge-phoenix badge-phoenix-success mb-2 d-block">active</div>
                    <div class="badge badge-phoenix badge-phoenix-secondary mb-2 d-block">inactive</div>
                    <div class="badge badge-phoenix badge-phoenix-warning d-block">suspended</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/members/import.blade.php ENDPATH**/ ?>