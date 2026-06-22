@php $member = $expense->member; @endphp
<x-mail::message>
# Expense Voucher

Dear {{ $member?->name ?? 'Member' }},

An expense related to your account has been recorded. The details are below, and the voucher is attached as a PDF.

**Voucher No:** {{ $expense->expense_number ?: ('EXP-' . $expense->id) }}
**Amount:** Tk {{ number_format($expense->amount, 2) }}
**Date:** {{ $expense->expense_date?->format('d M Y') }}
**Category:** {{ $expense->category?->name ?? 'N/A' }}
**Status:** {{ ucfirst($expense->status) }}

Regards,<br>
{{ $org?->organization_name_en ?? \App\Support\Branding::name() }}
</x-mail::message>
