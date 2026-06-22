@extends('layouts.phoenix')

@section('title', 'Organization Profile Audit Logs | ' . \App\Support\Branding::name())

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('organization-profile.show', $profile) }}">Organization Profile</a></li>
        <li class="breadcrumb-item active">Audit Logs</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">Audit Logs</h2>
            <p class="text-body-secondary mt-2">Complete audit trail of all changes to the organization profile</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('organization-profile.show', $profile) }}" class="btn btn-outline-secondary btn-sm">
                <span class="fas fa-arrow-left me-2"></span>Back to Profile
            </a>
        </div>
    </div>

    @if($logs->count())
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Timestamp</th>
                            <th>Action</th>
                            <th>Section</th>
                            <th>Field Changed</th>
                            <th>Changed By</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="ps-4">
                                    <small class="text-muted">
                                        {{ $log->timestamp->format('d M Y, H:i') }}
                                    </small>
                                </td>
                                <td>
                                    @switch($log->action_type)
                                        @case('created')
                                            <span class="badge bg-success">Created</span>
                                            @break
                                        @case('updated')
                                            <span class="badge bg-primary">Updated</span>
                                            @break
                                        @case('deleted')
                                            <span class="badge bg-danger">Deleted</span>
                                            @break
                                        @case('section_updated')
                                            <span class="badge bg-info">Section Updated</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($log->section_name)
                                        <small>{{ str_replace('_', ' ', ucfirst($log->section_name)) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->field_name)
                                        <small><code>{{ str_replace('_', ' ', ucfirst($log->field_name)) }}</code></small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->changedBy)
                                        <small>
                                            <strong>{{ $log->changedBy->name }}</strong><br>
                                            <span class="text-muted">{{ $log->changedBy->email }}</span>
                                        </small>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->old_value)
                                        <small class="text-muted">
                                            @if(is_array($log->old_value))
                                                <pre style="max-width: 200px; overflow: auto; margin: 0; background: #f8f9fa; padding: 4px; border-radius: 3px;">{{ json_encode($log->old_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                {{ $log->old_value }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->new_value)
                                        <small class="text-muted">
                                            @if(is_array($log->new_value))
                                                <pre style="max-width: 200px; overflow: auto; margin: 0; background: #f8f9fa; padding: 4px; border-radius: 3px;">{{ json_encode($log->new_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                {{ $log->new_value }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        @if($log->ip_address)
                                            {{ $log->ip_address }}
                                        @else
                                            -
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-sm-items-center gap-3 mt-3">
                <div class="flex-shrink-0">
                    <small class="text-muted">
                        Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
                    </small>
                </div>
                <nav aria-label="pagination" class="flex-shrink-0">
                    <ul class="pagination pagination-sm mb-0">
                        @if ($logs->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">Previous</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->previousPageUrl() }}">Previous</a>
                            </li>
                        @endif

                        @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                            @if ($page == $logs->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        @if ($logs->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->nextPageUrl() }}">Next</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Next</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    @else
        <div class="alert alert-info mb-0">
            <span class="fas fa-info-circle me-2"></span>
            No audit logs available yet.
        </div>
    @endif
</div>
@endsection
