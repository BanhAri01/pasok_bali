@props(['size' => 40, 'tone' => 'gelap', 'wordmark' => true])

@php
    $roof = $tone === 'terang' ? 'var(--color-kertas)' : 'var(--color-daun-tua)';
    $wood = $tone === 'terang' ? 'var(--color-kertas)' : 'var(--color-tinta)';
    $mid = $tone === 'terang' ? 'var(--color-daun-tua)' : 'var(--color-kertas)';
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" role="img" aria-label="Logo Pasok Bali: jineng, lumbung padi Bali" class="shrink-0">
        <path d="M24 2.5C16.5 8 9.5 16.5 6.5 27h35C38.5 16.5 31.5 8 24 2.5Z" fill="{{ $roof }}"/>
        <path d="M24 2.5c-2.6 6.2-3.6 14.6-3.4 24.5M24 2.5c2.6 6.2 3.6 14.6 3.4 24.5" stroke="{{ $mid }}" stroke-opacity=".28" stroke-width="1.2" fill="none"/>
        <rect x="20.5" y="15" width="7" height="8" rx="3.5" fill="var(--color-kunyit)"/>
        <rect x="6.5" y="27" width="35" height="2.4" fill="var(--color-terakota)"/>
        <rect x="6.5" y="29.4" width="35" height="2.2" fill="{{ $mid }}"/>
        <rect x="6.5" y="31.6" width="35" height="2.4" fill="{{ $wood }}"/>
        <rect x="12" y="34" width="3" height="11.5" fill="{{ $wood }}"/>
        <rect x="33" y="34" width="3" height="11.5" fill="{{ $wood }}"/>
        <rect x="10" y="38" width="28" height="2.2" fill="{{ $wood }}"/>
    </svg>
    @if ($wordmark)
        <span @class(['font-display text-[1.45rem] leading-none tracking-tight', 'text-daun-tua' => $tone !== 'terang', 'text-kertas' => $tone === 'terang'])>Pasok Bali</span>
    @endif
</span>
