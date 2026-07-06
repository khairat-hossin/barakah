@props([
    'icon' => 'inbox',
    'title' => '',
    'message' => null,
    'variant' => 'neutral',  // neutral | success
])

@php
    $color = $variant === 'success' ? 'text-success' : 'text-body-tertiary';
@endphp

<div class="text-center py-4 px-2">
    <span class="fas fa-{{ $icon }} fs-2 {{ $color }} mb-2 d-block"></span>
    @if($title)<p class="mb-0 fw-semibold text-body-secondary">{{ $title }}</p>@endif
    @if($message)<p class="mb-0 small text-body-tertiary">{{ $message }}</p>@endif
    {{ $slot }}
</div>
