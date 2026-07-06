<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('auth-title', 'Sign in') — {{ \App\Support\Branding::name() }}</title>
    <link rel="icon" href="{{ \App\Support\Branding::faviconUrl() ?? asset('assets/logo/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @php
        $eouName = \App\Support\Branding::name();
        $eouOrg  = \App\Models\OrganizationProfile::first();
        $eouSub  = $eouOrg?->motto ?: 'Friendship · Growth · Strength';
        $eouLogo = \App\Support\Branding::logoUrl() ?? asset('assets/logo/logo-icon.png');
    @endphp
    <style>
        :root{
            --navy:#152a52; --gold:#f2b41c; --gold-2:#ffd873; --gold-line:rgba(242,180,28,.5);
            --title:#ffd873; --btn:#f2b41c; --btn-h:#d99e10; --btn-text:#12244a;
            --field-bg:rgba(255,255,255,.05); --field-bd:rgba(180,198,232,.28);
            --txt:#eaf0fb; --muted:rgba(223,232,250,.68);
        }
        *{box-sizing:border-box;}
        html,body{margin:0;min-height:100%;}
        body{
            font-family:'Inter',system-ui,sans-serif;color:var(--txt);
            display:flex;flex-direction:column;min-height:100vh;position:relative;overflow-x:hidden;
            background:
                radial-gradient(1100px 720px at 50% 34%, #1d366a 0%, #14294f 46%, #0b1730 100%),
                #0b1730;
        }
        body::before{ /* faint texture */
            content:"";position:fixed;inset:0;pointer-events:none;opacity:.5;
            background-image:radial-gradient(rgba(255,255,255,.015) 1px, transparent 1px);
            background-size:3px 3px;
        }
        a{color:inherit;text-decoration:none;}

        /* Decorative corner line-work */
        .corner{position:fixed;width:340px;height:260px;pointer-events:none;opacity:.85;z-index:0;color:var(--gold);}
        .corner.tl{top:0;left:0;}
        .corner.tr{top:0;right:0;transform:scaleX(-1);}
        .corner.bl{bottom:0;left:0;transform:scaleY(-1);}
        .corner.br{bottom:0;right:0;transform:scale(-1,-1);}

        /* Side emblems */
        .emblem{position:fixed;top:50%;transform:translateY(-50%);width:250px;pointer-events:none;z-index:0;opacity:.96;}
        .emblem.left{left:2.5vw;}
        .emblem.right{right:2.5vw;}

        /* Center column */
        .auth-shell{position:relative;z-index:2;flex:1;display:flex;flex-direction:column;
            align-items:center;justify-content:center;padding:48px 20px;width:100%;}
        .auth-inner{width:100%;max-width:440px;text-align:center;}
        .brand-badge{
            width:104px;height:104px;margin:0 auto 18px;border-radius:50%;
            background:radial-gradient(circle at 50% 40%, #ffffff, #eef1f8);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 0 0 6px rgba(255,255,255,.06), 0 14px 40px -18px rgba(0,0,0,.6);
        }
        .brand-badge img{width:80px;height:80px;object-fit:contain;}
        .brand-title{font-size:1.7rem;font-weight:800;color:var(--title);letter-spacing:-.01em;margin:0;}
        .brand-sub{color:var(--muted);font-size:1rem;margin:.35rem 0 0;}
        .auth-body{margin-top:30px;text-align:left;}

        /* Form controls */
        .field{margin-bottom:1.15rem;}
        .field label{display:block;font-size:1rem;font-weight:500;margin:0 0 .55rem;color:var(--txt);}
        .control{position:relative;}
        .control input{
            width:100%;height:54px;padding:0 1rem;border-radius:8px;
            background:var(--field-bg);border:1px solid var(--field-bd);
            color:var(--txt);font-size:1rem;font-family:inherit;outline:none;
            transition:border-color .15s, box-shadow .15s;
        }
        .control input::placeholder{color:rgba(223,232,250,.4);}
        .control input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(242,180,28,.18);}
        .control .toggle-eye{
            position:absolute;right:.75rem;top:50%;transform:translateY(-50%);
            background:none;border:0;color:var(--muted);cursor:pointer;padding:.35rem;display:flex;
        }
        .control .toggle-eye:hover{color:var(--gold-2);}

        .row-between{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:-.15rem 0 1.35rem;}
        .check{display:flex;align-items:center;gap:.55rem;color:var(--txt);font-size:.95rem;cursor:pointer;}
        .check input{width:18px;height:18px;accent-color:var(--btn);}
        .link-muted{color:var(--muted);font-size:.95rem;}
        .link-muted:hover{color:var(--gold-2);}

        .btn{
            display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;height:54px;
            border-radius:8px;border:1px solid transparent;font-size:1.05rem;font-weight:700;
            font-family:inherit;cursor:pointer;transition:background .15s, opacity .15s, transform .1s;
        }
        .btn:active{transform:translateY(1px);}
        .btn-primary{background:var(--btn);color:var(--btn-text);}
        .btn-primary:hover{background:var(--btn-h);}
        .btn-primary:disabled{background:rgba(255,255,255,.10);color:rgba(255,255,255,.45);cursor:not-allowed;}
        .btn-outline{background:transparent;color:#fff;border-color:rgba(255,255,255,.55);margin-top:.85rem;}
        .btn-outline:hover{background:rgba(255,255,255,.08);border-color:#fff;}

        .error-text{display:block;color:#ffb4b4;font-size:.875rem;margin-top:.4rem;}
        .alert{padding:.8rem 1rem;border-radius:8px;font-size:.92rem;margin-bottom:1.1rem;}
        .alert-error{background:rgba(220,53,69,.14);border:1px solid rgba(255,120,120,.45);color:#ffd7d7;}
        .alert-ok{background:rgba(19,135,91,.16);border:1px solid rgba(85,200,150,.45);color:#c9f4de;}
        .helper{color:var(--muted);font-size:.9rem;line-height:1.55;text-align:center;margin-top:1.1rem;}
        .helper a{color:var(--gold-2);font-weight:600;}

        footer.auth-foot{position:relative;z-index:2;text-align:center;padding:22px 16px 26px;
            color:var(--muted);font-size:.85rem;}
        footer.auth-foot .gold{color:var(--gold-2);}

        @media (max-width:1024px){ .emblem{width:190px;opacity:.5;} }
        @media (max-width:820px){
            .emblem{display:none;} .corner{width:200px;height:150px;opacity:.6;}
            .brand-title{font-size:1.45rem;}
        }
    </style>
    @stack('auth-styles')
</head>
<body>
    {{-- Corner ornaments --}}
    @foreach(['tl','tr','bl','br'] as $c)
    <svg class="corner {{ $c }}" viewBox="0 0 340 260" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g stroke="currentColor" stroke-linecap="round">
            <path d="M-20 12 C120 30 210 70 250 150 C270 195 300 220 360 232" stroke-width="1.6" opacity=".9"/>
            <path d="M-20 40 C110 58 190 96 232 168 C255 210 292 236 360 250" stroke-width="1.1" opacity=".6"/>
            <path d="M-20 -14 C140 6 236 48 276 130 C296 172 322 196 360 208" stroke-width="1.1" opacity=".5"/>
            <path d="M-20 70 C90 84 160 118 200 182" stroke-width=".9" opacity=".4"/>
        </g>
    </svg>
    @endforeach

    {{-- Adapted Echo of Unity gold emblems (unity ripple + figures) --}}
    @foreach(['left','right'] as $side)
    <svg class="emblem {{ $side }}" viewBox="0 0 240 320" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <linearGradient id="eg-{{ $side }}" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#ffd873"/><stop offset=".5" stop-color="#f2b41c"/><stop offset="1" stop-color="#c98f0a"/>
            </linearGradient>
        </defs>
        {{-- woven base --}}
        <g stroke="url(#eg-{{ $side }})" stroke-width="2.4" stroke-linecap="round" opacity=".9">
            <path d="M55 250 C95 262 145 262 185 250"/>
            <path d="M48 262 C95 278 145 278 192 262"/>
            <path d="M60 274 C95 288 145 288 180 274"/>
            <path d="M74 286 C100 296 140 296 166 286"/>
            <path d="M120 246 L120 300"/>
            <path d="M100 250 L100 296"/><path d="M140 250 L140 296"/>
        </g>
        {{-- shield --}}
        <path d="M46 40 H194 Q206 40 206 54 V150 Q206 214 120 258 Q34 214 34 150 V54 Q34 40 46 40 Z"
              stroke="url(#eg-{{ $side }})" stroke-width="4"/>
        <path d="M56 52 H184 Q194 52 194 64 V148 Q194 202 120 242 Q46 202 46 148 V64 Q46 52 56 52 Z"
              stroke="url(#eg-{{ $side }})" stroke-width="1.4" opacity=".7"/>
        {{-- unity ripples --}}
        <g stroke="url(#eg-{{ $side }})" fill="none">
            <circle cx="120" cy="128" r="60" stroke-width="1.2" opacity=".45"/>
            <circle cx="120" cy="128" r="44" stroke-width="1.4" opacity=".7"/>
        </g>
        {{-- three unity figures --}}
        <g fill="url(#eg-{{ $side }})">
            <circle cx="120" cy="104" r="11"/><path d="M101 150 a19 21 0 0 1 38 0 z"/>
            <circle cx="92" cy="120" r="9"/><path d="M76 158 a16 18 0 0 1 32 0 z"/>
            <circle cx="148" cy="120" r="9"/><path d="M132 158 a16 18 0 0 1 32 0 z"/>
        </g>
    </svg>
    @endforeach

    <main class="auth-shell">
        <div class="auth-inner">
            <div class="brand-badge"><img src="{{ $eouLogo }}" alt="{{ $eouName }}"></div>
            <h1 class="brand-title">{{ $eouName }}</h1>
            <p class="brand-sub">{{ $eouSub }}</p>

            <div class="auth-body">
                @yield('auth-body')
            </div>
        </div>
    </main>

    <footer class="auth-foot">
        &copy; {{ date('Y') }} {{ $eouName }} · Built on <span class="gold">trust</span>, guided by <span class="gold">unity</span>.
    </footer>

    @stack('auth-scripts')
</body>
</html>
