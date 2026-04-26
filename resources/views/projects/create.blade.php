@extends('layouts.phoenix')

@section('title', 'Create Project | Barakah')

@section('content')
    <h2 class="mb-4">Create an investment project</h2>

    <div class="row">
        <div class="col-xl-9">
            <form class="row g-3 mb-6" method="POST" action="{{ route('projects.store') }}">
                @csrf

                <div class="col-sm-6 col-md-8">
                    <div class="form-floating">
                        <input class="form-control @error('name') is-invalid @enderror" id="projectName" type="text" name="name" placeholder="Project title" value="{{ old('name') }}" required>
                        <label for="projectName">Project title</label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <select class="form-select @error('status') is-invalid @enderror" id="projectStatus" name="status" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <label for="projectStatus">Project status</label>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('category') is-invalid @enderror" id="projectCategory" type="text" name="category" placeholder="Category" value="{{ old('category') }}">
                        <label for="projectCategory">Category</label>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('budget_requested') is-invalid @enderror" id="budgetRequested" type="number" step="0.01" min="0" name="budget_requested" placeholder="Requested capital" value="{{ old('budget_requested') }}">
                        <label for="budgetRequested">Requested capital</label>
                        @error('budget_requested')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('budget_approved') is-invalid @enderror" id="budgetApproved" type="number" step="0.01" min="0" name="budget_approved" placeholder="Approved capital" value="{{ old('budget_approved') }}">
                        <label for="budgetApproved">Approved capital</label>
                        @error('budget_approved')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('expected_return_percentage') is-invalid @enderror" id="expectedReturn" type="number" step="0.01" min="0" max="999.99" name="expected_return_percentage" placeholder="Expected return %" value="{{ old('expected_return_percentage') }}">
                        <label for="expectedReturn">Expected return %</label>
                        @error('expected_return_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('start_date') is-invalid @enderror" id="startDate" type="date" name="start_date" value="{{ old('start_date') }}">
                        <label for="startDate">Start date</label>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('deadline') is-invalid @enderror" id="deadline" type="date" name="deadline" value="{{ old('deadline') }}">
                        <label for="deadline">Deadline</label>
                        @error('deadline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-floating">
                        <input class="form-control @error('progress_percentage') is-invalid @enderror" id="progressPercentage" type="number" min="0" max="100" name="progress_percentage" placeholder="Progress" value="{{ old('progress_percentage', 0) }}" required>
                        <label for="progressPercentage">Progress %</label>
                        @error('progress_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 gy-6">
                    <div class="form-floating">
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="projectNotes" name="notes" placeholder="Project notes" style="height: 120px">{{ old('notes') }}</textarea>
                        <label for="projectNotes">Project overview</label>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 gy-6">
                    <div class="row g-3 justify-content-end">
                        <div class="col-auto">
                            <a class="btn btn-phoenix-primary px-5" href="{{ route('projects.index') }}">Cancel</a>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary px-5 px-sm-15" type="submit">Create Project</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Field intent</h5>
                    <p class="fs-9 text-body-secondary mb-3">This first version is scoped to pooled association investments, so the form captures capital, timeline, and expected return before deeper workflow features arrive.</p>
                    <ul class="fs-9 text-body-secondary ps-3 mb-0">
                        <li class="mb-2">`Requested capital` is the amount proposed for the project.</li>
                        <li class="mb-2">`Approved capital` can remain empty until the group approves funding.</li>
                        <li class="mb-2">`Progress %` keeps the project list useful before task tracking exists.</li>
                        <li>`Status` is deliberately simple for the first persisted module.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
