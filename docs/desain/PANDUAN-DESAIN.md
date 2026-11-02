# Panduan Desain Pasok Bali (Gaya Pigmen Kamasan)

Dokumen ini menjadi acuan visual dan komponen antarmuka **Pasok Bali** (Bali DigiFest 2026 — *AI Web Innovation Challenge*). Arah visual mengangkat **palet pigmen lukisan klasik Kamasan (Klungkung, Bali)** yang berpadu dengan garis tinta hitam tegas (`2px`), sudut siku/tegas (`0.25rem`), blok warna bidang datar tanpa bayangan kabur, serta tipografi variabel yang dipadatkan.

---

## 1. Prinsip Utama & Larangan Desain

### Prinsip Utama
1. **Pigmen Lukisan Kamasan & Bidang Warna Berani:** Mengangkat warna pigmen mineral/alami khas lukisan wayang Kamasan—kuning *atal* (`kunyit`), merah oker *pere* (`terakota`), biru nila *taum* (`taum`), jelaga *mangsi* (`tinta`), hijau daun (`daun`), dan kain blacu/ulon (`santan` & `kertas`). Halaman dibuka langsung di atas bidang kuning *atal* (`bg-kunyit`), bukan latar putih/krem pucat.
2. **Garis Kontur Mangsi 2px (`border-2 border-tinta`):** Sebagaimana lukisan Kamasan membingkai setiap figur dengan garis kontur tinta hitam (*nyawi*), seluruh kartu, nota, pita blok kuota, dan kotak input dibingkai dengan garis `2px solid var(--color-tinta)` tanpa bayangan lembut (*drop shadow* kabur).
3. **Sudut Tegas (`0.25rem` / `rounded`):** Kartu dan tombol memakai radius kecil yang tegas (`--radius-kartu: 0.25rem`, `--radius-tombol: 0.25rem`), bukan kartu membulat besar (`rounded-2xl`) atau tombol pil (`rounded-full`).
4. **Tipografi Display Padat & Kontras Ukuran Ekstrem:** Judul menggunakan `Bricolage Grotesque Variable` yang dipadatkan (`font-stretch: 78%`, `font-weight: 760`, `letter-spacing: -0.025em`) dengan ukuran besar dan rapat (`leading-[0.84]` hingga `leading-[0.92]`).
5. **Satu Interaksi Khas di Halaman Depan:** Komponen **Nota Dagang Men Sari** yang interaktif di halaman depan: pengunjung dapat mengetuk baris istilah pasar Bali (*kesuna*, *bawang barak*, *tabia*, *taluh*, *be siap*, *be tongkol*, *uyah*, *migor*) dan melihat 4 blok kuota Pergub di bawahnya bergerak secara langsung.
6. **Mandiri Tanpa CDN:** Seluruh berkas huruf (`.woff2`) dan aset SVG dibundel lokal di dalam repositori.

### Larangan Visual (Wajib Dihindari)
- **DILARANG memakai klise "krem hangat + huruf serif editorial"** (seperti Gloock/Playfair di atas latar krem pucat) yang terasa seperti templat generik AI.
- **DILARANG memakai kartu membulat besar (`rounded-xl` / `rounded-2xl`) dengan bayangan abu-abu lembut (`shadow-md` / `shadow-lg`).** Gunakan `rounded` dan `border-2 border-tinta`.
- **DILARANG memakai label huruf kapital kecil di atas judul** (`uppercase tracking-widest text-xs` atau *eyebrow badge* pil). Gunakan kalimat biasa (*sentence case*).
- **DILARANG menaruh ikon panah dekoratif di dalam tombol utama** (seperti `Coba demo →` atau ikon SVG panah di samping teks tombol). Tulis label tombol secara lugas: `Coba demo`, `Coba dengan dapur contoh`, `Daftarkan usaha`.

---

## 2. Palet Warna Pigmen Kamasan (`resources/css/app.css`)

Seluruh token didefinisikan pada blok `@theme` di `resources/css/app.css`:

