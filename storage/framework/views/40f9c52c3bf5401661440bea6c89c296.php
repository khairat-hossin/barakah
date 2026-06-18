<?php $__env->startSection('title', 'Chart of Accounts | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Chart of Accounts</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Chart of Accounts</h2>
            <p class="text-body-secondary">Manage your general ledger account structure</p>
        </div>
        <div class="col-auto">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage accounting')): ?>
                <a href="<?php echo e(route('accounting.chart-of-accounts.create')); ?>" class="btn btn-primary">
                    <span class="fas fa-plus me-2"></span>New Account
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Account Type Filters -->
    <div class="row mb-3">
        <div class="col">
            <div class="btn-group" role="group">
                <a href="<?php echo e(route('accounting.chart-of-accounts.index')); ?>" class="btn btn-outline-secondary <?php echo e(!request('type') ? 'active' : ''); ?>">
                    All
                </a>
                <a href="<?php echo e(route('accounting.chart-of-accounts.index', ['type' => 'ASSET'])); ?>" class="btn btn-outline-secondary <?php echo e(request('type') === 'ASSET' ? 'active' : ''); ?>">
                    Assets
                </a>
                <a href="<?php echo e(route('accounting.chart-of-accounts.index', ['type' => 'LIABILITY'])); ?>" class="btn btn-outline-secondary <?php echo e(request('type') === 'LIABILITY' ? 'active' : ''); ?>">
                    Liabilities
                </a>
                <a href="<?php echo e(route('accounting.chart-of-accounts.index', ['type' => 'EQUITY'])); ?>" class="btn btn-outline-secondary <?php echo e(request('type') === 'EQUITY' ? 'active' : ''); ?>">
                    Equity
                </a>
                <a href="<?php echo e(route('accounting.chart-of-accounts.index', ['type' => 'INCOME'])); ?>" class="btn btn-outline-secondary <?php echo e(request('type') === 'INCOME' ? 'active' : ''); ?>">
                    Income
                </a>
                <a href="<?php echo e(route('accounting.chart-of-accounts.index', ['type' => 'EXPENSE'])); ?>" class="btn btn-outline-secondary <?php echo e(request('type') === 'EXPENSE' ? 'active' : ''); ?>">
                    Expenses
                </a>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="fw-semibold">CODE</th>
                        <th class="fw-semibold">ACCOUNT NAME</th>
                        <th class="fw-semibold">TYPE</th>
                        <th class="fw-semibold">BALANCE</th>
                        <th class="fw-semibold">STATUS</th>
                        <th class="fw-semibold">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark font-monospace"><?php echo e($account->code); ?></span>
                        </td>
                        <td>
                            <strong><?php echo e($account->name); ?></strong>
                            <?php if($account->parent): ?>
                                <br><small class="text-body-secondary">Parent: <?php echo e($account->parent->name); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($account->account_type === 'ASSET'): ?>
                                <span class="badge bg-primary"><?php echo e($account->account_type); ?></span>
                            <?php elseif($account->account_type === 'LIABILITY'): ?>
                                <span class="badge bg-danger"><?php echo e($account->account_type); ?></span>
                            <?php elseif($account->account_type === 'EQUITY'): ?>
                                <span class="badge bg-info"><?php echo e($account->account_type); ?></span>
                            <?php elseif($account->account_type === 'INCOME'): ?>
                                <span class="badge bg-success"><?php echo e($account->account_type); ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning"><?php echo e($account->account_type); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="font-monospace">
                                <?php
                                    try {
                                        $balance = $account->getBalance();
                                        echo number_format($balance, 2);
                                    } catch (\Exception $e) {
                                        echo '<span class="text-body-secondary">-</span>';
                                    }
                                ?>
                            </span>
                        </td>
                        <td>
                            <?php if($account->is_active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('accounting.chart-of-accounts.show', $account)); ?>" class="btn btn-sm btn-outline-primary" title="View">
                                <span class="fas fa-eye"></span>
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $account)): ?>
                                <a href="<?php echo e(route('accounting.chart-of-accounts.edit', $account)); ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <span class="fas fa-edit"></span>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $account)): ?>
                                <form action="<?php echo e(route('accounting.chart-of-accounts.destroy', $account)); ?>" method="POST" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                        <span class="fas fa-trash"></span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <p class="text-body-secondary mb-2">No accounts found</p>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage accounting')): ?>
                                <a href="<?php echo e(route('accounting.chart-of-accounts.create')); ?>" class="btn btn-sm btn-primary">Create the first account</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if($accounts->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($accounts->links()); ?>

        </div>
    <?php endif; ?>

    <!-- Account Summary -->
    <div class="row mt-5">
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Total Assets</h6>
                    <p class="card-text fs-5 fw-bold text-primary">
                        <?php echo e(number_format($totalAssets, 2)); ?>

                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Total Liabilities</h6>
                    <p class="card-text fs-5 fw-bold text-danger">
                        <?php echo e(number_format($totalLiabilities, 2)); ?>

                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Total Equity</h6>
                    <p class="card-text fs-5 fw-bold text-info">
                        <?php echo e(number_format($totalEquity, 2)); ?>

                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Active Accounts</h6>
                    <p class="card-text fs-5 fw-bold text-success">
                        <?php echo e($accounts->count()); ?>

                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/accounting/chart-of-accounts/index.blade.php ENDPATH**/ ?>