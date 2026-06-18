@extends('layouts.phoenix')

@section('title', 'Deposit Status | Barakah')

@section('content')
<style>
.status-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; }
.status-deposited { background-color: rgba(25, 135, 84, 0.15); color: #157347; }
.status-pending { background-color: rgba(220, 53, 69, 0.15); color: #842029; }
.member-row { border-bottom: 1px solid #e9ecef; padding: 0.4rem 0; }
.member-row:last-child { border-bottom: none; }
.contact-info { font-size: 0.75rem; }
.compact-card { padding: 1rem 1.25rem; }
.compact-card .card-body { padding: 0.75rem 0; }
.member-row h6 { margin-bottom: 0 !important; font-size: 0.9rem; }
.member-row small { line-height: 1.2; }
</style>

<div class="mb-6">
    <!-- Header -->
    <div class="row align-items-center justify-content-between mb-2">
        <div class="col">
            <h2 class="mb-0 h5">Deposit Status Tracker</h2>
            <p class="text-body-secondary mb-0 small">Monitor member deposits for {{ now()->format('F Y') }}</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                <span class="fas fa-sync-alt me-1"></span>Refresh
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">Total Members</p>
                            <h4 class="mb-0">{{ $members->count() }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-success rounded-pill">All</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">✓ Deposited This Month</p>
                            <h4 class="mb-0">{{ $deposited }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-success rounded-pill">{{ $members->count() > 0 ? round(($deposited / $members->count()) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card summary-card bg-body-highlight border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-body-secondary fs-9 mb-2">✗ Pending Deposits</p>
                            <h4 class="mb-0">{{ $pending }}</h4>
                        </div>
                        <span class="badge badge-phoenix badge-phoenix-danger rounded-pill">Action</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3 compact-card">
        <div class="card-body" style="padding: 0.75rem 0;">
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold mb-1" style="font-size: 0.8rem;">Filter by Status</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="filter" id="filter-all" value="all" checked onchange="filterTable()">
                        <label class="btn btn-outline-secondary btn-sm" for="filter-all" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">All ({{ $members->count() }})</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-deposited" value="deposited" onchange="filterTable()">
                        <label class="btn btn-outline-success btn-sm" for="filter-deposited" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">✓ Deposited ({{ $deposited }})</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-pending" value="pending" onchange="filterTable()">
                        <label class="btn btn-outline-danger btn-sm" for="filter-pending" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">✗ Pending ({{ $pending }})</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold mb-1" style="font-size: 0.8rem;">Search</label>
                    <input type="text" class="form-control form-control-sm" id="search-input" placeholder="Search by name, code, phone, or email..." onkeyup="filterTable()" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                </div>
            </div>
        </div>
    </div>

    <!-- Member List -->
    <div class="card border-0 shadow-sm compact-card">
        <div class="card-body" style="padding: 0.75rem 0;">
            <div id="member-list">
                @forelse($members as $member)
                <div class="member-row" data-status="{{ $member['status'] }}">
                    <div class="row align-items-center" style="font-size: 0.8rem;">
                        <!-- Member Info -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div style="width: 28px; height: 28px; margin-right: 0.6rem; flex-shrink: 0;">
                                    <span class="avatar-initials rounded-circle {{ $member['has_deposited'] ? 'bg-success' : 'bg-danger' }} text-white fw-bold d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; font-size: 0.75rem;">
                                        {{ strtoupper(substr($member['name'], 0, 2)) }}
                                    </span>
                                </div>
                                <div style="min-width: 0;">
                                    <h6 class="mb-0 fw-semibold" style="font-size: 0.85rem; line-height: 1.2;">{{ $member['name'] }}</h6>
                                    <small class="text-body-secondary d-block" style="font-size: 0.7rem; line-height: 1.1;">Code: {{ $member['code'] }}</small>
                                    <div class="contact-info" style="margin-top: 0.15rem; line-height: 1.1;">
                                        @if($member['phone'] !== 'N/A')
                                            <span class="badge bg-light text-dark" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">{{ $member['phone'] }}</span>
                                        @endif
                                        @if($member['email'] !== 'N/A')
                                            <span class="badge bg-light text-dark" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">{{ $member['email'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Amount -->
                        <div class="col-md-3">
                            <div style="margin-bottom: 0.25rem;">
                                @if($member['has_deposited'])
                                    <span class="status-badge status-deposited">
                                        <span class="fas fa-check-circle"></span>
                                        Deposited
                                    </span>
                                @else
                                    <span class="status-badge status-pending">
                                        <span class="fas fa-exclamation-circle"></span>
                                        Pending
                                    </span>
                                @endif
                            </div>
                            @if($member['has_deposited'])
                                <small class="text-body-secondary d-block" style="font-size: 0.7rem; line-height: 1.1;">Amount:</small>
                                <strong style="font-size: 0.85rem; line-height: 1.1;">₱{{ number_format($member['amount_deposited'], 2) }}</strong>
                            @else
                                <small class="text-danger" style="font-size: 0.7rem;">No deposit yet</small>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="col-md-2">
                            <small class="text-body-secondary d-block" style="font-size: 0.7rem; line-height: 1.1;">Last Deposit:</small>
                            <small style="font-size: 0.8rem; line-height: 1.1;">{{ $member['last_deposit_date'] }}</small>
                            <div style="margin-top: 0.25rem;">
                                <small class="text-body-secondary d-block" style="font-size: 0.7rem; line-height: 1.1;">Shares:</small>
                                <strong style="font-size: 0.85rem; line-height: 1.1;">{{ $member['shares'] }}</strong>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="col-md-3 text-end">
                            @if(!$member['has_deposited'])
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    @if($member['phone'] !== 'N/A')
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member['phone']) }}" target="_blank" class="btn btn-success" title="Send WhatsApp reminder" style="padding: 0.3rem 0.5rem; font-size: 0.7rem;">
                                            <span class="fas fa-whatsapp"></span>
                                        </a>
                                    @endif
                                    @if($member['email'] !== 'N/A')
                                        <a href="mailto:{{ $member['email'] }}?subject=Monthly%20Deposit%20Reminder&body=Dear%20{{ urlencode($member['name']) }},%0A%0AThis%20is%20a%20reminder%20to%20submit%20your%20monthly%20deposit%20for%20{{ now()->format('F Y') }}." class="btn btn-info" title="Send email reminder" style="padding: 0.3rem 0.5rem; font-size: 0.7rem;">
                                            <span class="fas fa-envelope"></span>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-success-subtle text-success-emphasis" style="font-size: 0.7rem;">✓ Complete</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <p class="text-muted"><span class="fas fa-inbox me-2"></span>No members found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if($pending > 0)
    <div class="card border-0 shadow-sm mt-3 compact-card">
        <div class="card-body" style="padding: 0.75rem 0;">
            <h6 class="fw-semibold mb-2" style="font-size: 0.9rem;">📢 Bulk Reminders</h6>
            <p class="text-body-secondary small mb-2" style="font-size: 0.8rem;">Send reminders to all {{ $pending }} members who haven't deposited yet:</p>
            <div class="d-flex gap-2">
                @php
                    $pendingMembers = $members->where('has_deposited', false);
                    $pendingPhones = $pendingMembers->filter(fn($m) => $m['phone'] !== 'N/A')->pluck('phone')->join(',');
                    $pendingEmails = $pendingMembers->filter(fn($m) => $m['email'] !== 'N/A')->pluck('email')->join(',');
                @endphp
                @if($pendingPhones)
                    <a href="https://wa.me/?text=Dear%20Members,%0A%0AThis%20is%20a%20reminder%20to%20submit%20your%20monthly%20deposit%20for%20{{ now()->format('F Y') }}." class="btn btn-success" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                        <span class="fas fa-whatsapp me-1"></span>WhatsApp All
                    </a>
                @endif
                @if($pendingEmails)
                    <a href="mailto:{{ $pendingEmails }}?subject=Monthly%20Deposit%20Reminder%20-%20{{ now()->format('F Y') }}&body=Dear%20Members,%0A%0AThis%20is%20a%20reminder%20to%20submit%20your%20monthly%20deposit%20for%20{{ now()->format('F Y') }}." class="btn btn-info" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                        <span class="fas fa-envelope me-1"></span>Email All
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif
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
@endsection
