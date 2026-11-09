<x-app.layout title="Periksa nota">
    @if ($receipt->status === 'diproses')
        <div class="mx-auto max-w-xl border border-garis bg-kertas p-8 text-center rounded-2xl"
             x-data x-init="const cek = async () => { const r = await fetch('{{ route('receipts.status', $receipt) }}', { headers: { Accept: 'application/json' } }); const d = await r.json(); if (d.status !== 'diproses') { location.reload() } else { setTimeout(cek, 2000) } }; setTimeout(cek, 1500)">
            <div class="mx-auto size-12 animate-spin rounded-full border-4 border-daun-muda border-t-daun" aria-hidden="true"></div>
            <h1 class="mt-4 font-display text-2xl font-semibold">AI sedang membaca nota…</h1>
            <p class="mt-2 text-abu">Biasanya 5–20 detik. Halaman akan diperbarui otomatis.</p>
            @if ($receipt->image_path)
                <img src="{{ route('receipts.image', $receipt) }}" alt="Foto nota" class="mx-auto mt-6 max-h-72 rounded-lg">
            @endif
        </div>
    @else
        @php
            $rows = $receipt->items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name_raw,
                'product_id' => $item->product_id,
                'category' => $item->category,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'total_price' => $item->total_price,
                'quantity_kg' => $item->quantity_kg,
                'kg_estimated' => $item->kg_estimated,
                'origin' => $item->origin,
                'evidence' => $item->evidence,
                'confidence' => $item->confidence,
                'needs_review' => $item->needs_review,
                'match_source' => $item->match_source,
                'matched_name' => $item->product_id ? $products->firstWhere('id', $item->product_id)['name'] ?? null : null,
                'ai_kg_per_unit' => $item->ai_kg_per_unit,
            ])->values();
            $initialRows = old('items') ? collect(old('items'))->values() : $rows;
            $summary = [
                'kamus' => $receipt->items->where('match_source', \App\Models\ReceiptItem::MATCH_DICTIONARY)->count(),
                'ai' => $receipt->items->where('match_source', \App\Models\ReceiptItem::MATCH_AI)->count(),
                'cek' => $receipt->items->where('needs_review', true)->count(),
                'lokal' => $receipt->items->where('origin', 'bali')->count(),
                'belum' => $receipt->items->whereNotNull('category')->where('origin', \App\Models\ReceiptItem::UNKNOWN)->count(),
            ];
            $origin = [
                'supplier' => $receipt->supplier?->name,
                'regency' => $receipt->supplier?->regency,
            ];
        @endphp

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <a href="{{ route('receipts.index') }}" class="text-sm font-semibold text-daun">← Semua nota</a>
                <h1 class="font-display text-3xl font-semibold text-daun-tua">Periksa nota</h1>
                <p class="mt-1 text-abu">
                    {{ $receipt->statusLabel() }}
                    @if ($receipt->ai_used) · dibaca AI dari foto, {{ $receipt->items->count() }} baris @endif
                </p>
                @if ($receipt->ai_used && $receipt->items->isNotEmpty())
                    <p class="mt-2 flex flex-wrap gap-2 text-xs">
                        @if ($summary['kamus'])<span class="rounded-full bg-daun-muda px-2.5 py-1 font-semibold text-daun-tua">{{ $summary['kamus'] }} dikenali kamus istilah</span>@endif
                        @if ($summary['ai'])<span class="rounded-full bg-kunyit-muda px-2.5 py-1 font-semibold ring-1 ring-kunyit">{{ $summary['ai'] }} ditebak AI</span>@endif
                        @if ($summary['cek'])<span class="rounded-full bg-terakota-muda px-2.5 py-1 font-semibold text-terakota">{{ $summary['cek'] }} perlu dicek</span>@endif
                        @if ($summary['lokal'])<span class="rounded-full bg-daun px-2.5 py-1 font-semibold text-kertas">{{ $summary['lokal'] }} lokal Bali</span>@endif
                        @if ($summary['belum'])<span class="rounded-full bg-santan px-2.5 py-1 font-semibold text-abu ring-1 ring-garis">{{ $summary['belum'] }} asal belum diketahui</span>@endif
                    </p>
                @endif
            </div>
            <form method="POST" action="{{ route('receipts.destroy', $receipt) }}" x-data @submit="if (! window.confirm('Hapus nota ini?')) $event.preventDefault()">
                @csrf @method('DELETE')
                <button class="rounded-lg px-3 py-2 text-sm font-semibold text-terakota hover:bg-terakota-muda">Hapus nota</button>
            </form>
        </div>

        @if ($receipt->note)
            <div class="mt-4 rounded-lg border border-kunyit bg-kunyit-muda px-4 py-3 text-sm">{{ $receipt->note }}</div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,320px)_1fr]">
            <aside>
                @if ($receipt->image_path)
                    <a href="{{ route('receipts.image', $receipt) }}" target="_blank" rel="noopener" class="block overflow-hidden border border-garis bg-kertas rounded-2xl">
                        <img src="{{ route('receipts.image', $receipt) }}" alt="Foto nota" class="w-full">
                    </a>
                    <p class="mt-2 text-xs text-abu">Ketuk foto untuk memperbesar.</p>
                @else
                    <div class="border border-garis bg-kertas p-6 text-center text-sm text-abu rounded-2xl">Tidak ada foto (nota diisi manual atau foto sudah dihapus otomatis).</div>
                @endif
            </aside>

            <form method="POST" action="{{ route('receipts.update', $receipt) }}" class="min-w-0"
                  x-data="notaForm(@js($initialRows), @js($products), @js($categories), @js($origin))">
                @csrf @method('PUT')

                <div class="grid gap-4 border border-garis bg-kertas p-5 sm:grid-cols-2 rounded-2xl">
                    <div>
                        <label for="supplier_id" class="text-sm font-semibold">Pemasok</label>
                        <select id="supplier_id" name="supplier_id" class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
                            <option value="">Belum terdaftar di Pasok Bali</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((int) old('supplier_id', $receipt->supplier_id) === $supplier->id)>{{ $supplier->name }}{{ $supplier->regency ? ' · '.$supplier->regency : '' }}{{ $supplier->verification === 'terverifikasi' ? ' ✓' : '' }}</option>
                            @endforeach
                        </select>
                        @if ($receipt->supplier_name_raw)
                            <p class="mt-1 text-xs text-abu">Tertulis di nota: “{{ $receipt->supplier_name_raw }}”</p>
                        @endif
                    </div>
                    <div>
                        <label for="receipt_date" class="text-sm font-semibold">Tanggal nota</label>
                        <input id="receipt_date" type="date" name="receipt_date" value="{{ old('receipt_date', $receipt->receipt_date?->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required class="mt-1 w-full rounded-lg border border-garis px-3 py-2.5">
                    </div>
                    <p class="text-xs text-abu sm:col-span-2">Asal barang (lokal Bali atau bukan) ditentukan dari data produk pemasok yang terdaftar, bukan diisi manual. Pemasok belum terdaftar membuat asal barang “belum diketahui”.</p>
                </div>

                <div class="mt-4 space-y-3">
                    <template x-for="(row, index) in rows" :key="row.key">
                        <div class="rounded-2xl border bg-kertas p-5" :class="row.needs_review ? 'border-kunyit ring-2 ring-kunyit/40' : 'border-garis'">
                            <input type="hidden" :name="`items[${index}][id]`" :value="row.id ?? ''">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <label class="text-xs font-semibold text-abu" :for="`nama-${row.key}`">Tertulis di nota</label>
                                    <input :id="`nama-${row.key}`" :name="`items[${index}][name]`" x-model="row.name" required maxlength="100" class="mt-0.5 w-full rounded-lg border border-garis px-3 py-2 font-semibold">
                                </div>
                                <button type="button" @click="remove(index)" class="mt-5 rounded-lg px-2 py-2 text-sm text-terakota hover:bg-terakota-muda" aria-label="Hapus baris">Hapus</button>
                            </div>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <div class="sm:col-span-2">
                                    <label class="text-xs font-semibold text-abu" :for="`produk-${row.key}`">Produk (katalog)</label>
                                    <select :id="`produk-${row.key}`" :name="`items[${index}][product_id]`" x-model="row.product_id" class="mt-0.5 w-full rounded-lg border border-garis px-3 py-2">
                                        <option value="">Bukan produk katalog</option>
                                        <template x-for="product in products" :key="product.id">
                                            <option :value="product.id" x-text="product.name + ' · ' + categories[product.category]" :selected="String(product.id) === String(row.product_id)"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="sm:col-span-2" x-show="!row.product_id">
                                    <label class="text-xs font-semibold text-abu" :for="`kategori-${row.key}`">Kelompok Pergub</label>
                                    <select :id="`kategori-${row.key}`" :name="`items[${index}][category]`" x-model="row.category" class="mt-0.5 w-full rounded-lg border border-garis px-3 py-2">
                                        <option value="">Tidak dihitung (bukan bahan/produk olahan)</option>
                                        <template x-for="(label, key) in categories" :key="key">
                                            <option :value="key" x-text="label" :selected="key === row.category"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-abu" :for="`jumlah-${row.key}`">Jumlah</label>
                                    <input :id="`jumlah-${row.key}`" type="number" step="0.001" min="0.001" :name="`items[${index}][quantity]`" x-model="row.quantity" required class="mt-0.5 w-full rounded-lg border border-garis px-3 py-2">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-abu" :for="`satuan-${row.key}`">Satuan</label>
                                    <input :id="`satuan-${row.key}`" :name="`items[${index}][unit]`" x-model="row.unit" required maxlength="20" list="daftar-satuan" class="mt-0.5 w-full rounded-lg border border-garis px-3 py-2">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="text-xs font-semibold text-abu" :for="`harga-${row.key}`">Total harga baris (Rp)</label>
                                    <input :id="`harga-${row.key}`" type="number" min="0" step="1" :name="`items[${index}][total_price]`" x-model="row.total_price" required class="mt-0.5 w-full rounded-lg border border-garis px-3 py-2">
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs" x-show="row.id">
                                <span class="rounded-full bg-santan px-2.5 py-0.5 font-semibold ring-1 ring-garis" x-text="row.quantity_kg !== null && row.quantity_kg !== undefined ? ('≈ ' + Number(row.quantity_kg).toLocaleString('id-ID') + ' kg' + (row.kg_estimated ? ' (perkiraan)' : '')) : 'Berat belum bisa dihitung'"></span>
                                <span class="rounded-full px-2.5 py-0.5 font-semibold" :class="badge(row).cls" x-text="badge(row).label"></span>
                                <span class="text-abu" x-show="row.confidence !== null && row.confidence !== undefined && row.confidence < 0.6">Keyakinan AI rendah, mohon dicek.</span>
                            </div>
                            <ul class="mt-2 space-y-0.5 text-xs text-abu" x-show="row.id && reasons(row).length">
                                <template x-for="reason in reasons(row)">
                                    <li x-text="'• ' + reason"></li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>

                <datalist id="daftar-satuan">
                    @foreach (['kg', 'gram', 'ons', 'liter', 'ml', 'butir', 'ekor', 'ikat', 'krat', 'tray', 'pack', 'botol', 'dus', 'karung', 'sisir', 'buah', 'jerigen', 'galon'] as $unit)
                        <option value="{{ $unit }}"></option>
                    @endforeach
                </datalist>

                <button type="button" @click="add()" class="mt-3 rounded-lg border-2 border-dashed border-garis px-4 py-2.5 text-sm font-semibold text-abu hover:border-daun hover:text-daun">+ Tambah baris</button>

                <div class="sticky bottom-0 mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-garis bg-santan/95 py-4 backdrop-blur">
                    <p class="mr-auto text-sm text-abu">Total: <strong class="text-tinta" x-text="'Rp' + total().toLocaleString('id-ID')"></strong></p>
                    <button name="action" value="simpan" class="rounded-lg border-2 border-daun px-5 py-2.5 font-bold text-daun hover:bg-daun-muda">Simpan &amp; hitung ulang</button>
                    <button name="action" value="setujui" class="rounded-full bg-daun-tua px-5 py-2.5 font-bold text-kertas hover:bg-taum">Setujui nota</button>
                </div>
            </form>
        </div>

        <script>
            function notaForm(rows, products, categories, origin) {
                const angka = (value) => Number(value).toLocaleString('id-ID', { maximumFractionDigits: 3 });
                let seq = 0;
                const withKey = (row) => ({ key: ++seq, product_id: '', category: '', ...row, product_id: row.product_id ?? '', category: row.category ?? '' });

                return {
                    rows: rows.map(withKey),
                    products,
                    categories,
                    add() {
                        this.rows.push(withKey({ name: '', quantity: 1, unit: 'kg', total_price: 0 }));
                    },
                    remove(index) {
                        if (this.rows.length > 1) this.rows.splice(index, 1);
                    },
                    total() {
                        return this.rows.reduce((sum, row) => sum + (parseInt(row.total_price) || 0), 0);
                    },
                    badge(row) {
                        if (row.origin === 'bali' && row.evidence === 'terverifikasi') return { label: 'Lokal Bali · terverifikasi', cls: 'bg-daun text-kertas' };
                        if (row.origin === 'bali') return { label: 'Lokal Bali · pernyataan pemasok', cls: 'bg-kunyit-muda ring-1 ring-kunyit' };
                        if (row.origin === 'luar') return { label: 'Luar Bali', cls: 'bg-terakota-muda text-terakota' };
                        if (row.origin === 'campuran') return { label: 'Campuran', cls: 'bg-terakota-muda text-terakota' };
                        return { label: 'Asal belum diketahui', cls: 'bg-santan text-abu ring-1 ring-garis' };
                    },
                    reasons(row) {
                        const list = [];
                        const name = (row.name || '').trim();

                        if (row.match_source === 'kamus' && row.matched_name) {
                            list.push(name.toLowerCase() === row.matched_name.toLowerCase()
                                ? `Cocok langsung dengan katalog: ${row.matched_name}.`
                                : `Kamus istilah: “${name}” = ${row.matched_name}.`);
                        } else if (row.match_source === 'ai' && row.matched_name) {
                            const yakin = row.confidence !== null && row.confidence !== undefined ? `, keyakinan ${Math.round(row.confidence * 100)}%` : '';
                            list.push(`Tidak ada di kamus, AI menebak “${name}” = ${row.matched_name}${yakin}.`);
                        } else if (row.match_source === 'pengguna' && row.matched_name) {
                            list.push(`Dipilih manual: ${row.matched_name}.`);
                        }

                        const unit = (row.unit || '').toLowerCase();
                        if (row.quantity_kg !== null && row.quantity_kg !== undefined && unit !== 'kg' && Number(row.quantity) > 0) {
                            const perUnit = row.quantity_kg / row.quantity;
                            const sumber = row.kg_estimated ? (row.ai_kg_per_unit ? 'perkiraan AI' : 'perkiraan') : 'tabel satuan';
                            list.push(`${angka(row.quantity)} ${row.unit} × ${angka(perUnit)} kg = ${angka(row.quantity_kg)} kg (${sumber}).`);
                        }

                        if (row.origin === 'bali' && origin.supplier) {
                            list.push(`Lokal karena ${origin.supplier}${origin.regency ? ' (' + origin.regency + ')' : ''} mendaftarkan produk ini sebagai hasil Bali.`);
                        } else if ((row.origin === 'luar' || row.origin === 'campuran') && origin.supplier) {
                            list.push(`${origin.supplier} mencatat produk ini dari luar Bali atau campuran.`);
                        } else if (row.origin === 'tidak_diketahui' && row.category) {
                            list.push(origin.supplier
                                ? `${origin.supplier} belum mendaftarkan produk ini, jadi asalnya belum diketahui.`
                                : 'Toko di nota belum terdaftar di Pasok Bali. Pilih pemasok terdaftar di atas bila sesuai agar asal barang bisa dihitung.');
                        }

                        return list;
                    },
                };
            }
        </script>
    @endif
</x-app.layout>
