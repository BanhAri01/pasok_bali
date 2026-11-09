<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Ukur sendiri kepatuhan Pergub Bali No. 99/2018 dari foto nota belanja dapur dengan AI, lalu terhubung dengan petani, peternak, dan nelayan lokal Bali lewat WhatsApp.">
    <meta name="theme-color" content="#f4f6f0">
    <title>Pasok Bali: ukur produk lokal Bali dari nota belanja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-santan font-sans text-tinta antialiased selection:bg-daun-muda selection:text-daun-tua">
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
            'pertanian' => ['Pertanian', 30],
            'peternakan' => ['Peternakan', 30],
            'perikanan' => ['Perikanan', 30],
            'industri' => ['Industri lokal', 20],
        ];
        $istilah = ['kesuna', 'bawang barak', 'taluh', 'baas', 'tabia', 'biu', 'nyuh', 'be siap', 'be celeng', 'gula bali', 'uyah', 'arak'];
    @endphp

    <div class="terasering" x-data="notaPasar(@js(collect($nota)->map(fn ($n) => ['kelompok' => $n[3], 'kg' => $n[4], 'lokal' => $n[5]])), @js(collect($kelompok)->map(fn ($k) => $k[1])))">
        <header x-data="{ buka: false }" class="relative">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-6 sm:px-8">
                <a href="{{ route('home') }}" aria-label="Pasok Bali, halaman depan"><x-ui.logo /></a>

                <nav class="hidden items-center gap-9 text-sm text-abu lg:flex" aria-label="Navigasi halaman depan">
                    <a href="#cara-kerja" class="hover:text-daun-tua">Cara kerja</a>
                    <a href="#bukti" class="hover:text-daun-tua">Tingkat bukti</a>
                    <a href="#pemasok" class="hover:text-daun-tua">Untuk pemasok</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-daun-tua px-5 py-2.5 text-sm font-semibold text-kertas hover:bg-taum">Buka dasbor</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-medium text-abu hover:text-daun-tua sm:inline-block">Masuk</a>
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded-full bg-daun-tua px-5 py-2.5 text-sm font-semibold text-kertas hover:bg-taum">Coba demo</button>
                        </form>
                    @endauth
                    <button type="button" class="ml-1 rounded-full border border-garis bg-kertas px-4 py-2 text-sm font-medium lg:hidden" @click="buka = !buka" :aria-expanded="buka" aria-controls="menu-ponsel">Menu</button>
                </div>
            </div>
            <nav id="menu-ponsel" x-show="buka" x-cloak class="mx-4 rounded-2xl border border-garis bg-kertas px-5 py-2 lg:hidden" aria-label="Menu ponsel">
                <a href="#cara-kerja" class="block py-2.5" @click="buka = false">Cara kerja</a>
                <a href="#bukti" class="block py-2.5" @click="buka = false">Tingkat bukti</a>
                <a href="#pemasok" class="block py-2.5" @click="buka = false">Untuk pemasok</a>
                @guest
                    <a href="{{ route('login') }}" class="block py-2.5">Masuk</a>
                    <a href="{{ route('register') }}" class="block py-2.5">Daftarkan usaha</a>
                @endguest
            </nav>
        </header>

        <section class="mx-auto grid max-w-6xl items-center gap-14 px-4 pt-10 pb-16 sm:px-8 lg:grid-cols-12 lg:gap-10 lg:pt-16 lg:pb-24">
            <div class="lg:col-span-7">
                <h1 class="font-display text-[clamp(3.4rem,8.4vw,7.2rem)] leading-[0.92] text-daun-tua">Dari kesuna sampai kuota.</h1>
                <p class="mt-8 max-w-[32rem] text-lg leading-[1.75] text-tinta/80">
                    Foto nota belanja dapur Anda. Pasok Bali membaca istilah pasar Bali, mengubah ikat, krat, dan ekor menjadi kilogram, lalu menghitung kuota Pergub Bali No. 99/2018 untuk hotel, restoran, kafe, villa, dan katering.
                </p>
                <div class="mt-10 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-daun-tua px-7 py-3.5 font-semibold text-kertas hover:bg-taum">Buka dasbor</a>
                    @else
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded-full bg-daun-tua px-7 py-3.5 font-semibold text-kertas hover:bg-taum">Coba dengan dapur contoh</button>
                        </form>
                        <a href="{{ route('register') }}" class="rounded-full border border-daun-tua/25 bg-kertas/70 px-7 py-3.5 font-semibold text-daun-tua hover:border-daun-tua">Daftarkan usaha</a>
                    @endauth
                </div>
                @error('demo')
                    <p class="mt-4 font-semibold text-terakota" role="alert">{{ $message }}</p>
                @enderror
                <p class="mt-5 text-sm text-abu">Demo tanpa daftar. Isinya data fiktif dan terhapus sendiri setelah {{ config('pasok.demo.ttl_hours') }} jam.</p>
            </div>

            <div class="lg:col-span-5">
                <div class="nota-pasar overflow-hidden">
                    <div class="flex items-baseline justify-between gap-3 border-b border-garis px-6 pt-5 pb-4">
                        <p class="font-display text-2xl text-daun-tua">Nota Dagang Men Sari</p>
                        <p class="text-xs text-abu">contoh fiktif</p>
                    </div>
                    <ul class="divide-y divide-garis/70">
                        @foreach ($nota as $i => [$kata, $jumlah, $produk, $grup, $kg, $lokal, $asal])
                            <li>
                                <button type="button" @click="baca({{ $i }})" :aria-pressed="terbaca[{{ $i }}]" class="grid h-12 w-full grid-cols-[1fr_auto] items-center gap-3 px-6 text-left transition-colors hover:bg-santan" :class="terbaca[{{ $i }}] && 'bg-santan/70'">
                                    <span class="truncate"><span class="font-display text-[1.35rem] italic text-daun-tua">{{ $kata }}</span> <span class="ml-1 text-sm text-abu">{{ $jumlah }}</span></span>
                                    <span class="text-right text-xs leading-tight">
                                        <span x-show="!terbaca[{{ $i }}]" class="text-abu/80">ketuk untuk baca</span>
                                        <span x-show="terbaca[{{ $i }}]" x-cloak>{{ $produk }}, {{ str_replace('.', ',', (string) $kg) }} kg<br><span @class(['font-medium', 'text-daun' => $lokal, 'text-terakota' => ! $lokal])>{{ $lokal ? 'Lokal, '.$asal : 'Dari '.$asal }}</span></span>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="flex items-center justify-between gap-3 border-t border-garis px-6 py-4">
                        <p class="text-xs text-abu">Asal barang mengikuti data pemasok contoh.</p>
                        <button type="button" @click="bacaSemua()" class="shrink-0 whitespace-nowrap rounded-full bg-daun-tua px-4 py-2 text-sm font-semibold text-kertas hover:bg-taum" x-text="semuaTerbaca ? 'Ulangi' : 'Baca semua'">Baca semua</button>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-6xl px-4 pb-16 sm:px-8 lg:pb-20">
            <div class="grid rounded-2xl border border-garis bg-kertas sm:grid-cols-2 lg:grid-cols-4" aria-live="polite">
                @foreach ($kelompok as $kunci => [$nama, $kuota])
                    <div class="border-garis px-6 py-7 max-sm:[&:not(:last-child)]:border-b sm:max-lg:[&:nth-child(-n+2)]:border-b sm:odd:border-r lg:border-r lg:last:border-r-0">
                        <div class="flex items-baseline justify-between gap-3">
                            <h2 class="text-sm font-semibold text-tinta">{{ $nama }}</h2>
                            <p class="text-xs text-abu">kuota {{ $kuota }}%</p>
                        </div>
                        <p class="mt-3 font-display text-6xl leading-none text-daun-tua angka-tabel" x-text="persen('{{ $kunci }}') === null ? '–' : persen('{{ $kunci }}') + '%'">–</p>
                        <div class="relative mt-5 h-1 rounded-full bg-daun-muda">
                            <div class="absolute inset-y-0 left-0 rounded-full transition-[width] duration-500 ease-out" :class="persen('{{ $kunci }}') >= {{ $kuota }} ? 'bg-daun' : 'bg-terakota'" :style="`width: ${persen('{{ $kunci }}') ?? 0}%`"></div>
                            <div class="absolute -inset-y-1.5 w-px bg-prada" style="left: {{ $kuota }}%"></div>
                        </div>
                        <p class="mt-4 min-h-5 text-sm text-abu" x-text="keterangan('{{ $kunci }}')">Ketuk barang di nota untuk menghitung.</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <main>
        <section id="cara-kerja" class="border-t border-garis bg-kertas">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-8 lg:py-28">
                <h2 class="max-w-2xl font-display text-[clamp(2.4rem,4.6vw,3.8rem)] leading-[1.02] text-daun-tua">Tiga pekerjaan yang selama ini dikerjakan manual.</h2>
                <ol class="mt-14 grid gap-12 md:grid-cols-3 md:gap-10">
                    @foreach ([
                        ['Membaca nota', 'Nota cetak, tulisan tangan kios pasar, atau faktur pemasok difoto. AI menyalin pemasok, tanggal, barang, jumlah, satuan, dan harga. Anda memeriksa sebelum menyimpan.'],
                        ['Menghitung persen', 'Istilah dicocokkan ke kamus, satuan diubah ke kilogram, lalu persentase lokal dihitung per kelompok. Hitungan ini memakai aturan tetap, bukan tebakan AI.'],
                        ['Mencari pemasok', 'Kelompok yang kurang mendapat daftar pemasok lokal yang cocok, perkiraan kenaikan persennya, dan draf pesan WhatsApp yang tinggal dikirim.'],
                    ] as $n => [$judul, $isi])
                        <li>
                            <p class="font-display text-5xl leading-none text-prada">{{ $n + 1 }}</p>
                            <h3 class="mt-5 text-lg font-semibold text-daun-tua">{{ $judul }}</h3>
                            <p class="mt-3 leading-[1.75] text-tinta/75">{{ $isi }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="border-y border-garis">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-8 lg:py-24">
                <p class="max-w-4xl font-display text-[clamp(1.9rem,3.6vw,3rem)] leading-[1.22] text-abu">
                    Kamusnya mengerti
                    @foreach ($istilah as $i => $kata)
                        <span class="italic text-daun-tua">{{ $kata }}</span>{{ $i < count($istilah) - 1 ? ',' : '' }}
                    @endforeach
                    dan ratusan singkatan nota lain. Koreksi Anda diingat untuk nota berikutnya.
                </p>
            </div>
        </section>

        <section id="bukti" class="bg-kertas">
            <div class="mx-auto grid max-w-6xl gap-12 px-4 py-20 sm:px-8 lg:grid-cols-12 lg:py-28">
                <div class="lg:col-span-5">
                    <h2 class="font-display text-[clamp(2.4rem,4.6vw,3.8rem)] leading-[1.02] text-daun-tua">Angka yang jujur soal asal barangnya.</h2>
                    <p class="mt-6 max-w-md leading-[1.75] text-tinta/75">Persen dihitung dari volume, bukan rupiah. Angka kuota bisa diatur admin dan perlu dicocokkan dengan teks resmi Pergub Bali No. 99 Tahun 2018.</p>
                </div>
                <ul class="divide-y divide-garis border-y border-garis lg:col-span-6 lg:col-start-7">
                    @foreach ([
                        ['bg-daun', 'Terverifikasi', 'Pemasok sudah diperiksa admin sebagai produsen di Bali.'],
                        ['bg-kunyit', 'Pernyataan pemasok', 'Pemasok menyatakan sendiri produknya dari Bali lewat WhatsApp.'],
                        ['bg-garis', 'Belum diketahui', 'Asal barang belum jelas, jadi tidak dihitung sebagai lokal.'],
                    ] as [$warna, $judul, $isi])
                        <li class="flex gap-4 py-6">
                            <span class="{{ $warna }} mt-1.5 size-3 shrink-0 rounded-full" aria-hidden="true"></span>
                            <div>
                                <p class="font-semibold text-daun-tua">{{ $judul }}</p>
                                <p class="mt-1 leading-relaxed text-tinta/75">{{ $isi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section id="pemasok" class="bg-daun-tua text-kertas">
            <div class="mx-auto grid max-w-6xl gap-14 px-4 py-20 sm:px-8 lg:grid-cols-12 lg:py-28">
                <div class="lg:col-span-6">
                    <h2 class="font-display text-[clamp(2.4rem,4.6vw,3.8rem)] leading-[1.02]">Petani, peternak, dan nelayan cukup pakai WhatsApp.</h2>
                    <p class="mt-6 max-w-md leading-[1.75] text-kertas/75">Satu pesan untuk mendaftar. Saat hotel atau restoran meminta pasokan, balas 1 untuk terima atau 2 untuk tolak. Tanpa aplikasi baru.</p>
                </div>
                <div class="max-w-md space-y-3 lg:col-span-5 lg:col-start-8" aria-label="Contoh percakapan WhatsApp, fiktif">
                    <p class="ml-auto w-fit max-w-[85%] rounded-2xl rounded-br-md bg-daun-muda px-4 py-3 text-daun-tua">PASOK DAFTAR bayam 30 ikat seminggu Tabanan</p>
                    <p class="w-fit max-w-[85%] rounded-2xl rounded-bl-md bg-kertas px-4 py-3 text-tinta">Data Anda: Bayam, 30 ikat per minggu, Tabanan. Balas YA bila benar.</p>
                    <p class="ml-auto w-fit rounded-2xl rounded-br-md bg-daun-muda px-4 py-3 text-daun-tua">YA</p>
                    <p class="pt-3 text-sm text-kertas/60">Contoh percakapan. Bisa dicoba di Simulator WA pada akun demo.</p>
                </div>
            </div>
        </section>

        <section>
            <div class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-20 sm:px-8 lg:flex-row lg:items-end lg:justify-between lg:py-28">
                <h2 class="max-w-2xl font-display text-[clamp(2.4rem,4.6vw,3.8rem)] leading-[1.02] text-daun-tua">Coba dengan dapur contoh, tanpa mendaftar.</h2>
                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-daun-tua px-7 py-3.5 font-semibold text-kertas hover:bg-taum">Buka dasbor</a>
                    @else
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded-full bg-daun-tua px-7 py-3.5 font-semibold text-kertas hover:bg-taum">Coba demo</button>
                        </form>
                        <a href="{{ route('register') }}" class="rounded-full border border-daun-tua/25 bg-kertas px-7 py-3.5 font-semibold text-daun-tua hover:border-daun-tua">Daftarkan usaha</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-garis bg-kertas">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 text-sm sm:px-8 md:flex-row md:items-center md:justify-between">
            <x-ui.logo :size="28" />
            <p class="max-w-xl leading-relaxed text-abu">Alat ukur mandiri untuk usaha, bukan alat pengawasan pemerintah. Peragaan Bali DigiFest 2026. Semua contoh usaha, nota, dan pemasok adalah data fiktif.</p>
            <div class="flex gap-6 font-medium text-daun-tua">
                <a href="{{ route('login') }}" class="hover:underline">Masuk</a>
                <a href="{{ route('register') }}" class="hover:underline">Daftar</a>
            </div>
        </div>
    </footer>
</body>
</html>
