<?php $__env->startSection('title', 'Approve Expense | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('expenses.index')); ?>">Expenses</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('expenses.show', $expense)); ?>"><?php echo e($expense->expense_number); ?></a></li>
        <li class="breadcrumb-item active">Approve</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Approve Expense</h2>

            <!-- Expense Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Expense Number</small>
                            <h6 class="mb-0"><?php echo e($expense->expense_number); ?></h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Amount</small>
                            <h6 class="mb-0">৳ <?php echo e(number_format($expense->amount, 2)); ?></h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Category</small>
                            <p class="mb-0"><?php echo e($expense->category->name); ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Expense Date</small>
                            <p class="mb-0"><?php echo e($expense->expense_date->format('d M Y')); ?></p>
                        </div>
                        <div class="col-12">
                            <small class="text-body-secondary d-block mb-1">Title</small>
                            <p class="mb-0"><?php echo e($expense->title); ?></p>
                        </div>
                        <div class="col-12">
                            <small class="text-body-secondary d-block mb-1">Description</small>
                            <p class="mb-0"><?php echo e($expense->description); ?></p>
                        </div>
                        <?php if($expense->member): ?>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Member</small>
                            <p class="mb-0"><?php echo e($expense->member->name); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if($expense->project): ?>
                        <div class="col-md-6">
                            <small class="text-body-secondary d-block mb-1">Project</small>
                            <p class="mb-0"><?php echo e($expense->project->name); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Approval Form -->
            <form method="POST" action="<?php echo e(route('expenses.approve-store', $expense)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="card mb-4">
                    <div class="card-header bg-body-tertiary">
                        <h5 class="mb-0">Approval Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Approval Notes (Optional)</label>
                            <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      name="notes" rows="4"
                                      placeholder="Add any notes related to this approval..."><?php echo e(old('notes')); ?></textarea>
                            <?php $__errorArgs = ['notes'];
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
                </div>

                <!-- Approval Confirmation -->
                <div class="alert alert-info mb-4">
                    <span class="fas fa-info-circle me-2"></span>
                    <strong>Confirm Approval:</strong> You are about to approve this expense for <strong>৳ <?php echo e(number_format($expense->amount, 2)); ?></strong>.
                    Once approved, the expense can be marked as paid.
                </div>

                <div class="row g-3">
                    <div class="col-auto">
                        <button class="btn btn-success btn-lg" type="submit">
                            <span class="fas fa-check me-2"></span>Approve Expense
                        </button>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-secondary btn-lg" href="<?php echo e(route('expenses.show', $expense)); ?>">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/expenses/approve.blade.php ENDPATH**/ ?>