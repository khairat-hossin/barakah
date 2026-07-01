@php
    $statusLabel = ucfirst($member->status);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: hindsiliguri, sans-serif; font-size: 11px; color: #212529; }
    h2.section { font-size: 13px; color: #0d6efd; border-bottom: 2px solid #0d6efd;
                 padding-bottom: 4px; margin: 18px 0 8px; }
    table { width: 100%; border-collapse: collapse; }
    .info td { padding: 4px 6px; vertical-align: top; }
    .info td.label { color: #6c757d; width: 28%; }
    .info td.val { font-weight: bold; }
    table.data { margin-top: 4px; }
    table.data th, table.data td { border-bottom: 1px solid #dee2e6; padding: 5px 6px; text-align: left; font-size: 10px; }
    table.data th { background: #f8f9fa; text-transform: uppercase; color: #6c757d; font-size: 9px; }
    table.data td.num, table.data th.num { text-align: right; }
    table.data tfoot td { font-weight: bold; border-top: 2px solid #adb5bd; }
    .empty { color: #adb5bd; font-style: italic; padding: 8px 0; font-size: 10px; }
    .summary-cards td { width: 25%; padding: 6px; }
    .scard { border: 1px solid #dee2e6; border-radius: 4px; padding: 8px; text-align: center; }
    .scard .v { font-size: 15px; font-weight: bold; }
    .scard .l { font-size: 9px; color: #6c757d; }
    .badge { font-size: 9px; padding: 2px 8px; border-radius: 3px; background: #e9ecef; color: #495057; }
</style>
</head>
<body>
    @include('pdf.partials.header', [
        'org' => $org,
        'title' => 'MEMBER PORTFOLIO',
        'color' => '#0d6efd',
        'meta' => $member->name . ' &nbsp;|&nbsp; ' . ($member->member_code ?: 'N/A') . ' &nbsp;|&nbsp; Generated: ' . now()->format('d M Y, h:i A'),
    ])

    {{-- Snapshot cards --}}
    <table class="summary-cards">
        <tr>
            <td><div class="scard"><div class="v">{{ number_format($currentShares) }}</div><div class="l">Shares Owned</div></div></td>
            <td><div class="scard"><div class="v">Tk {{ number_format($shareValue, 0) }}</div><div class="l">Share Value</div></div></td>
            <td><div class="scard"><div class="v">Tk {{ number_format($totalDeposits, 0) }}</div><div class="l">Total Deposited</div></div></td>
            <td><div class="scard"><div class="v">Tk {{ number_format($emiPerMonth, 0) }}</div><div class="l">Monthly EMI</div></div></td>
        </tr>
    </table>

    {{-- Personal --}}
    <h2 class="section">Personal Information</h2>
    <table class="info">
        <tr><td class="label">Full Name</td><td class="val">{{ $member->name }}</td>
            <td class="label">Member Code</td><td class="val">{{ $member->member_code ?: 'N/A' }}</td></tr>
        <tr><td class="label">Status</td><td class="val"><span class="badge">{{ $statusLabel }}</span></td>
            <td class="label">Join Date</td><td class="val">{{ $member->join_date ? \Illuminate\Support\Carbon::parse($member->join_date)->format('d M Y') : 'N/A' }}</td></tr>
        <tr><td class="label">Monthly Saving</td><td class="val">Tk {{ number_format($member->monthly_saving_amount ?? 0, 2) }}</td>
            <td class="label">Member Since</td><td class="val">{{ $member->created_at?->format('d M Y') }}</td></tr>
        @if($member->notes)
        <tr><td class="label">Notes</td><td class="val" colspan="3" style="font-weight: normal;">{{ $member->notes }}</td></tr>
        @endif
    </table>

    {{-- Contact --}}
    <h2 class="section">Contact Information</h2>
    <table class="info">
        <tr><td class="label">Email</td><td class="val">{{ $member->email ?: 'N/A' }}</td>
            <td class="label">Phone</td><td class="val">{{ $member->phone ?: 'N/A' }}</td></tr>
        <tr><td class="label">Address</td><td class="val" colspan="3" style="font-weight: normal;">
            {{ collect([$member->address, $member->city, $member->postal_code])->filter()->implode(', ') ?: 'N/A' }}
        </td></tr>
    </table>

    {{-- Nominee --}}
    <h2 class="section">Nominee Details</h2>
    @if($member->nominees->count())
        <table class="data">
            <thead>
                <tr><th>Name</th><th>Relationship</th><th>Mobile</th><th>NID</th><th class="num">Allocation</th><th>Primary</th></tr>
            </thead>
            <tbody>
                @foreach($member->nominees as $n)
                    <tr>
                        <td>{{ $n->full_name }}</td>
                        <td>{{ $n->relationship }}</td>
                        <td>{{ $n->mobile_number ?: '—' }}</td>
                        <td>{{ $n->nid_number ?: '—' }}</td>
                        <td class="num">{{ $n->allocation_percentage }}%</td>
                        <td>{{ $n->is_primary ? 'Yes' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($member->nominee_name)
        <table class="info">
            <tr><td class="label">Name</td><td class="val">{{ $member->nominee_name }}</td>
                <td class="label">Relation</td><td class="val">{{ $member->nominee_relation ?: 'N/A' }}</td></tr>
            <tr><td class="label">Phone</td><td class="val">{{ $member->nominee_phone ?: 'N/A' }}</td><td></td><td></td></tr>
        </table>
    @else
        <div class="empty">No nominee recorded.</div>
    @endif

    {{-- Share ownership --}}
    <h2 class="section">Share Ownership</h2>
    <table class="info">
        <tr><td class="label">Shares Owned</td><td class="val">{{ number_format($currentShares) }}</td>
            <td class="label">Face Value / Share</td><td class="val">Tk {{ number_format($shareFaceValue, 2) }}</td></tr>
        <tr><td class="label">Total Share Value</td><td class="val">Tk {{ number_format($shareValue, 2) }}</td>
            <td class="label">Expected Monthly EMI</td><td class="val">Tk {{ number_format($emiPerMonth, 2) }}</td></tr>
    </table>

    {{-- Deposits --}}
    <h2 class="section">Deposit History</h2>
    @if($member->savingsEntries->count())
        <table class="data">
            <thead>
                <tr><th>Date</th><th>Transaction ID</th><th>Method</th><th class="num">Amount</th></tr>
            </thead>
            <tbody>
                @foreach($member->savingsEntries as $d)
                    <tr>
                        <td>{{ $d->deposit_date?->format('d M Y') }}</td>
                        <td>{{ $d->transaction_id ?: '—' }}</td>
                        <td>{{ $d->paymentMethod?->name ?? ucfirst($d->payment_method) }}</td>
                        <td class="num">Tk {{ number_format($d->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="num">Total Deposited</td><td class="num">Tk {{ number_format($totalDeposits, 2) }}</td></tr>
            </tfoot>
        </table>
    @else
        <div class="empty">No deposits recorded.</div>
    @endif

    {{-- Share Transfers / Withdrawals --}}
    <h2 class="section">Share Transfers &amp; Withdrawals</h2>
    @php
        $transfers = $member->shareTransfersFrom->map(fn($t) => ['dir' => 'Out', 'party' => $t->toMember?->name, 'date' => $t->transfer_date, 'count' => $t->share_count, 'status' => $t->approval_status])
            ->concat($member->shareTransfersTo->map(fn($t) => ['dir' => 'In', 'party' => $t->fromMember?->name, 'date' => $t->transfer_date, 'count' => $t->share_count, 'status' => $t->approval_status]))
            ->sortByDesc('date');
    @endphp
    @if($transfers->count())
        <table class="data">
            <thead>
                <tr><th>Date</th><th>Direction</th><th>Counterparty</th><th class="num">Shares</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($transfers as $t)
                    <tr>
                        <td>{{ $t['date'] ? \Illuminate\Support\Carbon::parse($t['date'])->format('d M Y') : '—' }}</td>
                        <td>{{ $t['dir'] === 'Out' ? 'Transfer Out (Withdrawal)' : 'Transfer In' }}</td>
                        <td>{{ $t['party'] ?: '—' }}</td>
                        <td class="num">{{ number_format($t['count']) }}</td>
                        <td>{{ ucfirst($t['status']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">No share transfers or withdrawals recorded.</div>
    @endif

    {{-- Member-linked expenses --}}
    <h2 class="section">Related Expenses</h2>
    @if($expenses->count())
        <table class="data">
            <thead>
                <tr><th>Date</th><th>Category</th><th>Title</th><th>Status</th><th class="num">Amount</th></tr>
            </thead>
            <tbody>
                @foreach($expenses as $e)
                    <tr>
                        <td>{{ $e->expense_date?->format('d M Y') }}</td>
                        <td>{{ $e->category?->name ?? 'N/A' }}</td>
                        <td>{{ $e->title }}</td>
                        <td>{{ ucfirst($e->status) }}</td>
                        <td class="num">Tk {{ number_format($e->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="4" class="num">Total</td><td class="num">Tk {{ number_format($totalExpenses, 2) }}</td></tr>
            </tfoot>
        </table>
    @else
        <div class="empty">No expenses linked to this member.</div>
    @endif

    {{-- Investments --}}
    <h2 class="section">Investments</h2>
    @if($investments->count())
        <table class="data">
            <thead>
                <tr><th>Code</th><th>Name</th><th>Type</th><th>Status</th><th class="num">Invested</th><th class="num">Returned</th><th class="num">Net P/L</th></tr>
            </thead>
            <tbody>
                @foreach($investments as $i)
                    <tr>
                        <td>{{ $i->code }}</td>
                        <td>{{ $i->name }}</td>
                        <td>{{ $i->investmentType?->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($i->status) }}</td>
                        <td class="num">Tk {{ number_format($i->total_invested_amount, 0) }}</td>
                        <td class="num">Tk {{ number_format($i->total_returned_amount, 0) }}</td>
                        <td class="num">Tk {{ number_format($i->net_profit_loss, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">No investments held by this member.</div>
    @endif

    <div style="margin-top: 24px; text-align: center; font-size: 9px; color: #adb5bd;">
        {{ $org?->organization_name_en ?? 'Organization' }} — Member Portfolio — Generated {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>
