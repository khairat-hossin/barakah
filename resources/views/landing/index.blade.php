<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandName }} — Friendship. Unity. Ethical Growth.</title>
    <meta name="description" content="{{ $brandName }} is a friendship-driven social and economic association built on trust, disciplined savings, ethical investment, and collective responsibility.">

    <link rel="icon" href="{{ \App\Support\Branding::faviconUrl() ?? asset('assets/logo/logo-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root{
            --green:#0F6B4B; --green-2:#136F63; --green-dark:#0B5D3B;
            --gold:#C89B3C; --gold-2:#D4A84F; --gold-dark:#B8860B;
            --navy:#1F2A44; --navy-2:#24324A;
            --bg:#FAF8F3; --bg-2:#F4F6F8; --bg-3:#F7F3EA;
            --ink:#24324A; --muted:#5f6b7a; --line:#e9e3d6;
            --shadow:0 18px 50px -24px rgba(31,42,68,.28);
            --radius:20px;
        }
        *{box-sizing:border-box;}
        html{scroll-behavior:smooth;}
        body{margin:0;font-family:'Manrope','Hind Siliguri',system-ui,sans-serif;color:var(--ink);
            background:var(--bg);line-height:1.65;-webkit-font-smoothing:antialiased;}
        .bn{font-family:'Hind Siliguri',sans-serif;}
        a{text-decoration:none;color:inherit;}
        img{max-width:100%;display:block;}
        h1,h2,h3{color:var(--navy);font-weight:800;letter-spacing:-.02em;line-height:1.12;margin:0;}
        p{margin:0;color:var(--muted);}
        .wrap{max-width:1180px;margin:0 auto;padding:0 24px;}
        .eyebrow{font-size:.78rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-dark);margin-bottom:.6rem;}
        .section{padding:88px 0;}
        .section-head{text-align:center;max-width:660px;margin:0 auto 52px;}
        .section-head h2{font-size:clamp(1.7rem,3.4vw,2.5rem);}
        .section-head .rule{width:52px;height:3px;background:var(--gold);border-radius:3px;margin:16px auto 0;}
        .section-head p{margin-top:14px;font-size:1.02rem;}

        /* Buttons */
        .btn{display:inline-flex;align-items:center;gap:.55rem;font-weight:700;font-size:.92rem;
            padding:.85rem 1.5rem;border-radius:999px;border:1.5px solid transparent;cursor:pointer;
            transition:transform .18s ease, box-shadow .18s ease, background .18s ease;white-space:nowrap;}
        .btn:hover{transform:translateY(-2px);}
        .btn-green{background:linear-gradient(135deg,var(--green),var(--green-2));color:#fff;box-shadow:0 12px 26px -12px rgba(15,107,75,.6);}
        .btn-navy{background:var(--navy);color:#fff;box-shadow:0 12px 26px -12px rgba(31,42,68,.55);}
        .btn-gold{background:transparent;color:var(--gold-dark);border-color:var(--gold);}
        .btn-gold:hover{background:var(--gold);color:#fff;}
        .btn-ghost{background:#fff;color:var(--navy);border-color:var(--line);}
        .btn-light{background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.28);}
        .btn-light:hover{background:rgba(255,255,255,.22);}

        /* Nav */
        .nav{position:sticky;top:0;z-index:50;background:rgba(250,248,243,.85);backdrop-filter:blur(10px);
            border-bottom:1px solid var(--line);}
        .nav-inner{display:flex;align-items:center;gap:20px;height:74px;}
        .brand{display:flex;align-items:center;gap:12px;}
        .brand img{width:42px;height:42px;object-fit:contain;}
        .brand .bt{font-weight:800;color:var(--navy);font-size:1.02rem;line-height:1.05;letter-spacing:.02em;text-transform:uppercase;}
        .brand .bs{font-size:.68rem;color:var(--gold-dark);font-weight:600;letter-spacing:.04em;}
        .nav-links{display:flex;gap:22px;margin-left:auto;}
        .nav-links a{font-size:.82rem;font-weight:600;letter-spacing:.03em;text-transform:uppercase;color:var(--navy);opacity:.85;white-space:nowrap;}
        .nav-links a:hover{opacity:1;color:var(--green);}
        .nav-cta{display:flex;gap:10px;align-items:center;}
        .nav-cta .btn{padding:.6rem 1.1rem;font-size:.8rem;}
        .nav-toggle{display:none;background:none;border:0;font-size:1.4rem;color:var(--navy);}

        /* Hero */
        .hero{position:relative;overflow:hidden;background:
            radial-gradient(1200px 500px at 82% -10%, rgba(200,155,60,.14), transparent 60%),
            radial-gradient(900px 500px at 0% 110%, rgba(15,107,75,.10), transparent 60%),
            linear-gradient(180deg,var(--bg-3),var(--bg));}
        .hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:48px;align-items:center;padding:72px 0 84px;}
        .hero h1{font-size:clamp(2.2rem,5vw,3.5rem);}
        .hero h1 .gold{color:var(--gold-dark);}
        .hero .lead{margin-top:20px;font-size:1.08rem;max-width:520px;}
        .hero-cta{display:flex;flex-wrap:wrap;gap:14px;margin-top:30px;}
        .hero-chips{display:flex;flex-wrap:wrap;gap:10px 22px;margin-top:34px;padding-top:26px;border-top:1px solid var(--line);}
        .chip{display:flex;align-items:center;gap:9px;font-size:.85rem;font-weight:600;color:var(--navy);}
        .chip i{color:var(--green);width:20px;text-align:center;}
        .hero-visual{position:relative;display:flex;align-items:center;justify-content:center;min-height:360px;}

        /* Cards */
        .grid{display:grid;gap:22px;}
        .cols-4{grid-template-columns:repeat(4,1fr);}
        .cols-3{grid-template-columns:repeat(3,1fr);}
        .cols-2{grid-template-columns:repeat(2,1fr);}
        .card{background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:30px 26px;
            box-shadow:0 10px 30px -22px rgba(31,42,68,.35);transition:transform .2s ease, box-shadow .2s ease;}
        .card:hover{transform:translateY(-5px);box-shadow:var(--shadow);}
        .card .ico{width:60px;height:60px;border-radius:16px;display:flex;align-items:center;justify-content:center;
            font-size:1.4rem;margin-bottom:18px;background:linear-gradient(135deg,rgba(15,107,75,.10),rgba(200,155,60,.14));color:var(--green);}
        .card h3{font-size:1.12rem;margin-bottom:8px;}
        .card p{font-size:.92rem;}

        /* Vision / mission / values */
        .vmv{background:linear-gradient(180deg,var(--bg-2),var(--bg));}
        .vmv .card .ico{background:var(--navy);color:var(--gold-2);}
        .values-tags{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:8px;}
        .vtag{display:flex;align-items:center;gap:9px;background:#fff;border:1px solid var(--line);border-radius:999px;
            padding:.6rem 1.15rem;font-weight:700;color:var(--navy);font-size:.9rem;box-shadow:0 8px 20px -16px rgba(31,42,68,.4);}
        .vtag i{color:var(--gold-dark);}

        /* Process timeline */
        .steps{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;position:relative;}
        .steps:before{content:"";position:absolute;top:38px;left:8%;right:8%;height:2px;
            background:repeating-linear-gradient(90deg,var(--gold) 0 8px,transparent 8px 16px);opacity:.5;}
        .step{text-align:center;position:relative;}
        .step .dot{width:76px;height:76px;border-radius:50%;background:#fff;border:2px solid var(--gold);
            display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--green);margin:0 auto 14px;
            box-shadow:0 12px 28px -18px rgba(15,107,75,.6);}
        .step .num{font-weight:800;color:var(--gold-dark);font-size:1.05rem;}
        .step h3{font-size:1rem;margin:4px 0 6px;}
        .step p{font-size:.84rem;}

        /* Principles (navy) */
        .principles{position:relative;overflow:hidden;color:#eef2f7;
            background:radial-gradient(900px 500px at 90% 0%, rgba(200,155,60,.18), transparent 55%),
            linear-gradient(160deg,var(--navy),#16203a);}
        .principles h2{color:#fff;}
        .principles .eyebrow{color:var(--gold-2);}
        .principles .grid{margin-top:8px;}
        .pcard{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.10);border-radius:var(--radius);
            padding:26px 24px;transition:transform .2s ease,background .2s ease;}
        .pcard:hover{transform:translateY(-4px);background:rgba(255,255,255,.09);}
        .pcard .ico{width:52px;height:52px;border-radius:14px;background:rgba(200,155,60,.16);color:var(--gold-2);
            display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:16px;}
        .pcard h3{color:#fff;font-size:1.06rem;margin-bottom:7px;}
        .pcard p{color:rgba(238,242,247,.72);font-size:.9rem;}

        /* Why (split) */
        .split{display:grid;grid-template-columns:1.05fr .95fr;gap:48px;align-items:center;}
        .why-visual{background:linear-gradient(160deg,var(--green-dark),var(--green-2));border-radius:26px;padding:44px;color:#fff;
            position:relative;overflow:hidden;box-shadow:var(--shadow);}
        .why-visual .qm{font-family:'Playfair Display',serif;font-size:4rem;line-height:.6;color:var(--gold-2);}
        .why-visual blockquote{font-family:'Playfair Display',serif;font-size:1.5rem;line-height:1.4;margin:14px 0 0;}
        .why-list{margin-top:22px;display:grid;gap:14px;}
        .why-list .li{display:flex;gap:13px;align-items:flex-start;}
        .why-list .li i{color:var(--green);margin-top:4px;}
        .why-list .li b{color:var(--navy);}

        /* Manifesto */
        .manifesto{position:relative;text-align:center;color:#fff;overflow:hidden;padding:96px 0;
            background:radial-gradient(800px 400px at 50% 0%, rgba(200,155,60,.22), transparent 60%),
            linear-gradient(155deg,var(--green-dark),#0c4e34);}
        .manifesto .qm{font-family:'Playfair Display',serif;font-size:5rem;color:var(--gold-2);line-height:.4;}
        .manifesto blockquote{font-family:'Playfair Display',serif;font-weight:500;font-size:clamp(1.5rem,3.6vw,2.4rem);
            line-height:1.4;max-width:860px;margin:18px auto 0;}
        .manifesto .goldline{width:70px;height:3px;background:var(--gold-2);margin:26px auto 0;border-radius:3px;}
        .manifesto .sig{margin-top:18px;letter-spacing:.14em;text-transform:uppercase;font-size:.8rem;color:rgba(255,255,255,.75);}

        /* Aim pillars */
        .pillars{display:grid;grid-template-columns:repeat(5,1fr);gap:18px;}
        .pillar{text-align:center;background:#fff;border:1px solid var(--line);border-radius:18px;padding:28px 18px;
            transition:transform .2s ease,box-shadow .2s ease;}
        .pillar:hover{transform:translateY(-5px);box-shadow:var(--shadow);}
        .pillar .ico{width:64px;height:64px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;
            font-size:1.5rem;background:linear-gradient(135deg,rgba(15,107,75,.12),rgba(200,155,60,.16));color:var(--green);}
        .pillar h3{font-size:1rem;margin-bottom:6px;}
        .pillar p{font-size:.83rem;}

        /* Governance / CTA card */
        .govt{background:linear-gradient(180deg,var(--bg-3),var(--bg));}
        .cta-band{background:#fff;border:1px solid var(--line);border-radius:26px;padding:44px;box-shadow:var(--shadow);
            display:flex;align-items:center;justify-content:space-between;gap:30px;flex-wrap:wrap;}
        .cta-band .lo{display:flex;align-items:center;gap:20px;}
        .cta-band img{width:58px;height:58px;object-fit:contain;}
        .cta-band h3{font-size:1.5rem;}
        .cta-band p{margin-top:4px;}
        .final-cta{text-align:center;color:#fff;padding:92px 0;position:relative;overflow:hidden;
            background:radial-gradient(700px 360px at 50% -20%, rgba(200,155,60,.2), transparent 60%),
            linear-gradient(160deg,var(--navy),#141d33);}
        .final-cta h2{color:#fff;font-size:clamp(1.8rem,3.6vw,2.6rem);}
        .final-cta p{color:rgba(238,242,247,.78);margin-top:14px;max-width:620px;margin-left:auto;margin-right:auto;}
        .final-cta .btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:30px;}

        /* Footer */
        footer{background:linear-gradient(180deg,#16203a,#111a30);color:#c7cede;padding:64px 0 26px;}
        .foot-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1.2fr;gap:36px;}
        .foot-brand img{width:44px;height:44px;object-fit:contain;margin-bottom:14px;}
        .foot-brand .ft{font-weight:800;color:#fff;letter-spacing:.05em;text-transform:uppercase;}
        .foot-brand p{color:rgba(199,206,222,.72);font-size:.9rem;margin-top:10px;max-width:280px;}
        .foot-social{display:flex;gap:10px;margin-top:16px;}
        .foot-social a{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:#c7cede;transition:background .2s;}
        .foot-social a:hover{background:var(--gold);color:#fff;}
        footer h4{color:#fff;font-size:.82rem;letter-spacing:.14em;text-transform:uppercase;margin:0 0 16px;}
        footer ul{list-style:none;padding:0;margin:0;display:grid;gap:9px;}
        footer ul a{color:rgba(199,206,222,.82);font-size:.92rem;}
        footer ul a:hover{color:var(--gold-2);}
        .foot-contact li{display:flex;gap:10px;font-size:.9rem;color:rgba(199,206,222,.82);}
        .foot-contact i{color:var(--gold-2);margin-top:4px;width:16px;}
        .foot-bottom{border-top:1px solid rgba(255,255,255,.08);margin-top:44px;padding-top:20px;
            display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:.82rem;color:rgba(199,206,222,.6);}
        .foot-bottom .tag b.g{color:var(--gold-2);} .foot-bottom .tag b.e{color:#4ea883;}

        /* Reveal animation */
        .reveal{opacity:0;transform:translateY(24px);transition:opacity .7s ease, transform .7s ease;}
        .reveal.in{opacity:1;transform:none;}

        /* Responsive */
        @media (max-width:991px){
            .hero-grid,.split{grid-template-columns:1fr;}
            .hero-visual{order:-1;min-height:260px;}
            .cols-4{grid-template-columns:repeat(2,1fr);}
            .steps{grid-template-columns:repeat(2,1fr);gap:26px;} .steps:before{display:none;}
            .pillars{grid-template-columns:repeat(2,1fr);}
            .foot-grid{grid-template-columns:1fr 1fr;}
        }
        @media (max-width:767px){
            .section{padding:60px 0;}
            .nav-links{display:none;} .nav-toggle{display:block;}
            .nav-cta .btn span.lbl{display:none;}
            .cols-4,.cols-3,.cols-2,.pillars{grid-template-columns:1fr;}
            .steps{grid-template-columns:1fr;}
            .cta-band{flex-direction:column;text-align:center;align-items:center;}
            .cta-band .lo{flex-direction:column;text-align:center;}
            .foot-grid{grid-template-columns:1fr;gap:28px;}
            .manifesto{padding:64px 0;}
        }
        /* Mobile nav drawer */
        .mnav{display:none;position:fixed;inset:74px 0 auto 0;background:var(--bg);border-bottom:1px solid var(--line);
            padding:14px 24px 22px;z-index:49;box-shadow:var(--shadow);}
        .mnav.open{display:block;}
        .mnav a{display:block;padding:12px 0;font-weight:600;color:var(--navy);border-bottom:1px solid var(--line);text-transform:uppercase;font-size:.85rem;letter-spacing:.03em;}
    </style>
</head>
<body id="top">

{{-- ============================= NAV ============================= --}}
<header class="nav">
    <div class="wrap nav-inner">
        <a class="brand" href="#top">
            <img src="{{ $logo }}" alt="{{ $brandName }}">
            <span>
                <span class="bt">{{ $brandName }}</span><br>
                <span class="bs">{{ $motto }}</span>
            </span>
        </a>
        <nav class="nav-links">
            <a href="#top">Home</a>
            <a href="#about">About Us</a>
            <a href="#vision">Our Vision</a>
            <a href="#principles">Principles</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="nav-cta">
            <a class="btn btn-gold" href="{{ route('constitution') }}"><i class="fa-solid fa-book-open"></i><span class="lbl">Read Constitution</span></a>
            @auth
                <a class="btn btn-navy" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            @else
                <a class="btn btn-navy" href="{{ route('tyro-login.login') }}">Member Login</a>
            @endauth
            <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
        </div>
    </div>
</header>
<div class="mnav" id="mnav">
    <a href="#top">Home</a>
    <a href="#about">About Us</a>
    <a href="#vision">Our Vision</a>
    <a href="#principles">Principles</a>
    <a href="{{ route('constitution') }}">Constitution</a>
    <a href="#contact">Contact</a>
</div>

{{-- ============================= HERO ============================= --}}
<section class="hero">
    <div class="wrap hero-grid">
        <div>
            <div class="eyebrow">A Friendship-Driven Social &amp; Economic Association</div>
            <h1>From Friendship to Unity,<br>from Unity to <span class="gold">Prosperity.</span></h1>
            <p class="lead bn" style="margin-top:14px;color:var(--green-dark);font-weight:600;font-size:1.05rem;">
                বন্ধুত্ব থেকে ঐক্য, ঐক্য থেকে সমৃদ্ধি।
            </p>
            <p class="lead">
                {{ $brandName }} is a friendship-driven social and economic association built on trust,
                disciplined savings, ethical investment, and collective responsibility. Through unity and
                interest-free financial growth, we build long-term prosperity for our members and society.
            </p>
            <div class="hero-cta">
                <a class="btn btn-navy" href="#vision"><i class="fa-solid fa-compass"></i> Explore Our Vision</a>
                <a class="btn btn-gold" href="{{ route('constitution') }}"><i class="fa-solid fa-book-open"></i> Read Constitution</a>
            </div>
            <div class="hero-chips">
                <span class="chip"><i class="fa-solid fa-shield-halved"></i> Interest-Free Foundation</span>
                <span class="chip"><i class="fa-solid fa-people-group"></i> Transparency &amp; Accountability</span>
                <span class="chip"><i class="fa-solid fa-seedling"></i> Discipline &amp; Responsibility</span>
                <span class="chip"><i class="fa-solid fa-hand-holding-heart"></i> Community Welfare</span>
            </div>
        </div>
        <div class="hero-visual">
            @include('landing.partials.unity-graphic')
        </div>
    </div>
</section>

{{-- ============================= WHO WE ARE ============================= --}}
<section class="section" id="about">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">What We Stand For</div>
            <h2>A Collective Initiative for a Better Future</h2>
            <div class="rule"></div>
            <p>A friendship-based, transparent and disciplined association where saving, investing and
               growing happen together — for the dignity of every member and the good of the community.</p>
        </div>
        <div class="grid cols-4">
            @foreach([
                ['users','Unity &amp; Brotherhood','Built on sincere friendship, mutual respect, and strong, lasting bonds.'],
                ['piggy-bank','Collective Savings','Regular, disciplined savings that build shared financial strength and security.'],
                ['seedling','Ethical Investment','Funds grow through interest-free, productive and responsible opportunities.'],
                ['hand-holding-heart','Community Welfare','We grow together and give back for the betterment of society.'],
            ] as $c)
                <div class="card reveal">
                    <div class="ico"><i class="fa-solid fa-{{ $c[0] }}"></i></div>
                    <h3>{!! $c[1] !!}</h3>
                    <p>{{ $c[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================= VISION / MISSION / VALUES ============================= --}}
<section class="section vmv" id="vision">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Our Philosophy</div>
            <h2>Vision, Mission &amp; Values</h2>
            <div class="rule"></div>
        </div>
        <div class="grid cols-2 reveal" style="margin-bottom:26px;">
            <div class="card">
                <div class="ico"><i class="fa-solid fa-eye"></i></div>
                <h3>Our Vision</h3>
                <p>A self-reliant, dignified community where friendship becomes responsibility, savings become
                   security, and ethical growth becomes a lasting legacy for the next generation.</p>
            </div>
            <div class="card">
                <div class="ico"><i class="fa-solid fa-bullseye"></i></div>
                <h3>Our Mission</h3>
                <p>To unite members around shared values, build a transparent collective fund through disciplined
                   savings, and channel it into interest-free, productive initiatives that uplift members and society.</p>
            </div>
        </div>
        <div class="section-head reveal" style="margin:10px auto 26px;">
            <h3 style="font-size:1.15rem;">The Core Values That Guide Us</h3>
        </div>
        <div class="values-tags reveal">
            @foreach([['handshake-angle','Trust'],['people-group','Unity'],['scale-balanced','Discipline'],['eye','Transparency'],['clipboard-check','Accountability'],['hands-holding','Responsibility'],['seedling','Collective Prosperity']] as $v)
                <span class="vtag"><i class="fa-solid fa-{{ $v[0] }}"></i>{{ $v[1] }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================= HOW WE WORK ============================= --}}
<section class="section" id="how">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">How We Work</div>
            <h2>Our Journey of Growth</h2>
            <div class="rule"></div>
        </div>
        <div class="steps reveal">
            @foreach([
                ['people-group','01','Members unite','Friends unite around shared values and a common purpose.'],
                ['piggy-bank','02','Collective fund','Regular monthly savings build a strong collective fund.'],
                ['shield-halved','03','Managed with trust','Funds are managed with transparency &amp; accountability.'],
                ['chart-line','04','Ethical growth','Capital works through interest-free, productive initiatives.'],
                ['hand-holding-heart','05','Shared prosperity','Growth supports members and contributes to society.'],
            ] as $s)
                <div class="step">
                    <div class="dot"><i class="fa-solid fa-{{ $s[0] }}"></i></div>
                    <div class="num">{{ $s[1] }}</div>
                    <h3>{{ $s[2] }}</h3>
                    <p>{!! $s[3] !!}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================= FOUNDATION PRINCIPLES ============================= --}}
<section class="section principles" id="principles">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Our Foundation Principles</div>
            <h2>The Values That Guide Us</h2>
            <div class="rule"></div>
            <p style="color:rgba(238,242,247,.75)">Principles drawn from our constitution — the foundation on which every
               decision, saving and investment rests.</p>
        </div>
        <div class="grid cols-3">
            @foreach([
                ['ban','Interest-Free Economic Foundation','A financial model free from interest — rooted in fairness, ethics and mutual benefit.'],
                ['piggy-bank','Collective Savings &amp; Investment','Shared savings, thoughtfully invested in productive and responsible opportunities.'],
                ['handshake-angle','Shared Responsibility &amp; Accountability','Every member is a stakeholder — responsible to one another and to the whole.'],
                ['scale-balanced','Disciplined &amp; Transparent Governance','Clear rules, honest records, and decisions made openly and fairly.'],
                ['hand-holding-heart','Social Welfare &amp; Community Support','Growth that reaches beyond members to strengthen the wider community.'],
                ['seedling','A Better Future Through Unity','Building a lasting, dignified legacy for the generations to come.'],
            ] as $p)
                <div class="pcard reveal">
                    <div class="ico"><i class="fa-solid fa-{{ $p[0] }}"></i></div>
                    <h3>{!! $p[1] !!}</h3>
                    <p>{{ $p[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================= WHY WE EXIST ============================= --}}
<section class="section" id="why">
    <div class="wrap split">
        <div class="reveal">
            <div class="eyebrow">Why {{ $brandName }} Exists</div>
            <h2 style="font-size:clamp(1.7rem,3.2vw,2.3rem);">When friendship becomes responsibility.</h2>
            <p style="margin-top:18px;font-size:1.02rem;">
                Real friendship is more than companionship — it is trust, empathy, and standing beside one another
                when it matters. {{ $brandName }} turns that friendship into something enduring: a disciplined way to
                save together, grow ethically, and protect each other's future.
            </p>
            <div class="why-list">
                <div class="li"><i class="fa-solid fa-circle-check"></i><span><b>Savings become stability.</b> Small, steady contributions build lasting security.</span></div>
                <div class="li"><i class="fa-solid fa-circle-check"></i><span><b>Unity becomes opportunity.</b> Together we can do what none of us could alone.</span></div>
                <div class="li"><i class="fa-solid fa-circle-check"></i><span><b>Ethical finance becomes dignity.</b> Growth without interest, without exploitation.</span></div>
                <div class="li"><i class="fa-solid fa-circle-check"></i><span><b>Today becomes a legacy.</b> What we build now inspires the generations after us.</span></div>
            </div>
        </div>
        <div class="why-visual reveal">
            <div class="qm">&ldquo;</div>
            <blockquote class="bn">একসাথে সঞ্চয়, একসাথে অগ্রগতি, একসাথে আগামী।</blockquote>
            <p style="color:rgba(255,255,255,.82);margin-top:16px;">Together we save, together we grow, together we build tomorrow.</p>
            <div style="margin-top:28px;">@include('landing.partials.roots-graphic')</div>
        </div>
    </div>
</section>

{{-- ============================= MANIFESTO / QUOTE ============================= --}}
<section class="manifesto">
    <div class="wrap reveal">
        <div class="qm">&ldquo;</div>
        <blockquote class="bn">বন্ধুত্ব যখন দায়িত্বে রূপ নেয়, তখনই জন্ম নেয় ঐক্যের শক্তি।</blockquote>
        <div class="goldline"></div>
        <div class="sig">When friendship turns into responsibility, unity becomes a force for a better tomorrow.</div>
    </div>
</section>

{{-- ============================= WHAT WE AIM TO BUILD ============================= --}}
<section class="section" id="aim">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Our Aspiration</div>
            <h2>What We Aim to Build</h2>
            <div class="rule"></div>
        </div>
        <div class="pillars">
            @foreach([
                ['shield-halved','Financial Security','Stability and peace of mind for every member and their family.'],
                ['seedling','Ethical Investment','Interest-free, productive opportunities that grow with integrity.'],
                ['lightbulb','Self-Reliance','Support for entrepreneurship, skills and independent livelihoods.'],
                ['graduation-cap','Youth Engagement','Inspiring the next generation to lead with values and vision.'],
                ['hand-holding-heart','Community Impact','Welfare initiatives that create real, positive social change.'],
            ] as $p)
                <div class="pillar reveal">
                    <div class="ico"><i class="fa-solid fa-{{ $p[0] }}"></i></div>
                    <h3>{{ $p[1] }}</h3>
                    <p>{{ $p[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================= GOVERNANCE ============================= --}}
<section class="section govt" id="governance">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Governed by Trust</div>
            <h2>Guided by Our Constitution</h2>
            <div class="rule"></div>
            <p>A disciplined, structured organization where every decision is transparent, every member accountable,
               and every taka handled with clarity and care.</p>
        </div>
        <div class="grid cols-3 reveal" style="margin-bottom:40px;">
            @foreach([
                ['scale-balanced','Transparent Decisions','Open, fair and recorded decision-making at every level.'],
                ['people-roof','Committee Structure','A member-elected committee stewards the fund responsibly.'],
                ['file-shield','Financial Clarity','Clear records and honest reporting keep trust intact.'],
            ] as $g)
                <div class="card">
                    <div class="ico"><i class="fa-solid fa-{{ $g[0] }}"></i></div>
                    <h3>{{ $g[1] }}</h3>
                    <p>{{ $g[2] }}</p>
                </div>
            @endforeach
        </div>
        <div class="cta-band reveal">
            <div class="lo">
                <img src="{{ $logo }}" alt="{{ $brandName }}">
                <div>
                    <h3>Be a Part of This Journey</h3>
                    <p>Together, let's create a legacy of trust, discipline and unity.</p>
                </div>
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <a class="btn btn-navy" href="{{ route('constitution') }}"><i class="fa-solid fa-book-open"></i> Read Constitution</a>
                <a class="btn btn-gold" href="#contact"><i class="fa-solid fa-phone"></i> Contact Us</a>
            </div>
        </div>
    </div>
</section>

{{-- ============================= FINAL CTA ============================= --}}
<section class="final-cta">
    <div class="wrap reveal">
        <div class="eyebrow" style="color:var(--gold-2);">Join the Journey</div>
        <h2 class="bn">ঐক্যের এই যাত্রায় আপনিও হোন অনুপ্রেরণার অংশ।</h2>
        <p>A shared journey toward ethical growth, dignity and unity. Discover our vision, read our
           constitution, and become part of a community built on trust.</p>
        <div class="btns">
            <a class="btn btn-gold" href="#vision" style="color:var(--gold-2);border-color:var(--gold-2);"><i class="fa-solid fa-compass"></i> Explore Our Vision</a>
            <a class="btn btn-green" href="{{ route('constitution') }}"><i class="fa-solid fa-book-open"></i> Read Constitution</a>
            @auth
                <a class="btn btn-light" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge"></i> Go to Dashboard</a>
            @else
                <a class="btn btn-light" href="{{ route('tyro-login.login') }}"><i class="fa-solid fa-arrow-right-to-bracket"></i> Member Login</a>
            @endauth
        </div>
    </div>
</section>

{{-- ============================= FOOTER ============================= --}}
<footer id="contact">
    <div class="wrap">
        <div class="foot-grid">
            <div class="foot-brand">
                <img src="{{ $logo }}" alt="{{ $brandName }}">
                <div class="ft">{{ $brandName }}</div>
                <p>A friendship-based social and economic association for collective prosperity, ethical growth and community welfare.</p>
                <div class="foot-social">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#top">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#vision">Our Vision</a></li>
                    <li><a href="#principles">Principles</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Constitution</h4>
                <ul>
                    <li><a href="{{ route('constitution') }}">Read Constitution</a></li>
                    <li><a href="{{ route('constitution') }}">Rules &amp; Guidelines</a></li>
                    <li><a href="{{ route('constitution') }}">Membership</a></li>
                    <li><a href="{{ route('constitution') }}">Governance</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul class="foot-contact">
                    @if($address)<li><i class="fa-solid fa-location-dot"></i><span>{{ $address }}</span></li>@endif
                    @if($email)<li><i class="fa-solid fa-envelope"></i><a href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
                    @if($phone)<li><i class="fa-solid fa-phone"></i><a href="tel:{{ $phone }}">{{ $phone }}</a></li>@endif
                </ul>
            </div>
        </div>
        <div class="foot-bottom">
            <span>&copy; {{ date('Y') }} {{ $brandName }} — All rights reserved.</span>
            <span class="tag">Built on <b class="e">trust</b>. Guided by <b class="g">unity</b>. Growing <b class="g">together</b>.</span>
        </div>
    </div>
</footer>

<script>
    // Mobile nav
    const t = document.getElementById('navToggle'), m = document.getElementById('mnav');
    if (t) t.addEventListener('click', () => m.classList.toggle('open'));
    m && m.querySelectorAll('a').forEach(a => a.addEventListener('click', () => m.classList.remove('open')));

    // Reveal on scroll
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
</script>
</body>
</html>
