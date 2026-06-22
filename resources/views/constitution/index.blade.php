<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $constitution['title'] }} | Barakah</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/logo-white-bg.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        :root {
            --bg: #ffffff; --bg-soft: #f8f9fb; --text: #1a1d23; --text-soft: #5b6472;
            --border: #e8eaed; --accent: #2563eb; --accent-soft: #eaf1ff; --topbar: #ffffff;
        }
        [data-theme="dark"] {
            --bg: #0f1115; --bg-soft: #161922; --text: #e6e8ec; --text-soft: #9aa3b2;
            --border: #262b36; --accent: #5b8cff; --accent-soft: #1b2740; --topbar: #12151b;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); }
        a { color: inherit; text-decoration: none; }

        /* Top bar */
        .topbar { position: sticky; top: 0; z-index: 50; display: flex; align-items: center; gap: 16px;
            height: 64px; padding: 0 24px; background: var(--topbar); border-bottom: 1px solid var(--border); }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 18px; }
        .brand img { height: 30px; }
        .topbar .search { margin-left: auto; position: relative; width: 320px; max-width: 38vw; }
        .topbar .search input { width: 100%; height: 40px; border-radius: 10px; border: 1px solid var(--border);
            background: var(--bg-soft); color: var(--text); padding: 0 14px; font-size: 14px; outline: none; }
        .topbar .search input:focus { border-color: var(--accent); }
        .icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;
            border-radius: 10px; border: 1px solid var(--border); background: var(--bg); color: var(--text); cursor: pointer; }
        .back-btn { display: inline-flex; align-items: center; gap: 6px; height: 40px; padding: 0 16px; border-radius: 10px;
            background: var(--text); color: var(--bg); font-weight: 600; font-size: 14px; }

        /* Layout */
        .layout { display: grid; grid-template-columns: 270px minmax(0, 1fr) 240px; gap: 0;
            max-width: 1400px; margin: 0 auto; }
        .sidebar { position: sticky; top: 64px; align-self: start; height: calc(100vh - 64px); overflow-y: auto;
            padding: 24px 12px; border-right: 1px solid var(--border); }
        .toc { position: sticky; top: 64px; align-self: start; height: calc(100vh - 64px); overflow-y: auto;
            padding: 24px 16px; border-left: 1px solid var(--border); }
        .content { padding: 36px 48px 120px; min-width: 0; }

        /* Left nav */
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 8px;
            color: var(--text-soft); font-size: 14px; font-weight: 500; cursor: pointer; }
        .nav-link:hover { background: var(--bg-soft); color: var(--text); }
        .nav-link.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
        .nav-link svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* Content typography */
        .eyebrow { display: flex; align-items: center; gap: 8px; color: var(--text-soft); font-size: 14px; font-weight: 600; margin-bottom: 12px; }
        .doc-title { font-size: 40px; font-weight: 800; letter-spacing: -1px; margin: 0 0 32px; }
        .doc-subtitle { color: var(--text-soft); font-size: 16px; margin: -20px 0 32px; }
        .article { scroll-margin-top: 84px; margin-bottom: 48px; }
        .article h2 { font-size: 26px; font-weight: 700; letter-spacing: -0.5px; margin: 0 0 16px;
            padding-bottom: 10px; border-bottom: 1px solid var(--border); }
        .article p { font-size: 16px; line-height: 1.75; color: var(--text); margin: 0 0 16px; }
        .article ul { padding-left: 22px; margin: 0 0 16px; }
        .article li { font-size: 16px; line-height: 1.8; margin-bottom: 6px; }
        .article strong { font-weight: 700; }

        /* Right TOC */
        .toc-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--text-soft); margin-bottom: 14px; }
        .toc-link { display: block; padding: 6px 0; font-size: 13px; color: var(--text-soft); line-height: 1.4; }
        .toc-link:hover { color: var(--text); }
        .toc-link.active { color: var(--accent); font-weight: 600; }

        @media (max-width: 1100px) {
            .layout { grid-template-columns: 240px minmax(0,1fr); }
            .toc { display: none; }
        }
        @media (max-width: 768px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .content { padding: 24px 20px 80px; }
            .topbar .search { display: none; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ route('dashboard') }}" class="brand">
            <img src="{{ asset('assets/logo/logo-name.png') }}" alt="Barakah">
        </a>
        <div class="search">
            <input type="search" id="navSearch" placeholder="Search the constitution…" autocomplete="off">
        </div>
        <button class="icon-btn" id="themeToggle" title="Toggle theme"><span data-feather="sun"></span></button>
        <a href="{{ route('dashboard') }}" class="back-btn"><span data-feather="arrow-left" style="width:16px;height:16px;"></span> Back to app</a>
    </div>

    <div class="layout">
        <!-- Left nav -->
        <aside class="sidebar">
            <nav id="sideNav">
                @foreach($constitution['sections'] as $s)
                    <a class="nav-link" href="#{{ $s['id'] }}" data-target="{{ $s['id'] }}" data-label="{{ strip_tags($s['title']) }}">
                        <span data-feather="{{ $s['icon'] ?? 'file-text' }}"></span>
                        <span>{!! $s['title'] !!}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- Content -->
        <main class="content">
            <div class="eyebrow"><span data-feather="book" style="width:16px;height:16px;"></span> {{ $constitution['subtitle'] }}</div>
            <h1 class="doc-title">{{ $constitution['title'] }}</h1>
            @if(!empty($constitution['updated_at']))
                <p class="doc-subtitle">{{ $constitution['updated_at'] }}</p>
            @endif

            @foreach($constitution['sections'] as $s)
                <section class="article" id="{{ $s['id'] }}">
                    <h2>{!! $s['title'] !!}</h2>
                    {!! $s['body'] !!}
                </section>
            @endforeach
        </main>

        <!-- Right TOC -->
        <aside class="toc">
            <div class="toc-title">On This Page</div>
            <nav id="tocNav">
                @foreach($constitution['sections'] as $s)
                    <a class="toc-link" href="#{{ $s['id'] }}" data-target="{{ $s['id'] }}">{{ strip_tags($s['title']) }}</a>
                @endforeach
            </nav>
        </aside>
    </div>

    <script>
        feather.replace();

        // Theme toggle
        (function () {
            const html = document.documentElement;
            const btn = document.getElementById('themeToggle');
            const saved = localStorage.getItem('constitution-theme');
            if (saved) html.setAttribute('data-theme', saved);
            function syncIcon() {
                btn.innerHTML = '<span data-feather="' + (html.getAttribute('data-theme') === 'dark' ? 'moon' : 'sun') + '"></span>';
                feather.replace();
            }
            syncIcon();
            btn.addEventListener('click', () => {
                const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', next);
                localStorage.setItem('constitution-theme', next);
                syncIcon();
            });
        })();

        // Left-nav search filter
        document.getElementById('navSearch').addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#sideNav .nav-link').forEach(link => {
                const match = link.dataset.label.toLowerCase().includes(q);
                link.style.display = match ? '' : 'none';
            });
        });

        // Scroll-spy: highlight active section in both navs
        (function () {
            const sections = Array.from(document.querySelectorAll('.article'));
            const navLinks = document.querySelectorAll('#sideNav .nav-link');
            const tocLinks = document.querySelectorAll('#tocNav .toc-link');

            function setActive(id) {
                navLinks.forEach(l => l.classList.toggle('active', l.dataset.target === id));
                tocLinks.forEach(l => l.classList.toggle('active', l.dataset.target === id));
            }

            const observer = new IntersectionObserver((entries) => {
                const visible = entries.filter(e => e.isIntersecting)
                    .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
                if (visible.length) setActive(visible[0].target.id);
            }, { rootMargin: '-80px 0px -70% 0px', threshold: 0 });

            sections.forEach(s => observer.observe(s));
            if (sections.length) setActive(sections[0].id);
        })();
    </script>
</body>
</html>
