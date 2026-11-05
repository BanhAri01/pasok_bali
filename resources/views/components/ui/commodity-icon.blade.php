@props([
    'name' => 'sayur',
    'size' => 'md',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'size-8 rounded-lg',
        'lg' => 'size-14 rounded-2xl',
        'xl' => 'size-16 rounded-2xl',
        default => 'size-11 rounded-2xl',
    };

    $svgSize = match ($size) {
        'sm' => 'size-5',
        'lg' => 'size-8',
        'xl' => 'size-10',
        default => 'size-6',
    };

    $normalized = match ($name) {
        'hortikultura', 'pangan_kebun', 'sayuran', 'sayur' => 'sayur',
        'tanaman_pangan', 'padi', 'beras' => 'beras',
        'peternakan', 'telur' => 'telur',
        'perikanan', 'ikan' => 'ikan',
        'perkebunan', 'industri', 'kopi' => 'kopi',
        'nota' => 'nota',
        default => 'sayur',
    };

    $toneClass = match ($normalized) {
        'telur' => 'bg-kunyit-muda text-tinta ring-1 ring-kunyit/50',
        'ikan' => 'bg-daun-muda text-daun-tua ring-1 ring-daun/25',
        'beras' => 'bg-kunyit-muda text-daun-tua ring-1 ring-kunyit/50',
        'kopi' => 'bg-terakota-muda text-terakota ring-1 ring-terakota/25',
        'nota' => 'bg-santan text-tinta ring-1 ring-garis',
        default => 'bg-daun-muda text-daun-tua ring-1 ring-daun/25',
    };
@endphp

<span {{ $attributes->class(['inline-grid shrink-0 place-items-center', $sizeClass, $toneClass]) }} aria-hidden="true">
    @switch($normalized)
        @case('telur')
            <svg viewBox="0 0 32 32" fill="none" class="{{ $svgSize }}">
                <path d="M11.5 22C8.7 22 7 19.6 7 16.5C7 12.4 9.6 7.5 11.5 7.5C13.4 7.5 16 12.4 16 16.5C16 19.6 14.3 22 11.5 22Z" fill="#FFFDF7" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M20.5 22C17.7 22 16 19.6 16 16.5C16 12.4 18.6 7.5 20.5 7.5C22.4 7.5 25 12.4 25 16.5C25 19.6 23.3 22 20.5 22Z" fill="#FEF0C2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <circle cx="20.5" cy="16.5" r="2.2" fill="#F0B400"/>
                <path d="M4.5 21.5H27.5L25.5 25.5H6.5L4.5 21.5Z" fill="#B84320" fill-opacity="0.2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
            @break

        @case('ikan')
            <svg viewBox="0 0 32 32" fill="none" class="{{ $svgSize }}">
                <path d="M4 16C7.5 10.5 13.5 8.5 20.5 10.5C23.5 11.4 26 13.5 28 16C26 18.5 23.5 20.6 20.5 21.5C13.5 23.5 7.5 21.5 4 16Z" fill="#1B663A" fill-opacity="0.18" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M4 16L2 11.5L6.5 13.5M4 16L2 20.5L6.5 18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M19.5 11.2C17.8 13.8 17.8 18.2 19.5 20.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                <circle cx="22.5" cy="14.8" r="1.3" fill="currentColor"/>
                <path d="M11 14.5C12.2 15.5 12.2 16.5 11 17.5M14.5 14C15.7 15.3 15.7 16.7 14.5 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            @break

        @case('beras')
            <svg viewBox="0 0 32 32" fill="none" class="{{ $svgSize }}">
                <path d="M16 27V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M16 11C12.5 11 10 8.8 10 6C13.5 6 16 8.2 16 11Z" fill="#F0B400" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M16 11C19.5 11 22 8.8 22 6C18.5 6 16 8.2 16 11Z" fill="#F0B400" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M16 16.5C12 16.5 9.5 14.2 9.5 11.2C13.2 11.2 16 13.5 16 16.5Z" fill="#F0B400" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M16 16.5C20 16.5 22.5 14.2 22.5 11.2C18.8 11.2 16 13.5 16 16.5Z" fill="#F0B400" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M16 22C12.5 22 10 19.8 10 17C13.5 17 16 19.2 16 22Z" fill="#1B663A" fill-opacity="0.25" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M16 22C19.5 22 22 19.8 22 17C18.5 17 16 19.2 16 22Z" fill="#1B663A" fill-opacity="0.25" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            </svg>
            @break

        @case('kopi')
            <svg viewBox="0 0 32 32" fill="none" class="{{ $svgSize }}">
                <ellipse cx="13" cy="16.5" rx="6.5" ry="8.5" transform="rotate(-18 13 16.5)" fill="#B84320" fill-opacity="0.22" stroke="currentColor" stroke-width="1.8"/>
                <path d="M10.5 9.2C12.5 13 11.2 18.2 14.8 23.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                <ellipse cx="20.5" cy="15.5" rx="5.5" ry="7.5" transform="rotate(16 20.5 15.5)" fill="#FEF0C2" stroke="currentColor" stroke-width="1.8"/>
                <path d="M22 9C19.8 12.8 21.2 17.2 18.8 22" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            @break

        @case('nota')
            <svg viewBox="0 0 32 32" fill="none" class="{{ $svgSize }}">
                <path d="M7 5L9.5 6.8L12 5L14.5 6.8L17 5L19.5 6.8L22 5L25 6.8V27L22 25.2L19.5 27L17 25.2L14.5 27L12 25.2L9.5 27L7 25.2V5Z" fill="#FFFFFF" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M11 11.5H21M11 16H18M11 20.5H15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <circle cx="20" cy="20" r="2.2" fill="#F0B400"/>
            </svg>
            @break

        @default
            <svg viewBox="0 0 32 32" fill="none" class="{{ $svgSize }}">
                <path d="M7 24C6.5 14.5 13 7 25 6C24.5 17.5 18 24.5 7 24Z" fill="#1B663A" fill-opacity="0.22" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M7 24C12.5 18.5 18 13 25 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M13 18H19.5M16.5 14.5H22" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
    @endswitch
</span>
