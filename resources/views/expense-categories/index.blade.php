@extends('layouts.phoenix')

@section('title', 'Expense Categories | Barakah')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Expense Categories</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center justify-content-between mb-3">
        <div class="col">
            <h2 class="mb-0">Expense Categories</h2>
            <p class="text-body-secondary">Manage expense classification</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expense-categories.create') }}" class="btn btn-primary">
                <span class="fas fa-plus me-2"></span>New Category
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="fw-semibold">Name</th>
                        <th class="fw-semibold">Code</th>
                        <th class="fw-semibold">Description</th>
                        <th class="fw-semibold">Status</th>
                        <th class="fw-semibold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="fw-semibold">{{ $category->name }}</td>
                        <td><code>{{ $category->code }}</code></td>
                        <td class="text-body-secondary text-truncate" style="max-width: 300px;">{{ $category->description ?? '-' }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                            @else
                                <span class="badge badge-phoenix badge-phoenix-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="gap-2 d-flex justify-content-end">
                                <a href="{{ route('expense-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                    <span class="fas fa-edit"></span>
                                </a>
                                <form action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?')">
                                        <span class="fas fa-trash"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                            <p class="text-body-secondary">No categories found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-body-tertiary">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
