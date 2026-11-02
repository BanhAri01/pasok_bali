@props(['group'])

@php
    $percent = $group['percent'] ?? 0;
    $verified = $group['verified_percent'] ?? 0;
    $declared = max(0, $percent - $verified);
    $tone = match ($group['status']) {
        'patuh' => ['text-daun-tua', 'bg-daun-muda', 'Memenuhi kuota'],
        'kurang' => ['text-terakota', 'bg-terakota-muda', 'Belum memenuhi'],
        default => ['text-abu', 'bg-santan', 'Belum ada data'],
    };
@endphp

<article {{ $attributes->class(['border-2 border-tinta bg-kertas p-5']) }}>
    <div class="flex items-start justify-between gap-3">
        <h3 class="font-display text-base font-bold leading-snug">{{ $group['label'] }}</h3>
        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold {{ $tone[0] }} {{ $tone[1] }}">{{ $tone[2] }}</span>
    </div>

    <p class="mt-3 font-display text-4xl font-black {{ $tone[0] }}">
        {{ $group['percent'] === null ? '–' : \App\Support\Rupiah::number($group['percent']).'%' }}
        <span class="text-base font-semibold text-abu">lokal dari kuota {{ $group['quota'] }}%</span>
    </p>

    <div class="relative mt-4 h-4 overflow-hidden rounded-full bg-santan ring-1 ring-garis" role="img" aria-label="{{ \App\Support\Rupiah::number($percent) }} persen lokal, kuota {{ $group['quota'] }} persen">
        <div class="absolute inset-y-0 left-0 bg-daun" style="width: {{ min(100, $verified) }}%"></div>
        <div class="absolute inset-y-0 bg-kunyit" style="left: {{ min(100, $verified) }}%; width: {{ min(100 - min(100, $verified), $declared) }}%"></div>
        <div class="absolute inset-y-0 w-0.5 bg-tinta" style="left: {{ min(100, $group['quota']) }}%"></div>
    </div>

    <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
        <dt class="text-abu">Total tercatat</dt><dd class="text-right font-semibold">{{ \App\Support\Rupiah::number($group['total_kg']) }} kg</dd>
        <dt class="text-abu"><span class="mr-1 inline-block size-2 rounded-full bg-daun"></span>Lokal terverifikasi</dt><dd class="text-right font-semibold">{{ \App\Support\Rupiah::number($group['verified_kg']) }} kg</dd>
        <dt class="text-abu"><span class="mr-1 inline-block size-2 rounded-full bg-kunyit"></span>Lokal pernyataan</dt><dd class="text-right font-semibold">{{ \App\Support\Rupiah::number($group['declared_kg']) }} kg</dd>
        <dt class="text-abu">Luar Bali / campuran</dt><dd class="text-right font-semibold">{{ \App\Support\Rupiah::number($group['outside_kg']) }} kg</dd>
        <dt class="text-abu">Asal belum diketahui</dt><dd class="text-right font-semibold">{{ \App\Support\Rupiah::number($group['unknown_kg']) }} kg</dd>
    </dl>

    @if ($group['status'] === 'kurang')
        <p class="mt-3 rounded bg-terakota-muda px-3 py-2 text-sm text-terakota">Perlu tambahan sekitar <strong>{{ \App\Support\Rupiah::number($group['gap_kg']) }} kg</strong> produk lokal Bali bulan ini.</p>
    @endif

    @if ($group['unmeasured'] > 0)
        <p class="mt-2 text-xs text-abu">{{ $group['unmeasured'] }} baris belum bisa dihitung beratnya.</p>
    @endif
</article>
