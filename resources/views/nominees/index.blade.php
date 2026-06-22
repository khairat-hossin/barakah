@extends('layouts.phoenix')

@section('title', 'Nominees | ' . \App\Support\Branding::name())

@section('content')
    <div class="mb-9">

        <div class="row mb-4 align-items-center">
            <div class="col-auto"><h2 class="mb-0">Nominees for {{ $member->name }}</h2></div>
            @if ($canAddMore)
                <div class="col-auto">
                    <a href="{{ route('nominees.create', $member) }}" class="btn btn-primary px-5">
                        <i class="fa-solid fa-plus me-2"></i>Add Nominee
                    </a>
                </div>
            @endif
            <div class="col-auto">
                <a href="{{ route('members.index') }}" class="btn btn-secondary">Back to Members</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 text-body-secondary">Total Allocation</h6>
                        <h3 class="text-body-emphasis mb-0">{{ $totalAllocation }}%</h3>
                        <small class="text-body-secondary">
                            @if($totalAllocation === 100) ✓ Complete @elseif($totalAllocation === 0) No nominees @else Incomplete @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Nominees</h5></div>
            <div class="table-responsive scrollbar">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Full Name</th>
                            <th class="ps-3">Relationship</th>
                            <th class="ps-3">Allocation %</th>
                            <th class="ps-3">Primary</th>
                            <th class="text-end ps-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nominees as $nominee)
                            <tr>
                                <td class="ps-3">{{ $nominee->full_name }}</td>
                                <td class="ps-3">{{ ucfirst(str_replace('_', ' ', $nominee->relationship)) }}</td>
                                <td class="ps-3"><span class="badge badge-phoenix badge-phoenix-info">{{ $nominee->allocation_percentage }}%</span></td>
                                <td class="ps-3">
                                    @if($nominee->is_primary) <i class="fas fa-check text-success"></i> @endif
                                </td>
                                <td class="text-end ps-3">
                                    <a href="{{ route('nominees.edit', [$member, $nominee]) }}" class="btn btn-sm btn-phoenix-secondary">Edit</a>
                                    <form method="POST" action="{{ route('nominees.destroy', [$member, $nominee]) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-phoenix-danger" onclick="return confirm('Remove this nominee?')">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4">No nominees added yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
