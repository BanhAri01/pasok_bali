<x-app.layout title="Rekomendasi">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-daun-tua">Rekomendasi pemasok lokal</h1>
            <p class="mt-1 text-abu">Disusun dari produk yang paling banyak dibeli dari luar Bali atau asal belum diketahui, untuk kelompok yang belum memenuhi kuota.</p>
        </div>
        <x-app.month-picker :month="$month" route="recommendations.index" />
    </div>

    @forelse ($recommendations as $block)
        <section class="mt-8">
            <h2 class="font-display text-xl font-extrabold">{{ $block['group']['label'] }}
                <span class="ml-2 text-base font-semibold text-terakota">{{ \App\Support\Rupiah::number($block['group']['percent']) }}% dari kuota {{ $block['group']['quota'] }}%</span>
            </h2>

            <div class="mt-3 space-y-4">
                @foreach ($block['items'] as $item)
                    <article class="border-2 border-tinta bg-kertas p-5">
                        <p class="font-display text-lg font-bold">{{ $item['product']->name }}</p>
                        <p class="text-sm text-abu">{{ \App\Support\Rupiah::number($item['non_local_kg']) }} kg ({{ \App\Support\Rupiah::format($item['non_local_rupiah']) }}) bulan ini belum dari pemasok lokal terdaftar.</p>

                        @if ($item['candidates'])
                            @if ($item['candidates'][0]['alternative'])
                                <p class="mt-2 text-sm">Belum ada pemasok lokal untuk produk yang sama. Pertimbangkan pengganti produksi Bali dari kelompok yang sama:</p>
                            @endif
                            <div class="mt-4 grid gap-3 md:grid-cols-3">
                                @foreach ($item['candidates'] as $candidate)
                                    @php($offer = $candidate['offer'])
                                    <div class="flex flex-col rounded border-2 border-tinta p-4" x-data="permintaan({{ $offer->id }}, {{ $candidate['weekly_quantity'] ?: 1 }}, 'kg')">
                                        @if ($candidate['alternative'])
                                            <p class="mb-1 self-start rounded-full bg-kunyit-muda px-2 py-0.5 text-xs font-bold ring-1 ring-kunyit">Alternatif lokal: {{ $offer->product->name }}</p>
                                        @endif
                                        <p class="font-semibold">{{ $offer->supplier->name }}</p>
                                        <p class="text-xs text-abu">{{ $offer->supplier->regency }} · {{ $offer->supplier->verification === 'terverifikasi' ? 'Terverifikasi' : 'Pernyataan pemasok' }}</p>
                                        <ul class="mt-2 space-y-0.5 text-sm">
                                            @if ($offer->capacity_per_week)
                                                <li>Kapasitas {{ \App\Support\Rupiah::number($offer->capacity_per_week) }} {{ $offer->capacity_unit }}/minggu</li>
                                            @endif
                                            @if ($offer->price_per_unit)
                                                <li>Harga acuan {{ \App\Support\Rupiah::format($offer->price_per_unit) }}/{{ $offer->price_unit }}</li>
                                            @endif
                                            <li class="font-semibold text-daun-tua">Naik ke {{ \App\Support\Rupiah::number($candidate['new_percent']) }}%{{ $candidate['reaches_quota'] ? ' · memenuhi kuota' : '' }}</li>
                                        </ul>
                                        <button type="button" @click="open = true; if (!message) draft()" class="mt-auto pt-3 text-left text-sm font-bold text-daun">Minta pasokan ±{{ \App\Support\Rupiah::number($candidate['weekly_quantity']) }} kg/minggu →</button>

                                        <div x-show="open" x-cloak class="mt-3 border-t border-garis pt-3">
                                            <form method="POST" action="{{ route('recommendations.send') }}" class="space-y-2">
                                                @csrf
                                                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                                <div class="flex gap-2">
                                                    <input type="number" name="quantity" step="0.1" min="0.1" x-model="quantity" required class="w-24 rounded border-2 border-tinta px-2 py-1.5 text-sm" aria-label="Jumlah per minggu">
                                                    <input name="unit" x-model="unit" required maxlength="20" class="w-20 rounded border-2 border-tinta px-2 py-1.5 text-sm" aria-label="Satuan">
                                                    <span class="self-center text-xs text-abu">per minggu</span>
                                                </div>
                                                <textarea name="message" x-model="message" rows="6" required minlength="20" maxlength="700" class="w-full rounded border-2 border-tinta px-2 py-1.5 text-sm" :placeholder="loading ? 'AI sedang menyusun pesan…' : 'Tulis pesan untuk pemasok'"></textarea>
                                                <p class="text-xs text-abu" x-show="aiUsed">Draf disusun AI, silakan ubah sebelum mengirim.</p>
                                                <div class="flex flex-wrap gap-2">
                                                    <button type="button" @click="draft()" :disabled="loading" class="rounded border-2 border-tinta px-3 py-1.5 text-xs font-semibold">Susun ulang</button>
                                                    <button class="rounded bg-tinta px-3 py-1.5 text-xs font-bold text-kertas">Kirim lewat WhatsApp</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-3 rounded bg-santan px-4 py-3 text-sm text-abu">Belum ada pemasok lokal terdaftar untuk produk ini. Ajak pemasok Anda mendaftar lewat WhatsApp Pasok Bali.</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <div class="mt-8 border-2 border-tinta bg-kertas p-8 text-center">
            <p class="font-display text-xl font-bold">Tidak ada rekomendasi untuk bulan ini</p>
            <p class="mt-1 text-abu">Semua kelompok yang tercatat sudah memenuhi kuota, atau belum ada nota yang disetujui.</p>
        </div>
    @endforelse

    <section class="mt-10">
        <h2 class="font-display text-xl font-extrabold">Riwayat permintaan</h2>
        <div class="mt-3 overflow-x-auto border-2 border-tinta bg-kertas">
            <table class="w-full min-w-[560px] text-sm">
                <thead class="bg-santan text-left text-abu">
                    <tr><th class="px-4 py-3">Produk</th><th class="px-4 py-3">Pemasok</th><th class="px-4 py-3">Jumlah/minggu</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Dikirim</th></tr>
                </thead>
                <tbody class="divide-y divide-garis">
                    @forelse ($requests as $request)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $request->product->name }}</td>
                            <td class="px-4 py-3">{{ $request->supplier->name }}</td>
                            <td class="px-4 py-3">{{ \App\Support\Rupiah::number($request->quantity_per_week) }} {{ $request->unit }}</td>
                            <td class="px-4 py-3"><span @class(['rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-daun text-kertas' => $request->status === 'diterima', 'bg-terakota-muda text-terakota' => $request->status === 'ditolak', 'bg-kunyit-muda' => $request->status === 'menunggu'])>{{ ucfirst($request->status) }}</span></td>
                            <td class="px-4 py-3 text-abu">{{ $request->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-abu">Belum ada permintaan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <script>
        function permintaan(offerId, quantity, unit) {
            return {
                open: false, loading: false, aiUsed: false, message: '', quantity, unit,
                async draft() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('recommendations.draft') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: JSON.stringify({ offer_id: offerId, quantity: this.quantity, unit: this.unit }),
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.message = data.message;
                            this.aiUsed = data.ai;
                        }
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-app.layout>
