<x-app.layout title="Admin">
    <h1 class="font-display text-3xl font-semibold text-daun-tua">Admin Pasok Bali</h1>

    <section class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach (['businesses' => 'Usaha terdaftar', 'demo_businesses' => 'Akun demo aktif', 'receipts' => 'Nota', 'suppliers' => 'Pemasok'] as $key => $label)
            <div class="border border-garis bg-kertas p-4 rounded-2xl">
                <p class="text-sm text-abu">{{ $label }}</p>
                <p class="font-display text-3xl font-semibold">{{ number_format($stats[$key], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-2">
        <div>
            <h2 class="font-display text-xl font-semibold">Panggilan AI 7 hari</h2>
            <table class="mt-3 w-full border border-garis bg-kertas text-sm rounded-2xl">
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
            <h2 class="font-display text-xl font-semibold">Kuota Pergub</h2>
            <form method="POST" action="{{ route('admin.quotas') }}" class="mt-3 space-y-3 border border-garis bg-kertas p-4 rounded-2xl">
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
                <button class="rounded-full bg-daun-tua px-4 py-2 text-sm font-bold text-kertas">Simpan kuota</button>
            </form>
        </div>
    </section>

    <section class="mt-8" id="tambah-pemasok">
        <h2 class="font-display text-xl font-semibold">Tambah pemasok</h2>
        <p class="mt-1 max-w-2xl text-sm text-abu">Untuk pemasok yang mendaftar lewat telepon, kunjungan, atau kelompok tani. Pemasok juga bisa daftar sendiri dengan mengirim PASOK DAFTAR ke nomor WA bot.</p>
        @if ($errors->any())
            <div class="mt-3 rounded-2xl border border-terakota/40 bg-terakota/5 p-3 text-sm text-terakota">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="mt-3 space-y-4 rounded-2xl border border-garis bg-kertas p-4 sm:p-5">
            @csrf
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <label class="block text-sm">Nama usaha/pemasok
                    <input name="name" required maxlength="120" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-garis px-3 py-2">
                </label>
                <label class="block text-sm">Nomor WA
                    <input name="phone" inputmode="tel" maxlength="40" value="{{ old('phone') }}" placeholder="08…" class="mt-1 w-full rounded-lg border border-garis px-3 py-2">
                </label>
                <label class="block text-sm">Kabupaten/kota
                    <select name="regency" required class="mt-1 w-full rounded-lg border border-garis px-3 py-2">
                        <option value="">Pilih…</option>
                        @foreach ($regencies as $regency)
                            <option value="{{ $regency }}" @selected(old('regency') === $regency)>{{ $regency }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm">Status
                    <select name="verification" class="mt-1 w-full rounded-lg border border-garis px-3 py-2">
                        <option value="terverifikasi" @selected(old('verification', 'terverifikasi') === 'terverifikasi')>Terverifikasi</option>
                        <option value="pernyataan" @selected(old('verification') === 'pernyataan')>Pernyataan</option>
                    </select>
                </label>
            </div>

            <div class="space-y-2">
                <p class="text-sm font-semibold">Produk</p>
                @for ($i = 0; $i < 3; $i++)
                    <div class="grid gap-2 rounded-xl bg-santan p-3 sm:grid-cols-[2fr_1fr_1fr_1fr_1.2fr]">
                        <select name="products[{{ $i }}][product_id]" @required($i === 0) aria-label="Produk {{ $i + 1 }}" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm">
                            <option value="">{{ $i === 0 ? 'Pilih produk…' : 'Produk lain (opsional)' }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected((string) old("products.$i.product_id") === (string) $product->id)>{{ $product->name }} ({{ $product->base_unit }})</option>
                            @endforeach
                        </select>
                        <input name="products[{{ $i }}][capacity]" type="number" min="0" step="any" value="{{ old("products.$i.capacity") }}" placeholder="Jumlah/minggu" aria-label="Jumlah per minggu produk {{ $i + 1 }}" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm">
                        <input name="products[{{ $i }}][unit]" maxlength="20" value="{{ old("products.$i.unit") }}" placeholder="Satuan" aria-label="Satuan produk {{ $i + 1 }}" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm">
                        <input name="products[{{ $i }}][price]" type="number" min="0" value="{{ old("products.$i.price") }}" placeholder="Harga Rp" aria-label="Harga per satuan produk {{ $i + 1 }}" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm">
                        <select name="products[{{ $i }}][origin]" aria-label="Asal produk {{ $i + 1 }}" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm">
                            <option value="bali" @selected(old("products.$i.origin", 'bali') === 'bali')>Produksi Bali</option>
                            <option value="luar" @selected(old("products.$i.origin") === 'luar')>Dari luar Bali</option>
                        </select>
                    </div>
                @endfor
                <p class="text-xs text-abu">Satuan kosong memakai satuan dasar produk. Harga dihitung per satuan yang sama.</p>
            </div>

            <label class="block text-sm">Catatan (opsional)
                <input name="notes" maxlength="255" value="{{ old('notes') }}" class="mt-1 w-full rounded-lg border border-garis px-3 py-2">
            </label>
            <button class="rounded-full bg-daun-tua px-5 py-2 text-sm font-bold text-kertas">Simpan pemasok</button>
        </form>
    </section>

    <section class="mt-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h2 class="font-display text-xl font-semibold">Verifikasi pemasok</h2>
            <form method="GET"><input name="cari" value="{{ $search }}" placeholder="Cari nama…" class="rounded-lg border border-garis px-3 py-2 text-sm"></form>
        </div>
        <div class="mt-3 overflow-x-auto border border-garis bg-kertas rounded-2xl">
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
