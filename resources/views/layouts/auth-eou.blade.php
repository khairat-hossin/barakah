<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('auth-title', 'Sign in') — {{ \App\Support\Branding::name() }}</title>
    <link rel="icon" href="{{ \App\Support\Branding::faviconUrl() ?? asset('assets/logo/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @php
        $eouName = \App\Support\Branding::name();
        $eouLogo = \App\Support\Branding::logoUrl() ?? asset('assets/logo/logo-icon.png');
    @endphp
    <style>
        :root{
            --navy:#152a52; --navy-2:#0f2142;
            --gold:#f2b41c; --gold-2:#b8860b; --gold-soft:#fef6e3;
            --ink:#1f2a44; --txt:#1f2a44; --muted:#6b7280; --line:#e6e8ee;
            --field-bg:#ffffff; --field-bd:#d8dce4;
            --btn:#152a52; --btn-h:#0f2142; --btn-text:#ffffff;
        }
        *{box-sizing:border-box;}
        html,body{margin:0;min-height:100%;}
        body{
            font-family:'Inter',system-ui,sans-serif;color:var(--ink);
            background:
                radial-gradient(900px 500px at 50% -8%, #eef1f7 0%, transparent 60%),
                #f6f7fa;
            display:flex;flex-direction:column;min-height:100vh;
        }
        a{color:inherit;text-decoration:none;}
        img{max-width:100%;display:block;}

        .auth-wrap{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 18px;}
        .auth-card{
            width:100%;max-width:410px;background:#fff;border:1px solid var(--line);
            border-radius:16px;box-shadow:0 12px 40px -22px rgba(21,42,82,.35);
            padding:2.4rem 2.1rem;
        }
        .brand{display:flex;justify-content:center;margin-bottom:1.4rem;}
        .brand img{height:52px;width:auto;object-fit:contain;}

        .auth-title{font-size:1.5rem;font-weight:700;color:var(--navy);text-align:center;margin:0;letter-spacing:-.01em;}
        .auth-sub{color:var(--muted);text-align:center;font-size:.95rem;margin:.4rem 0 1.75rem;}

        /* Fields */
        .field{margin-bottom:1.1rem;}
        .field label{display:block;font-size:.9rem;font-weight:600;color:var(--ink);margin:0 0 .45rem;}
        .control{position:relative;}
        .control input{
            width:100%;height:48px;padding:0 1rem;border-radius:10px;
            background:var(--field-bg);border:1px solid var(--field-bd);
            color:var(--ink);font-size:.95rem;font-family:inherit;outline:none;
            transition:border-color .15s, box-shadow .15s;
        }
        .control input::placeholder{color:#9aa2b1;}
        .control input:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(21,42,82,.12);}
        .control .toggle-eye{
            position:absolute;right:.65rem;top:50%;transform:translateY(-50%);
            background:none;border:0;color:#9aa2b1;cursor:pointer;padding:.3rem;display:flex;
        }
        .control .toggle-eye:hover{color:var(--navy);}

        .row-between{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:-.1rem 0 1.3rem;}
        .check{display:flex;align-items:center;gap:.5rem;color:var(--ink);font-size:.9rem;cursor:pointer;}
        .check input{width:16px;height:16px;accent-color:var(--navy);}
        .link{color:var(--navy);font-weight:600;font-size:.9rem;}
        .link:hover{color:var(--gold-2);}

        .btn{
            display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;height:48px;
            border-radius:10px;border:1px solid transparent;font-size:.98rem;font-weight:600;
            font-family:inherit;cursor:pointer;transition:background .15s, opacity .15s, transform .08s;
        }
        .btn:active{transform:translateY(1px);}
        .btn-primary{background:var(--btn);color:var(--btn-text);}
        .btn-primary:hover{background:var(--btn-h);}
        .btn-primary:disabled{background:#c3c9d6;cursor:not-allowed;}
        .btn-outline{background:#fff;color:var(--navy);border-color:var(--field-bd);margin-top:.7rem;}
        .btn-outline:hover{background:#f5f6f9;border-color:var(--navy);}

        .foot-note{text-align:center;color:var(--muted);font-size:.9rem;margin-top:1.3rem;}
        .foot-note .link{font-weight:600;}

        .error-text{display:block;color:#c0392b;font-size:.83rem;margin-top:.4rem;}
        .alert{padding:.75rem 1rem;border-radius:10px;font-size:.9rem;margin-bottom:1.1rem;}
        .alert-error{background:#fdecec;border:1px solid #f5c2c0;color:#a12a24;}
        .alert-ok{background:#e8f6ee;border:1px solid #b6e0c6;color:#1c7a45;}

        .auth-foot{text-align:center;color:#9aa2b1;font-size:.82rem;padding:18px 12px 24px;}
        .auth-foot .g{color:var(--gold-2);font-weight:600;}

        @media (max-width:420px){ .auth-card{padding:1.9rem 1.4rem;} }
    </style>
    @stack('auth-styles')
</head>
<body>
    <main class="auth-wrap">
        <div class="auth-card">
            <div class="brand"><img src="{{ $eouLogo }}" alt="{{ $eouName }}"></div>
            @yield('auth-body')
        </div>
    </main>
    <footer class="auth-foot">
        &copy; {{ date('Y') }} {{ $eouName }} · Built on <span class="g">trust</span>, guided by <span class="g">unity</span>.
    </footer>
    @stack('auth-scripts')
</body>
</html>
