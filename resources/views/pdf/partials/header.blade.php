@php
    $org = $org ?? \App\Models\OrganizationProfile::first();
    $color = $color ?? '#198754';
    $meta = $meta ?? null;
@endphp
<table style="width: 100%; border-bottom: 1px solid #dee2e6; padding-bottom: 8px; margin-bottom: 12px;">
    <tr>
        <td style="width: 90px; vertical-align: middle;">
            <img src="{{ public_path('assets/logo/logo-icon-sm.png') }}" alt="logo" style="width: 70px; height: 70px;">
        </td>
        <td style="text-align: center; vertical-align: middle;">
            <div style="font-size: 22px; font-weight: bold; letter-spacing: 1px; color: #212529;">
                {{ $org?->organization_name_en ?? 'Organization' }}
            </div>
            @if($org?->motto)
                <div style="font-size: 11px; color: #6c757d; font-style: italic;">{{ $org->motto }}</div>
            @endif
            @if($org?->registration_date)
                <div style="font-size: 10px; color: #6c757d;">Estd. {{ \Illuminate\Support\Carbon::parse($org->registration_date)->format('Y') }}</div>
            @endif
            <div style="display: inline-block; margin-top: 6px; font-size: 12px; font-weight: bold; letter-spacing: 1px; color: #fff; background: {{ $color }}; padding: 3px 14px; border-radius: 3px;">
                {{ $title }}
            </div>
            @if(!empty($meta))
                <div style="font-size: 10px; color: #6c757d; margin-top: 6px;">{!! $meta !!}</div>
            @endif
        </td>
        <td style="width: 90px;"></td>
    </tr>
</table>
