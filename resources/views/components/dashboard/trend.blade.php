@props([
    'value' => 0,      // percentage change
    'suffix' => 'vs last month',
])

@php
    $v = (float) $value;
    $cls = $v > 0 ? 'text-success' : ($v < 0 ? 'text-danger' : 'text-body-tertiary');
    $arrow = $v > 0 ? 'fa-arrow-up' : ($v < 0 ? 'fa-arrow-down' : 'fa-minus');
@endphp

<small class="fw-bold {{ $cls }} kpi-sub" style="white-space: nowrap;">
    <span class="fas {{ $arrow }} fa-xs me-1"></span>{{ number_format(abs($v), 1) }}%
    @if($suffix)<span class="text-body-tertiary fw-normal ms-1">{{ $suffix }}</span>@endif
</small>
