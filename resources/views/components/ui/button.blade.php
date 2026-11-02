@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $baseClass = 'inline-flex items-center justify-center gap-2 rounded font-semibold transition-colors focus-visible:outline-3 focus-visible:outline-offset-2 focus-visible:outline-taum disabled:pointer-events-none disabled:opacity-50 cursor-pointer';

    $variantClass = match ($variant) {
        'daun' => 'bg-daun text-kertas hover:bg-daun-tua',
        'taum' => 'bg-taum text-kertas hover:bg-tinta',
        'kunyit', 'accent' => 'border-2 border-tinta bg-kunyit text-tinta hover:bg-kunyit-muda',
        'secondary', 'outline', 'garis' => 'border-2 border-tinta bg-transparent text-tinta hover:bg-tinta hover:text-kertas',
        'subtle', 'halus' => 'border-2 border-tinta bg-kertas text-tinta hover:bg-santan',
        'terakota', 'danger' => 'bg-terakota text-kertas hover:bg-tinta',
        'ghost' => 'bg-transparent text-abu hover:bg-santan hover:text-tinta',
        default => 'bg-tinta text-kertas hover:bg-taum',
    };

    $sizeClass = match ($size) {
        'sm' => 'px-3.5 py-2 text-xs',
        'lg' => 'px-7 py-4 text-base',
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
