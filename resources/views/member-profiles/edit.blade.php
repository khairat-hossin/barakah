@extends('layouts.phoenix')

@section('title', 'Complete Your Profile | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('member-profiles.show', $member) }}">My Profile</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</nav>

@unless($member->hasCompleteProfile())
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <span class="fas fa-circle-exclamation fs-5 me-3"></span>
    <div>
        <strong>Please complete your profile.</strong>
        The fields marked <span class="text-danger">*</span> are required before you can continue to the rest of the system.
    </div>
</div>
@endunless

<div class="d-flex align-items-center mb-4">
    <div>
        <h2 class="mb-0">My Profile</h2>
        <p class="text-body-secondary mb-0">{{ $member->name }}
            <span class="badge badge-phoenix badge-phoenix-info ms-1">Code: {{ $member->member_code }}</span>
        </p>
    </div>
</div>

<form class="row g-3 mb-6" method="POST" action="{{ route('member-profiles.update', $member) }}">
    @csrf
    @method('PUT')

    <!-- PERSONAL INFORMATION -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">1. Personal Information</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('name_bn') is-invalid @enderror" id="nameBn" type="text" name="name_bn" placeholder="Full name (Bangla)" value="{{ old('name_bn', $member->name_bn) }}" required>
                            <label for="nameBn">Full name (Bangla) <span class="text-danger">*</span></label>
                            @error('name_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('father_name') is-invalid @enderror" id="fatherName" type="text" name="father_name" placeholder="Father name" value="{{ old('father_name', $member->father_name) }}" required>
                            <label for="fatherName">Father name <span class="text-danger">*</span></label>
                            @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('mother_name') is-invalid @enderror" id="motherName" type="text" name="mother_name" placeholder="Mother name" value="{{ old('mother_name', $member->mother_name) }}" required>
                            <label for="motherName">Mother name <span class="text-danger">*</span></label>
                            @error('mother_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('spouse_name') is-invalid @enderror" id="spouseName" type="text" name="spouse_name" placeholder="Spouse name" value="{{ old('spouse_name', $member->spouse_name) }}">
                            <label for="spouseName">Spouse name</label>
                            @error('spouse_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('date_of_birth') is-invalid @enderror" id="dateOfBirth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}" required>
                            <label for="dateOfBirth">Date of birth <span class="text-danger">*</span></label>
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">Select gender</option>
                                <option value="male" @selected(old('gender', $member->gender) === 'male')>Male</option>
                                <option value="female" @selected(old('gender', $member->gender) === 'female')>Female</option>
                                <option value="other" @selected(old('gender', $member->gender) === 'other')>Other</option>
                            </select>
                            <label for="gender">Gender</label>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <select class="form-select @error('marital_status') is-invalid @enderror" id="maritalStatus" name="marital_status">
                                <option value="">Select marital status</option>
                                <option value="single" @selected(old('marital_status', $member->marital_status) === 'single')>Single</option>
                                <option value="married" @selected(old('marital_status', $member->marital_status) === 'married')>Married</option>
                                <option value="divorced" @selected(old('marital_status', $member->marital_status) === 'divorced')>Divorced</option>
                                <option value="widowed" @selected(old('marital_status', $member->marital_status) === 'widowed')>Widowed</option>
                            </select>
                            <label for="maritalStatus">Marital status</label>
                            @error('marital_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('nationality') is-invalid @enderror" id="nationality" type="text" name="nationality" placeholder="Nationality" value="{{ old('nationality', $member->nationality) }}">
                            <label for="nationality">Nationality</label>
                            @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('nid_number') is-invalid @enderror" id="nidNumber" type="text" name="nid_number" placeholder="NID number" value="{{ old('nid_number', $member->nid_number) }}" required>
                            <label for="nidNumber">NID number <span class="text-danger">*</span></label>
                            @error('nid_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('birth_registration') is-invalid @enderror" id="birthRegistration" type="text" name="birth_registration" placeholder="Birth registration" value="{{ old('birth_registration', $member->birth_registration) }}">
                            <label for="birthRegistration">Birth registration number</label>
                            @error('birth_registration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('passport_number') is-invalid @enderror" id="passportNumber" type="text" name="passport_number" placeholder="Passport number" value="{{ old('passport_number', $member->passport_number) }}">
                            <label for="passportNumber">Passport number</label>
                            @error('passport_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('tax_id') is-invalid @enderror" id="taxId" type="text" name="tax_id" placeholder="Tax ID" value="{{ old('tax_id', $member->tax_id) }}">
                            <label for="taxId">Tax ID (TIN)</label>
                            @error('tax_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTACT INFORMATION -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">2. Contact Information</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('phone') is-invalid @enderror" id="memberPhone" type="text" name="phone" placeholder="Phone" value="{{ old('phone', $member->phone) }}" required>
                            <label for="memberPhone">Phone <span class="text-danger">*</span></label>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('secondary_mobile') is-invalid @enderror" id="secondaryMobile" type="text" name="secondary_mobile" placeholder="Secondary mobile" value="{{ old('secondary_mobile', $member->secondary_mobile) }}">
                            <label for="secondaryMobile">Secondary mobile</label>
                            @error('secondary_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('email') is-invalid @enderror" id="memberEmail" type="email" name="email" placeholder="Email" value="{{ old('email', $member->email) }}" required>
                            <label for="memberEmail">Email <span class="text-danger">*</span></label>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('whatsapp_number') is-invalid @enderror" id="whatsappNumber" type="text" name="whatsapp_number" placeholder="WhatsApp number" value="{{ old('whatsapp_number', $member->whatsapp_number) }}">
                            <label for="whatsappNumber">WhatsApp number</label>
                            @error('whatsapp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PERMANENT ADDRESS -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">3. Permanent Address</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('permanent_address_village') is-invalid @enderror" id="permanentVillage" type="text" name="permanent_address_village" placeholder="Village/House" value="{{ old('permanent_address_village', $member->permanent_address_village) }}" required>
                            <label for="permanentVillage">Village/House <span class="text-danger">*</span></label>
                            @error('permanent_address_village')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('permanent_address_po') is-invalid @enderror" id="permanentPO" type="text" name="permanent_address_po" placeholder="Post Office" value="{{ old('permanent_address_po', $member->permanent_address_po) }}" required>
                            <label for="permanentPO">Post Office <span class="text-danger">*</span></label>
                            @error('permanent_address_po')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('permanent_address_union') is-invalid @enderror" id="permanentUnion" type="text" name="permanent_address_union" placeholder="Union/Ward" value="{{ old('permanent_address_union', $member->permanent_address_union) }}">
                            <label for="permanentUnion">Union/Ward</label>
                            @error('permanent_address_union')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('permanent_address_upazila') is-invalid @enderror" id="permanentUpazila" type="text" name="permanent_address_upazila" placeholder="Upazila" value="{{ old('permanent_address_upazila', $member->permanent_address_upazila) }}" required>
                            <label for="permanentUpazila">Upazila <span class="text-danger">*</span></label>
                            @error('permanent_address_upazila')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('permanent_address_district') is-invalid @enderror" id="permanentDistrict" type="text" name="permanent_address_district" placeholder="District" value="{{ old('permanent_address_district', $member->permanent_address_district) }}" required>
                            <label for="permanentDistrict">District <span class="text-danger">*</span></label>
                            @error('permanent_address_district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('permanent_address_postal') is-invalid @enderror" id="permanentPostal" type="text" name="permanent_address_postal" placeholder="Postal code" value="{{ old('permanent_address_postal', $member->permanent_address_postal) }}" required>
                            <label for="permanentPostal">Postal code <span class="text-danger">*</span></label>
                            @error('permanent_address_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PRESENT ADDRESS -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">4. Present Address</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" id="sameAsPermanent" type="checkbox" name="same_as_permanent" value="1" @checked(old('same_as_permanent', $member->same_as_permanent))>
                            <label class="form-check-label" for="sameAsPermanent">Same as permanent address</label>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('present_address_village') is-invalid @enderror" id="presentVillage" type="text" name="present_address_village" placeholder="Village/House" value="{{ old('present_address_village', $member->present_address_village) }}">
                            <label for="presentVillage">Village/House</label>
                            @error('present_address_village')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('present_address_po') is-invalid @enderror" id="presentPO" type="text" name="present_address_po" placeholder="Post Office" value="{{ old('present_address_po', $member->present_address_po) }}">
                            <label for="presentPO">Post Office</label>
                            @error('present_address_po')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('present_address_union') is-invalid @enderror" id="presentUnion" type="text" name="present_address_union" placeholder="Union/Ward" value="{{ old('present_address_union', $member->present_address_union) }}">
                            <label for="presentUnion">Union/Ward</label>
                            @error('present_address_union')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('present_address_upazila') is-invalid @enderror" id="presentUpazila" type="text" name="present_address_upazila" placeholder="Upazila" value="{{ old('present_address_upazila', $member->present_address_upazila) }}">
                            <label for="presentUpazila">Upazila</label>
                            @error('present_address_upazila')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('present_address_district') is-invalid @enderror" id="presentDistrict" type="text" name="present_address_district" placeholder="District" value="{{ old('present_address_district', $member->present_address_district) }}">
                            <label for="presentDistrict">District</label>
                            @error('present_address_district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('present_address_postal') is-invalid @enderror" id="presentPostal" type="text" name="present_address_postal" placeholder="Postal code" value="{{ old('present_address_postal', $member->present_address_postal) }}">
                            <label for="presentPostal">Postal code</label>
                            @error('present_address_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROFESSIONAL INFORMATION -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">5. Professional Information</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('occupation') is-invalid @enderror" id="occupation" type="text" name="occupation" placeholder="Occupation" value="{{ old('occupation', $member->occupation) }}">
                            <label for="occupation">Occupation</label>
                            @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('business_name') is-invalid @enderror" id="businessName" type="text" name="business_name" placeholder="Business name" value="{{ old('business_name', $member->business_name) }}">
                            <label for="businessName">Business name</label>
                            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('trade_license_number') is-invalid @enderror" id="tradeLicense" type="text" name="trade_license_number" placeholder="Trade license number" value="{{ old('trade_license_number', $member->trade_license_number) }}">
                            <label for="tradeLicense">Trade license number</label>
                            @error('trade_license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('office_designation') is-invalid @enderror" id="officeDesignation" type="text" name="office_designation" placeholder="Office designation" value="{{ old('office_designation', $member->office_designation) }}">
                            <label for="officeDesignation">Office designation</label>
                            @error('office_designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating">
                            <input class="form-control @error('employer_name') is-invalid @enderror" id="employerName" type="text" name="employer_name" placeholder="Employer name" value="{{ old('employer_name', $member->employer_name) }}">
                            <label for="employerName">Employer name</label>
                            @error('employer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control @error('office_address') is-invalid @enderror" id="officeAddress" name="office_address" placeholder="Office address" style="height: 80px">{{ old('office_address', $member->office_address) }}</textarea>
                            <label for="officeAddress">Office address</label>
                            @error('office_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 gy-6">
        <div class="row g-3 justify-content-end">
            <div class="col-auto">
                <a class="btn btn-phoenix-secondary px-5" href="{{ route('member-profiles.show', $member) }}">Cancel</a>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary px-5 px-sm-15" type="submit">Save Profile</button>
            </div>
        </div>
    </div>
</form>

<script>
    (function () {
        var same = document.getElementById('sameAsPermanent');
        var pairs = [['presentVillage','permanentVillage'],['presentPO','permanentPO'],['presentUnion','permanentUnion'],
                     ['presentUpazila','permanentUpazila'],['presentDistrict','permanentDistrict'],['presentPostal','permanentPostal']];
        function apply() {
            var on = same.checked;
            pairs.forEach(function (p) {
                var pres = document.getElementById(p[0]), perm = document.getElementById(p[1]);
                if (on) { pres.value = perm.value; pres.readOnly = true; }
                else { pres.readOnly = false; }
            });
        }
        same.addEventListener('change', apply);
        window.addEventListener('load', function () { if (same.checked) apply(); });
    })();
</script>
@endsection
