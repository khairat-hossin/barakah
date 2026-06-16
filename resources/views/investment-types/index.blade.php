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
            <p class="text-body-secondary">Manage investment type categories</p>
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
                        <th class="fw-semibold">DEFAULT TENURE</th>
                        <th class="fw-semibold">STATUS</th>
                        <th class="fw-semibold">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                    <tr>
                        <td class="fw-semibold">{{ $type->code }}</td>
                        <td>{{ $type->name }}</td>
                        <td>{{ $type->category ?? '-' }}</td>
                        <td>{{ $type->default_tenure_months ?? '-' }} months</td>
                        <td>
                            @if($type->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('investment-types.edit', $type) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('investment-types.destroy', $type) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this type?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <p class="text-body-secondary">No investment types found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $types->links() }}
</div>
@endsection
