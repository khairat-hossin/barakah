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
                                    <p class="mb-1 text-body-secondary">{{ $member->member_code }}</p>
                                    <span class="badge badge-phoenix @if($member->status === 'active') badge-phoenix-success @elseif($member->status === 'inactive') badge-phoenix-secondary @else badge-phoenix-warning @endif">
                                        {{ ucfirst($member->status) }}
                                    </span>
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
            <!-- Tabs for Additional Information -->
            <div class="card">
                <div class="card-header bg-body-tertiary">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="shares-tab" data-bs-toggle="tab" data-bs-target="#shares" type="button" role="tab" aria-controls="shares" aria-selected="true">
                                <span class="fas fa-share me-2"></span>Shares
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="nominees-tab" data-bs-toggle="tab" data-bs-target="#nominees" type="button" role="tab" aria-controls="nominees" aria-selected="false">
                                <span class="fas fa-users me-2"></span>Nominees
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false">
                                <span class="fas fa-file me-2"></span>Documents
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Shares Tab -->
                        <div class="tab-pane fade show active" id="shares" role="tabpanel" aria-labelledby="shares-tab">
                            <div class="row align-items-center mb-3">
                                <div class="col">
                                    <h5>Share Ownership</h5>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Share #</th>
                                            <th>Status</th>
                                            <th>Owned Since</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($member->shares()->get() as $share)
                                        <tr>
                                            <td>{{ $share->share_number }}</td>
                                            <td><span class="badge badge-phoenix badge-phoenix-success">{{ ucfirst($share->status) }}</span></td>
                                            <td>{{ $share->ownershipHistory()->where('member_id', $member->id)->latest()->first()?->created_at->format('d M Y') ?? '-' }}</td>
                                            <td><a href="#!" class="btn btn-sm btn-phoenix-secondary">View</a></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-body-secondary py-4">No shares owned</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Nominees Tab -->
                        <div class="tab-pane fade" id="nominees" role="tabpanel" aria-labelledby="nominees-tab">
                            <div class="row align-items-center mb-3">
                                <div class="col">
                                    <h5>Nominees</h5>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="btn btn-sm btn-primary">Add Nominee</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Allocation</th>
                                            <th>Primary</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($member->nominees()->get() as $nominee)
                                        <tr>
                                            <td>{{ $nominee->name }}</td>
                                            <td>{{ $nominee->allocation_percentage }}%</td>
                                            <td>
                                                @if($nominee->is_primary)
                                                <span class="badge badge-phoenix badge-phoenix-success">Yes</span>
                                                @else
                                                <span class="badge badge-phoenix badge-phoenix-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-phoenix-secondary" type="button">Edit</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-body-secondary py-4">No nominees added</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                            <div class="row align-items-center mb-3">
                                <div class="col">
                                    <h5>Documents</h5>
                                </div>
                                <div class="col-auto">
                                    <a href="#!" class="btn btn-sm btn-primary">Upload Document</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Type</th>
                                            <th>Verified</th>
                                            <th>Uploaded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($member->documents()->get() as $document)
                                        <tr>
                                            <td>{{ $document->title }}</td>
                                            <td>{{ $document->document_type }}</td>
                                            <td>
                                                @if($document->is_verified)
                                                <span class="badge badge-phoenix badge-phoenix-success">Yes</span>
                                                @else
                                                <span class="badge badge-phoenix badge-phoenix-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>{{ $document->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="#!" class="btn btn-sm btn-phoenix-secondary">Download</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-body-secondary py-4">No documents uploaded</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" action="{{ route('members.destroy', $member) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection
