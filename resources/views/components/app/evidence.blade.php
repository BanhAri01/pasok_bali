@props(['origin', 'evidence'])

@php
    [$label, $class] = match (true) {
        $origin === 'bali' && $evidence === 'terverifikasi' => ['Lokal Bali · terverifikasi', 'bg-daun text-kertas'],
        $origin === 'bali' => ['Lokal Bali · pernyataan pemasok', 'bg-kunyit-muda text-tinta ring-1 ring-kunyit'],
        $origin === 'luar' => ['Luar Bali', 'bg-terakota-muda text-terakota'],
        $origin === 'campuran' => ['Campuran (tidak dihitung lokal)', 'bg-terakota-muda text-terakota'],
        default => ['Asal belum diketahui', 'bg-santan text-abu ring-1 ring-garis'],
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold', $class]) }}>{{ $label }}</span>
