@extends('layouts.phoenix')

@section('title', 'Member Details | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('members.index') }}">Members</a></li>
        <li class="breadcrumb-item active">{{ $member->name }}</li>
    </ol>
</nav>

<div class="pb-9">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center justify-content-between g-3 mb-3">
                <div class="col-12 col-md-auto">
                    <h2 class="mb-0">Member Details</h2>
                </div>
                <div class="col-12 col-md-auto">
                    <div class="d-flex gap-2">
                        <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                            <span class="fas fa-pencil me-2"></span><span>Edit</span>
                        </a>
                        <button class="btn btn-phoenix-secondary px-3 px-sm-5" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false">
                            <span class="fas fa-ellipsis"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-0">
                            <li><a class="dropdown-item" href="#!">Download Profile</a></li>
                            <li><a class="dropdown-item" href="#!">Print</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#!" onclick="if(confirm('Delete this member?')) { document.getElementById('deleteForm').submit(); }">Delete Member</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-0 g-md-4 g-xl-6">
        <!-- Left Sidebar -->
        <div class="col-md-5 col-lg-5 col-xl-4">
            <div class="sticky-leads-sidebar">
                <div class="lead-details-offcanvas bg-body scrollbar">
                    <!-- Member Avatar Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center g-3 text-center text-xxl-start">
                                <div class="col-12 col-xxl-auto">
                                    <div class="avatar avatar-5xl">
                                        <div class="avatar-name rounded-circle bg-primary-subtle">
                                            <span class="text-primary fw-bold">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-auto flex-1">
                                    <h3 class="fw-bolder mb-2">{{ $member->name }}</h3>
                                    <p class="mb-2 text-body-secondary">{{ $member->member_code }}</p>
                                    <div class="mb-3">
                                        <span class="badge badge-phoenix @if($member->status === 'active') badge-phoenix-success @elseif($member->status === 'inactive') badge-phoenix-secondary @else badge-phoenix-warning @endif">
                                            {{ ucfirst($member->status) }}
                                        </span>
                                    </div>
                                    <!-- Profile Completeness Badge -->
                                    <x-profile-completeness :member="$member" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Personal Information</h3>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-link px-3">Edit</a>
                            </div>

                            @if($member->name_bn)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-signature"></span>
                                    <h5 class="text-body-highlight mb-0">Name (Bangla)</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->name_bn }}</p>
                            </div>
                            @endif

                            @if($member->date_of_birth)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-birthday-cake"></span>
                                    <h5 class="text-body-highlight mb-0">Date of Birth</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y') }}</p>
                            </div>
                            @endif

                            @if($member->gender)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-venus-mars"></span>
                                    <h5 class="text-body-highlight mb-0">Gender</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ ucfirst($member->gender) }}</p>
                            </div>
                            @endif

                            @if($member->nationality)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-globe"></span>
                                    <h5 class="text-body-highlight mb-0">Nationality</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->nationality }}</p>
                            </div>
                            @endif

                            @if($member->marital_status)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-heart"></span>
                                    <h5 class="text-body-highlight mb-0">Marital Status</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ ucfirst($member->marital_status) }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Contact Information</h3>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-link px-3">Edit</a>
                            </div>

                            @if($member->email)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-envelope"></span>
                                    <h5 class="text-body-highlight mb-0">Email</h5>
                                </div>
                                <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                            </div>
                            @endif

                            @if($member->phone)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-phone"></span>
                                    <h5 class="text-body-highlight mb-0">Phone</h5>
                                </div>
                                <a href="tel:{{ $member->phone }}">{{ $member->phone }}</a>
                            </div>
                            @endif

                            @if($member->secondary_mobile)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-mobile-alt"></span>
                                    <h5 class="text-body-highlight mb-0">Secondary Mobile</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->secondary_mobile }}</p>
                            </div>
                            @endif

                            @if($member->whatsapp_number)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fab fa-whatsapp"></span>
                                    <h5 class="text-body-highlight mb-0">WhatsApp</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->whatsapp_number }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Identity Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Identity Documents</h3>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-link px-3">Edit</a>
                            </div>

                            @if($member->nid_number)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-id-card"></span>
                                    <h5 class="text-body-highlight mb-0">NID</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->nid_number }}</p>
                            </div>
                            @endif

                            @if($member->birth_registration)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-certificate"></span>
                                    <h5 class="text-body-highlight mb-0">Birth Registration</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->birth_registration }}</p>
                            </div>
                            @endif

                            @if($member->passport_number)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-passport"></span>
                                    <h5 class="text-body-highlight mb-0">Passport</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->passport_number }}</p>
                            </div>
                            @endif

                            @if($member->tax_id)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-file-invoice"></span>
                                    <h5 class="text-body-highlight mb-0">Tax ID</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->tax_id }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Address Information Card -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Addresses</h3>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-link px-3">Edit</a>
                            </div>

                            @if($member->permanent_address_village)
                            <div class="mb-4">
                                <h5 class="text-body-highlight mb-3">Permanent Address</h5>
                                <div class="mb-2">
                                    <span class="text-body-secondary">{{ $member->permanent_address_village }}, {{ $member->permanent_address_po }}, {{ $member->permanent_address_union }}, {{ $member->permanent_address_upazila }}, {{ $member->permanent_address_district }} - {{ $member->permanent_address_postal }}</span>
                                </div>
                            </div>
                            @endif

                            @if($member->present_address_village)
                            <div class="mb-4">
                                <h5 class="text-body-highlight mb-3">Present Address</h5>
                                <div class="mb-2">
                                    <span class="text-body-secondary">{{ $member->present_address_village }}, {{ $member->present_address_po }}, {{ $member->present_address_union }}, {{ $member->present_address_upazila }}, {{ $member->present_address_district }} - {{ $member->present_address_postal }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Professional Information Card -->
                    @if($member->occupation || $member->business_name || $member->office_designation || $member->employer_name)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-5">
                                <h3>Professional Information</h3>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-link px-3">Edit</a>
                            </div>

                            @if($member->occupation)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-briefcase"></span>
                                    <h5 class="text-body-highlight mb-0">Occupation</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->occupation }}</p>
                            </div>
                            @endif

                            @if($member->business_name)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-store"></span>
                                    <h5 class="text-body-highlight mb-0">Business Name</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->business_name }}</p>
                            </div>
                            @endif

                            @if($member->office_designation)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-user-tie"></span>
                                    <h5 class="text-body-highlight mb-0">Office Designation</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->office_designation }}</p>
                            </div>
                            @endif

                            @if($member->employer_name)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-building"></span>
                                    <h5 class="text-body-highlight mb-0">Employer</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->employer_name }}</p>
                            </div>
                            @endif

                            @if($member->office_address)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 fas fa-map-marker-alt"></span>
                                    <h5 class="text-body-highlight mb-0">Office Address</h5>
                                </div>
                                <p class="mb-0 text-body-secondary">{{ $member->office_address }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Content -->
        <div class="col-md-7 col-lg-7 col-xl-8">
            <!-- Summary Cards Section -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-success border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">Total Shares</p>
                                    <h4 class="mb-0">{{ $member->shares()->count() }}</h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-success rounded-pill">{{ $member->shares()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-primary border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">EMI/Month</p>
                                    <h4 class="mb-0">৳ {{ number_format($emiPerMonth ?? 0, 0) }}</h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-primary rounded-pill">Monthly</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-info border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">Total Deposit</p>
                                    <h4 class="mb-0">৳ {{ number_format($member->savingsEntries()->sum('amount'), 0) }}</h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-info rounded-pill">All Time</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card summary-card bg-body-highlight border-start border-warning border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-2">Pending EMI</p>
                                    <h4 class="mb-0">0</h4>
                                </div>
                                <span class="badge badge-phoenix badge-phoenix-warning rounded-pill">Due</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deposits Section -->
            <div class="card mb-4">
                <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-arrow-down-to-line me-2 text-success"></span>Recent Deposits
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $deposits = $member->savingsEntries()->orderByDesc('deposit_date')->get();
                    @endphp
                    @if($deposits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover fs-9 mb-0">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="fw-semibold">Amount</th>
                                        <th class="fw-semibold">Date</th>
                                        <th class="fw-semibold">Method</th>
                                        <th class="fw-semibold">TXN ID</th>
                                        <th class="fw-semibold">Recorded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deposits as $deposit)
                                    <tr>
                                        <td class="fw-semibold">৳ {{ number_format($deposit->amount, 2) }}</td>
                                        <td>{{ $deposit->deposit_date->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($deposit->payment_method) {
                                                    'cash' => 'badge-phoenix-primary',
                                                    'bank_transfer' => 'badge-phoenix-info',
                                                    'mobile_banking' => 'badge-phoenix-success',
                                                    'check' => 'badge-phoenix-warning',
                                                    'other' => 'badge-phoenix-secondary',
                                                    default => 'badge-phoenix-secondary'
                                                };
                                            @endphp
                                            <span class="badge badge-phoenix {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $deposit->payment_method)) }}</span>
                                        </td>
                                        <td><code class="text-body-tertiary fs-10">{{ $deposit->transaction_id ?? '-' }}</code></td>
                                        <td>{{ $deposit->recorder?->name ?? 'System' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No deposits recorded yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Nominees Section - Card Grid Layout -->
            <div class="card mb-4">
                <div class="card-header bg-info-subtle d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-users me-2 text-info"></span>Nominees
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNomineeModal">
                        <span class="fas fa-plus me-2"></span>Add Nominee
                    </button>
                </div>
                <div class="card-body">
                    @php
                        $nominees = $member->nominees()->get();
                        $totalAllocation = $nominees->sum('allocation_percentage');
                    @endphp
                    @if($nominees->count() > 0)
                        <div class="row g-3">
                            @foreach($nominees as $nominee)
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-body-tertiary-hover">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-semibold mb-0">{{ $nominee->full_name }}</h6>
                                        @if($nominee->is_primary)
                                        <span class="badge badge-phoenix badge-phoenix-success">Primary</span>
                                        @endif
                                    </div>
                                    <p class="text-body-secondary fs-9 mb-2">{{ ucfirst($nominee->relationship) }}</p>
                                    <div class="mb-3">
                                        <p class="text-body-tertiary fs-9 mb-1">Allocation</p>
                                        <p class="fw-bold mb-0">{{ $nominee->allocation_percentage }}%</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-info flex-grow-1" data-bs-toggle="modal" data-bs-target="#viewNomineeModal{{ $nominee->id }}">
                                            <span class="fas fa-eye me-1"></span>View
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNomineeModal{{ $nominee->id }}">
                                            <span class="fas fa-edit me-1"></span>Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteNominee({{ $nominee->id }})">
                                            <span class="fas fa-trash"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-body-secondary fs-9 mt-3 pt-3 border-top">
                            <strong>Total Allocation: {{ $totalAllocation }}%</strong>
                            @if($totalAllocation < 100)
                            <span class="ms-2">({{ 100 - $totalAllocation }}% remaining)</span>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No nominees added yet</p>
                            <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addNomineeModal">
                                <span class="fas fa-plus me-2"></span>Add First Nominee
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents Section - Table Layout -->
            <div class="card mb-4">
                <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="fas fa-file me-2 text-warning"></span>Documents
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                        <span class="fas fa-plus me-2"></span>Upload Document
                    </button>
                </div>
                <div class="card-body">
                    @php
                        $documents = $member->documents()->orderByDesc('upload_date')->get();
                    @endphp
                    @if($documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover fs-9 mb-0">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th class="fw-semibold">Document</th>
                                        <th class="fw-semibold">Type</th>
                                        <th class="fw-semibold">Verified</th>
                                        <th class="fw-semibold">Uploaded</th>
                                        <th class="fw-semibold text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $document)
                                    <tr>
                                        <td class="fw-semibold">
                                            <span class="fas fa-file-pdf text-danger me-2"></span>{{ $document->file_name }}
                                        </td>
                                        <td>{{ config('constants.document_types.' . $document->document_type, $document->document_type) }}</td>
                                        <td>
                                            @if($document->verified)
                                            <span class="badge badge-phoenix badge-phoenix-success">Verified</span>
                                            @else
                                            <span class="badge badge-phoenix badge-phoenix-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $document->upload_date->format('d M Y') }}</td>
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewDocumentModal{{ $document->id }}">
                                                    <span class="fas fa-eye"></span>
                                                </button>
                                                <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary">
                                                    <span class="fas fa-download"></span>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument({{ $document->id }})">
                                                    <span class="fas fa-trash"></span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No documents uploaded yet</p>
                            <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <span class="fas fa-plus me-2"></span>Upload Document
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Nominee Modal -->
<div class="modal fade" id="addNomineeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Nominee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('nominees.store', $member) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @php
                        $remainingAllocation = 100 - $nominees->sum('allocation_percentage');
                    @endphp

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" placeholder="Enter nominee's full name" required>
                        @error('full_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Father's name">
                        </div>
                        <div class="col-md-6">
                            <label for="mother_name" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name" placeholder="Mother's name">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                        </div>
                        <div class="col-md-6">
                            <label for="relationship" class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select class="form-select @error('relationship') is-invalid @enderror" id="relationship" name="relationship" required>
                                <option value="">Select relationship...</option>
                                <option value="son">Son</option>
                                <option value="daughter">Daughter</option>
                                <option value="wife">Wife</option>
                                <option value="husband">Husband</option>
                                <option value="parent">Parent</option>
                                <option value="sibling">Sibling</option>
                                <option value="other">Other</option>
                            </select>
                            @error('relationship')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="nid_number" class="form-label">NID Number</label>
                            <input type="text" class="form-control" id="nid_number" name="nid_number" placeholder="NID">
                        </div>
                        <div class="col-md-6">
                            <label for="birth_registration" class="form-label">Birth Registration</label>
                            <input type="text" class="form-control" id="birth_registration" name="birth_registration" placeholder="BRN">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="mobile_number" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Phone">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label for="allocation_percentage" class="form-label">Allocation Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('allocation_percentage') is-invalid @enderror" id="allocation_percentage" name="allocation_percentage" min="1" max="{{ $remainingAllocation }}" placeholder="Enter percentage" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-body-secondary">Maximum available: {{ $remainingAllocation }}%</small>
                        @error('allocation_percentage')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter address"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary">
                            Set as Primary Nominee
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Nominee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Nominee Modal (dynamic for each nominee) -->
@php
    $nominees = $member->nominees()->get();
@endphp
@foreach($nominees as $nominee)
<div class="modal fade" id="viewNomineeModal{{ $nominee->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nominee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-body-tertiary rounded">
                            <h6 class="fw-semibold mb-2">{{ $nominee->full_name }}</h6>
                            <div class="d-flex gap-2">
                                <span class="badge badge-phoenix badge-phoenix-info">{{ ucfirst($nominee->relationship) }}</span>
                                @if($nominee->is_primary)
                                <span class="badge badge-phoenix badge-phoenix-success">Primary Nominee</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Father's Name</label>
                        <p class="mb-0">{{ $nominee->father_name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Mother's Name</label>
                        <p class="mb-0">{{ $nominee->mother_name ?? '-' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Date of Birth</label>
                        <p class="mb-0">{{ $nominee->date_of_birth?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Relationship</label>
                        <p class="mb-0">{{ ucfirst($nominee->relationship) }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">NID Number</label>
                        <p class="mb-0">{{ $nominee->nid_number ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Birth Registration</label>
                        <p class="mb-0">{{ $nominee->birth_registration ?? '-' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Mobile Number</label>
                        <p class="mb-0">
                            @if($nominee->mobile_number)
                                <a href="tel:{{ $nominee->mobile_number }}">{{ $nominee->mobile_number }}</a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Email</label>
                        <p class="mb-0">
                            @if($nominee->email)
                                <a href="mailto:{{ $nominee->email }}">{{ $nominee->email }}</a>
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-body-secondary fs-9">Address</label>
                        <p class="mb-0">{{ $nominee->address ?? '-' }}</p>
                    </div>

                    <div class="col-12">
                        <div class="p-3 bg-info-subtle rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-body-secondary fs-9 mb-1">Allocation Percentage</p>
                                    <h5 class="mb-0">{{ $nominee->allocation_percentage }}%</h5>
                                </div>
                                <div class="text-end">
                                    <p class="text-body-secondary fs-9 mb-1">Created</p>
                                    <p class="text-body-secondary fs-9 mb-0">{{ $nominee->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editNomineeModal{{ $nominee->id }}">
                    <span class="fas fa-edit me-2"></span>Edit
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Edit Nominee Modal (dynamic for each nominee) -->
@foreach($nominees as $nominee)
<div class="modal fade" id="editNomineeModal{{ $nominee->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Nominee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('nominees.update', [$member, $nominee]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @php
                        $otherAllocation = $member->nominees()
                            ->where('id', '!=', $nominee->id)
                            ->sum('allocation_percentage');
                        $remainingForEdit = 100 - $otherAllocation;
                    @endphp

                    <div class="mb-3">
                        <label for="edit_full_name{{ $nominee->id }}" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_full_name{{ $nominee->id }}" name="full_name" value="{{ old('full_name', $nominee->full_name) }}" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="edit_father_name{{ $nominee->id }}" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="edit_father_name{{ $nominee->id }}" name="father_name" value="{{ old('father_name', $nominee->father_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_mother_name{{ $nominee->id }}" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" id="edit_mother_name{{ $nominee->id }}" name="mother_name" value="{{ old('mother_name', $nominee->mother_name) }}">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="edit_date_of_birth{{ $nominee->id }}" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="edit_date_of_birth{{ $nominee->id }}" name="date_of_birth" value="{{ old('date_of_birth', $nominee->date_of_birth?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_relationship{{ $nominee->id }}" class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_relationship{{ $nominee->id }}" name="relationship" required>
                                <option value="">Select relationship...</option>
                                <option value="son" @selected(old('relationship', $nominee->relationship) === 'son')>Son</option>
                                <option value="daughter" @selected(old('relationship', $nominee->relationship) === 'daughter')>Daughter</option>
                                <option value="wife" @selected(old('relationship', $nominee->relationship) === 'wife')>Wife</option>
                                <option value="husband" @selected(old('relationship', $nominee->relationship) === 'husband')>Husband</option>
                                <option value="parent" @selected(old('relationship', $nominee->relationship) === 'parent')>Parent</option>
                                <option value="sibling" @selected(old('relationship', $nominee->relationship) === 'sibling')>Sibling</option>
                                <option value="other" @selected(old('relationship', $nominee->relationship) === 'other')>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="edit_nid_number{{ $nominee->id }}" class="form-label">NID Number</label>
                            <input type="text" class="form-control" id="edit_nid_number{{ $nominee->id }}" name="nid_number" value="{{ old('nid_number', $nominee->nid_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_birth_registration{{ $nominee->id }}" class="form-label">Birth Registration</label>
                            <input type="text" class="form-control" id="edit_birth_registration{{ $nominee->id }}" name="birth_registration" value="{{ old('birth_registration', $nominee->birth_registration) }}">
                        </div>
                    </div>

                    <div class="row g-2 mt-0">
                        <div class="col-md-6">
                            <label for="edit_mobile_number{{ $nominee->id }}" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="edit_mobile_number{{ $nominee->id }}" name="mobile_number" value="{{ old('mobile_number', $nominee->mobile_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email{{ $nominee->id }}" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email{{ $nominee->id }}" name="email" value="{{ old('email', $nominee->email) }}">
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label for="edit_allocation_percentage{{ $nominee->id }}" class="form-label">Allocation Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="edit_allocation_percentage{{ $nominee->id }}" name="allocation_percentage" min="1" max="{{ $remainingForEdit }}" value="{{ old('allocation_percentage', $nominee->allocation_percentage) }}" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-body-secondary">Maximum available: {{ $remainingForEdit }}%</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_address{{ $nominee->id }}" class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address{{ $nominee->id }}" name="address" rows="2">{{ old('address', $nominee->address) }}</textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_primary{{ $nominee->id }}" name="is_primary" value="1" @checked(old('is_primary', $nominee->is_primary))>
                        <label class="form-check-label" for="edit_is_primary{{ $nominee->id }}">
                            Set as Primary Nominee
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Nominee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('documents.store', $member) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('document_type') is-invalid @enderror" id="document_type" name="document_type" required>
                            <option value="">Select document type...</option>
                            <option value="nid_copy">NID Copy</option>
                            <option value="birth_registration_copy">Birth Registration Copy</option>
                            <option value="trade_license">Trade License</option>
                            <option value="passport_copy">Passport Copy</option>
                            <option value="membership_agreement">Membership Agreement</option>
                            <option value="nominee_form">Nominee Form</option>
                            <option value="bank_account_proof">Bank Account Proof</option>
                            <option value="other_attachment">Other Attachment</option>
                        </select>
                        @error('document_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <small class="text-body-secondary">Allowed: PDF, JPG, PNG (Max 5MB)</small>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Add any remarks about this document"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Document Modal (dynamic for each document) -->
@php
    $documents = $member->documents()->orderByDesc('upload_date')->get();
@endphp
@foreach($documents as $document)
<div class="modal fade" id="viewDocumentModal{{ $document->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-body-tertiary rounded">
                            <p class="text-body-secondary fs-9 mb-2">File Name</p>
                            <p class="fw-semibold mb-0">{{ $document->file_name }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Document Type</label>
                        <p class="mb-0">{{ config('constants.document_types.' . $document->document_type, $document->document_type) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">File Size</label>
                        <p class="mb-0">{{ number_format($document->file_size / 1024, 2) }} KB</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Upload Date</label>
                        <p class="mb-0">{{ $document->upload_date->format('d M Y, g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-body-secondary fs-9">Uploaded By</label>
                        <p class="mb-0">{{ $document->uploader?->name ?? 'System' }}</p>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-body-secondary fs-9">Status</label>
                        <p class="mb-0">
                            @if($document->verified)
                                <span class="badge badge-phoenix badge-phoenix-success">
                                    <span class="fas fa-check-circle me-1"></span>Verified
                                </span>
                                @if($document->verification_date)
                                    <small class="text-body-secondary ms-2">on {{ $document->verification_date->format('d M Y') }}</small>
                                @endif
                            @else
                                <span class="badge badge-phoenix badge-phoenix-warning">
                                    <span class="fas fa-clock me-1"></span>Pending Verification
                                </span>
                            @endif
                        </p>
                    </div>

                    @if($document->remarks)
                    <div class="col-12">
                        <label class="form-label text-body-secondary fs-9">Remarks</label>
                        <p class="mb-0">{{ $document->remarks }}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                    <span class="fas fa-download me-2"></span>Download
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Hidden Delete Form -->
<form id="deleteNomineeForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Hidden Delete Form for Document -->
<form id="deleteDocumentForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Hidden Delete Form for Member -->
<form id="deleteForm" action="{{ route('members.destroy', $member) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteNominee(nomineeId) {
    if (confirm('Are you sure you want to delete this nominee?')) {
        const form = document.getElementById('deleteNomineeForm');
        form.action = `/members/{{ $member->id }}/nominees/${nomineeId}`;
        form.submit();
    }
}

function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        const form = document.getElementById('deleteDocumentForm');
        form.action = `/documents/${documentId}`;
        form.submit();
    }
}
</script>
@endsection
