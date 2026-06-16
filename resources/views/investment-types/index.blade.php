@extends('layouts.phoenix')
@section('title', 'Investment Types | Barakah')
@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Investment Types</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Investment Types</h2>
            <p class="text-body-secondary">Manage investment type categories and configurations</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('investment-types.create') }}" class="btn btn-primary">
                <span class="fas fa-plus me-2"></span>New Type
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="fw-semibold">CODE</th>
                        <th class="fw-semibold">NAME</th>
                        <th class="fw-semibold">CATEGORY</th>
                        <th class="fw-semibold">RETURN TYPE</th>
                        <th class="fw-semibold">TENURE</th>
                        <th class="fw-semibold">STATUS</th>
                        <th class="fw-semibold">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark">{{ $type->code }}</span>
                        </td>
                        <td class="fw-semibold">{{ $type->name }}</td>
                        <td>{{ $type->category ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($type->default_return_type) }}</span>
                        </td>
                        <td>{{ $type->default_tenure_months ?? '-' }} months</td>
                        <td>
                            @if($type->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('investment-types.edit', $type) }}" class="btn btn-sm btn-outline-primary">
                                <span class="fas fa-edit me-1"></span>Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <p class="text-body-secondary mb-2">No investment types found</p>
                            <a href="{{ route('investment-types.create') }}" class="btn btn-sm btn-primary">Create the first type</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $types->links() }}
    </div>
</div>
@endsection
