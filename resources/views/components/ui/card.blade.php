@props([
    'variant' => 'default',
    'padding' => 'md',
    'as' => 'div',
])

@php
    $variantClass = match ($variant) {
        'santan' => 'rounded-lg border border-garis bg-santan text-tinta',
        'kunyit', 'sorot' => 'rounded-lg border border-garis bg-kunyit-muda text-tinta',
        'terakota' => 'rounded-lg border border-garis bg-terakota-muda text-tinta',
        'daun' => 'rounded-lg border border-garis bg-daun text-kertas',
        'taum', 'papan' => 'rounded-lg border border-garis bg-taum text-kertas',
        'nota' => 'nota-pasar rounded-lg text-tinta',
        default => 'rounded-lg border border-garis bg-kertas text-tinta',
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
