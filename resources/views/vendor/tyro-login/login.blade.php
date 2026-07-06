@extends('layouts.auth-eou')

@section('auth-title', 'Login')

@section('auth-body')
    <h1 class="auth-title">Log in to your account</h1>
    <p class="auth-sub">Enter your email, username or phone with your password.</p>

    @if(session('success'))<div class="alert alert-ok">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
    @error('login')<div class="alert alert-error">{{ $message }}</div>@enderror
    @error('email')<div class="alert alert-error">{{ $message }}</div>@enderror
    @error('username')<div class="alert alert-error">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('tyro-login.login.submit') }}">
        @csrf

        {{-- Single identifier: email / username / phone --}}
        <div class="field">
            <label for="login">Email / Username / Phone</label>
            <div class="control">
                <input type="text" id="login" name="login" value="{{ old('login') }}"
                       required autofocus autocomplete="username" placeholder="Enter your email, username or phone">
            </div>
        </div>

        {{-- Password --}}
        @if(!($features['disable_password'] ?? false))
        <div class="field">
            <label for="password">Password</label>
            <div class="control">
                <input type="password" id="password" name="password" required
                       autocomplete="current-password" placeholder="Enter your password">
                <button type="button" class="toggle-eye" aria-label="Show password"
                        onclick="(function(){var i=document.getElementById('password');i.type=i.type==='password'?'text':'password';})()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            @error('password')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        {{-- Captcha (only if enabled) --}}
        @if($captchaEnabled ?? false)
        <div class="field">
            <label for="captcha_answer">{{ $captchaConfig['label'] ?? 'Security Check' }} — {{ $captchaQuestion }}</label>
            <div class="control">
                <input type="number" id="captcha_answer" name="captcha_answer" required autocomplete="off"
                       placeholder="{{ $captchaConfig['placeholder'] ?? 'Enter the answer' }}">
            </div>
            @error('captcha_answer')<span class="error-text">{{ $message }}</span>@enderror
        </div>
        @endif

        <div class="row-between">
            @if($features['remember_me'] ?? true)
                <label class="check"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember me</label>
            @else<span></span>@endif
            @if($features['forgot_password'] ?? true)
                <a href="{{ route('tyro-login.password.request') }}" class="link">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Log in</button>
        @endif
    </form>

    @if($registrationEnabled ?? true)
    <p class="foot-note">Don&rsquo;t have an account? <a href="{{ route('tyro-login.register') }}" class="link">Sign up</a></p>
    @endif
@endsection
