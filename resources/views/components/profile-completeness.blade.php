@props(['member'])

@php
    $completeness = $member->getProfileCompleteness();
    $percentage = $completeness['percentage'];
    $status = $completeness['status'];
    $color = $completeness['color'];
    $details = $completeness['details'];
    $missingFields = $completeness['missing_fields'];

    $tooltipContent = "Status: $status\n";
    $tooltipContent .= "Personal Information: " . ($details['personal_information'] ? "✓" : "✗") . "\n";
    $tooltipContent .= "Contact Information: " . ($details['contact_information'] ? "✓" : "✗") . "\n";
    $tooltipContent .= "Nominee: " . ($details['nominee'] ? "✓" : "✗") . "\n";
    $tooltipContent .= "Share Ownership: " . ($details['share_ownership'] ? "✓" : "✗") . "\n";
    $tooltipContent .= "Documents: " . ($details['documents'] ? "✓" : "✗");
@endphp

<!-- Profile Completeness Progress Bar with Tooltip -->
<div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ $tooltipContent }}">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <small class="text-body-secondary fw-semibold">Profile Completeness</small>
        <small class="badge badge-phoenix badge-phoenix-{{ $color }}">{{ $percentage }}%</small>
    </div>
    <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-{{ $color }}" role="progressbar"
             style="width: {{ $percentage }}%"
             aria-valuenow="{{ $percentage }}"
             aria-valuemin="0"
             aria-valuemax="100">
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => {
        new bootstrap.Tooltip(el);
    });
});
</script>
