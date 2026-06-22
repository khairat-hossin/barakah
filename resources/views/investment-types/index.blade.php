@extends('layouts.phoenix')
@section('title', 'Investment Types | ' . config('app.name'))
@section('content')
<div class="mb-9">
    <div class="row mb-4 gx-6 gy-3 align-items-center">
        <div class="col-auto">
            <h2 class="mb-0">Investment Types<span class="fw-normal text-body-tertiary ms-3">({{ $types->total() }})</span></h2>
        </div>
        <div class="col-auto">
            <a class="btn btn-primary px-5" href="{{ route('investment-types.create') }}">
                <i class="fa-solid fa-plus me-2"></i>Add Type
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive scrollbar">
                <table class="table fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">NAME</th>
                            <th>CODE</th>
                            <th>CATEGORY</th>
                            <th>RETURN TYPE</th>
                            <th>TENURE</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                        <tr>
                            <td class="ps-4 py-3">
                                <p class="fw-semibold mb-0">{{ $type->name }}</p>
                            </td>
                            <td class="py-3">
                                <span class="badge badge-phoenix badge-phoenix-secondary">{{ $type->code }}</span>
                            </td>
                            <td class="py-3">{{ $type->category ?? '-' }}</td>
                            <td class="py-3">
                                <span class="badge badge-phoenix badge-phoenix-info">{{ ucfirst($type->default_return_type) }}</span>
                            </td>
                            <td class="py-3">{{ $type->default_tenure_months ?? '-' }} months</td>
                            <td class="py-3">
                                @if($type->is_active)
                                    <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                @else
                                    <span class="badge badge-phoenix badge-phoenix-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-sm btn-phoenix-primary" href="{{ route('investment-types.edit', $type) }}">Edit</a>
                                    <form action="{{ route('investment-types.destroy', $type) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-phoenix-danger" onclick="return confirm('Delete this investment type?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                                <p class="text-body-secondary">No investment types found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $types->links() }}
    </div>
</div>
@endsection
