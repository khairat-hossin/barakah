@php $member = $deposit->member; @endphp
<x-mail::message>
# Deposit Received

Dear {{ $member?->name ?? 'Member' }},

We have recorded your deposit. The details are below, and your official receipt is attached as a PDF.

**Amount:** Tk {{ number_format($deposit->amount, 2) }}
**Date:** {{ $deposit->deposit_date?->format('d M Y') }}
**Transaction ID:** {{ $deposit->transaction_id ?: 'N/A' }}
**Payment Method:** {{ $deposit->paymentMethod?->name ?? ucfirst(str_replace('_', ' ', $deposit->payment_method ?? '')) }}

Thank you for your continued contribution.

Regards,<br>
{{ $org?->organization_name_en ?? \App\Support\Branding::name() }}
</x-mail::message>
