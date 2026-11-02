<x-app.layout title="Kepatuhan produk lokal Bali">
    <section class="grid items-center gap-8 py-6 lg:grid-cols-2">
        <div>
            <p class="font-semibold text-daun">Untuk hotel, restoran, kafe, dan katering di Bali</p>
            <h1 class="mt-2 font-display text-4xl font-black leading-tight text-daun-tua sm:text-5xl">Foto nota belanja. Tahu langsung berapa persen produk lokal Bali Anda.</h1>
            <p class="mt-4 text-lg text-abu">Pergub Bali No. 99/2018 mewajibkan usaha akomodasi dan kuliner memakai produk pertanian, peternakan, perikanan, dan industri lokal Bali. Pasok Bali membaca nota dengan AI, menghitung kuota per kelompok, lalu menghubungkan Anda dengan pemasok lokal.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('demo') }}">
                    @csrf
                    <button class="rounded-xl bg-daun px-5 py-3 font-bold text-kertas hover:bg-daun-tua">Coba demo</button>
                </form>
                <a href="{{ route('register') }}" class="rounded-xl border-2 border-daun px-5 py-3 font-bold text-daun hover:bg-daun-muda">Daftarkan usaha</a>
                <a href="{{ route('login') }}" class="px-2 py-3 font-semibold text-abu hover:text-tinta">Masuk</a>
            </div>
            @error('demo')
                <p class="mt-3 text-sm text-terakota">{{ $message }}</p>
            @enderror
        </div>
        <ol class="space-y-3">
            @foreach ([['Foto nota', 'Nota cetak, tulisan tangan pasar, atau faktur pemasok dibaca AI menjadi daftar barang.'], ['Hitung kuota', 'Barang dikelompokkan sesuai Pergub, satuan diseragamkan ke kg, asal Bali dicek dari data pemasok.'], ['Sambungkan pemasok lokal', 'Jika kuota kurang, Pasok Bali menyarankan petani, peternak, dan nelayan Bali lalu mengirim permintaan lewat WhatsApp.']] as $i => [$title, $text])
                <li class="flex gap-4 rounded-2xl border border-garis bg-kertas p-5">
                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-kunyit font-black">{{ $i + 1 }}</span>
                    <span>
                        <span class="block font-display text-lg font-bold">{{ $title }}</span>
                        <span class="text-abu">{{ $text }}</span>
                    </span>
                </li>
            @endforeach
        </ol>
    </section>
</x-app.layout>