| Token CSS / Kelas Tailwind | Nilai Hex | Pigmen Kamasan & Makna Visual | Kegunaan Utama |
|---|---|---|---|
| `--color-kunyit` (`bg-kunyit`) | `#e0a526` | **Atal** (kuning batu oker/kunyit) | Latar panggung pembuka (hero landing), penanda pernyataan pemasok, pita navigasi aktif |
| `--color-kunyit-muda` (`bg-kunyit-muda`) | `#f8e4ad` | **Atal Muda** | Sorotan baris nota saat disentuh/dibaca, kartu rekomendasi, pita pemberitahuan mode demo |
| `--color-terakota` (`bg-terakota`, `text-terakota`) | `#c2381f` | **Pere / Gincu** (merah tanah/sinabar) | Blok kuota Peternakan, pita penutup ajakan (CTA), status kurang kuota, barang luar Bali |
| `--color-terakota-muda` (`bg-terakota-muda`) | `#f6d8cd` | **Pere Muda** | Latar pesan kekurangan kuota (`gap_kg`), badge luar Bali, dan kotak peringatan kesalahan |
| `--color-taum` (`bg-taum`, `text-taum`) | `#24305b` | **Taum** (biru nila/indigo daun taum) | Blok kuota Perikanan, bagian pemasok WhatsApp, tombol aksi sekunder (`Baca semua`), hover tombol utama, outline fokus |
| `--color-taum-muda` (`bg-taum-muda`) | `#dde1ef` | **Taum Muda** | Permukaan aksen biru nila lembut dan pembagi halus |
| `--color-daun` (`bg-daun`, `text-daun`) | `#3b6a33` | **Hijau Daun / Gadang** (campuran atal & taum) | Blok kuota Pertanian, bar progres lokal terverifikasi, badge status patuh |
| `--color-daun-tua` (`bg-daun-tua`, `text-daun-tua`) | `#23401e` | **Hijau Tua** | Judul halaman dalam aplikasi dan teks status patuh di atas latar terang |
| `--color-daun-muda` (`bg-daun-muda`) | `#dfead0` | **Hijau Muda** | Latar notifikasi berhasil dan pesan masuk pada Simulator WA |
| `--color-tinta` (`bg-tinta`, `text-tinta`, `border-tinta`) | `#1a1612` | **Mangsi** (hitam jelaga lampu minyak) | Teks utama, garis kontur `2px` pada seluruh kartu/tabel/tombol, tombol aksi utama, blok kuota Industri lokal |
| `--color-kertas` (`bg-kertas`, `text-kertas`) | `#fffcf5` | **Kain Ulon / Kapur** (putih gading terang) | Permukaan kertas nota (`.nota-pasar`), kartu konten, tabel, dan teks di atas latar gelap |
| `--color-santan` (`bg-santan`) | `#f2ece0` | **Kain Blacu Mentah** | Latar dasar halaman tubuh (`<body>`) dan latar trek meter kuota |
| `--color-abu` (`text-abu`) | `#675e51` | **Abu Tanah Liat** | Teks sekunder, keterangan satuan, dan catatan kaki |
| `--color-garis` (`border-garis`, `bg-garis`) | `#d6cbb7` | **Garis Tenun** | Garis pembagi baris internal tabel dan penanda asal barang yang belum diketahui |

---

## 3. Tipografi Lokal (`resources/css/app.css`)

Seluruh huruf dimuat secara lokal dari paket `@fontsource-variable` (tanpa CDN eksternal):

1. **Huruf Judul & Angka (`--font-display` / `.font-display`):**
   - Keluarga: `'Bricolage Grotesque Variable'`, dibundel lewat `@import '@fontsource-variable/bricolage-grotesque/standard.css'`.
   - Pengaturan kustom di `.font-display`:
     ```css
     .font-display {
         font-stretch: 78%;
         font-weight: 760;
         letter-spacing: -0.025em;
     }
     ```
   - Dipakai pada judul hero (`text-[clamp(3.6rem,10vw,9.5rem)] leading-[0.84]`), judul bagian (`text-[clamp(2.6rem,5.5vw,4.8rem)] leading-[0.92]`), nama istilah Bali di nota (`font-display text-xl`), serta angka persentase kuota (`font-display text-7xl leading-none angka-tabel`).
