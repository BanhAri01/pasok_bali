<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Ukur sendiri kepatuhan Pergub Bali No. 99/2018 dari foto nota belanja dapur dengan AI, lalu terhubung dengan petani, peternak, dan nelayan lokal Bali lewat WhatsApp.">
    <meta name="theme-color" content="#e0a526">
    <title>Pasok Bali: ukur produk lokal Bali dari nota belanja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-santan font-sans text-tinta antialiased selection:bg-taum selection:text-kertas">
    @php
        $nota = [
            ['kesuna', '2 kg', 'Bawang putih', 'pertanian', 2, false, 'luar Bali'],
            ['bawang barak', '3 kg', 'Bawang merah', 'pertanian', 3, true, 'Bangli'],
            ['tabia', '1 kg', 'Cabai rawit', 'pertanian', 1, true, 'Tabanan'],
            ['taluh', '1 krat', 'Telur ayam ras', 'peternakan', 1.8, true, 'Tabanan'],
            ['be siap', '6 ekor', 'Daging ayam', 'peternakan', 7.2, false, 'luar Bali'],
            ['be tongkol', '3 kg', 'Ikan tongkol', 'perikanan', 3, true, 'Kedonganan'],
            ['uyah', '2 pack', 'Garam', 'industri', 0.5, true, 'Kusamba'],
            ['migor', '5 liter', 'Minyak goreng sawit', 'industri', 4.5, false, 'luar Bali'],
        ];
        $kelompok = [
            'pertanian' => ['Pertanian', 30, 'bg-daun'],
            'peternakan' => ['Peternakan', 30, 'bg-terakota'],
            'perikanan' => ['Perikanan', 30, 'bg-taum'],
            'industri' => ['Industri lokal', 20, 'bg-tinta'],
        ];
        $istilah = ['kesuna', 'bawang barak', 'taluh', 'baas', 'tabia', 'biu', 'nyuh', 'be siap', 'be celeng', 'gula bali', 'uyah', 'arak'];
    @endphp

    <div class="bg-kunyit" x-data="notaPasar(@js(collect($nota)->map(fn ($n) => ['kelompok' => $n[3], 'kg' => $n[4], 'lokal' => $n[5]])), @js(collect($kelompok)->map(fn ($k) => $k[1])))">
        <header x-data="{ buka: false }" class="relative">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-5 sm:px-8">
                <a href="{{ route('home') }}" aria-label="Pasok Bali, halaman depan"><x-ui.logo /></a>

                <nav class="hidden items-center gap-8 font-medium lg:flex" aria-label="Navigasi halaman depan">
                    <a href="#cara-kerja" class="underline-offset-4 hover:underline">Cara kerja</a>
                    <a href="#bukti" class="underline-offset-4 hover:underline">Tingkat bukti</a>
                    <a href="#pemasok" class="underline-offset-4 hover:underline">Untuk pemasok</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded bg-tinta px-5 py-3 text-sm font-semibold text-kertas hover:bg-taum">Buka dasbor</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-semibold underline-offset-4 hover:underline sm:inline-block">Masuk</a>
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded bg-tinta px-5 py-3 text-sm font-semibold text-kertas hover:bg-taum">Coba demo</button>
                        </form>
                    @endauth
                    <button type="button" class="ml-1 rounded border-2 border-tinta px-3 py-2 text-sm font-semibold lg:hidden" @click="buka = !buka" :aria-expanded="buka" aria-controls="menu-ponsel">Menu</button>
                </div>
            </div>
            <nav id="menu-ponsel" x-show="buka" x-cloak class="border-y-2 border-tinta bg-kertas px-4 py-2 lg:hidden" aria-label="Menu ponsel">
                <a href="#cara-kerja" class="block py-2" @click="buka = false">Cara kerja</a>
                <a href="#bukti" class="block py-2" @click="buka = false">Tingkat bukti</a>
                <a href="#pemasok" class="block py-2" @click="buka = false">Untuk pemasok</a>
                @guest
                    <a href="{{ route('login') }}" class="block py-2">Masuk</a>
                    <a href="{{ route('register') }}" class="block py-2">Daftarkan usaha</a>
                @endguest
            </nav>
        </header>

        <section class="mx-auto grid max-w-7xl gap-12 px-4 pt-6 pb-14 sm:px-8 lg:grid-cols-12 lg:gap-8 lg:pt-10 lg:pb-20">
            <div class="lg:col-span-7 lg:pr-6">
                <h1 class="font-display text-[clamp(3.6rem,10vw,9.5rem)] leading-[0.84]">Dari kesuna sampai kuota.</h1>
                <p class="mt-8 max-w-[34rem] text-lg leading-relaxed">
                    Foto nota belanja dapur Anda. Pasok Bali membaca istilah pasar Bali, mengubah ikat, krat, dan ekor menjadi kilogram, lalu menghitung kuota Pergub Bali No. 99/2018 untuk hotel, restoran, kafe, villa, dan katering.
                </p>
                <div class="mt-9 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded bg-tinta px-7 py-4 font-semibold text-kertas hover:bg-taum">Buka dasbor</a>
                    @else
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded bg-tinta px-7 py-4 font-semibold text-kertas hover:bg-taum">Coba dengan dapur contoh</button>
                        </form>
                        <a href="{{ route('register') }}" class="rounded border-2 border-tinta px-7 py-[0.9rem] font-semibold hover:bg-tinta hover:text-kertas">Daftarkan usaha</a>
                    @endauth
                </div>
                @error('demo')
                    <p class="mt-4 font-semibold text-terakota" role="alert">{{ $message }}</p>
                @enderror
                <p class="mt-5 text-sm">Demo tanpa daftar. Isinya data fiktif dan terhapus sendiri setelah 48 jam.</p>
            </div>

            <div class="lg:col-span-5">
                <div class="nota-pasar">
                    <div class="flex items-center justify-between gap-3 border-b-2 border-tinta px-5 py-3">
                        <p class="font-display text-xl">Nota Dagang Men Sari</p>
                        <p class="text-xs text-abu">contoh fiktif</p>
                    </div>
                    <ul class="divide-y divide-taum/15">
                        @foreach ($nota as $i => [$kata, $jumlah, $produk, $grup, $kg, $lokal, $asal])
                            <li>
                                <button type="button" @click="baca({{ $i }})" :aria-pressed="terbaca[{{ $i }}]" class="grid h-11 w-full grid-cols-[1fr_auto] items-center gap-3 px-5 text-left transition-colors hover:bg-kunyit-muda" :class="terbaca[{{ $i }}] && 'bg-kunyit-muda/60'">
                                    <span class="truncate"><span class="font-display text-xl">{{ $kata }}</span> <span class="text-sm text-abu">{{ $jumlah }}</span></span>
                                    <span class="text-right text-xs leading-tight">
                                        <span x-show="!terbaca[{{ $i }}]" class="text-abu">ketuk untuk baca</span>
                                        <span x-show="terbaca[{{ $i }}]" x-cloak>{{ $produk }}, {{ str_replace('.', ',', (string) $kg) }} kg<br><span @class(['font-semibold', 'text-daun' => $lokal, 'text-terakota' => ! $lokal])>{{ $lokal ? 'Lokal, '.$asal : 'Dari '.$asal }}</span></span>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="flex items-center justify-between gap-3 border-t-2 border-tinta px-5 py-3">
                        <p class="text-xs text-abu">Asal barang mengikuti data pemasok contoh.</p>
                        <button type="button" @click="bacaSemua()" class="rounded bg-taum px-4 py-2 text-sm font-semibold text-kertas hover:bg-tinta" x-text="semuaTerbaca ? 'Ulangi' : 'Baca semua'">Baca semua</button>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid border-y-2 border-tinta sm:grid-cols-2 lg:grid-cols-4" aria-live="polite">
            @foreach ($kelompok as $kunci => [$nama, $kuota, $warna])
                <div class="{{ $warna }} border-tinta px-5 py-7 text-kertas max-lg:[&:nth-child(-n+2)]:border-b-2 sm:odd:border-r-2 lg:border-r-2 lg:last:border-r-0 sm:px-8">
                    <div class="flex items-baseline justify-between gap-3">
                        <h2 class="font-semibold">{{ $nama }}</h2>
                        <p class="text-sm text-kertas/75">kuota {{ $kuota }}%</p>
                    </div>
                    <p class="mt-3 font-display text-7xl leading-none angka-tabel" x-text="persen('{{ $kunci }}') === null ? '–' : persen('{{ $kunci }}') + '%'">–</p>
                    <div class="relative mt-4 h-2 bg-kertas/25">
                        <div class="absolute inset-y-0 left-0 bg-kertas transition-[width] duration-500 ease-out" :style="`width: ${persen('{{ $kunci }}') ?? 0}%`"></div>
                        <div class="absolute -inset-y-1.5 w-0.5 bg-kunyit" style="left: {{ $kuota }}%"></div>
                    </div>
                    <p class="mt-3 min-h-5 text-sm" x-text="keterangan('{{ $kunci }}')">Ketuk barang di nota untuk menghitung.</p>
                </div>
            @endforeach
        </div>
    </div>

    <main>
        <section id="cara-kerja" class="mx-auto max-w-7xl px-4 py-20 sm:px-8 lg:py-28">
            <h2 class="max-w-3xl font-display text-[clamp(2.6rem,5.5vw,4.8rem)] leading-[0.92]">Tiga hal yang selama ini dikerjakan manual.</h2>
            <ol class="mt-14 grid gap-10 md:grid-cols-3 md:gap-8">
                @foreach ([
                    ['Membaca nota', 'Nota cetak, tulisan tangan kios pasar, atau faktur pemasok difoto. AI menyalin pemasok, tanggal, barang, jumlah, satuan, dan harga. Anda memeriksa sebelum menyimpan.'],
                    ['Menghitung persen', 'Istilah dicocokkan ke kamus, satuan diubah ke kilogram, lalu persentase lokal dihitung per kelompok. Hitungan ini memakai aturan tetap, bukan tebakan AI.'],
                    ['Mencari pemasok', 'Kelompok yang kurang mendapat daftar pemasok lokal yang cocok, perkiraan kenaikan persennya, dan draf pesan WhatsApp yang tinggal dikirim.'],
                ] as $n => [$judul, $isi])
                    <li class="border-t-[3px] border-tinta pt-5">
                        <p class="text-sm font-semibold text-abu">Langkah {{ $n + 1 }}</p>
                        <h3 class="mt-1 font-display text-4xl">{{ $judul }}</h3>
                        <p class="mt-4 leading-relaxed text-tinta/80">{{ $isi }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        <section class="border-y-2 border-tinta bg-kertas">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-8 lg:py-20">
                <p class="max-w-5xl font-display text-[clamp(2rem,4.2vw,3.6rem)] leading-[1.02] text-abu">
                    Kamusnya mengerti
                    @foreach ($istilah as $i => $kata)
                        <span class="text-tinta">{{ $kata }}</span>{{ $i < count($istilah) - 1 ? ',' : '' }}
                    @endforeach
                    dan ratusan singkatan nota lain. Koreksi Anda diingat untuk nota berikutnya.
                </p>
            </div>
        </section>

        <section id="bukti" class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-8 lg:grid-cols-12 lg:py-28">
            <div class="lg:col-span-5">
                <h2 class="font-display text-[clamp(2.6rem,5.5vw,4.8rem)] leading-[0.92]">Angka yang jujur soal asal barangnya.</h2>
                <p class="mt-6 max-w-md text-lg leading-relaxed text-tinta/80">Persen dihitung dari volume, bukan rupiah. Angka kuota bisa diatur admin dan perlu dicocokkan dengan teks resmi Pergub Bali No. 99 Tahun 2018.</p>
            </div>
            <ul class="grid content-start gap-4 lg:col-span-6 lg:col-start-7">
                @foreach ([
                    ['bg-daun', 'Terverifikasi', 'Pemasok sudah diperiksa admin sebagai produsen di Bali.'],
                    ['bg-kunyit', 'Pernyataan pemasok', 'Pemasok menyatakan sendiri produknya dari Bali lewat WhatsApp.'],
                    ['bg-garis', 'Belum diketahui', 'Asal barang belum jelas, jadi tidak dihitung sebagai lokal.'],
                ] as [$warna, $judul, $isi])
                    <li class="grid grid-cols-[3.5rem_1fr] border-2 border-tinta bg-kertas">
                        <span class="{{ $warna }} border-r-2 border-tinta" aria-hidden="true"></span>
                        <div class="px-5 py-4">
                            <p class="font-semibold">{{ $judul }}</p>
                            <p class="mt-1 text-tinta/75">{{ $isi }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        <section id="pemasok" class="border-t-2 border-tinta bg-taum text-kertas">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-8 lg:grid-cols-12 lg:py-28">
                <div class="lg:col-span-6">
                    <h2 class="font-display text-[clamp(2.6rem,5.5vw,4.8rem)] leading-[0.92]">Petani, peternak, dan nelayan cukup pakai WhatsApp.</h2>
                    <p class="mt-6 max-w-md text-lg leading-relaxed text-kertas/80">Satu pesan untuk mendaftar. Saat hotel atau restoran meminta pasokan, balas 1 untuk terima atau 2 untuk tolak. Tanpa aplikasi baru.</p>
                </div>
                <div class="max-w-md space-y-3 lg:col-span-5 lg:col-start-8" aria-label="Contoh percakapan WhatsApp, fiktif">
                    <p class="ml-auto w-fit max-w-[85%] rounded bg-kunyit px-4 py-3 font-medium text-tinta">DAFTAR bayam 30 ikat seminggu Tabanan</p>
                    <p class="w-fit max-w-[85%] rounded bg-kertas px-4 py-3 text-tinta">Data Anda: Bayam, 30 ikat per minggu, Tabanan. Balas YA bila benar.</p>
                    <p class="ml-auto w-fit rounded bg-kunyit px-4 py-3 font-medium text-tinta">YA</p>
                    <p class="pt-2 text-sm text-kertas/65">Contoh percakapan. Bisa dicoba di Simulator WA pada akun demo.</p>
                </div>
            </div>
        </section>

        <section class="border-t-2 border-tinta bg-terakota text-kertas">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-20 sm:px-8 lg:flex-row lg:items-end lg:justify-between lg:py-24">
                <h2 class="max-w-3xl font-display text-[clamp(2.8rem,6vw,5.4rem)] leading-[0.9]">Coba dengan dapur contoh. Tanpa daftar.</h2>
                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded bg-kertas px-7 py-4 font-semibold text-tinta hover:bg-kunyit">Buka dasbor</a>
                    @else
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded bg-kertas px-7 py-4 font-semibold text-tinta hover:bg-kunyit">Coba demo</button>
                        </form>
                        <a href="{{ route('register') }}" class="rounded border-2 border-kertas px-7 py-[0.9rem] font-semibold hover:bg-kertas hover:text-tinta">Daftarkan usaha</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <div class="tridatu h-2" aria-hidden="true"></div>
    <footer class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-10 text-sm sm:px-8 md:flex-row md:items-center md:justify-between">
        <x-ui.logo :size="30" />
        <p class="max-w-xl leading-relaxed text-abu">Alat ukur mandiri untuk usaha, bukan alat pengawasan pemerintah. Peragaan Bali DigiFest 2026. Semua contoh usaha, nota, dan pemasok adalah data fiktif.</p>
        <div class="flex gap-5 font-semibold">
            <a href="{{ route('login') }}" class="underline-offset-4 hover:underline">Masuk</a>
            <a href="{{ route('register') }}" class="underline-offset-4 hover:underline">Daftar</a>
        </div>
    </footer>
</body>
</html>
