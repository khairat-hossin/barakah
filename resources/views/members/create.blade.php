@extends('layouts.phoenix')

@section('title', 'Add Member | Barakah')

@section('content')
    <h2 class="mb-4">Onboard a member</h2>

    <div class="row">
        <div class="col-xl-9">
            <form class="row g-3 mb-6" method="POST" action="{{ route('members.store') }}">
                @csrf

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('member_code') is-invalid @enderror" id="memberCode" type="text" name="member_code" placeholder="Member code" value="{{ old('member_code') }}">
                        <label for="memberCode">Member code</label>
                        @error('member_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-8">
                    <div class="form-floating">
                        <input class="form-control @error('name') is-invalid @enderror" id="memberName" type="text" name="name" placeholder="Full name" value="{{ old('name') }}" required>
                        <label for="memberName">Full name</label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <select class="form-select @error('status') is-invalid @enderror" id="memberStatus" name="status" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', 'active') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <label for="memberStatus">Status</label>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('phone') is-invalid @enderror" id="memberPhone" type="text" name="phone" placeholder="Phone" value="{{ old('phone') }}">
                        <label for="memberPhone">Phone</label>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('email') is-invalid @enderror" id="memberEmail" type="email" name="email" placeholder="Email" value="{{ old('email') }}">
                        <label for="memberEmail">Email</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('join_date') is-invalid @enderror" id="joinDate" type="date" name="join_date" value="{{ old('join_date') }}">
                        <label for="joinDate">Join date</label>
                        @error('join_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('monthly_saving_amount') is-invalid @enderror" id="monthlySavingAmount" type="number" min="0" step="0.01" name="monthly_saving_amount" placeholder="Monthly saving amount" value="{{ old('monthly_saving_amount') }}">
                        <label for="monthlySavingAmount">Monthly saving amount</label>
                        @error('monthly_saving_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 gy-6">
                    <div class="form-floating">
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="memberNotes" name="notes" placeholder="Notes" style="height: 120px">{{ old('notes') }}</textarea>
                        <label for="memberNotes">Notes</label>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 gy-6">
                    <div class="row g-3 justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-phoenix-primary px-5" href="{{ route('members.index') }}">Cancel</a>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary px-5 px-sm-15" type="submit">Add Member</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Use this for existing members</h5>
                    <ul class="fs-9 text-body-secondary ps-3 mb-0">
                        <li class="mb-2">Add all current association members first.</li>
                        <li class="mb-2">Set `monthly saving amount` to the expected recurring contribution.</li>
                        <li class="mb-2">Keep `member code` optional unless your group already uses a numbering scheme.</li>
                        <li>After onboarding, record actual deposits from the savings entry screen.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
