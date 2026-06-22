<x-mail::message>
# Monthly Deposit Reminder

Dear {{ $member->name }},

This is a friendly reminder that your deposit for **{{ $monthLabel }}** has not yet been recorded.

@if($expectedAmount > 0)
**Expected amount:** Tk {{ number_format($expectedAmount, 2) }}
@endif

Please make your contribution at your earliest convenience. If you have already paid, kindly ignore this message.

Thank you,<br>
{{ $org?->organization_name_en ?? \App\Support\Branding::name() }}
</x-mail::message>
