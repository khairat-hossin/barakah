@extends('layouts.phoenix')

@section('title', 'Payment Methods | ' . config('app.name'))

@section('content')
<div class="mb-9">
    <div class="row mb-4 gx-6 gy-3 align-items-center">
        <div class="col-auto">
            <h2 class="mb-0">Payment Methods<span class="fw-normal text-body-tertiary ms-3">({{ $paymentMethods->total() }})</span></h2>
        </div>
        <div class="col-auto">
            <a class="btn btn-primary px-5" href="{{ route('payment-methods.create') }}">
                <i class="fa-solid fa-plus me-2"></i>Add Method
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
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $method)
                        <tr>
                            <td class="ps-4 py-3">
                                <p class="fw-semibold mb-0">{{ $method->name }}</p>
                            </td>
                            <td class="py-3"><code>{{ $method->code }}</code></td>
                            <td class="py-3 text-body-secondary text-truncate" style="max-width: 300px;">{{ $method->description ?? '-' }}</td>
                            <td class="py-3">
                                @if($method->is_active)
                                    <span class="badge badge-phoenix badge-phoenix-success">Active</span>
                                @else
                                    <span class="badge badge-phoenix badge-phoenix-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-sm btn-phoenix-primary" href="{{ route('payment-methods.edit', $method) }}">Edit</a>
                                    <form action="{{ route('payment-methods.destroy', $method) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-phoenix-danger" onclick="return confirm('Delete this payment method?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <span class="fas fa-inbox fs-1 text-body-tertiary mb-3 d-block"></span>
                                <p class="text-body-secondary">No payment methods found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $paymentMethods->links() }}
    </div>
</div>
@endsection
