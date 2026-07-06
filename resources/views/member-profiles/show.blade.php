@extends('layouts.phoenix')

@section('title', 'My Profile | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item active">My Profile</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">My Profile</h2>
        <a href="{{ route('member-profiles.edit', $member) }}" class="btn btn-primary">
            <span class="fas fa-pen me-2"></span>Edit Profile
        </a>
    </div>

    <div class="row g-4">
        <!-- Identity + stats -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($member->photo_url)
                            <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="rounded-circle border" style="width:96px;height:96px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center" style="width:96px;height:96px;">
                                <span class="text-primary fw-bold fs-2">{{ $member->initials }}</span>
                            </div>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $member->name }}</h4>
                    @if($member->name_bn)<p class="text-body-secondary mb-1">{{ $member->name_bn }}</p>@endif
                    <p class="text-body-secondary mb-2">{{ $member->member_code }}</p>
                    <span class="badge badge-phoenix @if($member->status === 'active') badge-phoenix-success @else badge-phoenix-secondary @endif">
                        {{ ucfirst($member->status) }}
                    </span>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-body-secondary">Total Shares</span>
                        <span class="fw-bold">{{ $totalShares }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-body-secondary">Monthly Deposit (EMI)</span>
                        <span class="fw-bold">৳ {{ number_format($emiPerMonth ?? 0, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-body-secondary">Total Deposited</span>
                        <span class="fw-bold text-success">৳ {{ number_format($member->savingsEntries()->sum('amount'), 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-body-secondary">Nominee Allocation</span>
                        <span class="fw-bold">{{ $nomineeAllocation }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="col-lg-8">
            <!-- Personal information -->
            <div class="card mb-4">
                <div class="card-header bg-body-tertiary"><h5 class="mb-0">Personal Information</h5></div>
                <div class="card-body">
                    <dl class="row mb-0 fs-9">
                        @php
                            $rows = [
                                'Father\'s Name' => $member->father_name,
                                'Mother\'s Name' => $member->mother_name,
                                'Spouse Name' => $member->spouse_name,
                                'Date of Birth' => $member->date_of_birth?->format('d M Y'),
                                'Gender' => $member->gender ? ucfirst($member->gender) : null,
                                'Marital Status' => $member->marital_status ? ucfirst($member->marital_status) : null,
                                'Email' => $member->email,
                                'Phone' => $member->phone,
                                'WhatsApp' => $member->whatsapp_number,
                                'NID' => $member->nid_number,
                                'Occupation' => $member->occupation,
                            ];
                        @endphp
                        @foreach($rows as $label => $value)
                            @if(filled($value))
                                <dt class="col-sm-4 text-body-secondary">{{ $label }}</dt>
                                <dd class="col-sm-8">{{ $value }}</dd>
                            @endif
                        @endforeach
                        @php
                            $address = collect([
                                $member->present_address_village, $member->present_address_po,
                                $member->present_address_upazila, $member->present_address_district,
                            ])->filter()->implode(', ');
                        @endphp
                        @if($address)
                            <dt class="col-sm-4 text-body-secondary">Address</dt>
                            <dd class="col-sm-8">{{ $address }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Recent deposits -->
            <div class="card mb-4">
                <div class="card-header bg-body-tertiary"><h5 class="mb-0">Recent Deposits</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover fs-9 mb-0">
                            <thead><tr><th>Date</th><th>Method</th><th>Txn ID</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @forelse($member->savingsEntries()->latest('deposit_date')->limit(12)->get() as $d)
                                    <tr>
                                        <td>{{ $d->deposit_date?->format('d M Y') }}</td>
                                        <td>{{ ucfirst(str_replace('_',' ', $d->payment_method)) }}</td>
                                        <td class="text-body-tertiary">{{ $d->transaction_id ?: '—' }}</td>
                                        <td class="text-end fw-semibold">৳ {{ number_format($d->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-body-secondary py-3">No deposits yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Nominees -->
            @if($member->nominees->isNotEmpty())
                <div class="card">
                    <div class="card-header bg-body-tertiary"><h5 class="mb-0">Nominees</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm fs-9 mb-0">
                                <thead><tr><th>Name</th><th>Relation</th><th class="text-end">Allocation</th></tr></thead>
                                <tbody>
                                    @foreach($member->nominees as $n)
                                        <tr>
                                            <td>{{ $n->name }}</td>
                                            <td>{{ $n->relationship ?? $n->relation ?? '—' }}</td>
                                            <td class="text-end">{{ $n->allocation_percentage }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
