<?php $__env->startSection('title', 'Executive Dashboard | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<style>
.kpi-card { background: linear-gradient(135deg, var(--card-bg, #fff) 0%, #fafbfc 100%); }
.kpi-value { font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px; }
.trend-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; }
.trend-up { background-color: rgba(25, 135, 84, 0.15); color: #157347; }
.trend-down { background-color: rgba(220, 53, 69, 0.15); color: #842029; }
.section-header { font-size: 0.9375rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--bs-body-color); }
</style>

<div class="mb-9">
    <!-- Page Header -->
    <div class="row align-items-center justify-content-between mb-5">
        <div class="col">
            <h1 class="mb-1 h3">Executive Dashboard</h1>
            <p class="text-body-secondary mb-0">Real-time organizational performance overview</p>
        </div>
        <div class="col-auto">
            <span class="text-body-secondary small">📅 As of <?php echo e(date('M d, Y')); ?></span>
        </div>
    </div>

    <!-- KPI Cards - Compact Style -->
    <div class="row g-2 mb-3">
        <!-- Members -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <div class="card" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Members</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;"><?php echo e($activeMembers); ?> active</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0;"><?php echo e($totalMembers); ?></h6>
                        <?php if($memberGrowth !== 0): ?>
                            <span class="badge" style="font-size: 0.65rem;" <?php echo e($memberGrowth > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?>><?php echo e($memberGrowth > 0 ? '↑' : '↓'); ?> <?php echo e(number_format(abs($memberGrowth), 2)); ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <div class="card" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Share Capital</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;"><?php echo e(number_format($allocatedShares)); ?> allocated</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0;"><?php echo e(number_format($totalShares)); ?></h6>
                        <span class="badge bg-light text-dark" style="font-size: 0.65rem; white-space: nowrap;"><?php echo e(number_format($availableShares)); ?> free</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <div class="card" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Monthly Deposits</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">This month</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0; color: #0d6efd;">৳<?php echo e(number_format($monthlyDeposits, 0)); ?></h6>
                        <?php if($depositChange !== 0): ?>
                            <span class="badge" style="font-size: 0.65rem;" <?php echo e($depositChange > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?>><?php echo e($depositChange > 0 ? '↑' : '↓'); ?> <?php echo e(number_format(abs($depositChange), 2)); ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <div class="card" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Investments</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;"><?php echo e($activeInvestments); ?> active</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0; color: #ffc107;">৳<?php echo e(number_format($totalInvested, 0)); ?></h6>
                        <span class="badge bg-warning-subtle text-warning" style="font-size: 0.65rem; white-space: nowrap;">+৳<?php echo e(number_format($investmentReturns, 0)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <div class="card" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Monthly Expenses</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">This month</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: 0.25rem;">
                        <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0; color: #dc3545;">৳<?php echo e(number_format($monthlyExpenses, 0)); ?></h6>
                        <?php if($expenseChange !== 0): ?>
                            <span class="badge" style="font-size: 0.65rem;" <?php echo e($expenseChange > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'); ?>><?php echo e($expenseChange > 0 ? '↑' : '↓'); ?> <?php echo e(number_format(abs($expenseChange), 2)); ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
            <div class="card" style="border-left: 4px solid <?php echo e($netPosition >= 0 ? '#198754' : '#dc3545'); ?> !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem; min-height: auto;">
                    <div class="d-flex justify-content-between align-items-start mb-0" style="gap: 0.5rem;">
                        <small class="text-body-secondary fw-semibold" style="font-size: 0.75rem;">Net Position</small>
                        <small class="text-body-secondary" style="font-size: 0.75rem;">Balance</small>
                    </div>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.75rem; line-height: 1.2; margin: 0.25rem 0; color: <?php echo e($netPosition >= 0 ? '#198754' : '#dc3545'); ?>;">৳<?php echo e(number_format(abs($netPosition), 0)); ?></h6>
                </div>
            </div>
        </div>
    </div>
    <hr class="">
    <!-- Deposit Analytics Section - CRM Style -->
    <div class="row g-3 mb-5">
        <!-- Member Deposits Card -->
        <div class="col-sm-12 col-md-4">
            <a href="<?php echo e(route('deposit-status')); ?>" class="card h-100 text-decoration-none" style="border-left: 4px solid #6f42c1 !important; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow=''">
                <?php
                    $memberTotal = $depositsPaid + $depositsUnpaid;
                    $amountRate = $totalDepositExpected > 0 ? min($monthlyDeposits / $totalDepositExpected * 100, 100) : 0;
                ?>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <small class="text-body-secondary d-block fw-semibold mb-2">Member Deposits</small>
                            <h2 class="mb-1 text-primary fw-bold"><?php echo e($depositsPaid); ?>/<?php echo e($memberTotal); ?> <span class="fs-5 fw-normal text-body-secondary">Members</span></h2>
                            <small class="text-body-secondary">Paid this month</small>
                        </div>
                        <?php if($depositChange != 0): ?>
                            <span class="badge <?php echo e($depositChange > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis'); ?>">
                                <?php echo e($depositChange > 0 ? '↑' : '↓'); ?> <?php echo e(number_format(abs($depositChange), 1)); ?>%
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-body-secondary">Amount collected</small>
                        <small class="fw-semibold">৳<?php echo e(number_format($monthlyDeposits, 0)); ?> / ৳<?php echo e(number_format($totalDepositExpected, 0)); ?></small>
                    </div>
                    <div class="mt-1" style="height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden;">
                        <div style="width: <?php echo e($amountRate); ?>%; height: 100%; background: #0d6efd;"></div>
                    </div>

                    <div class="mt-3 d-flex gap-5">
                        <div>
                            <small class="text-success d-block">✓ Paid</small>
                            <span class="text-success fw-bold display-6"><?php echo e($depositsPaid); ?></span>
                        </div>
                        <div>
                            <small class="text-danger d-block">✗ Unpaid</small>
                            <span class="text-danger fw-bold display-6"><?php echo e($depositsUnpaid); ?></span>
                        </div>
                    </div>

                    <?php if($depositsUnpaid > 0): ?>
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-body-secondary d-block fw-semibold mb-2">Pending this month</small>
                            <ul class="list-unstyled mb-0">
                                <?php $__currentLoopData = $pendingMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="d-flex align-items-center justify-content-between py-1">
                                        <span class="fs-9 text-body text-truncate"><?php echo e($pending->name); ?></span>
                                        <span class="badge bg-danger-subtle text-danger-emphasis fs-10">Unpaid</span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php if($depositsUnpaid > $pendingMembers->count()): ?>
                                <small class="text-body-tertiary d-block mt-1">+ <?php echo e($depositsUnpaid - $pendingMembers->count()); ?> more</small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-auto pt-3 border-top text-end">
                        <small class="text-primary fw-semibold">View Details <span class="fas fa-arrow-right fa-xs ms-1"></span></small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Last 10 Deposits List -->
        <div class="col-sm-12 col-md-3">
            <div class="border-bottom border-translucent">
                <h5 class="pb-4 border-bottom border-translucent">Last 10 Deposits</h5>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $lastDeposits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depositor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-normal fs-9"><?php echo e($depositor['name']); ?></span>
                                <div class="text-end">
                                    <span class="fw-normal fs-9">৳<?php echo e(number_format($depositor['amount'], 0)); ?></span>
                                    <p class="mb-0 fs-9 text-body-tertiary"><?php echo e($depositor['date']); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item bg-transparent text-body-tertiary fs-9 py-2">
                            No deposits yet
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-sm-12 col-md-5 d-flex flex-column">
            <h3>Deposit Expected vs Received (Last 6 Months)</h3>
            <p class="text-body-tertiary mb-3">Expected deposits (members × shares × face value) vs actual amount received</p>
            <div class="flex-grow-1 position-relative" style="min-height: 250px;">
                <canvas id="depositExpectedVsReceivedChart"></canvas>
            </div>
        </div>
    </div>


    <!-- Financial Charts -->
    <div class="row g-3 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">💰 Deposit Trend</h6>
                    <canvas id="depositChart" height="80"></canvas>
                    <small class="text-body-secondary d-block mt-2">Last 12 months</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">📊 Expense Trend</h6>
                    <canvas id="expenseChart" height="80"></canvas>
                    <small class="text-body-secondary d-block mt-2">Last 12 months</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions, Organization, New Members & Top Shareholders -->
    <div class="row g-3 mb-5">
        <!-- Quick Actions -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="section-header mb-3">⚙️ Quick Actions</h6>
                    <div class="row g-2">
                        <div class="col-auto"><a href="<?php echo e(route('deposit-status')); ?>" class="btn btn-sm btn-warning" style="font-size: 0.8125rem;"><span class="fas fa-check-double me-1"></span>Check Deposits</a></div>
                        <div class="col-auto"><a href="<?php echo e(route('expenses.create')); ?>" class="btn btn-sm btn-primary" style="font-size: 0.8125rem;"><span class="fas fa-plus me-1"></span>Add Expense</a></div>
                        <div class="col-auto"><a href="<?php echo e(route('investments.create')); ?>" class="btn btn-sm btn-success" style="font-size: 0.8125rem;"><span class="fas fa-plus me-1"></span>Create Investment</a></div>
                        <div class="col-auto"><a href="<?php echo e(route('members.index')); ?>" class="btn btn-sm btn-info" style="font-size: 0.8125rem;"><span class="fas fa-users me-1"></span>View Members</a></div>
                        <div class="col-auto"><a href="<?php echo e(route('accounting.reports.dashboard')); ?>" class="btn btn-sm btn-outline-secondary" style="font-size: 0.8125rem;"><span class="fas fa-chart-bar me-1"></span>View Reports</a></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organization -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-primary-subtle h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="section-header mb-2">Organization</h6>
                            <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #04396c;"><?php echo e($totalMembers); ?></p>
                            <small class="text-body-secondary">Members registered</small>
                        </div>
                        <span class="fas fa-users fa-2x opacity-25" style="color: #04396c;"></span>
                    </div>
                    <a href="<?php echo e(route('members.index')); ?>" class="btn btn-sm btn-primary mt-3" style="font-size: 0.8125rem;">View All →</a>
                </div>
            </div>
        </div>

        <!-- New Members -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="section-header mb-3">✨ New Members</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Member</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Joined</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><small><?php echo e($member->name); ?></small></td>
                                    <td class="text-end"><small class="text-body-secondary"><?php echo e($member->created_at->format('M d, Y')); ?></small></td>
                                    <td class="text-end"><span class="badge bg-success-subtle text-success-emphasis">Active</span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="text-muted">
                                    <td colspan="3" class="text-center py-4"><small>No new members</small></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Shareholders -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="section-header mb-3">👑 Top Shareholders</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Member</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Shares</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">% Ownership</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $topShareholders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><small><?php echo e($sh['name']); ?></small></td>
                                    <td class="text-end"><strong><?php echo e(number_format($sh['shares'])); ?></strong></td>
                                    <td class="text-end"><span class="badge bg-primary-subtle text-primary-emphasis"><?php echo e(number_format($sh['percentage'], 1)); ?>%</span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="text-muted">
                                    <td colspan="3" class="text-center py-4"><small>No shareholders</small></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Section -->
    <div class="row g-3 mb-5">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-4">💼 Investment Allocation</h6>
                    <canvas id="investmentChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="section-header mb-3">📈 Investment Performance</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Investment</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Capital</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Returns</th>
                                    <th class="text-end" style="font-size: 0.8125rem; font-weight: 600; color: #6c757d;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $investmentPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><span class="small"><?php echo e($inv['name']); ?></span></td>
                                    <td class="text-end"><small class="text-body-secondary">৳<?php echo e(number_format($inv['invested'], 0)); ?></small></td>
                                    <td class="text-end"><span class="badge bg-success-subtle text-success-emphasis">+৳<?php echo e(number_format($inv['returns'], 0)); ?></span></td>
                                    <td class="text-end"><strong>৳<?php echo e(number_format($inv['invested'] + $inv['returns'], 0)); ?></strong></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="text-muted">
                                    <td colspan="4" class="text-center py-4"><small>No active investments</small></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Health -->
    <div class="row g-3 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-success-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">💵 Cash Available</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #0b5345;">৳<?php echo e(number_format($cashAvailable, 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-primary-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">📥 Total Deposits</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #04396c;">৳<?php echo e(number_format($totalDeposits, 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-warning-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">💼 Total Invested</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #664d03;">৳<?php echo e(number_format($totalInvested, 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-success-subtle">
                <div class="card-body">
                    <small class="text-body-secondary d-block mb-1">🎁 Total Returns</small>
                    <p class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #0b5345;">৳<?php echo e(number_format($totalReturns, 0)); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body">
            <h6 class="section-header mb-4">⚡ Recent Activity</h6>
            <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="d-flex mb-4 pb-4 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                <div class="timeline-marker me-3 flex-shrink-0" style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;background-color:<?php echo e($activity['type'] === 'deposit' ? '#d4edda' : ($activity['type'] === 'expense' ? '#f8d7da' : '#d1ecf1')); ?>;">
                    <span class="fas fa-<?php echo e($activity['icon']); ?> fa-sm <?php echo e($activity['type'] === 'deposit' ? 'text-success' : ($activity['type'] === 'expense' ? 'text-danger' : 'text-info')); ?>"></span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 small fw-semibold"><?php echo e($activity['title']); ?></h6>
                    <small class="text-body-secondary d-block mb-1"><?php echo e($activity['description']); ?></small>
                    <small class="text-muted"><?php echo e($activity['date']->format('M d, Y H:i')); ?></small>
                </div>
                <div class="text-end flex-shrink-0">
                    <p class="mb-0 fw-bold <?php echo e($activity['amount'] > 0 ? 'text-success' : 'text-danger'); ?>"><?php echo e($activity['amount'] > 0 ? '+' : ''); ?>৳<?php echo e(number_format(abs($activity['amount']), 0)); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted text-center py-5"><small>No recent activity</small></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Actions -->
    <div class="row g-3 mb-5">
        <?php if($pendingExpenses > 0): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-warning-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="section-header mb-2">Pending Expenses</h6>
                            <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #664d03;"><?php echo e($pendingExpenses); ?></p>
                        </div>
                        <span class="fas fa-receipt fa-2x opacity-25" style="color: #664d03;"></span>
                    </div>
                    <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-sm btn-warning mt-3" style="font-size: 0.8125rem;">Review Now →</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($pendingInvestments > 0): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm bg-info-subtle">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h6 class="section-header mb-2">Pending Investments</h6>
                            <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #055160;"><?php echo e($pendingInvestments); ?></p>
                        </div>
                        <span class="fas fa-chart-line fa-2x opacity-25" style="color: #055160;"></span>
                    </div>
                    <a href="<?php echo e(route('investments.index')); ?>" class="btn btn-sm btn-info mt-3" style="font-size: 0.8125rem;">Review Now →</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const depositCtx = document.getElementById('depositChart').getContext('2d');
    new Chart(depositCtx, {type: 'line', data: {labels: <?php echo json_encode($depositTrend['months'], 15, 512) ?>, datasets: [{label: 'Monthly Deposits', data: <?php echo json_encode($depositTrend['totals'], 15, 512) ?>, borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}});

    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {type: 'line', data: {labels: <?php echo json_encode($expenseTrend['months'], 15, 512) ?>, datasets: [{label: 'Monthly Expenses', data: <?php echo json_encode($expenseTrend['totals'], 15, 512) ?>, borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true}}}});

    const investmentCtx = document.getElementById('investmentChart').getContext('2d');
    new Chart(investmentCtx, {type: 'doughnut', data: {labels: <?php echo json_encode(array_column($investmentDistribution, 'type'), 512) ?>, datasets: [{data: <?php echo json_encode(array_column($investmentDistribution, 'amount'), 512) ?>, backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#fd7e14', '#6f42c1', '#20c997'], borderColor: '#fff', borderWidth: 2}]}, options: {responsive: true, maintainAspectRatio: true, plugins: {legend: {position: 'bottom'}}}});

    const depositExpectedCtx = document.getElementById('depositExpectedVsReceivedChart').getContext('2d');
    new Chart(depositExpectedCtx, {type: 'bar', data: {labels: <?php echo json_encode($depositExpectedVsReceived['months'], 15, 512) ?>, datasets: [{label: 'Expected', data: <?php echo json_encode($depositExpectedVsReceived['expected'], 15, 512) ?>, backgroundColor: '#0d6efd', borderColor: '#0d6efd', borderWidth: 1, borderRadius: 4}, {label: 'Received', data: <?php echo json_encode($depositExpectedVsReceived['received'], 15, 512) ?>, backgroundColor: '#198754', borderColor: '#198754', borderWidth: 1, borderRadius: 4}]}, options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {position: 'top', labels: {usePointStyle: true, padding: 15}}}, scales: {y: {beginAtZero: true}}}});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/dashboard/index.blade.php ENDPATH**/ ?>