@php
    $org = $org ?? \App\Models\OrganizationProfile::first();
    $color = $color ?? '#198754';
    $meta = $meta ?? null;

    // Organisation line: "English Name – বাংলা নাম" (Bangla part shown only if set).
    $nameEn = $org?->organization_name_en ?? 'Organization';
    $nameBn = $org?->organization_name_bn;
    $orgLine = $nameBn ? ($nameEn . ' – ' . $nameBn) : $nameEn;

    // Address line, built from whichever address fields are filled in.
    $address = collect([
        $org?->address_line, $org?->village_area, $org?->post_office,
        $org?->union_ward, $org?->upazila, $org?->district,
    ])->filter(fn ($p) => filled($p))->implode(', ');
    if ($address !== '') {
        $address .= '।';
    }

    $logoPath = \App\Support\Branding::pdfLogoPath();
@endphp

<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="width: 80px; vertical-align: middle; border: none; padding: 0;">
            @if ($logoPath)
                <img src="{{ $logoPath }}" alt="logo" style="width: 68px; height: 68px;">
            @endif
        </td>
        <td style="text-align: center; vertical-align: middle; border: none; padding: 0;">
            <div style="font-size: 18px; font-weight: bold; color: #212529;">{{ $orgLine }}</div>
            @if ($address !== '')
                <div style="font-size: 11px; color: #495057; margin-top: 2px;">{{ $address }}</div>
            @endif
            <div style="font-size: 18px; font-weight: bold; color: #212529; margin-top: 5px;">{{ $title }}</div>
            @if (!empty($meta))
                <div style="font-size: 10px; color: #6c757d; margin-top: 3px;">{!! $meta !!}</div>
            @endif
        </td>
        <td style="width: 80px; border: none;"></td>
    </tr>
</table>

<div style="border-bottom: 1px solid #dee2e6; margin: 8px 0 12px;"></div>
