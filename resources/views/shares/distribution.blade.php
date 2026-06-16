@extends('layouts.phoenix')

@section('title', 'Share Distribution | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('shares.index') }}">Shares</a></li>
        <li class="breadcrumb-item active">Distribution</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h2 class="mb-0">Share Distribution</h2>
            <p class="text-body-secondary">Allocate and manage member share counts</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('shares.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-2"></span>Back
            </a>
        </div>
    </div>

    <!-- Compact Summary Bar -->
    <div class="row g-2 mb-4">
        <div class="col-md-3">
            <div class="bg-primary bg-opacity-10 px-3 py-2 rounded">
                <small class="text-primary d-block fw-semibold">Total Shares</small>
                <h4 class="mb-0 text-primary">{{ number_format($totalShares) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-success bg-opacity-10 px-3 py-2 rounded">
                <small class="text-success d-block fw-semibold">Assigned</small>
                <h4 class="mb-0 text-success" id="assignedTotal">{{ number_format($assignedShares) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-warning bg-opacity-10 px-3 py-2 rounded">
                <small class="text-warning d-block fw-semibold">Available</small>
                <h4 class="mb-0 text-warning" id="availableTotal">{{ number_format($availableShares) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-info bg-opacity-10 px-3 py-2 rounded">
                <small class="text-info d-block fw-semibold">Allocation %</small>
                <h4 class="mb-0 text-info" id="allocationPercent">{{ $totalShares > 0 ? round(($assignedShares / $totalShares) * 100) : 0 }}%</h4>
            </div>
        </div>
    </div>

    <!-- Interactive Share Allocator -->
    @if($memberShares->count())
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Member Share Allocation</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 200px;">Member</th>
                                <th style="width: 60px;">Status</th>
                                <th>Allocation</th>
                                <th style="width: 100px;" class="text-center">Shares</th>
                                <th style="width: 130px;" class="text-center">Quick Adjust</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberShares as $member)
                                <tr class="align-middle member-row" data-member-id="{{ $member->id }}">
                                    <!-- Member Info -->
                                    <td class="ps-3">
                                        <div>
                                            <small class="fw-semibold">{{ $member->name }}</small>
                                            <small class="text-muted d-block">{{ $member->member_id }}</small>
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td>
                                        <span class="badge @if($member->status === 'active') bg-success @elseif($member->status === 'inactive') bg-secondary @else bg-danger @endif">
                                            {{ ucfirst($member->status) }}
                                        </span>
                                    </td>

                                    <!-- Visual Allocation Bar -->
                                    <td>
                                        <div class="position-relative">
                                            <div class="progress" style="height: 24px;">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: {{ $totalShares > 0 ? ($member->shares_count / $totalShares) * 100 : 0 }}%"
                                                    data-percent="{{ $totalShares > 0 ? round(($member->shares_count / $totalShares) * 100) : 0 }}">
                                                </div>
                                            </div>
                                            <small class="position-absolute top-50 start-50 translate-middle text-dark fw-semibold"
                                                style="font-size: 11px;">
                                                <span class="member-percent">{{ $totalShares > 0 ? round(($member->shares_count / $totalShares) * 100) : 0 }}</span>%
                                            </small>
                                        </div>
                                    </td>

                                    <!-- Share Input -->
                                    <td class="text-center">
                                        <div class="input-group input-group-sm" style="width: 90px; margin: 0 auto;">
                                            <input type="number" class="form-control text-center share-input"
                                                value="{{ $member->shares_count }}"
                                                min="0"
                                                max="{{ $totalShares }}"
                                                data-original="{{ $member->shares_count }}">
                                        </div>
                                    </td>

                                    <!-- Quick Adjust Buttons -->
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary adjust-btn" data-adjust="-5" title="Decrease by 5">
                                                <i class="fas fa-minus"></i>5
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary adjust-btn" data-adjust="+5" title="Increase by 5">
                                                +5<i class="fas fa-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm save-btn" title="Save changes">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info mb-0">
            <span class="fas fa-info-circle me-2"></span>No members found.
        </div>
    @endif
</div>

<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    .member-row {
        border-bottom: 1px solid #f1f3f5;
    }

    .member-row:last-child {
        border-bottom: none;
    }

    .share-input {
        border: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 14px;
    }

    .share-input.changed {
        background-color: #fff3cd;
        border-color: #ffc107;
    }

    .btn-group-sm .btn {
        padding: 4px 8px;
        font-size: 12px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalShares = {{ $totalShares }};
    let memberData = {
        @foreach($memberShares as $member)
            {{ $member->id }}: { name: '{{ $member->name }}', original: {{ $member->shares_count }} },
        @endforeach
    };

    function updateSummary() {
        let totalAssigned = 0;
        document.querySelectorAll('.share-input').forEach(input => {
            totalAssigned += parseInt(input.value) || 0;
        });

        const available = totalShares - totalAssigned;
        const percent = totalShares > 0 ? Math.round((totalAssigned / totalShares) * 100) : 0;

        document.getElementById('assignedTotal').textContent = totalAssigned.toLocaleString();
        document.getElementById('availableTotal').textContent = available.toLocaleString();
        document.getElementById('allocationPercent').textContent = percent + '%';

        // Update each member's allocation percentage
        document.querySelectorAll('.member-row').forEach(row => {
            const input = row.querySelector('.share-input');
            const shares = parseInt(input.value) || 0;
            const memberPercent = totalShares > 0 ? Math.round((shares / totalShares) * 100) : 0;
            const progressBar = row.querySelector('.progress-bar');
            const percentSpan = row.querySelector('.member-percent');

            progressBar.style.width = (shares / totalShares * 100) + '%';
            percentSpan.textContent = memberPercent;
        });
    }

    // Handle quick adjust buttons
    document.querySelectorAll('.adjust-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.member-row');
            const input = row.querySelector('.share-input');
            const adjust = parseInt(this.dataset.adjust);
            const newValue = Math.max(0, Math.min(totalShares, parseInt(input.value) || 0 + adjust));
            input.value = newValue;
            input.classList.add('changed');
            updateSummary();
        });
    });

    // Handle share input changes
    document.querySelectorAll('.share-input').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.add('changed');
            let value = parseInt(this.value) || 0;
            if (value < 0) value = 0;
            if (value > totalShares) value = totalShares;
            this.value = value;
            updateSummary();
        });

        input.addEventListener('change', function() {
            const row = this.closest('.member-row');
            const memberId = row.dataset.memberId;
            const newValue = parseInt(this.value) || 0;
            const originalValue = memberData[memberId].original;

            if (newValue !== originalValue) {
                saveMemberShares(memberId, newValue, row);
            }
        });
    });

    // Handle save button
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.member-row');
            const input = row.querySelector('.share-input');
            const memberId = row.dataset.memberId;
            const newValue = parseInt(input.value) || 0;
            const originalValue = memberData[memberId].original;

            if (newValue !== originalValue) {
                saveMemberShares(memberId, newValue, row);
            }
        });
    });

    function saveMemberShares(memberId, newValue, row) {
        const input = row.querySelector('.share-input');
        const saveBtn = row.querySelector('.save-btn');
        const originalBtn = saveBtn.innerHTML;

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/shares/member/${memberId}/shares`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ share_count: newValue })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                memberData[memberId].original = newValue;
                input.classList.remove('changed');
                saveBtn.classList.remove('btn-outline-secondary');
                saveBtn.classList.add('btn-success');
                saveBtn.innerHTML = '<i class="fas fa-check"></i>';

                setTimeout(() => {
                    saveBtn.classList.remove('btn-success');
                    saveBtn.classList.add('btn-outline-secondary');
                    saveBtn.innerHTML = originalBtn;
                    saveBtn.disabled = false;
                }, 1500);
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
                input.value = memberData[memberId].original;
                input.classList.remove('changed');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtn;
            }
        })
        .catch(error => {
            alert('Error saving shares: ' + error.message);
            input.value = memberData[memberId].original;
            input.classList.remove('changed');
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtn;
        });
    }

    // Initial summary calculation
    updateSummary();
});
</script>
@endsection
