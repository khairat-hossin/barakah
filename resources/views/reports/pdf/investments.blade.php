@php $org = \App\Models\OrganizationProfile::first(); @endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #212529; }
    .org { font-size: 16px; font-weight: bold; }
    .title { font-size: 13px; font-weight: bold; margin-top: 4px; }
    .meta { color: #6c757d; font-size: 10px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border-bottom: 1px solid #dee2e6; padding: 5px 6px; text-align: left; }
    th { background: #f8f9fa; font-size: 9px; text-transform: uppercase; color: #6c757d; }
    td.num, th.num { text-align: right; }
    tfoot td { font-weight: bold; border-top: 2px solid #adb5bd; }
    .summary { margin: 8px 0; }
    .summary span { margin-right: 18px; font-size: 11px; }
</style>
</head>
<body>
    <div class="org">{{ $org?->organization_name_en ?? 'Organization' }}</div>
    <div class="title">Investment Report</div>
    <div class="meta">
        Started: {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }} &nbsp;|&nbsp;
        Generated: {{ now()->format('d M Y, h:i A') }}
    </div>

    <div class="summary">
        <span><strong>Invested:</strong> ৳{{ number_format($totalInvested, 2) }}</span>
        <span><strong>Returned:</strong> ৳{{ number_format($totalReturned, 2) }}</span>
        <span><strong>Net P/L:</strong> ৳{{ number_format($netProfit, 2) }}</span>
        <span><strong>Count:</strong> {{ number_format($count) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Status</th>
                <th class="num">Invested</th>
                <th class="num">Returned</th>
                <th class="num">Net P/L</th>
            </tr>
        </thead>
        <tbody>
            @forelse($investments as $i)
                <tr>
                    <td>{{ $i->code }}</td>
                    <td>{{ $i->name }}</td>
                    <td>{{ $i->investmentType?->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($i->status) }}</td>
                    <td class="num">৳{{ number_format($i->total_invested_amount, 2) }}</td>
                    <td class="num">৳{{ number_format($i->total_returned_amount, 2) }}</td>
                    <td class="num">৳{{ number_format($i->net_profit_loss, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding:16px;">No investments found for this range.</td></tr>
            @endforelse
        </tbody>
        @if($investments->count())
            <tfoot>
                <tr>
                    <td colspan="4" class="num">Total</td>
                    <td class="num">৳{{ number_format($totalInvested, 2) }}</td>
                    <td class="num">৳{{ number_format($totalReturned, 2) }}</td>
                    <td class="num">৳{{ number_format($netProfit, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
