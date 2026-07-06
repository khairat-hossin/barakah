@props(['title', 'subtitle' => null])

<div class="d-flex align-items-baseline justify-content-between mb-3 mt-1">
    <div>
        <h5 class="mb-0 fw-bold">{{ $title }}</h5>
        @if($subtitle)<small class="text-body-secondary">{{ $subtitle }}</small>@endif
    </div>
    {{ $slot }}
</div>
