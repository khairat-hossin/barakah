@extends('layouts.phoenix')

@section('title', 'Notifications | ' . \App\Support\Branding::name())

@section('content')
<div class="mb-6">
    <div class="row align-items-center justify-content-between mb-3 g-2">
        <div class="col">
            <h2 class="mb-0 h4">Notifications</h2>
            <p class="text-body-secondary mb-0 small">{{ $notifications->total() }} total</p>
        </div>
        <div class="col-auto">
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary"><span class="fas fa-check-double me-1"></span>Mark all read</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @forelse($notifications as $n)
                <a href="{{ route('notifications.read', $n->id) }}"
                   class="d-flex gap-3 p-3 border-bottom text-decoration-none {{ $n->read_at ? '' : 'bg-primary-subtle' }}">
                    <span class="d-flex align-items-center justify-content-center flex-shrink-0"
                          style="width:40px;height:40px;border-radius:50%;background:#e7f1ff;">
                        <span data-feather="{{ $n->data['icon'] ?? 'bell' }}" style="width:18px;height:18px;" class="text-primary"></span>
                    </span>
                    <div class="flex-1" style="min-width:0;">
                        <div class="fw-semibold text-body-emphasis">{{ $n->data['title'] ?? 'Notification' }}</div>
                        <div class="text-body-secondary small">{{ $n->data['message'] ?? '' }}</div>
                        <div class="text-body-tertiary" style="font-size:0.72rem;">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                    @if(!$n->read_at)
                        <span class="badge bg-primary align-self-center">New</span>
                    @endif
                </a>
            @empty
                <div class="text-center py-5 text-body-secondary">
                    <span class="fas fa-bell-slash me-2"></span>No notifications yet.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
