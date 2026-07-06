@extends('layouts.auth-eou')

@section('auth-title', 'Verify Identity')

@push('auth-styles')
<style>
    .otp-head{font-size:1.15rem;font-weight:700;color:var(--txt);text-align:center;margin:0 0 .35rem;}
    .otp-sub{color:var(--muted);font-size:.95rem;text-align:center;margin:0 0 1.5rem;}
    .otp-boxes{display:flex;align-items:center;justify-content:center;gap:.55rem;margin-bottom:1.6rem;}
    .otp-boxes .dash{color:var(--muted);font-size:1.4rem;padding:0 .1rem;}
    .otp-digit{
        width:56px;height:64px;text-align:center;font-size:1.6rem;font-weight:700;
        color:var(--gold-2);background:var(--field-bg);border:1px solid var(--field-bd);
        border-radius:10px;outline:none;transition:border-color .15s, box-shadow .15s;
        -moz-appearance:textfield;
    }
    .otp-digit::-webkit-outer-spin-button,.otp-digit::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
    .otp-digit:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(212,175,95,.18);}
    @media (max-width:420px){ .otp-digit{width:44px;height:56px;font-size:1.3rem;} .otp-boxes{gap:.35rem;} }
</style>
@endpush

@section('auth-body')
    <div class="otp-head">Verify Your Identity</div>
    <div class="otp-sub">Enter the {{ $otpLength }}-digit code sent to your registered contact method to proceed.</div>

    @if(session('success'))<div class="alert alert-ok">{{ session('success') }}</div>@endif
    @error('otp')<div class="alert alert-error">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('otp.verify') }}" id="otp-form">
        @csrf
        <input type="hidden" name="otp" id="otp-value">
        <div class="otp-boxes">
            @for($i = 0; $i < $otpLength; $i++)
                @if($i === intdiv($otpLength, 2))<span class="dash">–</span>@endif
                <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" data-otp
                       aria-label="Digit {{ $i + 1 }}" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}" {{ $i === 0 ? 'autofocus' : '' }}>
            @endfor
        </div>
        <button type="submit" class="btn btn-primary" id="otp-submit" disabled>Submit</button>
    </form>

    <a href="{{ route('tyro-login.logout') }}" class="btn btn-outline">Cancel</a>

    <p class="helper">
        A one-time code was sent to your registered contact method (<strong>{{ $maskedDestination }}</strong>).
        Didn&rsquo;t receive it?
        <a href="#" id="resend-link">Resend code<span id="resend-timer"></span></a>.
    </p>

    <form method="POST" action="{{ route('otp.resend') }}" id="resend-form" hidden>@csrf</form>
@endsection

@push('auth-scripts')
<script>
    (function () {
        var boxes = Array.prototype.slice.call(document.querySelectorAll('[data-otp]'));
        var hidden = document.getElementById('otp-value');
        var submit = document.getElementById('otp-submit');

        function sync() {
            var code = boxes.map(function (b) { return b.value; }).join('');
            hidden.value = code;
            submit.disabled = code.length !== boxes.length;
        }

        boxes.forEach(function (box, idx) {
            box.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 1);
                if (this.value && idx < boxes.length - 1) boxes[idx + 1].focus();
                sync();
            });
            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && idx > 0) boxes[idx - 1].focus();
            });
            box.addEventListener('paste', function (e) {
                e.preventDefault();
                var digits = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, boxes.length).split('');
                digits.forEach(function (d, i) { if (boxes[i]) boxes[i].value = d; });
                (boxes[digits.length] || boxes[boxes.length - 1]).focus();
                sync();
            });
        });

        // Resend with cooldown
        var link = document.getElementById('resend-link');
        var timer = document.getElementById('resend-timer');
        var form = document.getElementById('resend-form');
        var remaining = {{ (int) $cooldownRemaining }};

        function tick() {
            if (remaining <= 0) { link.style.pointerEvents = 'auto'; link.style.opacity = '1'; timer.textContent = ''; return; }
            link.style.pointerEvents = 'none'; link.style.opacity = '.55';
            timer.textContent = ' (' + remaining + 's)';
            remaining -= 1; setTimeout(tick, 1000);
        }
        if (remaining > 0) tick();
        link.addEventListener('click', function (e) { e.preventDefault(); if (remaining <= 0) form.submit(); });
    })();
</script>
@endpush
