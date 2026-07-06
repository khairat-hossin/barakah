<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">

    <title>{{ $branding['app_name'] ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @include('tyro-login::partials.styles')

    {{-- Echo of Unity brand theme (navy + gold) — overrides the default shadcn palette --}}
    <style>
        :root, html.light {
            --primary: #1f2a44;
            --primary-foreground: #ffffff;
            --ring: #c89b3c;
            --radius: 0.75rem;
        }
        body { font-family: 'Inter', system-ui, sans-serif; }

        /* Brand panel: navy gradient with subtle unity-ripple motif instead of a stock photo */
        .background-panel { background: linear-gradient(160deg, #1f2a44 0%, #16203a 100%) !important; }
        .background-panel::after {
            content: ""; position: absolute; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(closest-side, transparent 67%, rgba(200,155,60,.22) 68%, transparent 71%) -70px -50px / 320px 320px no-repeat,
                radial-gradient(closest-side, transparent 62%, rgba(255,255,255,.10) 63%, transparent 66%) -30px -10px / 480px 480px no-repeat,
                radial-gradient(closest-side, transparent 72%, rgba(19,111,99,.30) 73%, transparent 76%) 60% 40% / 260px 260px no-repeat,
                radial-gradient(620px 320px at 90% 115%, rgba(19,111,99,.40), transparent 60%);
        }
        .background-panel-content { position: relative; z-index: 1; }
        .background-panel-content h1 { color: #ffffff; letter-spacing: -0.02em; }
        .background-panel-content h1::after {
            content: ""; display: block; width: 54px; height: 3px; border-radius: 3px;
            background: #c89b3c; margin: 18px 0 0;
        }
        .background-panel-content p { color: rgba(255,255,255,.82); }

        /* Premium form card */
        .form-card { border: 1px solid #ece7db; box-shadow: 0 24px 60px -30px rgba(31,42,68,.35); }
        .app-logo { color: #1f2a44; }
        .form-header h2 { letter-spacing: -0.02em; }

        /* Gold focus + navy button */
        .form-input:focus {
            border-color: #c89b3c !important;
            box-shadow: 0 0 0 3px rgba(200,155,60,.18) !important;
        }
        .btn-primary { background-color: #1f2a44; border-color: #1f2a44; }
        .btn-primary:hover { background-color: #16203a; border-color: #16203a; }
        .form-link { color: #0f6b4b; }
        .form-link:hover { color: #b8860b; }

        /* Disabled "coming soon" social buttons */
        .social-soon-container { margin-top: 1.5rem; }
        .social-soon-divider { display: flex; align-items: center; text-align: center; margin-bottom: 1.25rem; }
        .social-soon-divider::before, .social-soon-divider::after {
            content: ''; flex: 1; border-bottom: 1px solid var(--border);
        }
        .social-soon-divider span { padding: 0 1rem; font-size: 0.8125rem; color: var(--muted-foreground); white-space: nowrap; }
        .social-soon-stack { display: flex; flex-direction: column; gap: 0.625rem; }
        .social-soon-btn {
            display: flex; align-items: center; gap: 0.75rem; width: 100%;
            padding: 0.75rem 1rem; border-radius: 0.625rem; border: 1px solid var(--border);
            background: var(--muted); color: var(--muted-foreground);
            font-size: 0.9375rem; font-weight: 500; font-family: inherit;
            cursor: not-allowed; opacity: .85;
        }
        .social-soon-btn svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        .social-soon-btn .soon-badge {
            margin-left: auto; font-size: 0.6875rem; font-weight: 600; letter-spacing: .04em;
            text-transform: uppercase; color: #b8860b; background: rgba(200,155,60,.14);
            padding: 0.15rem 0.5rem; border-radius: 999px; white-space: nowrap; flex-shrink: 0;
        }

        /* Mobile: keep the card fully within the viewport (tighten the padding stack) */
        @media (max-width: 560px) {
            .auth-container.split-left,
            .auth-container.split-right { padding: 1rem; }
            .form-panel { padding: 1rem; }
            .form-card { max-width: 100%; }
            .social-soon-btn { padding: 0.7rem 0.85rem; font-size: 0.875rem; gap: 0.6rem; }
        }
    </style>
</head>

<body>
    <!-- Theme Toggle Button -->
    <button type="button" class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg class="sun-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg class="moon-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    @yield('content')

    <script>
        // Theme management
        function getTheme() {
            if (localStorage.getItem('tyro-login-theme')) {
                return localStorage.getItem('tyro-login-theme');
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function setTheme(theme) {
            localStorage.setItem('tyro-login-theme', theme);
            document.documentElement.classList.remove('light', 'dark');
            document.documentElement.classList.add(theme);
        }

        function toggleTheme() {
            const currentTheme = getTheme();
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        }

        // Apply theme on load
        setTheme(getTheme());

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('tyro-login-theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });

        // Form validation enhancement
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.classList.contains('loading')) {
                        submitBtn.dataset.originalText = submitBtn.textContent;
                        submitBtn.textContent = 'Working...';
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    }
                });
            });

            // Real-time validation feedback
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('blur', function () {
                    if (this.value && this.checkValidity()) {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function () {
                    if (this.classList.contains('is-invalid') && this.checkValidity()) {
                        this.classList.remove('is-invalid');
                        const errorEl = this.parentNode.querySelector('.error-message');
                        if (errorEl) {
                            errorEl.remove();
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>