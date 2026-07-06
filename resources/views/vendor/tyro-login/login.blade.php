@extends('layouts.auth-eou')

@section('auth-title', 'Login')

@section('auth-body')
    @php($field = $loginField ?? 'email')

    @if(session('success'))
        <div class="alert alert-ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @error('login')<div class="alert alert-error">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('tyro-login.login.submit') }}">
        @csrf

        {{-- Login identifier --}}
        <div class="field">
            @if($field === 'username')
                <label for="username">Username</label>
                <div class="control">
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                           required autofocus autocomplete="username" placeholder="Enter your username">
                </div>
                @error('username')<span class="error-text">{{ $message }}</span>@enderror
            @elseif($field === 'both')
                <label for="login">Email or Username</label>
                <div class="control">
                    <input type="text" id="login" name="login" value="{{ old('login') }}"
                           required autofocus autocomplete="username" placeholder="Email or username">
                </div>
                @error('login')<span class="error-text">{{ $message }}</span>@enderror
            @else
                <label for="email">Email</label>
                <div class="control">
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="email" placeholder="email@example.com">
                </div>
                @error('email')<span class="error-text">{{ $message }}</span>@enderror
            @endif
        </div>

        {{-- Password --}}
        @if(!($features['disable_password'] ?? false))
        <div class="field">
            <label for="password">Password</label>
            <div class="control">
                <input type="password" id="password" name="password" required
                       autocomplete="current-password" placeholder="Enter your password">
                <button type="button" class="toggle-eye" aria-label="Show password"
                        onclick="(function(b){var i=document.getElementById('password');i.type=i.type==='password'?'text':'password';})(this)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            @error('password')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        {{-- Captcha (only when enabled) --}}
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
                <a href="{{ route('tyro-login.password.request') }}" class="link-muted">Forgot Password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
        @endif
    </form>
@endsection
