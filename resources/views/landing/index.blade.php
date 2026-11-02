<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Ukur sendiri kepatuhan Pergub Bali No. 99/2018 dari foto nota belanja dapur dengan AI, lalu terhubung langsung dengan petani, peternak, dan nelayan lokal Bali lewat WhatsApp.">
    <title>Pasok Bali · Ukur serapan produk lokal Bali dari nota belanja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-santan font-sans text-tinta antialiased selection:bg-kunyit selection:text-tinta">
    <div class="border-b border-garis bg-kunyit-muda px-4 py-2 text-center text-xs sm:text-sm font-medium text-tinta">
        Peragaan <strong>Bali DigiFest 2026</strong> · Seluruh contoh nota, usaha, dan pemasok di mode demo adalah <strong>data fiktif</strong>.
    </div>

    <header class="sticky top-0 z-30 border-b border-garis bg-santan/95 backdrop-blur-sm" x-data="{ menuTerbuka: false }">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3.5">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-daun text-kunyit shadow-xs">
                    <svg viewBox="0 0 28 28" fill="none" class="size-6" aria-hidden="true">
                        <path d="M6 21C5.5 12.5 11.2 6 22 5C21.5 15.2 15.8 21.5 6 21Z" fill="currentColor" fill-opacity="0.25" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M6 21C11 16 16 11 22 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="19" cy="19" r="3" fill="#FEF0C2"/>
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block font-display text-xl font-extrabold text-daun-tua">Pasok Bali</span>
                    <span class="block text-xs text-abu">Kepatuhan Pergub Bali No. 99/2018</span>
                </span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-semibold text-abu md:flex" aria-label="Navigasi halaman depan">
                <a href="#masalah" class="transition hover:text-tinta">Masalah di dapur</a>
                <a href="#cara-kerja" class="transition hover:text-tinta">Cara kerja</a>
                <a href="#kuota-pergub" class="transition hover:text-tinta">Kuota Pergub</a>
                <a href="#untuk-siapa" class="transition hover:text-tinta">Untuk usaha &amp; pemasok</a>
            </nav>

            <div class="flex items-center gap-2.5">
                @auth
                    <x-ui.button :href="route('dashboard')" variant="primary" size="sm">
                        Buka dasbor
                    </x-ui.button>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-xl px-3 py-2 text-sm font-semibold text-abu transition hover:bg-kertas hover:text-tinta sm:inline-block">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="hidden rounded-xl border-2 border-daun px-4 py-2 text-sm font-bold text-daun transition hover:bg-daun-muda lg:inline-block">
                        Daftarkan usaha
                    </a>
                    <form method="POST" action="{{ route('demo') }}">
                        @csrf
                        <x-ui.button type="submit" variant="primary" size="sm">
                            Coba demo
                        </x-ui.button>
                    </form>
                @endauth

                <button
                    type="button"
                    class="rounded-xl border border-garis bg-kertas px-3 py-2 text-sm font-semibold text-tinta md:hidden"
                    @click="menuTerbuka = !menuTerbuka"
                    :aria-expanded="menuTerbuka"
                    aria-label="Buka menu navigasi"
                >
                    Menu
                </button>
            </div>
        </div>

        <nav class="border-t border-garis bg-kertas px-4 py-3 md:hidden" x-show="menuTerbuka" x-cloak aria-label="Menu seluler">
            <div class="flex flex-col gap-1 text-sm font-semibold">
                <a href="#masalah" @click="menuTerbuka = false" class="rounded-lg px-3 py-2 text-tinta hover:bg-santan">Masalah di dapur</a>
                <a href="#cara-kerja" @click="menuTerbuka = false" class="rounded-lg px-3 py-2 text-tinta hover:bg-santan">Cara kerja</a>
                <a href="#kuota-pergub" @click="menuTerbuka = false" class="rounded-lg px-3 py-2 text-tinta hover:bg-santan">Kuota Pergub</a>
                <a href="#untuk-siapa" @click="menuTerbuka = false" class="rounded-lg px-3 py-2 text-tinta hover:bg-santan">Untuk usaha &amp; pemasok</a>
                @guest
                    <hr class="my-1 border-garis">
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-tinta hover:bg-santan">Masuk ke akun</a>
                    <a href="{{ route('register') }}" class="rounded-lg px-3 py-2 text-daun hover:bg-daun-muda">Daftarkan usaha</a>
                @endguest
            </div>
        </nav>
    </header>

    <main>
        <section class="mx-auto max-w-6xl px-4 pt-8 pb-14 sm:pt-12 sm:pb-20">
            <div class="grid items-start gap-10 lg:grid-cols-12 lg:gap-8">
                <div class="lg:col-span-6 lg:pt-2">
                    <span class="inline-flex items-center gap-2 rounded-full border border-daun/25 bg-daun-muda px-3.5 py-1.5 text-xs sm:text-sm font-semibold text-daun-tua">
                        <span class="size-2 rounded-full bg-daun"></span>
                        Untuk hotel, restoran, kafe, villa, dan katering di Bali
                    </span>

                    <h1 class="mt-4 font-display text-4xl font-black leading-[1.12] tracking-tight text-daun-tua sm:text-5xl">
                        Foto nota belanja dapur. Tahu langsung berapa persen produk lokal Bali Anda.
                    </h1>

                    <p class="mt-4 text-base sm:text-lg leading-relaxed text-abu">
                        Pergub Bali No. 99 Tahun 2018 mewajibkan usaha akomodasi dan kuliner menyerap hasil pertanian, peternakan, perikanan, dan industri lokal Bali. Pasok Bali membaca nota belanja dengan AI, menyeragamkan satuan pasar ke kilogram, lalu menyambungkan dapur Anda dengan pemasok lokal lewat WhatsApp.
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <x-ui.button type="submit" variant="primary" size="lg">
                                Coba demo
                                <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                </svg>
                            </x-ui.button>
                        </form>

                        <x-ui.button :href="route('register')" variant="secondary" size="lg">
                            Daftarkan usaha
                        </x-ui.button>

                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-abu underline-offset-4 hover:text-tinta hover:underline">
                            Sudah punya akun? Masuk
                        </a>
                    </div>

                    @error('demo')
                        <p class="mt-3 rounded-xl border border-terakota/30 bg-terakota-muda px-4 py-2.5 text-sm font-semibold text-terakota" role="alert">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-3 text-xs text-abu">
                        Sekali klik <strong>Coba demo</strong> membuat ruang uji mandiri berisi nota dan pemasok contoh fiktif selama 48 jam — tanpa perlu mengisi formulir pendaftaran.
                    </p>

                    <div class="mt-8 rounded-2xl border border-garis bg-kertas p-4 shadow-[var(--shadow-kartu)]">
                        <p class="text-xs font-semibold text-abu">Mengenali istilah pasar tradisional &amp; mengonversi satuan ke kilogram:</p>
                        <div class="mt-3 grid grid-cols-2 gap-2.5 sm:grid-cols-5">
                            <div class="flex items-center gap-2 rounded-xl bg-santan px-2.5 py-2">
                                <x-ui.commodity-icon name="beras" size="sm" />
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-xs font-bold text-tinta">Beras &amp; umbi</p>
                                    <p class="truncate text-[11px] text-abu">karung → kg</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 rounded-xl bg-santan px-2.5 py-2">
                                <x-ui.commodity-icon name="sayur" size="sm" />
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-xs font-bold text-tinta">Sayur &amp; bumbu</p>
                                    <p class="truncate text-[11px] text-abu">kesuna, ikat → kg</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 rounded-xl bg-santan px-2.5 py-2">
                                <x-ui.commodity-icon name="telur" size="sm" />
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-xs font-bold text-tinta">Telur &amp; daging</p>
                                    <p class="truncate text-[11px] text-abu">taluh, krat → kg</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 rounded-xl bg-santan px-2.5 py-2">
                                <x-ui.commodity-icon name="ikan" size="sm" />
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-xs font-bold text-tinta">Ikan &amp; laut</p>
                                    <p class="truncate text-[11px] text-abu">ekor, keranjang</p>
                                </div>
                            </div>
                            <div class="col-span-2 flex items-center gap-2 rounded-xl bg-santan px-2.5 py-2 sm:col-span-1">
                                <x-ui.commodity-icon name="kopi" size="sm" />
                                <div class="min-w-0 leading-tight">
                                    <p class="truncate text-xs font-bold text-tinta">Kopi &amp; olahan</p>
                                    <p class="truncate text-[11px] text-abu">pack, botol → kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="papan-daun rounded-3xl p-4 sm:p-6 shadow-[var(--shadow-angkat)]">
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-4">
                            <div class="flex items-center gap-2">
                                <span class="size-2.5 rounded-full bg-kunyit"></span>
                                <span class="font-display text-sm font-bold text-kertas">Peragaan alur pembacaan nota &amp; kuota</span>
                            </div>
                            <span class="rounded-full bg-kunyit px-2.5 py-0.5 text-xs font-bold text-tinta">
                                Contoh simulasi fiktif
                            </span>
                        </div>

                        <div class="nota-pasar rounded-2xl border border-garis p-4 sm:p-5 text-tinta">
                            <div class="flex items-start justify-between gap-3 border-b border-dashed border-garis pb-3">
                                <div>
                                    <p class="font-display text-base font-extrabold text-tinta">Nota Kios Pasar Badung (Contoh)</p>
                                    <p class="text-xs text-abu">Ditulis tangan · Dibaca AI menjadi baris terstruktur</p>
                                </div>
                                <span class="rounded-lg bg-daun-muda px-2.5 py-1 text-xs font-bold text-daun-tua">
                                    4 baris terbaca
                                </span>
                            </div>

                            <ul class="mt-3 divide-y divide-garis/70 text-xs sm:text-sm">
                                <li class="flex items-center justify-between gap-2 py-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <x-ui.commodity-icon name="sayur" size="sm" />
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-tinta">“Kesuna Bali” <span class="font-normal text-abu">→ Bawang putih</span></p>
                                            <p class="text-xs text-abu">Hortikultura · 4 ikat diseragamkan ke <strong>2,0 kg</strong></p>
                                        </div>
                                    </div>
                                    <x-ui.evidence-badge origin="bali" evidence="terverifikasi" />
                                </li>
                                <li class="flex items-center justify-between gap-2 py-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <x-ui.commodity-icon name="telur" size="sm" />
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-tinta">“Taluh ayam” <span class="font-normal text-abu">→ Telur ayam ras</span></p>
                                            <p class="text-xs text-abu">Peternakan · 2 krat diseragamkan ke <strong>3,6 kg</strong></p>
                                        </div>
                                    </div>
                                    <x-ui.evidence-badge origin="bali" evidence="pernyataan" />
                                </li>
                                <li class="flex items-center justify-between gap-2 py-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <x-ui.commodity-icon name="ikan" size="sm" />
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-tinta">“Tongkol segar” <span class="font-normal text-abu">→ Ikan tongkol</span></p>
                                            <p class="text-xs text-abu">Perikanan · Nelayan Kedonganan · <strong>6,0 kg</strong></p>
                                        </div>
                                    </div>
                                    <x-ui.evidence-badge origin="bali" evidence="terverifikasi" />
                                </li>
                            </ul>
                        </div>

                        <div class="my-3 flex items-center justify-center gap-2 text-xs font-semibold text-kunyit-muda">
                            <span class="h-px flex-1 bg-kertas/20"></span>
                            <span>Dihitung otomatis ke meter kuota Pergub 99/2018</span>
                            <span class="h-px flex-1 bg-kertas/20"></span>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <x-ui.quota-meter
                                label="Pertanian"
                                commodity="sayur"
                                :percent="42"
                                :verifiedPercent="28"
                                :quota="30"
                                status="patuh"
                                :compact="true"
                            />
                            <x-ui.quota-meter
                                label="Peternakan"
                                commodity="telur"
                                :percent="18"
                                :verifiedPercent="10"
                                :quota="30"
                                status="kurang"
                                :gapKg="12"
                                :compact="true"
                            />
                        </div>

                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-2xl border-2 border-kunyit bg-kunyit-muda px-4 py-3 text-tinta">
                            <div class="min-w-0 flex-1">
                                <p class="font-display text-sm font-bold text-tinta">
                                    Peternakan masih 18% (kurang dari kuota 30%)
                                </p>
                                <p class="text-xs text-tinta/80">
                                    Sistem menyarankan peternak telur lokal terdaftar di Bali &amp; menyiapkan draf pesan WhatsApp.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('demo') }}">
                                @csrf
                                <button type="submit" class="rounded-xl bg-daun px-3.5 py-2 text-xs font-bold text-kertas hover:bg-daun-tua cursor-pointer">
                                    Lihat di demo →
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="masalah" class="border-y border-garis bg-kertas py-14 sm:py-20">
            <div class="mx-auto max-w-6xl px-4">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold text-terakota">Tantangan pengukuran di dapur usaha</p>
                    <h2 class="mt-1 font-display text-3xl font-black leading-tight text-daun-tua sm:text-4xl">
                        Pergub Bali No. 99/2018 sudah berlaku sejak 2019, tetapi kepatuhannya sulit diukur dari hari ke hari.
                    </h2>
                    <p class="mt-3 text-base leading-relaxed text-abu">
                        Banyak hotel, restoran, kafe, dan katering di Bali sebenarnya sudah membeli beras, sayur, telur, ikan, dan kopi dari pasar maupun pemasok sekitar — namun tidak memiliki alat sederhana untuk membuktikan persentase volumenya.
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <x-ui.card variant="santan" as="article">
                        <div class="flex items-center gap-3">
                            <x-ui.commodity-icon name="nota" size="md" />
                            <h3 class="font-display text-lg font-bold text-tinta">Nota belanja tidak seragam</h3>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-abu">
                            Belanja harian dapur datang dalam bentuk struk kasir toko, kuitansi tulisan tangan kios pasar pagi, hingga faktur mingguan. Mengetik ulang semuanya ke tabel bulanan menyita waktu staf.
                        </p>
                    </x-ui.card>

                    <x-ui.card variant="santan" as="article">
                        <div class="flex items-center gap-3">
                            <x-ui.commodity-icon name="telur" size="md" />
                            <h3 class="font-display text-lg font-bold text-tinta">Istilah pasar &amp; satuan beragam</h3>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-abu">
                            Di nota tertulis <em>“kesuna 4 ikat”</em>, <em>“taluh 2 krat”</em>, atau <em>“bw mrh”</em>, sedangkan kuota Pergub dihitung berdasarkan <strong>berat volume (kilogram)</strong> per kelompok komoditas.
                        </p>
                    </x-ui.card>

                    <x-ui.card variant="santan" as="article">
                        <div class="flex items-center gap-3">
                            <x-ui.commodity-icon name="sayur" size="md" />
                            <h3 class="font-display text-lg font-bold text-tinta">Bingung mencari pemasok pengganti</h3>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-abu">
                            Saat mengetahui suatu kelompok produk masih di bawah batas kuota, pengelola belanja sering kesulitan menemukan petani, peternak, atau nelayan lokal Bali yang kapasitas mingguannya cocok.
                        </p>
                    </x-ui.card>
                </div>
            </div>
        </section>

        <section id="cara-kerja" class="mx-auto max-w-6xl px-4 py-14 sm:py-20">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold text-daun">Alur kerja sederhana &amp; terkendali</p>
                <h2 class="mt-1 font-display text-3xl font-black leading-tight text-daun-tua sm:text-4xl">
                    Tiga langkah dari foto nota belanja hingga terhubung dengan pemasok lokal.
                </h2>
                <p class="mt-3 text-base leading-relaxed text-abu">
                    AI membantu mempercepat pembacaan nota dan penyusunan draf pesan, sementara keputusan persetujuan data dan perhitungan persentase kepatuhan tetap transparan di tangan usaha Anda.
                </p>
            </div>

            <ol class="mt-10 grid gap-6 lg:grid-cols-3">
                <x-ui.card as="li" class="flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="grid size-11 place-items-center rounded-2xl bg-kunyit font-display text-xl font-black text-tinta">
                                1
                            </span>
                            <span class="rounded-full bg-santan px-3 py-1 text-xs font-semibold text-abu ring-1 ring-garis">
                                Pembaca nota AI + koreksi manusia
                            </span>
                        </div>
                        <h3 class="mt-5 font-display text-xl font-extrabold text-daun-tua">
                            Foto nota belanja dapur
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-abu">
                            Unggah foto nota cetak, kuitansi tulisan tangan kios pasar, atau faktur pemasok. AI mengekstrak nama pemasok, tanggal, serta daftar barang beserta jumlah, satuan, dan harganya ke layar pemeriksaan.
                        </p>
                    </div>
                    <div class="mt-5 rounded-xl bg-santan p-3.5 text-xs text-abu">
                        <strong class="text-tinta">Tetap jalan walau tanpa AI:</strong> Jika foto buram atau Anda memilih pengisian manual, formulir koreksi tetap siap dipakai kapan saja.
                    </div>
                </x-ui.card>

                <x-ui.card as="li" class="flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="grid size-11 place-items-center rounded-2xl bg-kunyit font-display text-xl font-black text-tinta">
                                2
                            </span>
                            <span class="rounded-full bg-santan px-3 py-1 text-xs font-semibold text-abu ring-1 ring-garis">
                                Kamus produk Bali &amp; konversi kg
                            </span>
                        </div>
                        <h3 class="mt-5 font-display text-xl font-extrabold text-daun-tua">
                            Hitung kuota &amp; tingkat bukti
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-abu">
                            Istilah lokal (seperti <em>kesuna</em>, <em>bawang barak</em>, <em>taluh</em>) dicocokkan ke kamus produk, satuan pasar (<em>krat</em>, <em>ikat</em>, <em>tray</em>, <em>ekor</em>) dikonversi ke kilogram, lalu dihitung persentase lokal per kelompok Pergub.
                        </p>
                    </div>
                    <div class="mt-5 rounded-xl bg-santan p-3.5 text-xs text-abu">
                        <strong class="text-tinta">Belajar dari koreksi Anda:</strong> Istilah barang baru yang Anda setujui disimpan khusus untuk usaha Anda agar nota berikutnya langsung dikenali.
                    </div>
                </x-ui.card>

                <x-ui.card as="li" class="flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="grid size-11 place-items-center rounded-2xl bg-kunyit font-display text-xl font-black text-tinta">
                                3
                            </span>
                            <span class="rounded-full bg-santan px-3 py-1 text-xs font-semibold text-abu ring-1 ring-garis">
                                Simulasi kuota &amp; WhatsApp
                            </span>
                        </div>
                        <h3 class="mt-5 font-display text-xl font-extrabold text-daun-tua">
                            Sambungkan pemasok lokal Bali
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-abu">
                            Untuk kelompok yang belum mencapai batas kuota, Pasok Bali menampilkan petani, peternak, dan nelayan lokal beserta simulasi kenaikan persentasenya jika Anda mengalihkan pasokan mingguan.
                        </p>
                    </div>
                    <div class="mt-5 rounded-xl bg-santan p-3.5 text-xs text-abu">
                        <strong class="text-tinta">Langsung ke WhatsApp:</strong> Pesan permintaan pasokan dikirim ke nomor WhatsApp pemasok dan dapat dijawab cukup dengan mengetik angka <strong>1</strong> atau <strong>2</strong>.
                    </div>
                </x-ui.card>
            </ol>
        </section>

        <section id="kuota-pergub" class="border-y border-garis bg-kertas py-14 sm:py-20">
            <div class="mx-auto max-w-6xl px-4">
                <div class="grid items-end justify-between gap-6 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        <p class="text-sm font-semibold text-daun">Acuan pengukuran mandiri</p>
                        <h2 class="mt-1 font-display text-3xl font-black leading-tight text-daun-tua sm:text-4xl">
                            Empat kelompok komoditas yang diukur sesuai Pergub Bali No. 99 Tahun 2018
                        </h2>
                    </div>
                    <p class="text-sm leading-relaxed text-abu lg:col-span-5">
                        Batas kuota disimpan di pengaturan sistem (bukan angka mati di dalam kode) agar dapat disesuaikan dengan teks resmi JDIH Provinsi Bali maupun aturan turunannya.
                    </p>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-ui.card variant="santan" as="article" class="flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <x-ui.commodity-icon name="beras" size="md" />
                                    <x-ui.commodity-icon name="sayur" size="md" />
                                </div>
                                <span class="rounded-full bg-daun-muda px-3 py-1 font-display text-sm font-black text-daun-tua angka-tabel">
                                    Min. 30%
                                </span>
                            </div>
                            <h3 class="mt-4 font-display text-lg font-bold text-tinta">
                                Tanaman pangan, hortikultura &amp; perkebunan
                            </h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-abu">
                                Beras, umbi-umbian, sayuran daun, buah-buahan segar, bumbu dapur (bawang merah, kesuna, cabai), kelapa, hingga biji kopi dan kakao kebun Bali.
                            </p>
                        </div>
                        <p class="mt-4 border-t border-garis pt-2.5 text-xs font-semibold text-daun-tua">
                            Diukur dari volume berat (kg)
                        </p>
                    </x-ui.card>

                    <x-ui.card variant="santan" as="article" class="flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <x-ui.commodity-icon name="telur" size="md" />
                                <span class="rounded-full bg-daun-muda px-3 py-1 font-display text-sm font-black text-daun-tua angka-tabel">
                                    Min. 30%
                                </span>
                            </div>
                            <h3 class="mt-4 font-display text-lg font-bold text-tinta">
                                Peternakan
                            </h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-abu">
                                Telur ayam ras, telur ayam kampung, telur bebek, daging ayam, daging babi, daging sapi Bali, serta hasil peternakan lokal lainnya.
                            </p>
                        </div>
                        <p class="mt-4 border-t border-garis pt-2.5 text-xs font-semibold text-daun-tua">
                            Diukur dari volume berat (kg)
                        </p>
                    </x-ui.card>

                    <x-ui.card variant="santan" as="article" class="flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <x-ui.commodity-icon name="ikan" size="md" />
                                <span class="rounded-full bg-daun-muda px-3 py-1 font-display text-sm font-black text-daun-tua angka-tabel">
                                    Min. 30%
                                </span>
                            </div>
                            <h3 class="mt-4 font-display text-lg font-bold text-tinta">
                                Perikanan
                            </h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-abu">
                                Ikan tangkap laut Bali (tongkol, tuna, kakap, mahi-mahi), cumi, udang, rumput laut, serta ikan air tawar budidaya lokal.
                            </p>
                        </div>
                        <p class="mt-4 border-t border-garis pt-2.5 text-xs font-semibold text-daun-tua">
                            Diukur dari volume berat (kg)
                        </p>
                    </x-ui.card>

                    <x-ui.card variant="santan" as="article" class="flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <x-ui.commodity-icon name="kopi" size="md" />
                                <span class="rounded-full bg-kunyit-muda px-3 py-1 font-display text-sm font-black text-tinta ring-1 ring-kunyit angka-tabel">
                                    Min. 20%*
                                </span>
                            </div>
                            <h3 class="mt-4 font-display text-lg font-bold text-tinta">
                                Industri lokal Bali
                            </h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-abu">
                                Garam tradisional (Amed/Kusamba), gula aren, minyak kelapa, tahu, tempe, kopi sangrai kemasan, dan olahan pangan UMKM Bali.
                            </p>
                        </div>
                        <p class="mt-4 border-t border-garis pt-2.5 text-[11px] text-abu">
                            *Angka kuota mengikuti pengaturan admin; cocokkan selalu dengan teks resmi Pergub Bali No. 99 Tahun 2018.
                        </p>
                    </x-ui.card>
                </div>

                <div class="mt-8 rounded-2xl border border-garis bg-santan p-5 sm:p-6">
                    <div class="grid items-center gap-6 lg:grid-cols-12">
                        <div class="lg:col-span-5">
                            <h3 class="font-display text-xl font-extrabold text-daun-tua">
                                Kejujuran data: setiap angka dilengkapi tingkat bukti
                            </h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-abu">
                                Pasok Bali tidak mengklaim seluruh barang di nota otomatis terverifikasi. Setiap kilogram produk dipisahkan menurut tingkat kejelasan asalnya:
                            </p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3 lg:col-span-7">
                            <div class="rounded-xl border border-garis bg-kertas p-3.5">
                                <x-ui.evidence-badge level="hijau" />
                                <p class="mt-2 text-xs leading-relaxed text-abu">
                                    Dibeli dari pemasok produksi Bali yang lokasi usahanya sudah diperiksa oleh pengelola platform.
                                </p>
                            </div>
                            <div class="rounded-xl border border-garis bg-kertas p-3.5">
                                <x-ui.evidence-badge level="kuning" />
                                <p class="mt-2 text-xs leading-relaxed text-abu">
                                    Berdasarkan pernyataan mandiri pemasok saat mendaftar lewat WhatsApp atau tanda asal pada koreksi nota.
                                </p>
                            </div>
                            <div class="rounded-xl border border-garis bg-kertas p-3.5">
                                <x-ui.evidence-badge level="abu" />
                                <p class="mt-2 text-xs leading-relaxed text-abu">
                                    Nota dari toko umum atau grosir yang belum diketahui apakah barangnya diproduksi di Bali atau luar Bali.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="untuk-siapa" class="mx-auto max-w-6xl px-4 py-14 sm:py-20">
            <div class="grid gap-8 lg:grid-cols-2">
                <x-ui.card padding="lg" as="article" class="flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full bg-daun-muda px-3.5 py-1 text-xs font-bold text-daun-tua">
                                Untuk restoran, hotel, kafe &amp; katering
                            </span>
                            <x-ui.commodity-icon name="nota" size="sm" />
                        </div>

                        <h2 class="mt-4 font-display text-2xl font-black text-daun-tua sm:text-3xl">
                            Rekap belanja lokal tanpa menambah beban pembukuan dapur
                        </h2>

                        <ul class="mt-5 space-y-3 text-sm leading-relaxed text-tinta">
                            <li class="flex items-start gap-3">
                                <span class="mt-1 grid size-5 shrink-0 place-items-center rounded-full bg-daun text-xs font-bold text-kertas">✓</span>
                                <span><strong>Satu tempat untuk semua nota:</strong> Unggah foto nota pasar maupun faktur pemasok, periksa hasil bacaan AI, lalu setujui.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1 grid size-5 shrink-0 place-items-center rounded-full bg-daun text-xs font-bold text-kertas">✓</span>
                                <span><strong>Isolasi data per usaha:</strong> Riwayat nota, harga belanja, dan kamus istilah yang Anda koreksi terkunci rapat hanya untuk akun usaha Anda.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1 grid size-5 shrink-0 place-items-center rounded-full bg-daun text-xs font-bold text-kertas">✓</span>
                                <span><strong>Laporan mandiri siap cetak:</strong> Cetak atau simpan PDF laporan bulanan kepatuhan lengkap dengan rincian berat per pemasok.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-7 flex flex-wrap items-center gap-3 border-t border-garis pt-5">
                        <x-ui.button :href="route('register')" variant="primary">
                            Daftarkan usaha Anda
                        </x-ui.button>
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <x-ui.button type="submit" variant="subtle">
                                Lihat contoh dasbor
                            </x-ui.button>
                        </form>
                    </div>
                </x-ui.card>

                <x-ui.card variant="kunyit" padding="lg" as="article" class="flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full bg-kertas px-3.5 py-1 text-xs font-bold text-tinta ring-1 ring-kunyit">
                                Untuk petani, peternak, nelayan &amp; UMKM Bali
                            </span>
                            <x-ui.commodity-icon name="sayur" size="sm" />
                        </div>

                        <h2 class="mt-4 font-display text-2xl font-black text-tinta sm:text-3xl">
                            Terhubung dengan dapur hotel &amp; restoran cukup lewat WhatsApp
                        </h2>

                        <p class="mt-3 text-sm leading-relaxed text-tinta/85">
                            Pemasok lokal tidak perlu mengunduh aplikasi baru atau membuat akun rumit. Pendaftaran komoditas dan penerimaan pesanan dilakukan langsung dari percakapan WhatsApp biasa:
                        </p>

                        <div class="mt-4 space-y-2.5 rounded-2xl border border-garis bg-kertas p-4 text-xs sm:text-sm">
                            <div class="rounded-xl bg-santan px-3.5 py-2.5">
                                <p class="font-semibold text-abu">1. Pemasok mengirim chat pendaftaran:</p>
                                <p class="mt-0.5 font-mono font-bold text-tinta">DAFTAR telur ayam 120 kg seminggu Tabanan</p>
                            </div>
                            <div class="rounded-xl bg-daun-muda px-3.5 py-2.5 text-daun-tua">
                                <p class="font-semibold">2. Bot merangkum data &amp; meminta konfirmasi:</p>
                                <p class="mt-0.5">Balas <strong>YA</strong> untuk menyimpan ke direktori pemasok lokal Bali.</p>
                            </div>
                            <div class="rounded-xl bg-santan px-3.5 py-2.5">
                                <p class="font-semibold text-abu">3. Saat ada restoran meminta pasokan rutin:</p>
                                <p class="mt-0.5 text-tinta">Pemasok cukup membalas <strong>1</strong> (Terima) atau <strong>2</strong> (Belum bisa) langsung dari WhatsApp.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-kunyit/60 pt-4 text-xs text-tinta/80">
                        <span>Di dalam mode demo tersedia <strong>Simulator WhatsApp</strong> untuk mencoba alur ini secara langsung.</span>
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <button type="submit" class="font-bold text-daun-tua underline hover:text-tinta cursor-pointer">
                                Coba simulator WA →
                            </button>
                        </form>
                    </div>
                </x-ui.card>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 pb-16">
            <div class="papan-daun rounded-3xl p-6 sm:p-10 lg:p-12 shadow-[var(--shadow-angkat)]">
                <div class="grid items-center gap-8 lg:grid-cols-12">
                    <div class="lg:col-span-8">
                        <h2 class="font-display text-3xl font-black leading-tight text-kertas sm:text-4xl">
                            Coba langsung dengan nota contoh di ruang demo mandiri.
                        </h2>
                        <p class="mt-3 max-w-2xl text-base leading-relaxed text-kertas/85">
                            Anda dapat menguji unggah nota contoh, melihat cara AI mengenali istilah pasar Bali, memantau meter kuota Pergub 99/2018, hingga mencoba pengiriman permintaan ke pemasok melalui simulator WhatsApp.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 lg:col-span-4 lg:justify-end">
                        <form method="POST" action="{{ route('demo') }}">
                            @csrf
                            <x-ui.button type="submit" variant="kunyit" size="lg">
                                Coba demo sekarang
                            </x-ui.button>
                        </form>
                        <a href="{{ route('register') }}" class="rounded-xl border-2 border-kertas/40 px-5 py-3.5 text-sm font-bold text-kertas transition hover:border-kertas hover:bg-kertas/10">
                            Daftarkan usaha
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-garis bg-kertas py-8 text-xs text-abu">
        <div class="mx-auto flex max-w-6xl flex-col justify-between gap-4 px-4 sm:flex-row sm:items-center">
            <p>
                <strong class="text-tinta">Pasok Bali</strong> · Alat bantu ukur mandiri kepatuhan Pergub Bali No. 99 Tahun 2018. Laporan aplikasi ini bersifat mandiri dan bukan dokumen pengesahan resmi pemerintah.
            </p>
            <div class="flex items-center gap-4 font-semibold">
                <a href="{{ route('login') }}" class="hover:text-tinta">Masuk</a>
                <a href="{{ route('register') }}" class="hover:text-tinta">Daftar</a>
            </div>
        </div>
    </footer>
</body>
</html>
