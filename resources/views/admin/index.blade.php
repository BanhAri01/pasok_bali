<x-app.layout title="Admin">
    <h1 class="font-display text-3xl font-black text-daun-tua">Admin Pasok Bali</h1>

    <section class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach (['businesses' => 'Usaha terdaftar', 'demo_businesses' => 'Akun demo aktif', 'receipts' => 'Nota', 'suppliers' => 'Pemasok'] as $key => $label)
            <div class="rounded-2xl border border-garis bg-kertas p-4">
                <p class="text-sm text-abu">{{ $label }}</p>
                <p class="font-display text-3xl font-black">{{ number_format($stats[$key], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-2">
        <div>
            <h2 class="font-display text-xl font-extrabold">Panggilan AI 7 hari</h2>
            <table class="mt-3 w-full rounded-2xl border border-garis bg-kertas text-sm">
                <thead class="text-left text-abu"><tr><th class="px-3 py-2">Tujuan</th><th class="px-3 py-2">Status</th><th class="px-3 py-2 text-right">Jumlah</th><th class="px-3 py-2 text-right">Rata-rata</th></tr></thead>
                <tbody class="divide-y divide-garis">
                    @forelse ($aiCalls as $call)
                        <tr><td class="px-3 py-2">{{ $call->purpose }}</td><td class="px-3 py-2">{{ $call->status }}</td><td class="px-3 py-2 text-right">{{ $call->total }}</td><td class="px-3 py-2 text-right">{{ number_format($call->latency / 1000, 1, ',', '.') }} dtk</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center text-abu">Belum ada panggilan AI.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <h2 class="font-display text-xl font-extrabold">Kuota Pergub</h2>
            <form method="POST" action="{{ route('admin.quotas') }}" class="mt-3 space-y-3 rounded-2xl border border-garis bg-kertas p-4">
                @csrf @method('PUT')
                @foreach ($groups as $key => $group)
                    <label class="flex items-center justify-between gap-3 text-sm">
                        <span>{{ $group['label'] }}</span>
                        <span><input type="number" name="quota[{{ $key }}]" min="0" max="100" value="{{ $quotas[$key]->min_percent ?? $group['percent'] }}" class="w-20 rounded-lg border border-garis px-2 py-1 text-right"> %</span>
                    </label>
                @endforeach
                <label class="block text-sm">Sumber/dasar
                    <input name="source" required maxlength="255" value="{{ $quotas->first()->source ?? 'Pergub Bali No. 99 Tahun 2018' }}" class="mt-1 w-full rounded-lg border border-garis px-2 py-1.5">
                </label>
                <button class="rounded-lg bg-daun px-4 py-2 text-sm font-bold text-kertas">Simpan kuota</button>
            </form>
        </div>
    </section>

    <section class="mt-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h2 class="font-display text-xl font-extrabold">Verifikasi pemasok</h2>
            <form method="GET"><input name="cari" value="{{ $search }}" placeholder="Cari nama…" class="rounded-lg border border-garis px-3 py-2 text-sm"></form>
        </div>
        <div class="mt-3 overflow-x-auto rounded-2xl border border-garis bg-kertas">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="bg-santan text-left text-abu"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Lokasi</th><th class="px-4 py-3">Telepon</th><th class="px-4 py-3">Produk</th><th class="px-4 py-3">Status</th></tr></thead>
                <tbody class="divide-y divide-garis">
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $supplier->name }}</td>
                            <td class="px-4 py-3">{{ $supplier->regency ?? '–' }}</td>
                            <td class="px-4 py-3">{{ $supplier->phone ? \App\Support\Phone::display($supplier->phone) : '–' }}</td>
                            <td class="px-4 py-3">{{ $supplier->products_count }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.suppliers.verify', $supplier) }}" class="flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <select name="verification" class="rounded-lg border border-garis px-2 py-1 text-xs" onchange="this.form.submit()">
                                        <option value="pernyataan" @selected($supplier->verification === 'pernyataan')>Pernyataan</option>
                                        <option value="terverifikasi" @selected($supplier->verification === 'terverifikasi')>Terverifikasi</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $suppliers->links() }}</div>
    </section>
</x-app.layout>
