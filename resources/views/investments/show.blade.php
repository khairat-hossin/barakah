@extends('layouts.phoenix')
@section('title', $investment->name . ' | ' . \App\Support\Branding::name())
@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('investments.index') }}">Investments</a></li>
        <li class="breadcrumb-item active">{{ $investment->code }}</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">{{ $investment->name }}</h2>
            <p class="text-body-secondary">{{ $investment->code }} • {{ $investment->investmentType?->name }}</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-phoenix badge-phoenix-{{ $investment->status === 'draft' ? 'secondary' : ($investment->status === 'active' ? 'success' : 'warning') }}">
                {{ ucfirst($investment->status) }}
            </span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">Principal Amount</p>
                    <h5 class="mb-0">৳ {{ number_format($performance['total_invested'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">Current Value</p>
                    <h5 class="mb-0">৳ {{ number_format($performance['current_value'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">Net Profit/Loss</p>
                    <h5 class="mb-0">৳ {{ number_format($performance['net_profit_loss'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-body-secondary fs-9 mb-1">ROI %</p>
                    <h5 class="mb-0">{{ number_format($performance['roi_percentage'], 2) }}%</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#information" data-bs-toggle="tab">Information</a></li>
                <li class="nav-item"><a class="nav-link" href="#transactions" data-bs-toggle="tab">Transactions</a></li>
                <li class="nav-item"><a class="nav-link" href="#documents" data-bs-toggle="tab">Documents</a></li>
                <li class="nav-item"><a class="nav-link" href="#history" data-bs-toggle="tab">History</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="information" role="tabpanel">
                    <p><strong>Description:</strong> {{ $investment->description }}</p>
                    <p><strong>Type:</strong> {{ $investment->investmentType?->name }}</p>
                    <p><strong>Start Date:</strong> {{ $investment->start_date->format('d M Y') }}</p>
                    <p><strong>Maturity Date:</strong> {{ $investment->maturity_date?->format('d M Y') ?? 'N/A' }}</p>
                    <p><strong>Risk Level:</strong> {{ ucfirst($investment->risk_level) }}</p>
                    <p><strong>Expected Return:</strong> {{ $investment->expected_return_percentage }}%</p>
                </div>
                <div class="tab-pane fade" id="transactions" role="tabpanel">
                    @php
                        $typeLabels = [
                            'INITIAL_INVESTMENT' => 'Initial Investment',
                            'ADDITIONAL_INVESTMENT' => 'Additional Investment',
                            'PROFIT_DISTRIBUTION' => 'Profit Distribution',
                            'DIVIDEND_PAYMENT' => 'Dividend Payment',
                            'LOSS_ADJUSTMENT' => 'Loss Adjustment',
                            'WITHDRAWAL' => 'Withdrawal',
                            'MATURITY_CLOSURE' => 'Maturity Closure',
                            'ADMINISTRATIVE_ADJUSTMENT' => 'Administrative Adjustment',
                            'REINVESTMENT' => 'Reinvestment',
                        ];
                        $inflowTypes = ['INITIAL_INVESTMENT', 'ADDITIONAL_INVESTMENT', 'REINVESTMENT'];
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Transactions</h6>
                        @can('create investment transactions')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                <span class="fas fa-plus me-1"></span>Add Transaction
                            </button>
                        @endcan
                    </div>

                    @php $transactions = $investment->transactions->sortByDesc('transaction_date'); @endphp
                    @if($transactions->isEmpty())
                        <div class="alert alert-info mb-0">
                            <span class="fas fa-info-circle me-2"></span>No transactions yet. Add an <strong>Initial Investment</strong> to record the amount, then approve it.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="fs-9 text-body-secondary">Date</th>
                                        <th class="fs-9 text-body-secondary">Type</th>
                                        <th class="fs-9 text-body-secondary text-end">Amount</th>
                                        <th class="fs-9 text-body-secondary">Reference</th>
                                        <th class="fs-9 text-body-secondary text-center">Status</th>
                                        <th class="fs-9 text-body-secondary text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $txn)
                                        <tr>
                                            <td class="fs-9">{{ $txn->transaction_date->format('d M Y') }}</td>
                                            <td class="fs-9">{{ $typeLabels[$txn->transaction_type] ?? $txn->transaction_type }}</td>
                                            <td class="fs-9 text-end fw-semibold {{ in_array($txn->transaction_type, $inflowTypes) ? 'text-success' : 'text-body-emphasis' }}">
                                                ৳{{ number_format($txn->amount, 2) }}
                                            </td>
                                            <td class="fs-9 text-body-tertiary">{{ $txn->reference_number ?? '—' }}</td>
                                            <td class="text-center">
                                                @php
                                                    $statusClass = match($txn->status) {
                                                        'processed' => 'success',
                                                        'pending' => 'warning',
                                                        'reversed' => 'secondary',
                                                        default => 'info',
                                                    };
                                                @endphp
                                                <span class="badge badge-phoenix badge-phoenix-{{ $statusClass }}">{{ ucfirst($txn->status) }}</span>
                                            </td>
                                            <td class="text-end">
                                                @if($txn->status === 'pending')
                                                    @can('approve investment transactions')
                                                        <form action="{{ route('investments.transactions.approve', [$investment, $txn]) }}" method="POST" class="d-inline">
                                                            @csrf @method('PUT')
                                                            <button type="submit" class="btn btn-success btn-sm py-0 px-2" title="Approve">
                                                                <span class="fas fa-check"></span>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @elseif($txn->status === 'processed')
                                                    @can('manage investment transactions')
                                                        <form action="{{ route('investments.transactions.reverse', [$investment, $txn]) }}" method="POST" class="d-inline"
                                                              data-confirm="Reverse this transaction? It will be excluded from totals.">
                                                            @csrf @method('PUT')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Reverse">
                                                                <span class="fas fa-undo"></span>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @else
                                                    <span class="text-body-tertiary fs-9">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <small class="text-body-tertiary d-block mt-2">Only <strong>approved (processed)</strong> transactions count toward the investment totals.</small>
                    @endif
                </div>
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Documents</h6>
                        @can('manage investment documents')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <span class="fas fa-upload me-1"></span>Upload Document
                            </button>
                        @endcan
                    </div>

                    @php $documents = $investment->documents->sortByDesc('created_at'); @endphp
                    @if($documents->isEmpty())
                        <div class="alert alert-info mb-0">
                            <span class="fas fa-info-circle me-2"></span>No documents uploaded yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="fs-9 text-body-secondary">File</th>
                                        <th class="fs-9 text-body-secondary">Type</th>
                                        <th class="fs-9 text-body-secondary">Uploaded</th>
                                        <th class="fs-9 text-body-secondary text-center">Status</th>
                                        <th class="fs-9 text-body-secondary text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $doc)
                                        <tr>
                                            <td class="fs-9">
                                                <span class="fas fa-file me-1 text-body-tertiary"></span>{{ $doc->file_name }}
                                                <small class="text-body-tertiary d-block">{{ number_format($doc->file_size / 1024, 0) }} KB</small>
                                            </td>
                                            <td class="fs-9">{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}</td>
                                            <td class="fs-9 text-body-tertiary">{{ $doc->created_at->format('d M Y') }}<small class="d-block">{{ $doc->uploader?->name }}</small></td>
                                            <td class="text-center">
                                                @if($doc->verified_at)
                                                    <span class="badge badge-phoenix badge-phoenix-success">Verified</span>
                                                @else
                                                    <span class="badge badge-phoenix badge-phoenix-warning">Unverified</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('investments.documents.download', [$investment, $doc]) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Download">
                                                    <span class="fas fa-download"></span>
                                                </a>
                                                @if(!$doc->verified_at)
                                                    @can('verify investment documents')
                                                        <form action="{{ route('investments.documents.verify', [$investment, $doc]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm py-0 px-2" title="Verify"><span class="fas fa-check"></span></button>
                                                        </form>
                                                    @endcan
                                                @endif
                                                @can('delete investment documents')
                                                    <form action="{{ route('investments.documents.destroy', [$investment, $doc]) }}" method="POST" class="d-inline" data-confirm="Delete this document?">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Delete"><span class="fas fa-trash"></span></button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="history" role="tabpanel">
                    <h6 class="mb-3">Status History</h6>
                    @php $histories = $investment->statusHistories; @endphp
                    @if($histories->isEmpty())
                        <div class="alert alert-info mb-0">
                            <span class="fas fa-info-circle me-2"></span>No status changes recorded yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="fs-9 text-body-secondary">When</th>
                                        <th class="fs-9 text-body-secondary">Change</th>
                                        <th class="fs-9 text-body-secondary">Reason</th>
                                        <th class="fs-9 text-body-secondary">By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($histories as $h)
                                        <tr>
                                            <td class="fs-9 text-body-tertiary">{{ \Illuminate\Support\Carbon::parse($h->changed_at)->format('d M Y, h:i A') }}</td>
                                            <td class="fs-9">
                                                @if($h->status_from)
                                                    <span class="badge badge-phoenix badge-phoenix-secondary">{{ ucfirst($h->status_from) }}</span>
                                                    <span class="fas fa-arrow-right mx-1 text-body-tertiary"></span>
                                                @endif
                                                <span class="badge badge-phoenix badge-phoenix-info">{{ ucfirst($h->status_to) }}</span>
                                            </td>
                                            <td class="fs-9">{{ $h->reason ?? '—' }}</td>
                                            <td class="fs-9 text-body-tertiary">{{ $h->changedByUser?->name ?? 'System' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-3">
        @if($investment->status === 'draft')
            <form action="{{ route('investments.destroy', $investment) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this investment?">Delete</button>
            </form>
            <a href="{{ route('investments.edit', $investment) }}" class="btn btn-primary btn-sm">Edit</a>
        @endif
        @if($investment->canTransitionTo('active'))
            <form action="{{ route('investments.activate', $investment) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Activate</button>
            </form>
        @endif
    </div>
</div>

@can('create investment transactions')
<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('investments.transactions.store', $investment) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTransactionLabel">Add Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 px-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="transaction_type" required>
                            <option value="INITIAL_INVESTMENT" selected>Initial Investment</option>
                            <option value="ADDITIONAL_INVESTMENT">Additional Investment</option>
                            <option value="PROFIT_DISTRIBUTION">Profit Distribution</option>
                            <option value="DIVIDEND_PAYMENT">Dividend Payment</option>
                            <option value="LOSS_ADJUSTMENT">Loss Adjustment</option>
                            <option value="WITHDRAWAL">Withdrawal</option>
                            <option value="MATURITY_CLOSURE">Maturity Closure</option>
                            <option value="REINVESTMENT">Reinvestment</option>
                            <option value="ADMINISTRATIVE_ADJUSTMENT">Administrative Adjustment</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="amount" value="{{ old('amount') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g., bank slip / cheque no.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="2" required placeholder="What is this transaction for?">{{ old('description') }}</textarea>
                    </div>

                    @can('approve investment transactions')
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="approve_immediately" id="approveImmediately" value="1" checked>
                            <label class="form-check-label" for="approveImmediately">
                                Approve immediately (count toward totals right away)
                            </label>
                        </div>
                    @endcan
                    <div class="alert alert-warning py-2 px-3 mb-0 fs-9">
                        <span class="fas fa-info-circle me-1"></span>Unapproved transactions stay <strong>pending</strong> and don't affect the investment totals until approved.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan

@can('manage investment documents')
<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('investments.documents.store', $investment) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentLabel">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Document Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="document_type" required>
                            <option value="agreement">Agreement</option>
                            <option value="certificate">Certificate</option>
                            <option value="statement">Statement</option>
                            <option value="proof_of_investment">Proof of Investment</option>
                            <option value="valuation">Valuation</option>
                            <option value="contract">Contract</option>
                            <option value="legal_document">Legal Document</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" required accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx">
                        <small class="text-body-tertiary">PDF, image, Word or Excel. Max 10 MB.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Optional"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_public" id="docIsPublic" value="1">
                        <label class="form-check-label" for="docIsPublic">Visible to members</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('addTransactionModal'));
        modal.show();
    });
</script>
@endif
@endpush
