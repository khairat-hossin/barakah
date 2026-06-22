@extends('layouts.phoenix')

@section('title', 'Share Distribution | ' . config('app.name'))

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('shares.index') }}">Shares</a></li>
        <li class="breadcrumb-item active">Distribution</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">Share Distribution</h2>
            <p class="text-body-secondary mb-0">Member share allocation overview</p>
            <small class="text-primary"><span class="fas fa-hand-pointer me-1"></span>Click any member card to assign shares</small>
        </div>
        <div class="col-auto">
            <a href="{{ route('shares.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-2"></span>Back
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-primary fw-semibold">Total Shares</small>
                <h6 class="mb-0">{{ number_format($totalShares) }}</h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-success fw-semibold">Assigned</small>
                <h6 class="mb-0" id="assignedTotal">{{ number_format($assignedShares) }}</h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-warning fw-semibold">Available</small>
                <h6 class="mb-0" id="availableTotal">{{ number_format($availableShares) }}</h6>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="text-center p-2 bg-light rounded">
                <small class="text-info fw-semibold">Members</small>
                <h6 class="mb-0">{{ $memberShares->count() }}</h6>
            </div>
        </div>
    </div>

    <!-- Member Boxes Grid -->
    @if($memberShares->count())
        <div class="row g-2">
            @foreach($memberShares as $member)
                <div class="col-6 col-md-4 col-lg-2-4">
                    <div class="card h-100 border-0 shadow-sm member-box"
                        style="padding: 10px; display: flex; flex-direction: column;"
                        data-member-id="{{ $member->id }}"
                        data-member-code="{{ $member->member_id }}"
                        data-member-name="{{ $member->name }}"
                        data-shares="{{ $member->shares_count }}"
                        onclick="openShareModal(this)">
                        <!-- Member Code - Name & Status -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; gap: 4px;">
                            <div style="font-weight: 500; font-size: 12px; color: #6c757d; flex: 0 0 auto;">
                                {{ $member->member_id }}
                            </div>
                            <div style="font-weight: 500; font-size: 13px; line-height: 1.2; flex: 1; min-width: 0;" class="text-truncate" title="{{ $member->name }}">
                                {{ $member->name }}
                            </div>
                            <div style="flex: 0 0 auto;">
                                <span class="badge @if($member->status === 'active') bg-success @elseif($member->status === 'inactive') bg-secondary @else bg-danger @endif" style="font-size: 10px; padding: 2px 6px;">
                                    {{ ucfirst(substr($member->status, 0, 1)) }}
                                </span>
                            </div>
                        </div>

                        <!-- Share Count & EMI Row -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 8px;">
                            <!-- Share Count (Left) -->
                            <div>
                                <div style="color: #6c757d; font-size: 11px; margin-bottom: 2px;">Shares</div>
                                <div class="shares-count-display" style="font-size: 28px; font-weight: bold; color: #0d6efd; line-height: 1;">
                                    {{ $member->shares_count }}
                                </div>
                            </div>

                            <!-- EMI Per Month (Right) -->
                            <div style="text-align: right;">
                                <div style="color: #6c757d; font-size: 11px; margin-bottom: 2px;">EMI/Month</div>
                                <div style="font-size: 13px; font-weight: 500; color: #198754; line-height: 1.2;">
                                    ৳ <span class="emi-amount">{{ number_format($member->emi_per_month ?? 0, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info mb-0">
            <span class="fas fa-info-circle me-2"></span>No members found.
        </div>
    @endif
</div>

<!-- Assign Shares Modal -->
<div class="modal fade" id="assignSharesModal" tabindex="-1" aria-labelledby="assignSharesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignSharesModalLabel">Assign Shares</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="text-body-secondary small">Member</div>
                    <div class="fw-semibold" id="modalMemberName">—</div>
                    <div class="text-body-secondary small" id="modalMemberCode"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label" for="modalShareInput">Number of Shares</label>
                    <input type="number" class="form-control" id="modalShareInput" min="0" value="0">
                    <small class="text-body-secondary">Available to assign: <span id="modalAvailable">{{ number_format($availableShares) }}</span></small>
                </div>
                <div class="alert alert-danger py-2 px-3 mb-0 d-none" id="modalError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="modalSaveBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<style>
    .member-box {
        transition: all 0.2s ease;
        cursor: pointer;
        border-radius: 8px;
    }

    .member-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* 5 cards per row on large screens only; falls back to col-6 / col-md-4 below */
    @media (min-width: 992px) {
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }

    @media (max-width: 768px) {
        .member-box .display-6 {
            font-size: 1.5rem !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalShares = {{ $totalShares }};
    let activeCard = null;
    const modalEl = document.getElementById('assignSharesModal');
    const modal = new bootstrap.Modal(modalEl);
    const shareInput = document.getElementById('modalShareInput');
    const errorBox = document.getElementById('modalError');
    const saveBtn = document.getElementById('modalSaveBtn');

    // Sum of shares currently assigned across all cards
    function totalAssigned() {
        let sum = 0;
        document.querySelectorAll('.member-box').forEach(card => {
            sum += parseInt(card.dataset.shares) || 0;
        });
        return sum;
    }

    function refreshSummary() {
        const assigned = totalAssigned();
        const available = totalShares - assigned;
        const assignedEl = document.getElementById('assignedTotal');
        const availableEl = document.getElementById('availableTotal');
        if (assignedEl) assignedEl.textContent = assigned.toLocaleString();
        if (availableEl) availableEl.textContent = available.toLocaleString();
    }

    window.openShareModal = function (card) {
        activeCard = card;
        errorBox.classList.add('d-none');
        errorBox.textContent = '';

        document.getElementById('modalMemberName').textContent = card.dataset.memberName;
        document.getElementById('modalMemberCode').textContent = card.dataset.memberCode;
        shareInput.value = parseInt(card.dataset.shares) || 0;

        // Available = total - everyone else's shares
        const othersAssigned = totalAssigned() - (parseInt(card.dataset.shares) || 0);
        document.getElementById('modalAvailable').textContent =
            (totalShares - othersAssigned).toLocaleString();

        modal.show();
    };

    saveBtn.addEventListener('click', function () {
        if (!activeCard) return;
        const memberId = activeCard.dataset.memberId;
        const newValue = parseInt(shareInput.value) || 0;

        if (newValue < 0) {
            showError('Share count cannot be negative.');
            return;
        }

        const original = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
        errorBox.classList.add('d-none');

        fetch(`/shares/member/${memberId}/shares`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ share_count: newValue })
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                activeCard.dataset.shares = newValue;
                activeCard.querySelector('.shares-count-display').textContent = newValue;
                const emiEl = activeCard.querySelector('.emi-amount');
                if (emiEl && data.emi_per_month !== undefined) {
                    emiEl.textContent = Number(data.emi_per_month).toLocaleString(undefined, { maximumFractionDigits: 0 });
                }
                refreshSummary();
                modal.hide();
            } else {
                showError(data.error || 'Unable to save shares.');
            }
            saveBtn.disabled = false;
            saveBtn.innerHTML = original;
        })
        .catch(err => {
            showError('Error saving shares: ' + err.message);
            saveBtn.disabled = false;
            saveBtn.innerHTML = original;
        });
    });

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
});
</script>
@endsection
