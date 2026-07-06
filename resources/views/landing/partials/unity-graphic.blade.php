{{-- Symbolic hero motif: echo/ripple rings + interconnected member nodes + central unity mark. --}}
<svg viewBox="0 0 460 420" width="100%" style="max-width:480px;overflow:visible" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Unity ripples connecting members">
    <defs>
        <radialGradient id="ug-core" cx="50%" cy="45%" r="60%">
            <stop offset="0%" stop-color="#136F63"/>
            <stop offset="100%" stop-color="#0B5D3B"/>
        </radialGradient>
        <linearGradient id="ug-gold" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#D4A84F"/>
            <stop offset="100%" stop-color="#B8860B"/>
        </linearGradient>
    </defs>

    {{-- ripple rings --}}
    <g fill="none" stroke-width="1.5">
        <circle cx="230" cy="210" r="200" stroke="#C89B3C" opacity=".16"/>
        <circle cx="230" cy="210" r="158" stroke="#0F6B4B" opacity=".22"/>
        <circle cx="230" cy="210" r="116" stroke="#C89B3C" opacity=".30"/>
    </g>

    {{-- connecting lines to nodes --}}
    <g stroke="#0F6B4B" stroke-width="1.5" opacity=".35">
        <line x1="230" y1="210" x2="230" y2="42"/>
        <line x1="230" y1="210" x2="388" y2="140"/>
        <line x1="230" y1="210" x2="360" y2="330"/>
        <line x1="230" y1="210" x2="100" y2="330"/>
        <line x1="230" y1="210" x2="72" y2="140"/>
    </g>

    {{-- member nodes --}}
    @foreach([[230,42],[388,140],[360,330],[100,330],[72,140]] as $n)
        <g transform="translate({{ $n[0] }},{{ $n[1] }})">
            <circle r="26" fill="#fff" stroke="#e9e3d6"/>
            <circle cx="0" cy="-6" r="6.5" fill="#1F2A44"/>
            <path d="M-11 12 a11 12 0 0 1 22 0 z" fill="#1F2A44"/>
        </g>
    @endforeach

    {{-- central unity core --}}
    <circle cx="230" cy="210" r="74" fill="url(#ug-core)"/>
    <circle cx="230" cy="210" r="74" fill="none" stroke="url(#ug-gold)" stroke-width="3"/>
    <g transform="translate(230,210)" fill="#fff">
        <circle cx="-20" cy="-14" r="9"/><path d="M-36 18 a16 18 0 0 1 32 0 z"/>
        <circle cx="20" cy="-14" r="9" fill="#D4A84F"/><path d="M4 18 a16 18 0 0 1 32 0 z" fill="#D4A84F"/>
        <circle cx="0" cy="-24" r="10"/><path d="M-17 12 a17 19 0 0 1 34 0 z"/>
    </g>
</svg>
