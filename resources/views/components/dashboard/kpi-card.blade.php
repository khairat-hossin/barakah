@props([
    'label',
    'value',
    'sub' => null,
    'color' => 'blue',   // blue | green | red | orange | dark | info
    'icon' => null,
])

@php
    $accents = [
        'blue'   => '#0d6efd',
        'green'  => '#198754',
        'red'    => '#dc3545',
        'orange' => '#fd7e14',
        'dark'   => '#344050',
        'info'   => '#0dcaf0',
    ];
    $accent = $accents[$color] ?? $accents['blue'];
@endphp

<div class="card h-100 kpi-card" style="border-left: 3px solid {{ $accent }} !important;">
    <div class="card-body p-3 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <span class="text-body-secondary fw-semibold text-uppercase kpi-label">{{ $label }}</span>
            @if($icon)
                <span class="fas fa-{{ $icon }} kpi-icon" style="color: {{ $accent }};"></span>
            @endif
        </div>
        <div class="kpi-value fw-bold text-body-emphasis">{{ $value }}</div>
        <div class="mt-auto pt-1 d-flex align-items-center justify-content-between gap-1">
            @if($sub)
                <small class="text-body-secondary kpi-sub">{{ $sub }}</small>
            @endif
            @isset($trend){{ $trend }}@endisset
        </div>
    </div>
</div>
