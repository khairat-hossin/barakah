@extends('layouts.phoenix')

@section('title', 'Record Savings | Barakah')

@section('content')
    <h2 class="mb-4">Record a savings deposit</h2>

    <div class="row">
        <div class="col-xl-9">
            @if ($members->isEmpty())
                <div class="alert alert-subtle-warning border border-warning-subtle" role="alert">
                    No active members are available yet. Add members first before recording savings entries.
                </div>
            @endif

            <form class="row g-3 mb-6" method="POST" action="{{ route('savings.store') }}">
                @csrf

                <div class="col-sm-6 col-md-6">
                    <div class="form-floating">
                        <select class="form-select @error('member_id') is-invalid @enderror" id="memberId" name="member_id" required @disabled($members->isEmpty())>
                            <option value="">Select member</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $member->name }}{{ $member->member_code ? ' ('.$member->member_code.')' : '' }}</option>
                            @endforeach
                        </select>
                        <label for="memberId">Member</label>
                        @error('member_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-6">
                    <div class="form-floating">
                        <input class="form-control @error('amount') is-invalid @enderror" id="amount" type="number" min="0.01" step="0.01" name="amount" placeholder="Amount" value="{{ old('amount') }}" required>
                        <label for="amount">Amount</label>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('deposit_date') is-invalid @enderror" id="depositDate" type="date" name="deposit_date" value="{{ old('deposit_date', now()->toDateString()) }}" required>
                        <label for="depositDate">Deposit date</label>
                        @error('deposit_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('contribution_month') is-invalid @enderror" id="contributionMonth" type="date" name="contribution_month" value="{{ old('contribution_month') }}">
                        <label for="contributionMonth">Contribution month</label>
                        @error('contribution_month')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="paymentMethod" name="payment_method" required>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method }}" @selected(old('payment_method', 'cash') === $method)>{{ ucwords(str_replace('_', ' ', $method)) }}</option>
                            @endforeach
                        </select>
                        <label for="paymentMethod">Payment method</label>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-6">
                    <div class="form-floating">
                        <input class="form-control @error('reference') is-invalid @enderror" id="reference" type="text" name="reference" placeholder="Reference" value="{{ old('reference') }}">
                        <label for="reference">Reference / receipt</label>
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 gy-6">
                    <div class="form-floating">
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="entryNotes" name="notes" placeholder="Notes" style="height: 120px">{{ old('notes') }}</textarea>
                        <label for="entryNotes">Notes</label>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 gy-6">
                    <div class="row g-3 justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-phoenix-primary px-5" href="{{ route('savings.index') }}">Cancel</a>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary px-5 px-sm-15" type="submit" @disabled($members->isEmpty())>Record Entry</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Entry guidance</h5>
                    <ul class="fs-9 text-body-secondary ps-3 mb-0">
                        <li class="mb-2">Use `deposit date` for the actual day the money was received.</li>
                        <li class="mb-2">Use `contribution month` when the deposit belongs to a specific monthly cycle.</li>
                        <li class="mb-2">Keep `reference` for transaction IDs or receipt numbers.</li>
                        <li>Every entry is linked to the logged-in user who recorded it.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
