@php $org = \App\Models\OrganizationProfile::first(); @endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 11px; color: #212529; }
    .org { font-size: 16px; font-weight: bold; }
    .title { font-size: 13px; font-weight: bold; margin-top: 4px; }
    .meta { color: #6c757d; font-size: 10px; margin-bottom: 10px; }
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
        'title' => 'Expense Report',
        'color' => '#dc3545',
        'meta' => 'Period: ' . $from->format('d M Y') . ' – ' . $to->format('d M Y') . ' &nbsp;|&nbsp; Generated: ' . now()->format('d M Y, h:i A'),
    ])

    <div class="summary">
        <span><strong>Total Expenses:</strong> Tk {{ number_format($totalAmount, 2) }}</span>
        <span><strong>Records:</strong> {{ number_format($count) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Title</th>
                <th>Status</th>
                <th class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $e)
                <tr>
                    <td>{{ $e->expense_date?->format('d M Y') }}</td>
                    <td>{{ $e->category?->name ?? 'N/A' }}</td>
                    <td>{{ $e->title }}</td>
                    <td>{{ ucfirst($e->status) }}</td>
                    <td class="num">Tk {{ number_format($e->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:16px;">No expenses found for this range.</td></tr>
            @endforelse
        </tbody>
        @if($expenses->count())
            <tfoot>
                <tr>
                    <td colspan="4" class="num">Total</td>
                    <td class="num">Tk {{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
