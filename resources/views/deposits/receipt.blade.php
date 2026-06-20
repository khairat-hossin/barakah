@php
    $amount = (float) $deposit->amount;
    $inWords = null;
    if (class_exists(\NumberFormatter::class)) {
        $f = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $inWords = ucwords($f->format($amount)) . ' Taka only';
    }
    $method = $deposit->paymentMethod?->name ?? ucfirst(str_replace('_', ' ', $deposit->payment_method ?? ''));
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; color: #212529; font-size: 12px; }
    .border-box { border: 1.5px solid #198754; border-radius: 6px; padding: 16px 20px; }
    .head { text-align: center; border-bottom: 1px solid #dee2e6; padding-bottom: 8px; margin-bottom: 12px; }
    .org { font-size: 20px; font-weight: bold; color: #198754; }
    .org-sub { font-size: 10px; color: #6c757d; margin-top: 2px; }
    .doc-title { display: inline-block; margin-top: 8px; font-size: 13px; font-weight: bold; letter-spacing: 1px;
                 background: #198754; color: #fff; padding: 3px 14px; border-radius: 3px; }
    .row { width: 100%; margin-bottom: 6px; }
    .row td { padding: 3px 0; vertical-align: top; }
    .label { color: #6c757d; width: 130px; }
    .val { font-weight: bold; }
    .amount-box { border: 1px dashed #198754; background: #f0fdf4; padding: 8px 14px; border-radius: 4px;
                  font-size: 18px; font-weight: bold; color: #198754; text-align: right; }
    .words { font-style: italic; font-size: 11px; color: #495057; margin-top: 4px; }
    .sign-row { width: 100%; margin-top: 40px; }
    .sign-row td { width: 50%; text-align: center; font-size: 10px; color: #6c757d; }
    .sign-line { border-top: 1px solid #adb5bd; margin: 0 30px; padding-top: 3px; }
    .footer { text-align: center; font-size: 9px; color: #adb5bd; margin-top: 10px; }
</style>
</head>
<body>
    <div class="border-box">
        <div class="head">
            <div class="org">{{ $org?->organization_name_en ?? 'Organization' }}</div>
            @if($org?->address_line)
                <div class="org-sub">{{ $org->address_line }}{{ $org->village_area ? ', ' . $org->village_area : '' }}</div>
            @endif
            @if($org?->mobile_number || $org?->email)
                <div class="org-sub">
                    @if($org?->mobile_number) Phone: {{ $org->mobile_number }} @endif
                    @if($org?->email) &nbsp;|&nbsp; Email: {{ $org->email }} @endif
                </div>
            @endif
            <div class="doc-title">DEPOSIT RECEIPT</div>
        </div>

        <table class="row">
            <tr>
                <td class="label">Receipt No</td>
                <td class="val">{{ $deposit->transaction_id ?: ('DEP-' . $deposit->id) }}</td>
                <td class="label" style="width:80px;">Date</td>
                <td class="val">{{ $deposit->deposit_date?->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Received From</td>
                <td class="val" colspan="3">{{ $deposit->member?->name ?? 'N/A' }}
                    @if($deposit->member?->member_code) ({{ $deposit->member->member_code }}) @endif
                </td>
            </tr>
            <tr>
                <td class="label">Payment Method</td>
                <td class="val" colspan="3">{{ $method ?: 'N/A' }}</td>
            </tr>
            @if($deposit->notes)
            <tr>
                <td class="label">Note</td>
                <td class="val" colspan="3" style="font-weight: normal;">{{ $deposit->notes }}</td>
            </tr>
            @endif
        </table>

        <table style="width:100%; margin-top: 10px;">
            <tr>
                <td style="vertical-align: bottom;">
                    @if($inWords)
                        <div class="words">In words: {{ $inWords }}</div>
                    @endif
                </td>
                <td style="width: 200px;">
                    <div class="amount-box">Tk {{ number_format($amount, 2) }}</div>
                </td>
            </tr>
        </table>

        <table class="sign-row">
            <tr>
                <td><div class="sign-line">Received By{{ $deposit->recorder ? ' (' . $deposit->recorder->name . ')' : '' }}</div></td>
                <td><div class="sign-line">Authorized Signature</div></td>
            </tr>
        </table>

        <div class="footer">Generated on {{ now()->format('d M Y, h:i A') }} — This is a system-generated receipt.</div>
    </div>
</body>
</html>
