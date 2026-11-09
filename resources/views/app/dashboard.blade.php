<x-app.layout title="Dasbor">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-daun">Kepatuhan Pergub Bali 99/2018</p>
            <h1 class="font-display text-3xl font-semibold text-daun-tua">{{ $month->translatedFormat('F Y') }}</h1>
            <p class="mt-1 text-abu">{{ $report['compliant_groups'] }} dari {{ $report['measured_groups'] }} kelompok tercatat memenuhi kuota · {{ $report['approved_receipts'] }} nota disetujui @if ($report['pending_receipts'])· <a href="{{ route('receipts.index', ['status' => 'perlu_cek']) }}" class="font-semibold text-terakota underline">{{ $report['pending_receipts'] }} nota perlu dicek</a>@endif</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-app.month-picker :month="$month" route="dashboard" />
            <a href="{{ route('receipts.create') }}" class="rounded-full bg-daun-tua px-5 py-3 font-bold text-kertas hover:bg-taum">+ Foto nota</a>
        </div>
    </div>

    @php($impact = session('impact'))
    @if ($impact && $impact['month'] === $month->format('Y-m'))
        <section class="mt-6 rounded-2xl border border-daun/30 bg-kertas p-5" aria-label="Dampak nota yang baru disetujui">
            <h2 class="font-display text-xl font-semibold">Dampak nota ini ke {{ $month->translatedFormat('F Y') }}</h2>
            @if ($impact['groups'])
                <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ($impact['groups'] as $change)
                        <li class="rounded-lg bg-santan px-4 py-3 text-sm ring-1 ring-garis">
                            <span class="font-semibold">{{ $change['label'] }}</span>
                            <span class="mt-0.5 block">
                                {{ $change['before'] === null ? 'belum ada data' : \App\Support\Rupiah::number($change['before']).'%' }}
                                →
                                <strong class="{{ $change['after'] !== null && $change['after'] >= $change['quota'] ? 'text-daun' : 'text-terakota' }}">{{ $change['after'] === null ? '–' : \App\Support\Rupiah::number($change['after']).'%' }}</strong>
                                <span class="text-abu">(kuota {{ $change['quota'] }}%)</span>
                            </span>
                            <span class="block text-xs text-abu">+{{ \App\Support\Rupiah::number($change['added_kg']) }} kg tercatat, {{ \App\Support\Rupiah::number($change['added_local_kg']) }} kg di antaranya lokal Bali</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-2 text-sm text-abu">Nota ini tidak berisi barang yang dihitung kuota.</p>
            @endif
            @if ($impact['unknown'])
                <p class="mt-3 text-sm text-abu">{{ $impact['unknown'] }} barang asalnya belum diketahui karena {{ $impact['supplier'] ? $impact['supplier'].' belum mendaftarkan produk tersebut' : 'toko di nota belum terdaftar di Pasok Bali' }}. Barang ini dihitung sebagai belanja, tetapi belum sebagai produk lokal.</p>
            @endif
            @if ($month->format('Y-m') !== now()->format('Y-m'))
                <a href="{{ route('dashboard') }}" class="mt-3 inline-block text-sm font-semibold text-daun">Kembali ke {{ now()->translatedFormat('F Y') }} →</a>
            @endif
        </section>
    @endif

    <section class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach ($report['groups'] as $group)
            <x-app.meter :group="$group" />
        @endforeach
    </section>

    @if ($recommendations)
        <section class="mt-8">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-display text-xl font-semibold">Langkah paling berdampak</h2>
                <a href="{{ route('recommendations.index', ['bulan' => $month->format('Y-m')]) }}" class="text-sm font-semibold text-daun">Semua rekomendasi →</a>
            </div>
            <div class="mt-3 grid gap-4 md:grid-cols-2">
                @foreach ($recommendations as $block)
                    @foreach ($block['items'] as $item)
                        @php($best = $item['candidates'][0] ?? null)
                        <article class="rounded-2xl border border-garis bg-kertas p-6">
                            <p class="text-sm font-semibold text-abu">{{ $block['group']['label'] }}</p>
                            <p class="mt-1 font-display text-2xl leading-tight font-semibold">{{ $item['product']->name }}: {{ \App\Support\Rupiah::number($item['non_local_kg']) }} kg bulan ini belum dari pemasok lokal</p>
                            @if ($best)
                                <p class="mt-2 text-sm">{{ $best['alternative'] ? 'Ganti dengan '.$best['offer']->product->name.' dari' : 'Beralih ke' }} <strong>{{ $best['offer']->supplier->name }}</strong> ({{ $best['offer']->supplier->regency }}) untuk ±{{ \App\Support\Rupiah::number($best['switch_kg']) }} kg/bulan menaikkan kelompok ini ke <strong>{{ \App\Support\Rupiah::number($best['new_percent']) }}%</strong>{{ $best['reaches_quota'] ? ' dan memenuhi kuota.' : '.' }}</p>
                            @else
                                <p class="mt-2 text-sm text-abu">Belum ada pemasok lokal terdaftar untuk produk ini.</p>
                            @endif
                        </article>
                    @endforeach
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-8 grid gap-6 lg:grid-cols-2 [&>*]:min-w-0">
        <div>
            <h2 class="font-display text-xl font-semibold">Nota terbaru</h2>
            <ul class="mt-3 divide-y divide-garis border border-garis bg-kertas rounded-2xl">
                @forelse ($recentReceipts as $receipt)
                    <li>
                        <a href="{{ route('receipts.show', $receipt) }}" class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-santan">
                            <span class="min-w-0">
                                <span class="block truncate font-semibold">{{ $receipt->supplier?->name ?? $receipt->supplier_name_raw ?? 'Nota tanpa nama pemasok' }}</span>
                                <span class="text-xs text-abu">{{ $receipt->receipt_date?->translatedFormat('j M Y') }} · {{ \App\Support\Rupiah::format($receipt->total) }}</span>
                            </span>
                            <span @class(['shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-daun-muda text-daun-tua' => $receipt->status === 'disetujui', 'bg-kunyit-muda text-tinta' => $receipt->status !== 'disetujui'])>{{ $receipt->statusLabel() }}</span>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-abu">Belum ada nota. <a href="{{ route('receipts.create') }}" class="font-semibold text-daun">Foto nota pertama</a>.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h2 class="font-display text-xl font-semibold">Permintaan ke pemasok</h2>
            <ul class="mt-3 divide-y divide-garis border border-garis bg-kertas rounded-2xl">
                @forelse ($requests as $request)
                    <li class="flex items-center justify-between gap-3 px-4 py-3">
                        <span class="min-w-0">
                            <span class="block truncate font-semibold">{{ $request->product->name }} · {{ $request->supplier->name }}</span>
                            <span class="text-xs text-abu">{{ \App\Support\Rupiah::number($request->quantity_per_week) }} {{ $request->unit }}/minggu · {{ $request->created_at->diffForHumans() }}</span>
                        </span>
                        <span @class(['shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-daun text-kertas' => $request->status === 'diterima', 'bg-terakota-muda text-terakota' => $request->status === 'ditolak', 'bg-kunyit-muda text-tinta' => $request->status === 'menunggu'])>{{ ucfirst($request->status) }}</span>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-abu">Belum ada permintaan. Buka <a href="{{ route('recommendations.index') }}" class="font-semibold text-daun">rekomendasi</a> untuk mulai.</li>
                @endforelse
            </ul>
        </div>
    </section>
</x-app.layout>
