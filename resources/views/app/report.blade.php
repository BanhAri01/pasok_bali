<x-app.layout title="Laporan">
    <div class="flex flex-wrap items-end justify-between gap-4 print:hidden">
        <h1 class="font-display text-3xl font-black text-daun-tua">Laporan bulanan</h1>
        <div class="flex flex-wrap items-center gap-3">
            <x-app.month-picker :month="$month" route="report" />
            <button type="button" onclick="window.print()" class="rounded-xl bg-daun px-5 py-2.5 font-bold text-kertas hover:bg-daun-tua">Cetak / simpan PDF</button>
        </div>
    </div>

    <article class="mt-6 rounded-2xl border border-garis bg-kertas p-6 print:border-0 print:p-0">
        <header class="border-b border-garis pb-4">
            <p class="text-sm font-semibold text-daun">Laporan mandiri kepatuhan produk lokal · Pergub Bali No. 99 Tahun 2018</p>
            <h2 class="font-display text-2xl font-black">{{ $business->name }}</h2>
            <p class="text-abu">{{ $business->typeLabel() }} · {{ $business->regency }} · Periode {{ $month->translatedFormat('F Y') }}</p>
            @if ($business->is_demo)
                <p class="mt-2 inline-block rounded-full bg-kunyit-muda px-3 py-1 text-xs font-bold">DATA CONTOH FIKTIF</p>
            @endif
        </header>

        @if ($summary)
            <section class="mt-5">
                <h3 class="font-display text-lg font-bold">Ringkasan</h3>
                <p class="mt-1 leading-relaxed">{{ $summary['text'] }}</p>
                <p class="mt-1 text-xs text-abu">{{ $summary['ai'] ? 'Ringkasan ditulis AI dari angka di bawah.' : 'Ringkasan otomatis dari angka di bawah.' }}</p>
            </section>
        @endif

        <section class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="text-left text-abu">
                    <tr class="border-b border-garis">
                        <th class="py-2 pr-3">Kelompok</th>
                        <th class="py-2 pr-3 text-right">Total (kg)</th>
                        <th class="py-2 pr-3 text-right">Lokal (kg)</th>
                        <th class="py-2 pr-3 text-right">Terverifikasi</th>
                        <th class="py-2 pr-3 text-right">% lokal</th>
                        <th class="py-2 pr-3 text-right">Kuota</th>
                        <th class="py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-garis">
                    @foreach ($report['groups'] as $group)
                        <tr>
                            <td class="py-2 pr-3 font-semibold">{{ $group['label'] }}</td>
                            <td class="py-2 pr-3 text-right">{{ \App\Support\Rupiah::number($group['total_kg']) }}</td>
                            <td class="py-2 pr-3 text-right">{{ \App\Support\Rupiah::number($group['local_kg']) }}</td>
                            <td class="py-2 pr-3 text-right">{{ \App\Support\Rupiah::number($group['verified_kg']) }}</td>
                            <td class="py-2 pr-3 text-right font-bold">{{ $group['percent'] === null ? '–' : \App\Support\Rupiah::number($group['percent']).'%' }}</td>
                            <td class="py-2 pr-3 text-right">{{ $group['quota'] }}%</td>
                            <td class="py-2">{{ ['patuh' => 'Memenuhi', 'kurang' => 'Belum memenuhi', 'kosong' => 'Belum ada data'][$group['status']] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="mt-8">
            <h3 class="font-display text-lg font-bold">Rincian per pemasok</h3>
            <div class="mt-2 overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead class="text-left text-abu">
                        <tr class="border-b border-garis"><th class="py-2 pr-3">Pemasok</th><th class="py-2 pr-3">Lokasi</th><th class="py-2 pr-3">Asal &amp; bukti</th><th class="py-2 pr-3 text-right">Berat (kg)</th><th class="py-2 text-right">Nilai</th></tr>
                    </thead>
                    <tbody class="divide-y divide-garis">
                        @forelse ($suppliers as $row)
                            <tr>
                                <td class="py-2 pr-3 font-semibold">{{ $row->supplier_name }}</td>
                                <td class="py-2 pr-3">{{ $row->regency ?? '–' }}</td>
                                <td class="py-2 pr-3"><x-app.evidence :origin="$row->origin" :evidence="$row->verification" /></td>
                                <td class="py-2 pr-3 text-right">{{ \App\Support\Rupiah::number($row->kg) }}</td>
                                <td class="py-2 text-right">{{ \App\Support\Rupiah::format($row->rupiah) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-abu">Belum ada nota disetujui pada bulan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="mt-8 border-t border-garis pt-4 text-xs text-abu">
            Dihitung dari {{ $report['approved_receipts'] }} nota yang disetujui pengguna. Persentase dihitung berdasarkan berat (kg) sesuai ketentuan volume. “Terverifikasi” berarti lokasi produksi pemasok sudah diperiksa admin Pasok Bali; “pernyataan” berarti berdasarkan keterangan pemasok. Laporan mandiri ini bukan dokumen resmi pemerintah. Dicetak {{ now()->translatedFormat('j F Y H:i') }}.
        </footer>
    </article>
</x-app.layout>
