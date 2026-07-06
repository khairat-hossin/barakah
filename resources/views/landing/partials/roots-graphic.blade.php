{{-- Growth / roots motif: foundation growing into a shared canopy of unity. --}}
<svg viewBox="0 0 320 150" width="100%" style="max-width:320px" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Roots growing into unity">
    <g fill="none" stroke="#D4A84F" stroke-width="2" stroke-linecap="round" opacity=".85">
        {{-- trunk --}}
        <path d="M160 150 L160 96"/>
        {{-- branches --}}
        <path d="M160 96 C140 78 118 74 100 60"/>
        <path d="M160 96 C180 78 202 74 220 60"/>
        <path d="M160 108 C132 96 108 96 84 86"/>
        <path d="M160 108 C188 96 212 96 236 86"/>
        <path d="M160 84 C152 66 150 50 160 34"/>
    </g>
    {{-- canopy nodes (people/leaves of unity) --}}
    @foreach([[100,60],[220,60],[84,86],[236,86],[160,34],[160,96]] as $n)
        <circle cx="{{ $n[0] }}" cy="{{ $n[1] }}" r="8" fill="#fff" opacity=".92"/>
        <circle cx="{{ $n[0] }}" cy="{{ $n[1] }}" r="8" fill="none" stroke="#D4A84F" stroke-width="1.5"/>
    @endforeach
    {{-- roots --}}
    <g fill="none" stroke="#fff" stroke-width="1.5" opacity=".5" stroke-linecap="round">
        <path d="M160 150 C150 150 140 146 128 150"/>
        <path d="M160 150 C170 150 182 146 196 150"/>
        <path d="M160 150 C158 150 150 148 146 150"/>
    </g>
</svg>
