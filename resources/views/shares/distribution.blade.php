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
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">Share Distribution</h2>
            <p class="text-body-secondary mt-2">Manage and assign shares to members</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('shares.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-2"></span>Back to Shares
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-primary bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-primary mb-2 fw-semibold">Total Shares</p>
                            <h3 class="text-primary mb-0">{{ number_format($totalShares) }}</h3>
                        </div>
                        <span class="fas fa-chart-pie text-primary fs-5 opacity-50"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-success mb-2 fw-semibold">Assigned Shares</p>
                            <h3 class="text-success mb-0">{{ number_format($assignedShares) }}</h3>
                        </div>
                        <span class="fas fa-check-circle text-success fs-5 opacity-50"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card @if($availableShares > 0) border-warning bg-warning bg-opacity-10 @else border-danger bg-danger bg-opacity-10 @endif">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="@if($availableShares > 0) text-warning @else text-danger @endif mb-2 fw-semibold">Available Shares</p>
                            <h3 class="@if($availableShares > 0) text-warning @else text-danger @endif mb-0">{{ number_format($availableShares) }}</h3>
                        </div>
                        <span class="fas fa-star @if($availableShares > 0) text-warning @else text-danger @endif fs-5 opacity-50"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Share Distribution Table -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Member Share Distribution</h5>
            <small class="text-muted">{{ $memberShares->count() }} members</small>
        </div>
        @if($memberShares->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Member Name</th>
                            <th>Member ID</th>
                            <th>Email</th>
                            <th class="text-center">Shares Assigned</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($memberShares as $member)
                            <tr>
                                <td class="ps-4">
                                    <strong>{{ $member->name }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $member->member_id }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $member->email }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ number_format($member->shares_count) }}</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editShareModal{{ $member->id }}" title="Edit shares">
                                        <span class="fas fa-edit me-1"></span>Edit
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Share Modal for this Member -->
                            <div class="modal fade" id="editShareModal{{ $member->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Shares - {{ $member->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form class="edit-share-form" data-member-id="{{ $member->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <small>
                                                        <strong>Current Shares:</strong> {{ $member->shares_count }}<br>
                                                        <strong>Available to Assign:</strong> {{ $availableShares + $member->shares_count }}
                                                    </small>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="shareCount{{ $member->id }}" class="form-label">Number of Shares *</label>
                                                    <input type="number" class="form-control" id="shareCount{{ $member->id }}" name="share_count" value="{{ $member->shares_count }}" min="0" required>
                                                    <small class="text-muted d-block mt-2">Maximum available: {{ $availableShares + $member->shares_count }}</small>
                                                    <div class="invalid-feedback" style="display: block;"></div>
                                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="fas fa-save me-1"></span>Update Shares
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <span class="fas fa-info-circle me-2"></span>
                    No active members found.
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.querySelectorAll('.edit-share-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const memberId = form.dataset.memberId;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        delete data._token;
        delete data._method;

        try {
            const response = await fetch(`/shares/member/${memberId}/shares`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            let result;
            try {
                result = await response.json();
            } catch (e) {
                console.error('Failed to parse JSON response:', e);
                const text = await response.text();
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server');
            }

            if (response.ok) {
                // Show success message
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                submitBtn.innerHTML = '<span class="fas fa-check me-1"></span>Updated!';

                // Close modal and reload
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById(`editShareModal${memberId}`)).hide();
                    location.reload();
                }, 1500);
            } else {
                // Show errors
                const feedbackEl = form.querySelector('.invalid-feedback');
                if (result.errors && result.errors.share_count) {
                    feedbackEl.textContent = result.errors.share_count[0];
                    feedbackEl.style.display = 'block';
                    const input = form.querySelector('input[name="share_count"]');
                    input.classList.add('is-invalid');
                } else if (result.error) {
                    feedbackEl.textContent = result.error;
                    feedbackEl.style.display = 'block';
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            alert('Error updating shares: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Clear errors on input change
    const input = form.querySelector('input[name="share_count"]');
    if (input) {
        input.addEventListener('change', () => {
            input.classList.remove('is-invalid');
            form.querySelector('.invalid-feedback').style.display = 'none';
        });
    }
});
</script>
@endsection