2. **Huruf Bacaan & Antarmuka (`--font-sans` / `font-sans`):**
   - Keluarga: `'Plus Jakarta Sans'` (`resources/css/fonts/fonts.css`).
   - Dipakai untuk paragraf penjelasan, tabel nota, formulir koreksi, dan percakapan WhatsApp.
3. **Utilitas Angka Tabel (`angka-tabel`):**
   - Menerapkan `font-variant-numeric: tabular-nums` agar angka berat (`kg`), harga (`Rp`), dan persentase (`%`) sejajar lurus secara vertikal.

---

## 4. Garis Kontur, Sudut, dan Utilitas Khas

Didefinisikan di `resources/css/app.css`:

- **Sudut Tegas:**
  - `--radius-kartu: 0.25rem` (`4px` / kelas `rounded`)
  - `--radius-tombol: 0.25rem` (`4px` / kelas `rounded`)
- **Garis Kontur Pengganti Bayangan:**
  - `--shadow-kartu: 0 0 0 1.5px var(--color-tinta)`
  - `--shadow-angkat: 0 0 0 2px var(--color-tinta)`
  - `@utility garis-tebal` → `border: 2px solid var(--color-tinta)`
- **Kelas Komponen CSS:**
  - `.nota-pasar`: `background-color: var(--color-kertas); border: 2px solid var(--color-tinta);`
  - `.papan-daun`: `background-color: var(--color-taum); color: var(--color-kertas);`
  - `@utility tridatu`: pita tiga jalur vertikal (`terakota`, `kertas`, `tinta`) setinggi `h-1` atau `h-2` sebagai pembatas header/footer.
- **Fokus Keyboard:**
  - `:focus-visible` menggunakan `outline: 3px solid var(--color-taum); outline-offset: 2px;`.

---

## 5. Komponen Blade Dasar (`resources/views/components/ui/`)

> **Catatan:** Komponen `<x-ui.logo>` (`resources/views/components/ui/logo.blade.php`) menampilkan ikon lumbung padi Bali (*jineng*) beserta teks `Pasok Bali` dan tidak boleh diubah strukturnya.

### 5.1. Tombol (`<x-ui.button>`)
File: `resources/views/components/ui/button.blade.php`

Menggunakan sudut `rounded`, teks `font-semibold`, tanpa bayangan, dan tanpa ikon panah.

- **Props:**
  - `variant`: `'primary'` (bawaan: `bg-tinta text-kertas hover:bg-taum`), `'daun'` (`bg-daun text-kertas hover:bg-daun-tua`), `'taum'` (`bg-taum text-kertas hover:bg-tinta`), `'kunyit'` (`border-2 border-tinta bg-kunyit text-tinta hover:bg-kunyit-muda`), `'secondary'` / `'outline'` / `'garis'` (`border-2 border-tinta bg-transparent text-tinta hover:bg-tinta hover:text-kertas`), `'subtle'` / `'halus'` (`border-2 border-tinta bg-kertas text-tinta hover:bg-santan`), `'terakota'` (`bg-terakota text-kertas hover:bg-tinta`), `'ghost'`.
  - `size`: `'sm'` (`px-3.5 py-2 text-xs`), `'md'` (bawaan `px-5 py-3 text-sm`), `'lg'` (`px-7 py-4 text-base`).
  - `href`: bila diisi akan merender elemen `<a>`, bila kosong merender `<button>`.

**Contoh pemakaian:**
```blade
<form method="POST" action="{{ route('demo') }}">
    @csrf
    <x-ui.button type="submit" variant="primary" size="lg">
        Coba dengan dapur contoh
    </x-ui.button>
</form>

<x-ui.button :href="route('register')" variant="secondary" size="lg">
    Daftarkan usaha
</x-ui.button>
```

---

### 5.2. Kartu (`<x-ui.card>`)
File: `resources/views/components/ui/card.blade.php`

Seluruh varian kartu menggunakan sudut `rounded` (`0.25rem`), bingkai `border-2 border-tinta`, dan tanpa bayangan kabur.

