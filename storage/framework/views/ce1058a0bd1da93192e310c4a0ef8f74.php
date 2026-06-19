<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['member']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['member']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<!-- Profile Completeness Progress Bar with Tooltip -->
<div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?php echo e($tooltipContent); ?>">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <small class="text-body-secondary fw-semibold">Profile Completeness</small>
        <small class="badge badge-phoenix badge-phoenix-<?php echo e($color); ?>"><?php echo e($percentage); ?>%</small>
    </div>
    <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-<?php echo e($color); ?>" role="progressbar"
             style="width: <?php echo e($percentage); ?>%"
             aria-valuenow="<?php echo e($percentage); ?>"
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
<?php /**PATH /Volumes/Works/kinvest/barakah/resources/views/components/profile-completeness.blade.php ENDPATH**/ ?>