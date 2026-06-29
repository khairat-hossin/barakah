@extends('layouts.phoenix')

@section('title', 'Deposit Status | ' . \App\Support\Branding::name())

@section('content')
<style>
.deposit-card {
    border: 1px solid var(--phoenix-border-color);
    border-radius: 0.5rem;
    border-left-width: 4px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    height: 100%;
}
.deposit-card:hover { transform: translateY(-2px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08); }
.deposit-card.paid   { border-left-color: #198754; }
.deposit-card.unpaid { border-left-color: #ffc107; }
.deposit-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.8rem; color: #fff; flex-shrink: 0;
}
</style>

<div class="mb-6">
    <!-- Header -->
    <div class="row align-items-center justify-content-between mb-3 g-2">
        <div class="col">
            <h2 class="mb-0 h5">Deposit Status</h2>
            <p class="text-body-secondary mb-0 small">Member deposit status for <strong>{{ $monthLabel }}</strong></p>
        </div>
        <div class="col-auto">
            <form method="GET" action="{{ route('deposit-status') }}" class="d-flex align-items-center gap-2">
                <label for="monthPicker" class="form-label mb-0 small fw-semibold">Month</label>
                <input type="month" id="monthPicker" name="month" value="{{ $selectedMonth }}"
                       class="form-control form-control-sm" style="width: auto;"
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-4 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #0d6efd !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">Total Members</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($members->count()) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">All members</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">✓ Paid</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($deposited) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">{{ $members->count() > 0 ? round(($deposited / $members->count()) * 100) : 0 }}% of members</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 d-flex">
            <div class="card h-100 w-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body" style="padding: 0.5rem 0.75rem;">
                    <small class="text-warning fw-semibold" style="font-size: 0.75rem;">✗ Unpaid</small>
                    <h6 class="mb-0" style="font-weight: 700; font-size: 1.5rem; line-height: 1.2; margin: 0.25rem 0;">{{ number_format($pending) }}</h6>
                    <small class="text-body-secondary" style="font-size: 0.7rem;">Needs action</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter + Search -->
    <div class="row g-2 mb-3 align-items-center">
        <div class="col-md-auto">
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="filter" id="filter-all" value="all" checked onchange="filterCards()">
                <label class="btn btn-outline-secondary" for="filter-all">All ({{ $members->count() }})</label>
                <input type="radio" class="btn-check" name="filter" id="filter-paid" value="paid" onchange="filterCards()">
                <label class="btn btn-outline-success" for="filter-paid">✓ Paid ({{ $deposited }})</label>
                <input type="radio" class="btn-check" name="filter" id="filter-unpaid" value="unpaid" onchange="filterCards()">
                <label class="btn btn-outline-warning" for="filter-unpaid">✗ Unpaid ({{ $pending }})</label>
            </div>
        </div>
        <div class="col-md">
            <input type="text" class="form-control form-control-sm" id="search-input"
                   placeholder="Search by name, code, phone, or email..." onkeyup="filterCards()">
        </div>
    </div>

    <!-- Member Cards -->
    <div class="row g-3" id="member-cards">
        @forelse($members as $member)
        <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 deposit-card-col"
             data-status="{{ $member['has_deposited'] ? 'paid' : 'unpaid' }}"
             data-search="{{ strtolower($member['name'] . ' ' . $member['code'] . ' ' . $member['phone'] . ' ' . $member['email']) }}">
            <div class="card deposit-card {{ $member['has_deposited'] ? 'paid' : 'unpaid' }}">
                <div class="card-body p-3">
                    <!-- Top: status badge -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        @if($member['has_deposited'])
                            <span class="badge badge-phoenix badge-phoenix-success"><span class="fas fa-check-circle me-1"></span>Paid</span>
                        @else
                            <span class="badge badge-phoenix badge-phoenix-warning"><span class="fas fa-exclamation-circle me-1"></span>Unpaid</span>
                        @endif
                        <small class="text-body-tertiary fw-semibold">{{ $monthLabel }}</small>
                    </div>

                    <!-- Member identity -->
                    <div class="d-flex align-items-center mb-3">
                        <span class="deposit-avatar {{ $member['has_deposited'] ? 'bg-success' : 'bg-warning' }} me-2">
                            {{ strtoupper(substr($member['name'], 0, 2)) }}
                        </span>
                        <div style="min-width: 0;">
                            <p class="fw-bold mb-0 line-clamp-1" title="{{ $member['name'] }}">{{ $member['name'] }}</p>
                            <small class="text-body-secondary">{{ $member['code'] }} · {{ $member['shares'] }} shares</small>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <small class="text-body-tertiary fw-semibold">
                            {{ $member['has_deposited'] ? 'Amount Paid' : 'Expected' }}
                        </small>
                        <span class="fw-bold {{ $member['has_deposited'] ? 'text-success' : 'text-body-emphasis' }}">
                            ৳{{ number_format($member['has_deposited'] ? $member['amount_deposited'] : $member['monthly_amount'], 0) }}
                        </span>
                    </div>

                    <!-- Action -->
                    @if($member['has_deposited'])
                        <button class="btn btn-success btn-sm w-100" disabled>
                            <span class="fas fa-check me-1"></span>Deposited
                        </button>
                    @else
                        <button class="btn btn-warning btn-sm w-100 mark-paid-btn"
                                data-member-id="{{ $member['id'] }}"
                                data-member-name="{{ $member['name'] }}"
                                {{ $member['monthly_amount'] <= 0 ? 'disabled title=No shares assigned' : '' }}>
                            <span class="fas fa-money-bill-wave me-1"></span>Mark as Paid
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 text-body-secondary">
                <span class="fas fa-inbox me-2"></span>No members found
            </div>
        </div>
        @endforelse
    </div>
    <div id="no-results" class="text-center py-5 text-body-secondary d-none">
        <span class="fas fa-search me-2"></span>No members match your filter.
    </div>
</div>

<script>
const SELECTED_MONTH = @json($selectedMonth);

function filterCards() {
    const filterValue = document.querySelector('input[name="filter"]:checked').value;
    const searchValue = document.getElementById('search-input').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.deposit-card-col');
    let visible = 0;

    cards.forEach(card => {
        const statusMatch = filterValue === 'all' || card.dataset.status === filterValue;
        const searchMatch = searchValue === '' || card.dataset.search.includes(searchValue);
        const show = statusMatch && searchMatch;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('no-results').classList.toggle('d-none', visible !== 0 || cards.length === 0);
}

document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.mark-paid-btn');
    if (!btn) return;

    const memberId = btn.dataset.memberId;
    const memberName = btn.dataset.memberName;

    if (!(await swalConfirm(`Mark ${memberName} as paid for this month? A deposit will be recorded automatically.`, { icon: 'question', confirmButtonColor: '#198754' }))) {
        return;
    }

    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

    try {
        const response = await fetch('{{ route('deposits.api.mark-paid') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ member_id: memberId, month: SELECTED_MONTH })
        });
        const data = await response.json();

        if (response.ok) {
            // Stash a toast to show after the page reloads.
            sessionStorage.setItem('pendingToast', JSON.stringify({
                type: 'success',
                message: data.message || 'Deposit recorded.'
            }));
            location.reload();
        } else {
            (window.appToast ? appToast('error', data.message || 'Failed to mark as paid.') : alert(data.message || 'Failed to mark as paid.'));
            btn.disabled = false;
            btn.innerHTML = original;
        }
    } catch (err) {
        (window.appToast ? appToast('error', 'Error: ' + err.message) : alert('Error: ' + err.message));
        btn.disabled = false;
        btn.innerHTML = original;
    }
});

// Show any toast stashed before a reload (e.g. after marking paid).
document.addEventListener('DOMContentLoaded', function () {
    const pending = sessionStorage.getItem('pendingToast');
    if (pending && window.appToast) {
        try { const t = JSON.parse(pending); appToast(t.type || 'success', t.message || ''); } catch (e) {}
        sessionStorage.removeItem('pendingToast');
    }
});
</script>
@endsection
