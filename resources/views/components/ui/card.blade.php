@props([
    'variant' => 'default',
    'padding' => 'md',
    'as' => 'div',
])

@php
    $variantClass = match ($variant) {
        'santan' => 'rounded border-2 border-tinta bg-santan text-tinta',
        'kunyit', 'sorot' => 'rounded border-2 border-tinta bg-kunyit-muda text-tinta',
        'terakota' => 'rounded border-2 border-tinta bg-terakota-muda text-tinta',
        'daun' => 'rounded border-2 border-tinta bg-daun text-kertas',
        'taum', 'papan' => 'rounded border-2 border-tinta bg-taum text-kertas',
        'nota' => 'nota-pasar rounded text-tinta',
        default => 'rounded border-2 border-tinta bg-kertas text-tinta',
    };

    $paddingClass = match ($padding) {
        'none' => '',
        'sm' => 'p-4',
        'lg' => 'p-6 sm:p-8',
        default => 'p-5 sm:p-6',
    };

    $tag = in_array($as, ['div', 'article', 'section', 'aside', 'li'], true) ? $as : 'div';
@endphp

<{{ $tag }} {{ $attributes->class([$variantClass, $paddingClass]) }}>
    {{ $slot }}
</{{ $tag }}>
