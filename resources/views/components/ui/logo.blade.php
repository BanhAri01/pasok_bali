@props(['size' => 36, 'tone' => 'gelap', 'wordmark' => true])

@php
    $ink = $tone === 'terang' ? 'var(--color-kertas)' : 'var(--color-daun-tua)';
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" role="img" aria-label="Logo Pasok Bali: jineng, lumbung padi Bali" class="shrink-0">
        <path d="M24 3.5C17 8.8 10.6 16.6 7.5 26.5h33C37.4 16.6 31 8.8 24 3.5Z" fill="{{ $ink }}"/>
        <path d="M24 3.5c-2.4 6-3.3 13.7-3.1 23M24 3.5c2.4 6 3.3 13.7 3.1 23" stroke="var(--color-kertas)" stroke-opacity=".22" stroke-width="1" fill="none"/>
        <ellipse cx="24" cy="18.5" rx="3.2" ry="4" fill="var(--color-prada)"/>
        <path d="M9 29h30" stroke="{{ $ink }}" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M13.5 29v15M34.5 29v15M11.5 37.5h25" stroke="{{ $ink }}" stroke-width="2.2" stroke-linecap="round"/>
    </svg>
    @if ($wordmark)
        <span @class(['font-display text-[1.65rem] font-semibold leading-none tracking-tight', 'text-daun-tua' => $tone !== 'terang', 'text-kertas' => $tone === 'terang'])>Pasok Bali</span>
    @endif
</span>
