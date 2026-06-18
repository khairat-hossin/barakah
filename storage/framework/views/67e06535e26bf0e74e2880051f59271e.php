<?php $__env->startSection('title', 'Deposit Status | Barakah'); ?>

<?php $__env->startSection('content'); ?>
<style>
.status-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.75rem; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; }
.status-deposited { background-color: rgba(25, 135, 84, 0.15); color: #157347; }
.status-pending { background-color: rgba(220, 53, 69, 0.15); color: #842029; }
.member-row { border-bottom: 1px solid #e9ecef; padding: 1rem 0; }
.member-row:last-child { border-bottom: none; }
.contact-info { font-size: 0.8125rem; }
</style>

<div class="mb-9">
    <!-- Header -->
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h1 class="mb-1 h3">Deposit Status Tracker</h1>
            <p class="text-body-secondary mb-0">Monitor member deposits for <?php echo e(now()->format('F Y')); ?></p>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <span class="fas fa-sync-alt me-1"></span>Refresh
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-body-secondary small fw-semibold mb-2">Total Members</h6>
                    <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #0d6efd;"><?php echo e($members->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success-subtle">
                <div class="card-body">
                    <h6 class="text-body-secondary small fw-semibold mb-2">✓ Deposited This Month</h6>
                    <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #0b5345;"><?php echo e($deposited); ?></p>
                    <small class="text-success"><?php echo e($members->count() > 0 ? round(($deposited / $members->count()) * 100) : 0); ?>% completion</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger-subtle">
                <div class="card-body">
                    <h6 class="text-body-secondary small fw-semibold mb-2">✗ Pending Deposits</h6>
                    <p class="mb-0" style="font-size: 2rem; font-weight: 700; color: #842029;"><?php echo e($pending); ?></p>
                    <small class="text-danger">Need to notify</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Filter by Status</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="filter" id="filter-all" value="all" checked onchange="filterTable()">
                        <label class="btn btn-outline-secondary" for="filter-all">All Members (<?php echo e($members->count()); ?>)</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-deposited" value="deposited" onchange="filterTable()">
                        <label class="btn btn-outline-success" for="filter-deposited">✓ Deposited (<?php echo e($deposited); ?>)</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-pending" value="pending" onchange="filterTable()">
                        <label class="btn btn-outline-danger" for="filter-pending">✗ Pending (<?php echo e($pending); ?>)</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" class="form-control" id="search-input" placeholder="Search by name, code, phone, or email..." onkeyup="filterTable()">
                </div>
            </div>
        </div>
    </div>

    <!-- Member List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div id="member-list">
                <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="member-row" data-status="<?php echo e($member['status']); ?>">
                    <div class="row align-items-center">
                        <!-- Member Info -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-m me-3">
                                    <span class="avatar-initials rounded-circle <?php echo e($member['has_deposited'] ? 'bg-success' : 'bg-danger'); ?> text-white fw-bold">
                                        <?php echo e(strtoupper(substr($member['name'], 0, 2))); ?>

                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-semibold"><?php echo e($member['name']); ?></h6>
                                    <small class="text-body-secondary">Code: <?php echo e($member['code']); ?></small>
                                    <div class="contact-info mt-1">
                                        <?php if($member['phone'] !== 'N/A'): ?>
                                            <span class="badge bg-light text-dark"><?php echo e($member['phone']); ?></span>
                                        <?php endif; ?>
                                        <?php if($member['email'] !== 'N/A'): ?>
                                            <span class="badge bg-light text-dark"><?php echo e($member['email']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Amount -->
                        <div class="col-md-3">
                            <div class="mb-2">
                                <?php if($member['has_deposited']): ?>
                                    <span class="status-badge status-deposited">
                                        <span class="fas fa-check-circle"></span>
                                        Deposited
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">
                                        <span class="fas fa-exclamation-circle"></span>
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if($member['has_deposited']): ?>
                                <div><small class="text-body-secondary">Amount:</small></div>
                                <div><strong>₱<?php echo e(number_format($member['amount_deposited'], 2)); ?></strong></div>
                            <?php else: ?>
                                <div><small class="text-danger">No deposit yet</small></div>
                            <?php endif; ?>
                        </div>

                        <!-- Details -->
                        <div class="col-md-3">
                            <div class="mb-2"><small class="text-body-secondary">Last Deposit:</small></div>
                            <div><small><?php echo e($member['last_deposit_date']); ?></small></div>
                            <div class="mt-2"><small class="text-body-secondary">Shares:</small></div>
                            <div><strong><?php echo e($member['shares']); ?></strong></div>
                        </div>

                        <!-- Actions -->
                        <div class="col-md-2 text-end">
                            <?php if(!$member['has_deposited']): ?>
                                <div class="btn-group-vertical w-100" role="group">
                                    <?php if($member['phone'] !== 'N/A'): ?>
                                        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $member['phone'])); ?>" target="_blank" class="btn btn-sm btn-success mb-1" title="Send WhatsApp reminder">
                                            <span class="fas fa-whatsapp"></span> Remind
                                        </a>
                                    <?php endif; ?>
                                    <?php if($member['email'] !== 'N/A'): ?>
                                        <a href="mailto:<?php echo e($member['email']); ?>?subject=Monthly%20Deposit%20Reminder&body=Dear%20<?php echo e(urlencode($member['name'])); ?>,%0A%0AThis%20is%20a%20reminder%20to%20submit%20your%20monthly%20deposit%20for%20<?php echo e(now()->format('F Y')); ?>." class="btn btn-sm btn-info" title="Send email reminder">
                                            <span class="fas fa-envelope"></span> Email
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success-emphasis">✓ Complete</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5">
                    <p class="text-muted"><span class="fas fa-inbox me-2"></span>No members found</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <?php if($pending > 0): ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">📢 Bulk Reminders</h6>
            <p class="text-body-secondary small mb-3">Send reminders to all <?php echo e($pending); ?> members who haven't deposited yet:</p>
            <div class="d-flex gap-2">
                <?php
                    $pendingMembers = $members->where('has_deposited', false);
                    $pendingPhones = $pendingMembers->filter(fn($m) => $m['phone'] !== 'N/A')->pluck('phone')->join(',');
                    $pendingEmails = $pendingMembers->filter(fn($m) => $m['email'] !== 'N/A')->pluck('email')->join(',');
                ?>
                <?php if($pendingPhones): ?>
                    <a href="https://wa.me/?text=Dear%20Members,%0A%0AThis%20is%20a%20reminder%20to%20submit%20your%20monthly%20deposit%20for%20<?php echo e(now()->format('F Y')); ?>." class="btn btn-sm btn-success">
                        <span class="fas fa-whatsapp me-1"></span>Send WhatsApp to All
                    </a>
                <?php endif; ?>
                <?php if($pendingEmails): ?>
                    <a href="mailto:<?php echo e($pendingEmails); ?>?subject=Monthly%20Deposit%20Reminder%20-%20<?php echo e(now()->format('F Y')); ?>&body=Dear%20Members,%0A%0AThis%20is%20a%20reminder%20to%20submit%20your%20monthly%20deposit%20for%20<?php echo e(now()->format('F Y')); ?>." class="btn btn-sm btn-info">
                        <span class="fas fa-envelope me-1"></span>Send Email to All
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function filterTable() {
    const filterValue = document.querySelector('input[name="filter"]:checked').value;
    const searchValue = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('.member-row');

    rows.forEach(row => {
        const status = row.dataset.status;
        const text = row.textContent.toLowerCase();

        let statusMatch = filterValue === 'all' || status === filterValue;
        let searchMatch = searchValue === '' || text.includes(searchValue);

        row.style.display = (statusMatch && searchMatch) ? '' : 'none';
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.phoenix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/dashboard/deposit-status.blade.php ENDPATH**/ ?>