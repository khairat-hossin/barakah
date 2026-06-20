@php
    $amount = (float) $expense->amount;
    $inWords = null;
    if (class_exists(\NumberFormatter::class)) {
        $f = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $inWords = ucwords($f->format($amount)) . ' Taka only';
    }
    $method = ucfirst(str_replace('_', ' ', $expense->payment_method ?? ''));
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; color: #212529; font-size: 12px; }
    .border-box { border: 1.5px solid #dc3545; border-radius: 6px; padding: 16px 20px; }
    .head { text-align: center; border-bottom: 1px solid #dee2e6; padding-bottom: 8px; margin-bottom: 12px; }
    .org { font-size: 20px; font-weight: bold; color: #dc3545; }
    .org-sub { font-size: 10px; color: #6c757d; margin-top: 2px; }
    .doc-title { display: inline-block; margin-top: 8px; font-size: 13px; font-weight: bold; letter-spacing: 1px;
                 background: #dc3545; color: #fff; padding: 3px 14px; border-radius: 3px; }
    .row { width: 100%; margin-bottom: 6px; }
    .row td { padding: 3px 0; vertical-align: top; }
    .label { color: #6c757d; width: 130px; }
    .val { font-weight: bold; }
    .amount-box { border: 1px dashed #dc3545; background: #fff5f5; padding: 8px 14px; border-radius: 4px;
                  font-size: 18px; font-weight: bold; color: #dc3545; text-align: right; }
    .words { font-style: italic; font-size: 11px; color: #495057; margin-top: 4px; }
    .badge { font-size: 10px; padding: 2px 8px; border-radius: 3px; background: #e9ecef; color: #495057; }
    .sign-row { width: 100%; margin-top: 40px; }
    .sign-row td { width: 50%; text-align: center; font-size: 10px; color: #6c757d; }
    .sign-line { border-top: 1px solid #adb5bd; margin: 0 30px; padding-top: 3px; }
    .footer { text-align: center; font-size: 9px; color: #adb5bd; margin-top: 10px; }
</style>
</head>
<body>
    <div class="border-box">
        @include('pdf.partials.header', ['org' => $org, 'title' => 'EXPENSE VOUCHER', 'color' => '#dc3545'])

        <table class="row">
            <tr>
                <td class="label">Voucher No</td>
                <td class="val">{{ $expense->expense_number ?: ('EXP-' . $expense->id) }}</td>
                <td class="label" style="width:80px;">Date</td>
                <td class="val">{{ $expense->expense_date?->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Category</td>
                <td class="val">{{ $expense->category?->name ?? 'N/A' }}</td>
                <td class="label" style="width:80px;">Status</td>
                <td class="val"><span class="badge">{{ ucfirst($expense->status) }}</span></td>
            </tr>
            <tr>
                <td class="label">Description</td>
                <td class="val" colspan="3">{{ $expense->title }}</td>
            </tr>
            @if($method)
            <tr>
                <td class="label">Payment Method</td>
                <td class="val" colspan="3">{{ $method }}</td>
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
                <td><div class="sign-line">Prepared By{{ $expense->creator ? ' (' . $expense->creator->name . ')' : '' }}</div></td>
                <td><div class="sign-line">Approved By{{ $expense->approver ? ' (' . $expense->approver->name . ')' : '' }}</div></td>
            </tr>
        </table>

        <div class="footer">Generated on {{ now()->format('d M Y, h:i A') }} — This is a system-generated voucher.</div>
    </div>
</body>
</html>
