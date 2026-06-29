@extends('layouts.phoenix')

@section('title', 'Manage Constitution | ' . \App\Support\Branding::name())

@section('content')
<div class="mb-9">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('constitution') }}">Constitution</a></li>
            <li class="breadcrumb-item active">Manage</li>
        </ol>
    </nav>

    <div class="row align-items-center justify-content-between mb-3 g-2">
        <div class="col">
            <h2 class="mb-0 h4">Manage Constitution</h2>
            <p class="text-body-secondary mb-0 small">Add, edit, reorder, and publish constitution sections.</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('constitution') }}" class="btn btn-sm btn-outline-secondary" target="_blank"><span class="fas fa-eye me-1"></span>View page</a>
            <a href="{{ route('constitution.create') }}" class="btn btn-sm btn-primary"><span class="fas fa-plus me-1"></span>Add Section</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th class="fs-9 text-body-secondary" style="width:90px;">Order</th>
                            <th class="fs-9 text-body-secondary">Title</th>
                            <th class="fs-9 text-body-secondary text-center">Status</th>
                            <th class="fs-9 text-body-secondary text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $s)
                            <tr>
                                <td class="text-nowrap">
                                    <form action="{{ route('constitution.move', $s) }}" method="POST" class="d-inline">
                                        @csrf <input type="hidden" name="direction" value="up">
                                        <button class="btn btn-sm btn-phoenix-secondary py-0 px-2" {{ $loop->first ? 'disabled' : '' }} title="Move up"><span class="fas fa-arrow-up"></span></button>
                                    </form>
                                    <form action="{{ route('constitution.move', $s) }}" method="POST" class="d-inline">
                                        @csrf <input type="hidden" name="direction" value="down">
                                        <button class="btn btn-sm btn-phoenix-secondary py-0 px-2" {{ $loop->last ? 'disabled' : '' }} title="Move down"><span class="fas fa-arrow-down"></span></button>
                                    </form>
                                </td>
                                <td>
                                    <span class="fas fa-{{ '' }}"></span>
                                    <span data-feather="{{ $s->icon ?: 'file-text' }}" style="width:14px;height:14px;" class="text-body-tertiary me-1"></span>
                                    {!! $s->title !!}
                                </td>
                                <td class="text-center">
                                    @if($s->is_published)
                                        <span class="badge badge-phoenix badge-phoenix-success">Published</span>
                                    @else
                                        <span class="badge badge-phoenix badge-phoenix-secondary">Draft</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('constitution.edit', $s) }}" class="btn btn-sm btn-outline-primary py-0 px-2">Edit</a>
                                    <form action="{{ route('constitution.destroy', $s) }}" method="POST" class="d-inline" data-confirm="Delete this section?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-body-secondary"><small>No sections yet. Click "Add Section" to start.</small></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script> if (window.feather) feather.replace(); </script>
@endpush