- **Props:**
  - `variant`: `'default'` (`bg-kertas text-tinta`), `'santan'` (`bg-santan text-tinta`), `'kunyit'` / `'sorot'` (`bg-kunyit-muda text-tinta`), `'terakota'` (`bg-terakota-muda text-tinta`), `'daun'` (`bg-daun text-kertas`), `'taum'` / `'papan'` (`bg-taum text-kertas`), `'nota'` (`nota-pasar text-tinta`).
  - `padding`: `'none'`, `'sm'` (`p-4`), `'md'` (`p-5 sm:p-6`), `'lg'` (`p-6 sm:p-8`).
  - `as`: `'div'`, `'article'`, `'section'`, `'aside'`, `'li'`.

**Contoh pemakaian:**
```blade
<x-ui.card variant="kunyit" padding="md" as="article">
    <p class="text-sm font-semibold text-abu">Peternakan</p>
    <p class="mt-1 font-display text-lg">Telur ayam ras: 30 kg bulan ini belum dari pemasok lokal</p>
</x-ui.card>
```

---

### 5.3. Badge Tingkat Bukti (`<x-ui.evidence-badge>`)
File: `resources/views/components/ui/evidence-badge.blade.php`

Menampilkan status kejelasan asal barang dengan sudut `rounded`, garis `border-2 border-tinta`, dan kotak penanda warna persegi (`size-2 border border-tinta`).

- **Props:**
  - `level`: `'hijau'` (`terverifikasi`), `'kuning'` (`pernyataan`), `'abu'` (`belum_diketahui`), `'luar'`, `'campuran'`.
  - Atau kombinasi `origin` (`'bali'`, `'luar'`, `'campuran'`, `'belum_diketahui'`) dan `evidence` (`'terverifikasi'`, `'pernyataan'`, `'belum_diketahui'`).

**Contoh pemakaian:**
```blade
<x-ui.evidence-badge level="hijau" />
<x-ui.evidence-badge origin="bali" evidence="pernyataan" />
<x-ui.evidence-badge level="abu" />
```

---

### 5.4. Meter Persentase Kuota (`<x-ui.quota-meter>`)
File: `resources/views/components/ui/quota-meter.blade.php`

Menampilkan kartu capaian kuota per kelompok Pergub Bali No. 99/2018 dengan bingkai `rounded border-2 border-tinta bg-kertas p-5`, angka persentase `.font-display`, dan batang progres siku berbingkai `border-2 border-tinta` dua lapis (`bg-daun` untuk lokal terverifikasi dan `bg-kunyit` untuk lokal pernyataan pemasok).

**Contoh pemakaian:**
```blade
<x-ui.quota-meter :group="$group" />
```

---

## 6. Contoh Pola Layout dari Halaman Depan (`landing/index.blade.php`)

### 6.1. Pita 4 Blok Kuota Berbingkai Mangsi
Empat kelompok kuota Pergub ditampilkan sebagai grid bidang warna penuh (`bg-daun`, `bg-terakota`, `bg-taum`, `bg-tinta`) yang dibatasi garis `border-tinta` `2px`:

```blade
<div class="grid border-y-2 border-tinta sm:grid-cols-2 lg:grid-cols-4" aria-live="polite">
    <div class="bg-daun border-tinta px-5 py-7 text-kertas sm:px-8 lg:border-r-2">
        <div class="flex items-baseline justify-between gap-3">
            <h2 class="font-semibold">Pertanian</h2>
            <p class="text-sm text-kertas/75">kuota 30%</p>
        </div>
        <p class="mt-3 font-display text-7xl leading-none angka-tabel">67%</p>
    </div>
</div>
```

### 6.2. Daftar Tingkat Bukti dengan Balok Warna Samping
Pada bagian `#bukti`, setiap tingkat bukti menggunakan kotak warna vertikal di sisi kiri (`grid-cols-[3.5rem_1fr] border-2 border-tinta bg-kertas`):

```blade
<li class="grid grid-cols-[3.5rem_1fr] border-2 border-tinta bg-kertas">
    <span class="bg-daun border-r-2 border-tinta" aria-hidden="true"></span>
    <div class="px-5 py-4">
        <p class="font-semibold">Terverifikasi</p>
        <p class="mt-1 text-tinta/75">Pemasok sudah diperiksa admin sebagai produsen di Bali.</p>
    </div>
</li>
```
