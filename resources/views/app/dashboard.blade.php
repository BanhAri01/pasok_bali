<x-app.layout title="Dasbor">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-daun">Kepatuhan Pergub Bali 99/2018</p>
            <h1 class="font-display text-3xl font-black text-daun-tua">{{ $month->translatedFormat('F Y') }}</h1>
            <p class="mt-1 text-abu">{{ $report['compliant_groups'] }} dari {{ $report['measured_groups'] }} kelompok tercatat memenuhi kuota · {{ $report['approved_receipts'] }} nota disetujui @if ($report['pending_receipts'])· <a href="{{ route('receipts.index', ['status' => 'perlu_cek']) }}" class="font-semibold text-terakota underline">{{ $report['pending_receipts'] }} nota perlu dicek</a>@endif</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-app.month-picker :month="$month" route="dashboard" />
            <a href="{{ route('receipts.create') }}" class="rounded bg-tinta px-5 py-3 font-bold text-kertas hover:bg-taum">+ Foto nota</a>
        </div>
    </div>

    <section class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach ($report['groups'] as $group)
            <x-app.meter :group="$group" />
        @endforeach
    </section>

    @if ($recommendations)
        <section class="mt-8">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-display text-xl font-extrabold">Langkah paling berdampak</h2>
                <a href="{{ route('recommendations.index', ['bulan' => $month->format('Y-m')]) }}" class="text-sm font-semibold text-daun">Semua rekomendasi →</a>
            </div>
            <div class="mt-3 grid gap-4 md:grid-cols-2">
                @foreach ($recommendations as $block)
                    @foreach ($block['items'] as $item)
                        @php($best = $item['candidates'][0] ?? null)
                        <article class="border-2 border-kunyit bg-kunyit-muda p-5">
                            <p class="text-sm font-semibold text-abu">{{ $block['group']['label'] }}</p>
                            <p class="mt-1 font-display text-lg font-bold">{{ $item['product']->name }}: {{ \App\Support\Rupiah::number($item['non_local_kg']) }} kg bulan ini belum dari pemasok lokal</p>
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

    <section class="mt-8 grid gap-6 lg:grid-cols-2">
        <div>
            <h2 class="font-display text-xl font-extrabold">Nota terbaru</h2>
            <ul class="mt-3 divide-y divide-garis border-2 border-tinta bg-kertas">
                @forelse ($recentReceipts as $receipt)
                    <li>
                        <a href="{{ route('receipts.show', $receipt) }}" class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-santan">
                            <span class="min-w-0">
                                <span class="block truncate font-semibold">{{ $receipt->supplier?->name ?? $receipt->supplier_name_raw ?? 'Nota tanpa nama pemasok' }}</span>
                                <span class="text-xs text-abu">{{ $receipt->receipt_date?->translatedFormat('j M Y') }} · {{ \App\Support\Rupiah::format($receipt->total) }}</span>
                            </span>
                            <span @class(['shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-kunyit text-tinta' => $receipt->status === 'disetujui', 'bg-kunyit-muda text-tinta' => $receipt->status !== 'disetujui'])>{{ $receipt->statusLabel() }}</span>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-abu">Belum ada nota. <a href="{{ route('receipts.create') }}" class="font-semibold text-daun">Foto nota pertama</a>.</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h2 class="font-display text-xl font-extrabold">Permintaan ke pemasok</h2>
            <ul class="mt-3 divide-y divide-garis border-2 border-tinta bg-kertas">
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
