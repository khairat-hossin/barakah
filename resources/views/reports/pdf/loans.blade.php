@php $org = \App\Models\OrganizationProfile::first(); @endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: hindsiliguri, sans-serif; font-size: 11px; color: #212529; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border-bottom: 1px solid #dee2e6; padding: 5px 6px; text-align: left; }
    th { background: #f8f9fa; font-size: 10px; text-transform: uppercase; color: #6c757d; }
    td.num, th.num { text-align: right; }
    tfoot td { font-weight: bold; border-top: 2px solid #adb5bd; }
    .summary { margin: 8px 0; }
    .summary span { margin-right: 18px; font-size: 11px; }
</style>
</head>
<body>
    @include('pdf.partials.header', [
        'org' => $org,
        'title' => 'Loan Report',
        'color' => '#fd7e14',
        'meta' => 'Period: ' . $from->format('d M Y') . ' – ' . $to->format('d M Y') . ' &nbsp;|&nbsp; Generated: ' . now()->format('d M Y, h:i A'),
    ])

    <div class="summary">
        <span><strong>Total Lent:</strong> Tk {{ number_format($totalLent, 2) }}</span>
        <span><strong>Total Repaid:</strong> Tk {{ number_format($totalRepaid, 2) }}</span>
        <span><strong>Outstanding:</strong> Tk {{ number_format($totalOutstanding, 2) }}</span>
        <span><strong>Loans:</strong> {{ number_format($count) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Member</th>
                <th>Taken</th>
                <th>Due</th>
                <th>Status</th>
                <th class="num">Loan</th>
                <th class="num">Repaid</th>
                <th class="num">Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $l)
                <tr>
                    <td>{{ $l->loan_code }}</td>
                    <td>{{ $l->member?->name ?? 'N/A' }}</td>
                    <td>{{ $l->taken_date?->format('d M Y') }}</td>
                    <td>{{ $l->due_date?->format('d M Y') ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_',' ',$l->status)) }}</td>
                    <td class="num">Tk {{ number_format($l->loan_amount, 2) }}</td>
                    <td class="num">Tk {{ number_format($l->total_repaid, 2) }}</td>
                    <td class="num">Tk {{ number_format($l->outstanding_balance, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center; padding:16px;">No loans found for this range.</td></tr>
            @endforelse
        </tbody>
        @if($loans->count())
            <tfoot>
                <tr>
                    <td colspan="5" class="num">Totals</td>
                    <td class="num">Tk {{ number_format($totalLent, 2) }}</td>
                    <td class="num">Tk {{ number_format($totalRepaid, 2) }}</td>
                    <td class="num">Tk {{ number_format($totalOutstanding, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
