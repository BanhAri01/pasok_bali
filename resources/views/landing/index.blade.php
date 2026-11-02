<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Ukur sendiri kepatuhan Pergub Bali No. 99/2018 dari foto nota belanja dapur dengan AI, lalu terhubung dengan petani, peternak, dan nelayan lokal Bali lewat WhatsApp.">
    <meta name="theme-color" content="#1b3a1e">
    <title>Pasok Bali · Ukur produk lokal Bali dari nota belanja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-santan font-sans text-tinta antialiased selection:bg-kunyit-muda">
    @php
        $nota = [
            ['kesuna', '2 kg', 'Bawang putih', 'Pertanian'],
            ['taluh', '3 tray', 'Telur ayam ras', 'Peternakan'],
            ['baas', '1 karung', 'Beras', 'Pertanian'],
            ['be siap', '4 ekor', 'Daging ayam', 'Peternakan'],
            ['tabia', '1 kg', 'Cabai rawit', 'Pertanian'],
            ['uyah', '2 pack', 'Garam', 'Industri lokal'],
        ];
        $kamus = [
            ['kesuna', 'bawang putih'],
            ['bawang barak', 'bawang merah'],
            ['taluh', 'telur'],
            ['baas', 'beras'],
            ['tabia', 'cabai rawit'],
            ['biu', 'pisang'],
            ['nyuh', 'kelapa'],
            ['be siap', 'daging ayam'],
            ['be celeng', 'daging babi'],
            ['gula bali', 'gula aren'],
            ['uyah', 'garam'],
            ['arak', 'arak Bali'],
        ];
        $kuota = [
            ['Pertanian', 'Tanaman pangan, hortikultura, dan perkebunan: beras, sayur, buah, kopi, kelapa.', 30],
            ['Peternakan', 'Telur, daging ayam, daging babi, daging sapi, dan susu.', 30],
            ['Perikanan', 'Ikan laut, ikan tawar, udang, cumi, dan hasil laut lain.', 30],
            ['Industri lokal', 'Olahan Bali seperti gula aren, garam, arak, dan minyak kelapa.', 20],
        ];
    @endphp

    <div class="tridatu h-1.5" aria-hidden="true"></div>

    <header class="relative z-20" x-data="{ buka: false }">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-5 sm:px-8">
            <a href="{{ route('home') }}" aria-label="Pasok Bali, halaman depan">
                <x-ui.logo />
            </a>

            <nav class="hidden items-center gap-8 text-[0.95rem] text-abu lg:flex" aria-label="Navigasi halaman depan">
                <a href="#cara-kerja" class="hover:text-tinta">Cara kerja</a>
                <a href="#kamus" class="hover:text-tinta">Bahasa pasar</a>
                <a href="#kuota" class="hover:text-tinta">Kuota Pergub</a>
                <a href="#pemasok" class="hover:text-tinta">Untuk pemasok</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-tinta px-5 py-2.5 text-sm font-semibold text-kertas hover:bg-daun-tua">Buka dasbor</a>
                @else
                    <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-semibold text-abu hover:text-tinta sm:inline-block">Masuk</a>
                    <form method="POST" action="{{ route('demo') }}">
                        @csrf
                        <button class="rounded-full bg-tinta px-5 py-2.5 text-sm font-semibold text-kertas hover:bg-daun-tua">Coba demo</button>
                    </form>
                @endauth
                <button type="button" class="ml-1 rounded-full border border-garis px-3 py-2 text-sm font-semibold lg:hidden" @click="buka = !buka" :aria-expanded="buka" aria-controls="menu-ponsel">Menu</button>
            </div>
        </div>
        <nav id="menu-ponsel" x-show="buka" x-cloak class="border-y border-garis bg-kertas px-4 py-3 lg:hidden" aria-label="Menu ponsel">
            <div class="flex flex-col text-base">
                <a href="#cara-kerja" class="py-2" @click="buka = false">Cara kerja</a>
                <a href="#kamus" class="py-2" @click="buka = false">Bahasa pasar</a>
                <a href="#kuota" class="py-2" @click="buka = false">Kuota Pergub</a>
                <a href="#pemasok" class="py-2" @click="buka = false">Untuk pemasok</a>
                @guest
                    <a href="{{ route('login') }}" class="py-2">Masuk</a>
                    <a href="{{ route('register') }}" class="py-2">Daftarkan usaha</a>
                @endguest
            </div>
        </nav>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl gap-14 px-4 pt-8 pb-20 sm:px-8 lg:grid-cols-12 lg:gap-10 lg:pt-14 lg:pb-28">
            <div class="lg:col-span-7">
                <h1 class="font-display text-[clamp(3.1rem,8.2vw,7.6rem)] leading-[0.92] text-daun-tua">
                    Berapa persen dapur Anda sudah Bali?
                </h1>
                <p class="mt-8 max-w-xl text-lg leading-relaxed text-tinta/80">
                    Foto nota belanja dari pasar, toko, atau pemasok. Pasok Bali membaca istilah seperti <em>kesuna</em> dan <em>taluh</em>, menghitung kuota Pergub Bali No. 99/2018 per kelompok, lalu mempertemukan Anda dengan petani, peternak, dan nelayan Bali.
                </p>
                <div class="mt-10 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-terakota px-7 py-4 text-base font-semibold text-kertas hover:bg-tinta">Buka dasbor</a>
                    @else
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded-full bg-terakota px-7 py-4 text-base font-semibold text-kertas hover:bg-tinta">Coba dengan dapur contoh</button>
                        </form>
                        <a href="{{ route('register') }}" class="rounded-full border border-tinta/25 px-7 py-4 text-base font-semibold hover:border-tinta">Daftarkan usaha</a>
                    @endauth
                </div>
                @error('demo')
                    <p class="mt-4 text-sm text-terakota" role="alert">{{ $message }}</p>
                @enderror
                <p class="mt-5 text-sm text-abu">Demo tanpa daftar, berisi data fiktif, terhapus sendiri setelah 48 jam.</p>
            </div>

            <div class="relative lg:col-span-5 lg:pt-6">
                <div class="nota-pasar mx-auto max-w-md -rotate-[1.5deg] rounded-[3px] px-6 pt-6 pb-8 sm:px-8">
                    <div class="flex items-baseline justify-between border-b-2 border-dashed border-tinta/20 pb-3">
                        <p class="font-display text-2xl">Dagang Men Sari</p>
                        <p class="text-xs text-abu">contoh fiktif</p>
                    </div>
                    <ol class="mt-1">
                        @foreach ($nota as $i => [$istilah, $jumlah, $produk, $kelompok])
                            <li class="nota-baris animate-baca grid grid-cols-[1fr_auto] items-baseline gap-x-3 py-[0.45rem] px-1" style="animation-delay: {{ 400 + $i * 260 }}ms">
                                <span class="font-display text-xl leading-snug">{{ $istilah }} <span class="font-sans text-sm text-abu">{{ $jumlah }}</span></span>
                                <span class="text-right text-xs leading-tight text-tinta/70">{{ $produk }}<br><span class="text-daun">{{ $kelompok }}</span></span>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="relative mx-auto -mt-4 max-w-md rotate-[1deg] bg-daun-tua px-6 py-5 text-kertas shadow-[var(--shadow-angkat)] sm:px-8">
                    <p class="text-sm text-kertas/70">Bulan ini, produk lokal Bali</p>
                    <dl class="mt-3 space-y-3">
                        @foreach ([['Pertanian', 42, 30], ['Peternakan', 18, 30]] as $j => [$nama, $persen, $batas])
                            <div>
                                <div class="flex items-baseline justify-between">
                                    <dt class="text-sm">{{ $nama }}</dt>
                                    <dd class="font-display text-2xl {{ $persen >= $batas ? 'text-kunyit-muda' : 'text-[#f2a49c]' }}">{{ $persen }}%</dd>
                                </div>
                                <div class="relative mt-1.5 h-1.5 bg-kertas/15">
                                    <div class="animate-isi absolute inset-y-0 left-0 origin-left {{ $persen >= $batas ? 'bg-kunyit' : 'bg-[#e0685c]' }}" style="width: {{ $persen }}%; animation-delay: {{ 2100 + $j * 200 }}ms"></div>
                                    <div class="absolute -inset-y-1 w-px bg-kertas" style="left: {{ $batas }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                    <p class="mt-4 text-xs text-kertas/60">Garis putih adalah kuota minimal 30%.</p>
                </div>
            </div>
        </section>

        <section id="masalah" class="border-t border-garis bg-kertas">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-8 lg:grid-cols-12 lg:py-28">
                <p class="font-display text-[clamp(2rem,3.6vw,3.2rem)] leading-[1.08] lg:col-span-7">
                    Pergub Bali No. 99/2018 berlaku sejak 2019. Masalahnya, nota dapur ditulis dalam bahasa pasar, bukan dalam persen.
                </p>
                <ul class="divide-y divide-garis border-y border-garis lg:col-span-5 lg:self-end">
                    <li class="py-5">
                        <p class="font-semibold">Nota tidak pernah seragam</p>
                        <p class="mt-1 leading-relaxed text-abu">Struk kasir, tulisan tangan kios pasar pagi, dan faktur mingguan pemasok bercampur jadi satu.</p>
                    </li>
                    <li class="py-5">
                        <p class="font-semibold">Satuannya macam-macam</p>
                        <p class="mt-1 leading-relaxed text-abu">Ikat, tray, karung, ekor. Kuota dihitung dari volume, jadi semuanya harus diseragamkan dulu.</p>
                    </li>
                    <li class="py-5">
                        <p class="font-semibold">Tahu kurang, tapi tidak tahu harus beli ke siapa</p>
                        <p class="mt-1 leading-relaxed text-abu">Mencari peternak atau nelayan lokal yang sanggup memasok tiap minggu butuh waktu dan kenalan.</p>
                    </li>
                </ul>
            </div>
        </section>

        <section id="cara-kerja" class="mx-auto max-w-7xl px-4 py-20 sm:px-8 lg:py-28">
            <h2 class="max-w-2xl font-display text-[clamp(2.2rem,4.4vw,3.8rem)] leading-[1.02] text-daun-tua">Dari selembar nota ke pemasok lokal, dalam tiga langkah.</h2>
            <ol class="mt-14 border-t border-tinta/80">
                @foreach ([
                    ['Foto notanya', 'Unggah foto nota cetak, tulisan tangan, atau faktur. AI membaca nama pemasok, tanggal, barang, jumlah, satuan, dan harga. Anda memeriksa dan mengoreksi sebelum disimpan.'],
                    ['Lihat persennya', 'Istilah pasar dicocokkan ke kamus produk, satuan diubah ke kilogram, lalu persentase lokal dihitung per kelompok. Tiap angka diberi label bukti: terverifikasi, pernyataan pemasok, atau belum diketahui.'],
                    ['Hubungi pemasok Bali', 'Untuk kelompok yang masih kurang, Pasok Bali menunjukkan pemasok lokal yang cocok, berapa persen yang bisa naik, dan menyiapkan pesan WhatsApp yang tinggal dikirim.'],
                ] as $n => [$judul, $isi])
                    <li class="grid gap-4 border-b border-garis py-9 sm:grid-cols-[7rem_1fr] lg:grid-cols-[10rem_22rem_1fr] lg:gap-10">
                        <span class="font-display text-6xl leading-none text-terakota lg:text-7xl">{{ $n + 1 }}</span>
                        <h3 class="font-display text-3xl leading-tight">{{ $judul }}</h3>
                        <p class="max-w-xl text-lg leading-relaxed text-tinta/75 sm:col-start-2 lg:col-start-auto">{{ $isi }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        <section id="kamus" class="bg-tinta text-kertas">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-8 lg:py-28">
                <div class="grid gap-8 lg:grid-cols-12">
                    <h2 class="font-display text-[clamp(2.2rem,4.4vw,3.8rem)] leading-[1.02] lg:col-span-6">Paham bahasa pasar Bali.</h2>
                    <p class="max-w-lg text-lg leading-relaxed text-kertas/70 lg:col-span-5 lg:col-start-8 lg:self-end">
                        Kamus produk sudah mengenal istilah Bali dan singkatan nota. Kalau Anda mengoreksi satu istilah, Pasok Bali mengingatnya untuk nota berikutnya.
                    </p>
                </div>
                <dl class="mt-14 grid grid-cols-2 border-t border-kertas/20 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($kamus as [$bali, $arti])
                        <div class="border-b border-kertas/20 py-6 pr-4">
                            <dt class="font-display text-[clamp(1.6rem,2.6vw,2.4rem)] leading-none text-kunyit">{{ $bali }}</dt>
                            <dd class="mt-2 text-sm text-kertas/65">{{ $arti }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        <section id="kuota" class="mx-auto max-w-7xl px-4 py-20 sm:px-8 lg:py-28">
            <div class="grid gap-8 lg:grid-cols-12">
                <h2 class="font-display text-[clamp(2.2rem,4.4vw,3.8rem)] leading-[1.02] text-daun-tua lg:col-span-6">Empat kelompok yang dihitung.</h2>
                <p class="max-w-lg text-lg leading-relaxed text-tinta/75 lg:col-span-5 lg:col-start-8 lg:self-end">
                    Persentase dihitung dari volume, bukan dari rupiah. Angka kuota bisa diatur admin dan selalu perlu dicocokkan dengan teks resmi Pergub Bali No. 99 Tahun 2018.
                </p>
            </div>
            <div class="mt-14 grid border-l border-garis sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($kuota as [$nama, $isi, $min])
                    <div class="border-y border-r border-garis bg-kertas p-7 sm:-mt-px">
                        <p class="font-display text-7xl leading-none text-terakota">{{ $min }}<span class="text-4xl">%</span></p>
                        <h3 class="mt-6 text-lg font-semibold">{{ $nama }}</h3>
                        <p class="mt-2 leading-relaxed text-abu">{{ $isi }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 grid gap-6 lg:grid-cols-12">
                <p class="font-display text-3xl leading-tight lg:col-span-4">Tiap angka jujur soal buktinya.</p>
                <ul class="grid gap-4 sm:grid-cols-3 lg:col-span-8">
                    <li class="border-t-4 border-daun pt-4">
                        <p class="font-semibold">Terverifikasi</p>
                        <p class="mt-1 text-sm leading-relaxed text-abu">Pemasok sudah diperiksa admin sebagai produsen di Bali.</p>
                    </li>
                    <li class="border-t-4 border-kunyit pt-4">
                        <p class="font-semibold">Pernyataan pemasok</p>
                        <p class="mt-1 text-sm leading-relaxed text-abu">Pemasok menyatakan sendiri produknya dari Bali lewat WhatsApp.</p>
                    </li>
                    <li class="border-t-4 border-abu/50 pt-4">
                        <p class="font-semibold">Belum diketahui</p>
                        <p class="mt-1 text-sm leading-relaxed text-abu">Asal barang belum jelas dan tidak dihitung sebagai lokal.</p>
                    </li>
                </ul>
            </div>
        </section>

        <section id="pemasok" class="grid lg:grid-cols-2">
            <div class="bg-kertas px-4 py-20 sm:px-8 lg:py-28 lg:pl-[max(2rem,calc((100vw-80rem)/2+2rem))] lg:pr-16">
                <h2 class="font-display text-[clamp(2rem,3.6vw,3.2rem)] leading-[1.05] text-daun-tua">Untuk hotel, restoran, kafe, villa, dan katering</h2>
                <ul class="mt-10 space-y-5 text-lg leading-relaxed text-tinta/80">
                    <li class="border-l-2 border-terakota pl-5">Laporan bulanan per kelompok, siap dicetak atau disimpan sebagai PDF.</li>
                    <li class="border-l-2 border-terakota pl-5">Data usaha Anda terpisah dari usaha lain, dan foto nota tersimpan privat.</li>
                    <li class="border-l-2 border-terakota pl-5">Tetap bisa dipakai tanpa AI: isian manual selalu tersedia.</li>
                </ul>
            </div>
            <div class="papan-daun px-4 py-20 sm:px-8 lg:py-28 lg:pr-[max(2rem,calc((100vw-80rem)/2+2rem))] lg:pl-16">
                <h2 class="font-display text-[clamp(2rem,3.6vw,3.2rem)] leading-[1.05]">Untuk petani, peternak, dan nelayan Bali</h2>
                <p class="mt-6 max-w-md text-lg leading-relaxed text-kertas/75">Cukup WhatsApp, tanpa memasang aplikasi. Kirim satu pesan untuk mendaftar, lalu balas 1 atau 2 saat ada permintaan pasokan.</p>
                <div class="mt-10 max-w-md space-y-3 text-[0.95rem]" aria-label="Contoh percakapan WhatsApp, fiktif">
                    <p class="ml-auto w-fit max-w-[85%] rounded-2xl rounded-br-sm bg-kunyit-muda px-4 py-3 text-tinta">DAFTAR bayam 30 ikat seminggu Tabanan</p>
                    <p class="w-fit max-w-[85%] rounded-2xl rounded-bl-sm bg-kertas/10 px-4 py-3">Data Anda: Bayam, 30 ikat per minggu, Tabanan. Balas YA bila benar.</p>
                    <p class="ml-auto w-fit rounded-2xl rounded-br-sm bg-kunyit-muda px-4 py-3 text-tinta">YA</p>
                </div>
                <p class="mt-4 text-xs text-kertas/55">Contoh percakapan, bisa dicoba di Simulator WA pada akun demo.</p>
            </div>
        </section>

        <div class="poleng h-7" aria-hidden="true"></div>

        <section class="bg-terakota text-kertas">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-20 sm:px-8 lg:flex-row lg:items-end lg:justify-between lg:py-24">
                <h2 class="max-w-3xl font-display text-[clamp(2.4rem,5vw,4.6rem)] leading-[1]">Coba dulu dengan dapur contoh. Tanpa daftar.</h2>
                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-kertas px-7 py-4 font-semibold text-terakota hover:bg-kunyit-muda">Buka dasbor</a>
                    @else
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button class="rounded-full bg-kertas px-7 py-4 font-semibold text-terakota hover:bg-kunyit-muda">Coba demo</button>
                        </form>
                        <a href="{{ route('register') }}" class="rounded-full border border-kertas/40 px-7 py-4 font-semibold hover:border-kertas">Daftarkan usaha</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-12 text-sm text-abu sm:px-8 md:flex-row md:items-center md:justify-between">
        <x-ui.logo :size="32" />
        <p class="max-w-xl leading-relaxed">Alat ukur mandiri untuk usaha, bukan alat pengawasan pemerintah. Peragaan Bali DigiFest 2026; semua contoh usaha, nota, dan pemasok adalah data fiktif.</p>
        <div class="flex gap-5">
            <a href="{{ route('login') }}" class="hover:text-tinta">Masuk</a>
            <a href="{{ route('register') }}" class="hover:text-tinta">Daftar</a>
        </div>
    </footer>
</body>
</html>
