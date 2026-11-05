<x-app.layout title="Pemasok lokal">
    <h1 class="font-display text-3xl font-semibold text-daun-tua">Pemasok lokal Bali</h1>
    <p class="mt-1 text-abu">Petani, peternak, nelayan, dan UMKM yang terdaftar. Label hijau berarti sudah diverifikasi admin; kuning berarti berdasarkan pernyataan pemasok.</p>

    <form method="GET" class="mt-4 flex flex-wrap gap-2">
        <select name="kategori" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm" onchange="this.form.submit()" aria-label="Kelompok">
            <option value="">Semua kelompok</option>
            @foreach (config('pasok.categories') as $key => $label)
                <option value="{{ $key }}" @selected($category === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="kabupaten" class="rounded-lg border border-garis bg-kertas px-3 py-2 text-sm" onchange="this.form.submit()" aria-label="Kabupaten">
            <option value="">Semua kabupaten</option>
            @foreach (config('pasok.regencies') as $name)
                <option value="{{ $name }}" @selected($regency === $name)>{{ $name }}</option>
            @endforeach
        </select>
    </form>

    <div class="mt-5 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($suppliers as $supplier)
            <article class="border border-garis bg-kertas p-5 rounded-2xl">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="font-display text-2xl leading-tight font-semibold leading-snug">{{ $supplier->name }}</h2>
                    <span @class(['shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-daun text-kertas' => $supplier->isVerified(), 'bg-kunyit-muda ring-1 ring-kunyit' => ! $supplier->isVerified()])>{{ $supplier->isVerified() ? 'Terverifikasi' : 'Pernyataan' }}</span>
                </div>
                <p class="text-sm text-abu">{{ $supplier->regency ?? 'Lokasi belum diisi' }}</p>
                <ul class="mt-3 space-y-1.5 text-sm">
                    @foreach ($supplier->products as $offer)
                        <li class="flex items-center justify-between gap-2">
                            <span>{{ $offer->product->name }}</span>
                            <x-app.evidence :origin="$offer->origin" :evidence="$supplier->verification" class="!text-[10px]" />
                        </li>
                    @endforeach
                </ul>
            </article>
        @empty
            <p class="text-abu">Belum ada pemasok yang cocok dengan filter.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $suppliers->links() }}</div>

    <aside class="mt-8 bg-daun-tua p-6 text-kertas">
        <h2 class="font-display text-xl font-semibold text-kunyit">Ajak pemasok Anda bergabung</h2>
        @php($botNumber = \App\Support\Phone::normalize(config('pasok.whatsapp.device')))
        <p class="mt-1">Pemasok cukup mengirim WhatsApp ke nomor Pasok Bali{{ $botNumber ? ' '.\App\Support\Phone::display($botNumber) : '' }}, misalnya:</p>
        <p class="mt-2 rounded-2xl bg-kertas/10 px-4 py-3 font-mono text-sm">PASOK DAFTAR telur ayam 200 krat seminggu, Tabanan, Rp52.000/krat, nama Peternakan Sari</p>
        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2">
            @if ($botNumber)
                <a href="https://wa.me/{{ $botNumber }}?text={{ rawurlencode('PASOK DAFTAR ') }}" target="_blank" rel="noopener" class="font-bold text-kunyit underline">Buka chat pendaftaran WA</a>
            @endif
            <a href="{{ route('simulator.index') }}" class="font-bold text-kunyit underline">Coba alurnya di Simulator WA</a>
        </div>
    </aside>
</x-app.layout>
