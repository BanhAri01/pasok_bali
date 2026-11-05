@props([
    'level' => null,
    'evidence' => null,
    'origin' => 'bali',
    'label' => null,
])

@php
    $rawLevel = $level ?? $evidence ?? 'belum_diketahui';

    [$defaultLabel, $toneClass, $swatchClass] = match (true) {
        $rawLevel === 'hijau' || ($origin === 'bali' && $rawLevel === 'terverifikasi') => [
            'Lokal Bali · terverifikasi',
            'border-2 border-tinta bg-daun text-kertas',
            'bg-kunyit border border-tinta',
        ],
        $rawLevel === 'kuning' || ($origin === 'bali' && in_array($rawLevel, ['pernyataan', 'pernyataan_pemasok'], true)) => [
            'Lokal Bali · pernyataan pemasok',
            'border-2 border-tinta bg-kunyit-muda text-tinta',
            'bg-kunyit border border-tinta',
        ],
        $origin === 'luar' || $rawLevel === 'luar' => [
            'Luar Bali',
            'border-2 border-tinta bg-terakota-muda text-terakota',
            'bg-terakota border border-tinta',
        ],
        $origin === 'campuran' || $rawLevel === 'campuran' => [
            'Campuran (tidak dihitung lokal)',
            'border-2 border-tinta bg-terakota-muda text-terakota',
            'bg-terakota border border-tinta',
        ],
        default => [
            'Asal belum diketahui',
            'border-2 border-tinta bg-santan text-tinta',
            'bg-garis border border-tinta',
        ],
    };

    $displayText = $label ?? ($slot->isNotEmpty() ? null : $defaultLabel);
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded px-2.5 py-0.5 text-xs font-semibold', $toneClass]) }}>
    <span class="size-2 shrink-0 {{ $swatchClass }}" aria-hidden="true"></span>
    @if ($displayText !== null)
        {{ $displayText }}
    @else
        {{ $slot }}
    @endif
</span>
