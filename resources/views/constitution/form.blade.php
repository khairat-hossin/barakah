@extends('layouts.phoenix')

@section('title', ($section->exists ? 'Edit' : 'Add') . ' Section | ' . \App\Support\Branding::name())

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style> #editor { min-height: 320px; } .ql-editor { font-size: 0.95rem; } </style>
@endpush

@section('content')
<div class="mb-9">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('constitution') }}">Constitution</a></li>
            <li class="breadcrumb-item"><a href="{{ route('constitution.manage') }}">Manage</a></li>
            <li class="breadcrumb-item active">{{ $section->exists ? 'Edit' : 'Add' }} Section</li>
        </ol>
    </nav>

    <h2 class="h4 mb-3">{{ $section->exists ? 'Edit Section' : 'Add Section' }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ $section->exists ? route('constitution.update', $section) : route('constitution.store') }}" id="sectionForm">
        @csrf
        @if($section->exists) @method('PUT') @endif

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                               value="{{ old('title', $section->title) }}" placeholder="e.g. Article 1 — Name &amp; Registered Office">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Icon</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', $section->icon ?: 'file-text') }}" placeholder="feather icon name">
                        <small class="text-body-tertiary">Feather icon name (e.g. <code>users</code>, <code>book-open</code>). <a href="https://feathericons.com" target="_blank">List</a></small>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Body</label>
                        <div id="editor">{!! old('body', $section->body) !!}</div>
                        <textarea name="body" id="bodyInput" class="d-none">{{ old('body', $section->body) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $section->exists ? $section->is_published : true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Published (visible on the constitution page)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><span class="fas fa-save me-1"></span>{{ $section->exists ? 'Save Changes' : 'Add Section' }}</button>
            <a href="{{ route('constitution.manage') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link', 'blockquote'],
                ['clean'],
            ],
        },
    });
    // Sync editor HTML into the hidden textarea on submit
    document.getElementById('sectionForm').addEventListener('submit', function () {
        const html = quill.root.innerHTML;
        document.getElementById('bodyInput').value = (html === '<p><br></p>') ? '' : html;
    });
</script>
@endpush
