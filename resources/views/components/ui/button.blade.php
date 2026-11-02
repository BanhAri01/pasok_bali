@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $baseClass = 'inline-flex items-center justify-center gap-2 rounded-xl font-bold transition duration-150 focus-visible:outline-3 focus-visible:outline-offset-2 focus-visible:outline-kunyit disabled:pointer-events-none disabled:opacity-50 cursor-pointer';

    $variantClass = match ($variant) {
        'kunyit', 'accent' => 'bg-kunyit text-tinta hover:bg-[#e0a800] shadow-xs',
        'secondary', 'outline', 'garis' => 'border-2 border-daun bg-transparent text-daun hover:bg-daun-muda',
        'subtle', 'halus' => 'border border-garis bg-kertas text-tinta hover:bg-santan',
        'terakota', 'danger' => 'bg-terakota text-kertas hover:opacity-95',
        'ghost' => 'bg-transparent text-abu hover:bg-santan hover:text-tinta',
        default => 'bg-daun text-kertas hover:bg-daun-tua shadow-xs',
    };

    $sizeClass = match ($size) {
        'sm' => 'px-3.5 py-2 text-xs',
        'lg' => 'px-6 py-3.5 text-base',
        default => 'px-5 py-3 text-sm',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$baseClass, $variantClass, $sizeClass]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$baseClass, $variantClass, $sizeClass]) }}>
        {{ $slot }}
    </button>
@endif
