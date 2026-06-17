<?php $__env->startSection('title', 'Expense Details | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('expenses.index')); ?>">Expenses</a></li>
        <li class="breadcrumb-item active"><?php echo e($expense->expense_number); ?></li>
    </ol>
</nav>

<div class="mb-9">
    <!-- Header -->
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <div class="mb-3">
                <h2 class="mb-2"><?php echo e($expense->expense_number); ?></h2>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge <?php if($expense->status === 'draft'): ?> badge-secondary <?php elseif($expense->status === 'pending'): ?> badge-warning <?php elseif($expense->status === 'approved'): ?> badge-success <?php else: ?> badge-info <?php endif; ?>">
                        <?php echo e(ucfirst($expense->status)); ?>

                    </span>
                    <span class="text-body-secondary"><?php echo e($expense->category->name); ?></span>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <?php if($expense->status === 'draft'): ?>
                    <a href="<?php echo e(route('expenses.edit', $expense)); ?>" class="btn btn-primary">
                        <span class="fas fa-edit me-2"></span>Edit
                    </a>
                    <form action="<?php echo e(route('expenses.destroy', $expense)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this expense?')">
                            <span class="fas fa-trash me-2"></span>Delete
                        </button>
                    </form>
                <?php elseif($expense->status === 'pending'): ?>
                    <a href="<?php echo e(route('expenses.approve', $expense)); ?>" class="btn btn-success">
                        <span class="fas fa-check me-2"></span>Approve
                    </a>
                <?php elseif($expense->status === 'approved'): ?>
                    <form action="<?php echo e(route('expenses.mark-paid', $expense)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <button type="submit" class="btn btn-info" onclick="return confirm('Mark this expense as paid?')">
                            <span class="fas fa-money-bill me-2"></span>Mark as Paid
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-2">Amount</small>
                    <h4 class="mb-0">৳ <?php echo e(number_format($expense->amount, 2)); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-2">Expense Date</small>
                    <h6 class="mb-0"><?php echo e($expense->expense_date->format('d M Y')); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-2">Fund Source</small>
                    <h6 class="mb-0"><?php echo e(ucfirst($expense->fund_source)); ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-2">Payment Method</small>
                    <h6 class="mb-0"><?php echo e(ucfirst(str_replace('_', ' ', $expense->payment_method))); ?></h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-underline mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" href="#expenseInfo" data-bs-toggle="tab" role="tab">Information</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="#attachments" data-bs-toggle="tab" role="tab">Attachments</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="#statusHistory" data-bs-toggle="tab" role="tab">Status History</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="#auditTrail" data-bs-toggle="tab" role="tab">Audit Trail</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Tab 1: Information -->
        <div class="tab-pane fade show active" id="expenseInfo" role="tabpanel">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-body-highlight mb-3">Title</h6>
                            <p class="mb-0"><?php echo e($expense->title); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-body-highlight mb-3">Category</h6>
                            <p class="mb-0"><?php echo e($expense->category->name); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-body-highlight mb-3">Created By</h6>
                            <p class="mb-0"><?php echo e($expense->creator->name); ?></p>
                        </div>
                        <?php if($expense->approver): ?>
                        <div class="col-md-6">
                            <h6 class="text-body-highlight mb-3">Approved By</h6>
                            <p class="mb-0"><?php echo e($expense->approver->name); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if($expense->member): ?>
                        <div class="col-md-6">
                            <h6 class="text-body-highlight mb-3">Member</h6>
                            <p class="mb-0"><a href="<?php echo e(route('members.show', $expense->member)); ?>"><?php echo e($expense->member->name); ?></a></p>
                        </div>
                        <?php endif; ?>
                        <?php if($expense->project): ?>
                        <div class="col-md-6">
                            <h6 class="text-body-highlight mb-3">Project</h6>
                            <p class="mb-0"><?php echo e($expense->project->name); ?></p>
                        </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <h6 class="text-body-highlight mb-3">Description</h6>
                            <p class="mb-0"><?php echo e($expense->description); ?></p>
                        </div>
                        <?php if($expense->notes): ?>
                        <div class="col-12">
                            <h6 class="text-body-highlight mb-3">Notes</h6>
                            <p class="mb-0"><?php echo e($expense->notes); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Attachments -->
        <div class="tab-pane fade" id="attachments" role="tabpanel">
            <?php if($expense->status !== 'paid'): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Add Attachment</h6>
                    <form action="<?php echo e(route('expenses.attachment-store', $expense)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <select class="form-select" name="attachment_type" required>
                                    <option value="">Select Type...</option>
                                    <?php $__currentLoopData = $attachmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $type))); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input class="form-control" type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" />
                            </div>
                            <div class="col-12">
                                <button class="btn btn-sm btn-primary" type="submit">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if($expense->attachments->count() > 0): ?>
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-body-tertiary">
                            <tr>
                                <th class="fw-semibold">Type</th>
                                <th class="fw-semibold">File Name</th>
                                <th class="fw-semibold">Size</th>
                                <th class="fw-semibold">Uploaded By</th>
                                <th class="fw-semibold">Date</th>
                                <th class="fw-semibold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $expense->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $attachment->attachment_type))); ?>

                                    </span>
                                </td>
                                <td><?php echo e($attachment->file_name); ?></td>
                                <td><?php echo e(number_format($attachment->file_size / 1024, 2)); ?> KB</td>
                                <td><?php echo e($attachment->uploader->name); ?></td>
                                <td><?php echo e($attachment->created_at->format('d M Y H:i')); ?></td>
                                <td class="text-end">
                                    <div class="gap-1 d-flex justify-content-end">
                                        <a href="<?php echo e(route('expenses.attachment-download', $attachment)); ?>" class="btn btn-sm btn-outline-primary" title="Download">
                                            <span class="fas fa-download"></span>
                                        </a>
                                        <?php if($expense->status !== 'paid'): ?>
                                        <form action="<?php echo e(route('expenses.attachment-delete', $attachment)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this attachment?')">
                                                <span class="fas fa-trash"></span>
                                            </button>
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
            <?php else: ?>
            <div class="alert alert-info mb-0">
                <span class="fas fa-info-circle me-2"></span>No attachments uploaded yet.
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab 3: Status History -->
        <div class="tab-pane fade" id="statusHistory" role="tabpanel">
            <?php if($expense->statusHistories->count() > 0): ?>
            <div class="timeline">
                <?php $__currentLoopData = $expense->statusHistories->sortByDesc('changed_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($history->status_from)); ?></span>
                                    <span class="fas fa-arrow-right mx-2"></span>
                                    <span class="badge bg-success"><?php echo e(ucfirst($history->status_to)); ?></span>
                                </h6>
                                <small class="text-body-secondary">Changed by <?php echo e($history->changedBy->name); ?></small>
                            </div>
                            <small class="text-body-secondary"><?php echo e($history->changed_at->format('d M Y H:i')); ?></small>
                        </div>
                        <?php if($history->notes): ?>
                        <p class="mt-2 mb-0 text-body-secondary"><?php echo e($history->notes); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info mb-0">
                <span class="fas fa-info-circle me-2"></span>No status changes yet.
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab 4: Audit Trail -->
        <div class="tab-pane fade" id="auditTrail" role="tabpanel">
            <?php if($auditLogs->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="fw-semibold">Action</th>
                            <th class="fw-semibold">User</th>
                            <th class="fw-semibold">Changes</th>
                            <th class="fw-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $log->action_type))); ?>

                                </span>
                            </td>
                            <td><?php echo e($log->user->name ?? 'System'); ?></td>
                            <td>
                                <?php if($log->old_value && $log->new_value): ?>
                                    <small class="text-muted">Changed</small>
                                <?php elseif($log->new_value): ?>
                                    <small class="text-muted">New</small>
                                <?php else: ?>
                                    <small class="text-muted">Deleted</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($log->timestamp->format('d M Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info mb-0">
                <span class="fas fa-info-circle me-2"></span>No audit history found.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/expenses/show.blade.php ENDPATH**/ ?>