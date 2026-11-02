@props([
    'level' => null,
    'evidence' => null,
    'origin' => 'bali',
    'label' => null,
])

@php
    $rawLevel = $level ?? $evidence ?? 'belum_diketahui';

    [$defaultLabel, $toneClass, $dotClass] = match (true) {
        $rawLevel === 'hijau' || ($origin === 'bali' && $rawLevel === 'terverifikasi') => [
            'Lokal Bali · terverifikasi',
            'bg-daun text-kertas',
            'bg-kunyit',
        ],
        $rawLevel === 'kuning' || ($origin === 'bali' && in_array($rawLevel, ['pernyataan', 'pernyataan_pemasok'], true)) => [
            'Lokal Bali · pernyataan pemasok',
            'bg-kunyit-muda text-tinta ring-1 ring-kunyit',
            'bg-daun',
        ],
        $origin === 'luar' || $rawLevel === 'luar' => [
            'Luar Bali',
            'bg-terakota-muda text-terakota ring-1 ring-terakota/30',
            'bg-terakota',
        ],
        $origin === 'campuran' || $rawLevel === 'campuran' => [
            'Campuran (tidak dihitung lokal)',
            'bg-terakota-muda text-terakota ring-1 ring-terakota/30',
            'bg-terakota',
        ],
        default => [
            'Asal belum diketahui',
            'bg-santan text-abu ring-1 ring-garis',
            'bg-abu',
        ],
    };

    $displayText = $label ?? ($slot->isNotEmpty() ? null : $defaultLabel);
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold', $toneClass]) }}>
    <span class="size-1.5 shrink-0 rounded-full {{ $dotClass }}" aria-hidden="true"></span>
    @if ($displayText !== null)
        {{ $displayText }}
    @else
        {{ $slot }}
    @endif
</span>
