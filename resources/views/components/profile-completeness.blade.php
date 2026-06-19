@props(['member'])

@php
    $completeness = $member->getProfileCompleteness();
    $percentage = $completeness['percentage'];
    $status = $completeness['status'];
    $color = $completeness['color'];
    $details = $completeness['details'];
    $missingFields = $completeness['missing_fields'];
@endphp

<!-- Profile Completeness Progress Bar -->
<div style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#profileCompletenessModal{{ $member->id }}">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <small class="text-body-secondary fw-semibold">Profile Completeness</small>
        <small class="badge badge-phoenix badge-phoenix-{{ $color }}">{{ $percentage }}%</small>
    </div>
    <div class="progress" style="height: 8px; cursor: pointer;">
        <div class="progress-bar bg-{{ $color }}" role="progressbar"
             style="width: {{ $percentage }}%"
             aria-valuenow="{{ $percentage }}"
             aria-valuemin="0"
             aria-valuemax="100">
        </div>
    </div>
</div>

<!-- Profile Completeness Modal -->
<div class="modal fade" id="profileCompletenessModal{{ $member->id }}" tabindex="-1"
     aria-labelledby="profileCompletenessLabel{{ $member->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="profileCompletenessLabel{{ $member->id }}">
                    Profile Completeness - {{ $member->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Overall Progress -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Overall Progress</strong>
                        <span class="badge badge-phoenix badge-phoenix-{{ $color }}">{{ $percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-{{ $color }}" role="progressbar"
                             style="width: {{ $percentage }}%"
                             aria-valuenow="{{ $percentage }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <small class="fw-bold text-white">{{ $percentage }}%</small>
                        </div>
                    </div>
                    <small class="text-body-secondary d-block mt-2">Status: <strong>{{ $status }}</strong></small>
                </div>

                <hr>

                <!-- Parameters Status -->
                <div>
                    <h6 class="fw-semibold mb-3">Profile Parameters (20% each)</h6>

                    <!-- Personal Information -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    @if($details['personal_information'])
                                        <span class="text-success">✓</span>
                                    @else
                                        <span class="text-danger">✗</span>
                                    @endif
                                    Personal Information
                                </h6>
                                @if(!$details['personal_information'] && isset($missingFields['personal_information']))
                                    <small class="text-body-secondary">
                                        Missing: <strong>{{ implode(', ', $missingFields['personal_information']) }}</strong>
                                    </small>
                                @else
                                    <small class="text-success">All fields completed</small>
                                @endif
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-{{ $details['personal_information'] ? 'success' : 'danger' }}">20%</span>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    @if($details['contact_information'])
                                        <span class="text-success">✓</span>
                                    @else
                                        <span class="text-danger">✗</span>
                                    @endif
                                    Contact Information
                                </h6>
                                @if(!$details['contact_information'] && isset($missingFields['contact_information']))
                                    <small class="text-body-secondary">
                                        Missing: <strong>{{ implode(', ', $missingFields['contact_information']) }}</strong>
                                    </small>
                                @else
                                    <small class="text-success">All fields completed</small>
                                @endif
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-{{ $details['contact_information'] ? 'success' : 'danger' }}">20%</span>
                        </div>
                    </div>

                    <!-- Nominee -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    @if($details['nominee'])
                                        <span class="text-success">✓</span>
                                    @else
                                        <span class="text-danger">✗</span>
                                    @endif
                                    Nominee
                                </h6>
                                @if(!$details['nominee'] && isset($missingFields['nominee']))
                                    <small class="text-body-secondary">
                                        Missing: <strong>{{ implode(', ', $missingFields['nominee']) }}</strong>
                                    </small>
                                @else
                                    <small class="text-success">All fields completed</small>
                                @endif
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-{{ $details['nominee'] ? 'success' : 'danger' }}">20%</span>
                        </div>
                    </div>

                    <!-- Share Ownership -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    @if($details['share_ownership'])
                                        <span class="text-success">✓</span>
                                    @else
                                        <span class="text-danger">✗</span>
                                    @endif
                                    Share Ownership
                                </h6>
                                @if(!$details['share_ownership'] && isset($missingFields['share_ownership']))
                                    <small class="text-body-secondary">
                                        <strong>{{ implode(', ', $missingFields['share_ownership']) }}</strong>
                                    </small>
                                @else
                                    <small class="text-success">Shares assigned</small>
                                @endif
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-{{ $details['share_ownership'] ? 'success' : 'danger' }}">20%</span>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    @if($details['documents'])
                                        <span class="text-success">✓</span>
                                    @else
                                        <span class="text-danger">✗</span>
                                    @endif
                                    Documents
                                </h6>
                                @if(!$details['documents'] && isset($missingFields['documents']))
                                    <small class="text-body-secondary">
                                        <strong>{{ implode(', ', $missingFields['documents']) }}</strong>
                                    </small>
                                @else
                                    <small class="text-success">Documents uploaded</small>
                                @endif
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-{{ $details['documents'] ? 'success' : 'danger' }}">20%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                    <span class="fas fa-pencil me-2"></span>Complete Profile
                </a>
            </div>
        </div>
    </div>
</div>
