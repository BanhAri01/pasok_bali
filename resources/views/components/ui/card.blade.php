@props([
    'variant' => 'default',
    'padding' => 'md',
    'as' => 'div',
])

@php
    $variantClass = match ($variant) {
        'santan' => 'rounded-2xl border border-garis bg-santan text-tinta',
        'kunyit', 'sorot' => 'rounded-2xl border-2 border-kunyit bg-kunyit-muda text-tinta',
        'terakota' => 'rounded-2xl border border-terakota/30 bg-terakota-muda text-tinta',
        'daun', 'papan' => 'rounded-2xl border border-daun bg-daun-tua text-kertas',
        'nota' => 'nota-pasar rounded-2xl border border-garis text-tinta',
        default => 'rounded-2xl border border-garis bg-kertas text-tinta shadow-[var(--shadow-kartu)]',
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
