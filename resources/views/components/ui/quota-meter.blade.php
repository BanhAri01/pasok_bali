@props([
    'group' => null,
    'label' => null,
    'commodity' => null,
    'percent' => null,
    'verifiedPercent' => null,
    'quota' => 30,
    'status' => null,
    'totalKg' => null,
    'verifiedKg' => null,
    'declaredKg' => null,
    'outsideKg' => null,
    'unknownKg' => null,
    'gapKg' => null,
    'unmeasured' => 0,
    'compact' => false,
])

@php
    $data = is_array($group) ? $group : [];
    $title = $label ?? ($data['label'] ?? 'Kelompok komoditas');
    $pct = $percent ?? ($data['percent'] ?? null);
    $numericPct = (float) ($pct ?? 0);
    $verifiedPct = (float) ($verifiedPercent ?? ($data['verified_percent'] ?? $numericPct));
    $declaredPct = max(0.0, $numericPct - $verifiedPct);
    $targetQuota = (float) ($quota ?? ($data['quota'] ?? 30));

    $resolvedStatus = $status ?? ($data['status'] ?? ($pct === null ? 'kosong' : ($numericPct >= $targetQuota ? 'patuh' : 'kurang')));

    [$pctTextColor, $badgeClass, $statusText] = match ($resolvedStatus) {
        'patuh' => ['text-daun-tua', 'border-2 border-tinta bg-daun text-kertas', 'Memenuhi kuota'],
        'kurang' => ['text-terakota', 'border-2 border-tinta bg-terakota-muda text-terakota', 'Belum memenuhi'],
        default => ['text-abu', 'border-2 border-tinta bg-santan text-abu', 'Belum ada data'],
    };

    $iconKey = $commodity ?? ($data['key'] ?? match (true) {
        str_contains(strtolower($title), 'ternak') => 'telur',
        str_contains(strtolower($title), 'ikan') => 'ikan',
        str_contains(strtolower($title), 'industri') => 'kopi',
        default => 'sayur',
    });

    $fmt = fn ($val) => number_format((float) $val, fmod((float) $val, 1.0) === 0.0 ? 0 : 1, ',', '.');

    $totalWeight = $totalKg ?? ($data['total_kg'] ?? null);
    $verifiedWeight = $verifiedKg ?? ($data['verified_kg'] ?? null);
    $declaredWeight = $declaredKg ?? ($data['declared_kg'] ?? null);
    $outsideWeight = $outsideKg ?? ($data['outside_kg'] ?? null);
    $unknownWeight = $unknownKg ?? ($data['unknown_kg'] ?? null);
    $gapWeight = $gapKg ?? ($data['gap_kg'] ?? null);
    $unmeasuredCount = $unmeasured ?: ($data['unmeasured'] ?? 0);
@endphp

<article {{ $attributes->class(['rounded border-2 border-tinta bg-kertas p-5']) }}>
    <div class="flex flex-wrap items-start justify-between gap-2">
        <div class="flex items-center gap-3">
            <x-ui.commodity-icon :name="$iconKey" size="sm" />
            <h3 class="font-display text-lg leading-snug text-tinta">{{ $title }}</h3>
        </div>
        <span class="shrink-0 rounded px-2.5 py-0.5 text-xs font-semibold {{ $badgeClass }}">
            {{ $statusText }}
        </span>
    </div>

    <p class="mt-3 font-display text-4xl sm:text-5xl leading-none angka-tabel {{ $pctTextColor }}">
        {{ $pct === null ? '–' : $fmt($numericPct).'%' }}
        <span class="font-sans text-sm sm:text-base font-semibold text-abu">lokal dari kuota {{ $fmt($targetQuota) }}%</span>
    </p>

    <div class="mt-3.5">
        <progress
            class="ui-progress sr-only"
            max="100"
            value="{{ min(100, max(0, $numericPct)) }}"
            aria-label="{{ $title }}: {{ $fmt($numericPct) }} persen produk lokal dari target kuota {{ $fmt($targetQuota) }} persen"
        ></progress>

        <div
            class="relative h-3.5 overflow-hidden border-2 border-tinta bg-santan"
            role="img"
            aria-label="{{ $fmt($numericPct) }} persen lokal ({{ $fmt($verifiedPct) }} persen terverifikasi, {{ $fmt($declaredPct) }} persen pernyataan pemasok), batas kuota {{ $fmt($targetQuota) }} persen"
        >
            <div class="absolute inset-y-0 left-0 bg-daun transition-all duration-300" style="width: {{ min(100, max(0, $verifiedPct)) }}%"></div>
            <div class="absolute inset-y-0 bg-kunyit transition-all duration-300" style="left: {{ min(100, max(0, $verifiedPct)) }}%; width: {{ min(100 - min(100, max(0, $verifiedPct)), max(0, $declaredPct)) }}%"></div>
            <div class="absolute inset-y-0 w-0.5 bg-tinta" style="left: {{ min(100, max(0, $targetQuota)) }}%" title="Batas kuota {{ $fmt($targetQuota) }}%"></div>
        </div>

        <div class="mt-1.5 flex items-center justify-between text-xs text-abu">
            <span class="inline-flex items-center gap-3">
                <span class="inline-flex items-center gap-1"><span class="size-2 border border-tinta bg-daun"></span>Terverifikasi</span>
                <span class="inline-flex items-center gap-1"><span class="size-2 border border-tinta bg-kunyit"></span>Pernyataan</span>
            </span>
            <span class="font-semibold text-tinta">Batas {{ $fmt($targetQuota) }}%</span>
        </div>
    </div>

    @if (! $compact && $totalWeight !== null)
        <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-1 border-t-2 border-tinta pt-3 text-sm">
            <dt class="text-abu">Total tercatat</dt>
            <dd class="text-right font-semibold angka-tabel">{{ $fmt($totalWeight) }} kg</dd>
            @if ($verifiedWeight !== null)
                <dt class="text-abu"><span class="mr-1.5 inline-block size-2 border border-tinta bg-daun"></span>Lokal terverifikasi</dt>
                <dd class="text-right font-semibold angka-tabel">{{ $fmt($verifiedWeight) }} kg</dd>
            @endif
            @if ($declaredWeight !== null)
                <dt class="text-abu"><span class="mr-1.5 inline-block size-2 border border-tinta bg-kunyit"></span>Lokal pernyataan</dt>
                <dd class="text-right font-semibold angka-tabel">{{ $fmt($declaredWeight) }} kg</dd>
            @endif
            @if ($outsideWeight !== null)
                <dt class="text-abu">Luar Bali / campuran</dt>
                <dd class="text-right font-semibold angka-tabel">{{ $fmt($outsideWeight) }} kg</dd>
            @endif
            @if ($unknownWeight !== null)
                <dt class="text-abu">Asal belum diketahui</dt>
                <dd class="text-right font-semibold angka-tabel">{{ $fmt($unknownWeight) }} kg</dd>
            @endif
        </dl>
    @endif

    @if ($resolvedStatus === 'kurang' && $gapWeight !== null && $gapWeight > 0)
        <p class="mt-3 rounded border-2 border-tinta bg-terakota-muda px-3 py-2 text-sm text-terakota">
            Perlu tambahan sekitar <strong>{{ $fmt($gapWeight) }} kg</strong> produk lokal Bali bulan ini.
        </p>
    @endif

    @if ($unmeasuredCount > 0)
        <p class="mt-2 text-xs text-abu">{{ $unmeasuredCount }} baris belum bisa dihitung beratnya.</p>
    @endif
</article>
