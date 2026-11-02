# Panduan Desain Pasok Bali

Dokumen ini menjadi acuan visual dan komponen antarmuka **Pasok Bali** (Bali DigiFest 2026 — *AI Web Innovation Challenge*). Arah visual mengangkat nuansa **pasar tradisional & hasil bumi Bali**: hijau daun pisang, kuning kunyit/janur, terakota bata Bali, dan latar santan hangat, dipadukan dengan estetika **nota belanja pasar** yang rapi dan mudah dipahami pengelola dapur hotel/restoran maupun pemasok lokal.

---

## 1. Prinsip Utama Desain

1. **Bumi & Pasar Bali (Bukan Bawaan Biru-Abu Tailwind):** Seluruh permukaan menggunakan palet hangat (`santan`, `kertas`, `garis`) dengan aksen berani hijau daun pisang (`daun`, `daun-tua`), kuning kunyit (`kunyit`), dan bata terakota (`terakota`).
2. **Tanpa Label Huruf Kapital (`uppercase`):** Seluruh teks, badge, dan subjudul menggunakan *sentence case* yang ramah dibaca. Hindari penggunaan `uppercase tracking-widest` di atas judul.
3. **Satu Elemen Khas Utama:** Komposisi **"Secarik Nota Pasar & Papan Daun Serapan"** (`.nota-pasar` di dalam `.papan-daun`) yang menggambarkan perjalanan selembar nota belanja dapur menjadi angka kepatuhan Pergub Bali No. 99/2018.
4. **Kejujuran Tingkat Bukti:** Setiap angka serapan lokal wajib membedakan antara lokal **terverifikasi** (hijau `daun`), lokal **pernyataan pemasok** (kuning `kunyit`), **asal belum diketahui** (abu `santan`), dan **luar Bali / campuran** (`terakota`).
5. **Mandiri Tanpa CDN:** Seluruh huruf (`Bricolage Grotesque` dan `Plus Jakarta Sans`) serta ilustrasi SVG komoditas dibundel secara lokal di dalam repositori.

---

## 2. Token Warna (`resources/css/app.css`)

Nama token di `@theme` dipertahankan agar selaras dengan seluruh halaman aplikasi:

| Token CSS / Kelas Tailwind | Nilai Hex | Peran & Kegunaan | Kontras WCAG |
|---|---|---|---|
| `--color-santan` (`bg-santan`) | `#FAF4E6` | Latar utama halaman (nuansa santan / anyaman lontar hangat) | Latar dasar |
| `--color-kertas` (`bg-kertas`) | `#FFFFFF` | Permukaan kartu, tabel, formulir, dan kertas nota | Latar kartu |
| `--color-tinta` (`text-tinta`) | `#17231C` | Teks utama arang-hutan gelap di atas `santan`, `kertas`, dan `kunyit` | **15,2 : 1** di atas `kertas` (AAA) |
| `--color-abu` (`text-abu`) | `#526056` | Teks sekunder, keterangan satuan, dan label pendukung | **5,5 : 1** di atas `santan` (AA) |
| `--color-garis` (`border-garis`) | `#E2D9C5` | Garis tepi kartu, pembatas baris tabel, dan garis nota | Pembatas struktural |
| `--color-daun` (`bg-daun`, `text-daun`) | `#1B663A` | Aksi utama (tombol primer), bar kuota terverifikasi, status patuh | **6,9 : 1** dengan `kertas` (AA) |
| `--color-daun-tua` (`bg-daun-tua`, `text-daun-tua`) | `#103F23` | Judul utama, hover tombol primer, dan latar `.papan-daun` | **11,5 : 1** dengan `kertas` (AAA) |
| `--color-daun-muda` (`bg-daun-muda`) | `#E2F2E6` | Latar pill navigasi aktif, badge memenuhi kuota, dan sorotan lembut | Dipasangkan dengan `text-daun-tua` |
| `--color-kunyit` (`bg-kunyit`, `ring-kunyit`) | `#F0B400` | Aksen perhatian, bar lokal pernyataan pemasok, nomor langkah, fokus keyboard | **10,6 : 1** dengan `tinta` (AAA) |
| `--color-kunyit-muda` (`bg-kunyit-muda`) | `#FEF0C2` | Latar kartu rekomendasi berdampak, pita pengumuman demo, badge pernyataan | Dipasangkan dengan `text-tinta` |
| `--color-terakota` (`text-terakota`, `bg-terakota`) | `#B84320` | Status kurang kuota, peringatan koreksi, dan barang luar Bali | **5,3 : 1** di atas `kertas` (AA) |
| `--color-terakota-muda` (`bg-terakota-muda`) | `#FCE3DA` | Latar pesan kekurangan kuota (gap kg) dan alert validasi | Dipasangkan dengan `text-terakota` |

---

## 3. Tipografi Lokal (`resources/css/fonts/`)

Huruf diambil dari paket `@fontsource-variable` dalam format `.woff2` (`latin` dan `latin-ext`) yang disimpan langsung di `resources/css/fonts/` dan dimuat melalui `resources/css/fonts/fonts.css`:

- **Huruf Judul & Angka (`--font-display` / `font-display`):**
  - Keluarga: **`Bricolage Grotesque`** (`font-weight: 200..900`).
  - Karakter: Tegas, hangat, dan memiliki kepribadian papan pasar modern untuk judul halaman (`text-3xl`/`text-4xl`/`text-5xl font-black`), subjudul (`font-extrabold`/`font-bold`), serta angka persentase kuota.
- **Huruf Teks Bacaan & Antarmuka (`--font-sans` / `font-sans`):**
  - Keluarga: **`Plus Jakarta Sans`** (`font-weight: 200..800`).
  - Karakter: Sangat jernih pada ukuran kecil (`text-xs`, `text-sm`) untuk tabel rincian nota, formulir koreksi, dan pesan simulasi WhatsApp.
- **Utilitas Angka (`angka-tabel`):**
  - Kelas `@utility angka-tabel` menerapkan `font-family: var(--font-display)` dan `font-variant-numeric: tabular-nums` agar deretan angka berat (`kg`), harga (`Rp`), dan persentase (`%`) sejajar lurus secara vertikal.

---

## 4. Radius, Bayangan, dan Kelas Khas

Didefinisikan di `resources/css/app.css`:

- **Radius:**
  - `--radius-kartu: 1rem` (`16px` / `rounded-2xl`) untuk kartu konten, daftar nota, dan meter kuota.
  - `--radius-tombol: 0.75rem` (`12px` / `rounded-xl`) untuk tombol aksi dan kotak input.
- **Bayangan:**
  - `--shadow-kartu`: `0 2px 6px -1px rgb(23 35 28 / 0.05), 0 1px 2px -1px rgb(23 35 28 / 0.04)` — bayangan tipis bernuansa hijau-arang untuk kartu di atas latar `santan`.
  - `--shadow-angkat`: `0 14px 28px -8px rgb(16 63 35 / 0.14), 0 4px 10px -2px rgb(16 63 35 / 0.06)` — bayangan tegas untuk papan sorotan utama.
- **Kelas Permukaan Khas (`@layer components`):**
  - `.nota-pasar`: Kartu kertas putih dengan garis-garis horizontal tipis menyerupai buku nota pasar (`repeating-linear-gradient` setiap `32px`).
  - `.papan-daun`: Panel hijau daun pisang tua (`--color-daun-tua`) dengan pendar hangat kuning kunyit dan daun di sudutnya.
  - `progress.ui-progress`: Styling elemen `<progress>` native lintas peramban (`::-webkit-progress-value` dan `::-moz-progress-bar` dipisahkan agar aturan CSS tidak diabaikan peramban).

---

## 5. Komponen Blade Dasar (`resources/views/components/ui/`)

### 5.1. Tombol (`<x-ui.button>`)
File: `resources/views/components/ui/button.blade.php`

Merender `<button>` secara bawaan, atau `<a>` bila prop `href` diisi.

- **Props:**
  - `variant`: `'primary'` (bawaan, hijau daun), `'kunyit'` (kuning kunyit teks tinta), `'secondary'` (garis tepi hijau daun), `'subtle'` (kertas garis halus), `'terakota'` (merah bata), `'ghost'`.
  - `size`: `'sm'`, `'md'` (bawaan), `'lg'`.
  - `type`: `'button'` (bawaan), `'submit'`.
  - `href`: URL tujuan bila berupa tautan.

**Contoh pemakaian:**
```blade
<form method="POST" action="{{ route('demo') }}">
    @csrf
    <x-ui.button type="submit" variant="primary" size="lg">
        Coba demo
    </x-ui.button>
</form>

<x-ui.button :href="route('register')" variant="secondary" size="lg">
    Daftarkan usaha
</x-ui.button>
```

---

### 5.2. Kartu (`<x-ui.card>`)
File: `resources/views/components/ui/card.blade.php`

- **Props:**
  - `variant`: `'default'` (kertas putih + border garis), `'santan'` (latar santan), `'kunyit'` (latar kunyit muda + border kunyit 2px), `'terakota'`, `'daun'` (papan daun tua teks terang), `'nota'` (efek kertas nota bergaris).
  - `padding`: `'none'`, `'sm'` (`p-4`), `'md'` (bawaan `p-5 sm:p-6`), `'lg'` (`p-6 sm:p-8`).
  - `as`: tag HTML pembungkus (`'div'`, `'article'`, `'section'`, `'aside'`, `'li'`).

**Contoh pemakaian:**
```blade
<x-ui.card variant="kunyit" padding="md" as="article">
    <h3 class="font-display text-lg font-bold">Langkah paling berdampak</h3>
    <p class="mt-1 text-sm">Beralih ke pemasok telur lokal di Tabanan menaikkan kuota peternakan ke 34%.</p>
</x-ui.card>
```

---

### 5.3. Badge Tingkat Bukti (`<x-ui.evidence-badge>`)
File: `resources/views/components/ui/evidence-badge.blade.php`

Menampilkan tingkat kejelasan asal komoditas dengan tiga warna utama (hijau, kuning, abu) serta varian terakota untuk luar Bali/campuran.

- **Props:**
  - `level`: `'hijau'` (`terverifikasi`), `'kuning'` (`pernyataan`), `'abu'` (`belum_diketahui`), atau `'luar'` / `'campuran'`.
  - Atau kombinasi `origin` (`'bali'`, `'luar'`, `'campuran'`, `'belum_diketahui'`) dan `evidence` (`'terverifikasi'`, `'pernyataan'`, `'belum_diketahui'`).
  - `label`: teks kustom opsional.

**Contoh pemakaian:**
```blade
<x-ui.evidence-badge level="hijau" />
<x-ui.evidence-badge origin="bali" evidence="pernyataan" />
<x-ui.evidence-badge level="abu" />
```

---

### 5.4. Meter Persentase Kuota (`<x-ui.quota-meter>`)
File: `resources/views/components/ui/quota-meter.blade.php`

Menampilkan capaian serapan lokal per kelompok Pergub Bali No. 99/2018 lengkap dengan ikon komoditas, angka persentase besar, progress bar dua lapis (**hijau** untuk lokal terverifikasi, **kuning** untuk lokal pernyataan pemasok), garis vertikal batas kuota (`30%` atau `20%`), serta rincian berat (`kg`).

- **Props:**
  - `group`: array kelompok dari `ComplianceCalculator` (`label`, `percent`, `verified_percent`, `quota`, `status`, `total_kg`, `verified_kg`, `declared_kg`, `outside_kg`, `unknown_kg`, `gap_kg`, `unmeasured`), **ATAU**
  - Props mandiri: `label`, `commodity` (`'sayur'`, `'beras'`, `'telur'`, `'ikan'`, `'kopi'`), `percent`, `verifiedPercent`, `quota`, `status` (`'patuh'`, `'kurang'`, `'kosong'`), `gapKg`, `compact` (`true`/`false`).

**Contoh pemakaian:**
```blade
{{-- Dengan data langsung dari ComplianceCalculator --}}
<x-ui.quota-meter :group="$group" />

{{-- Mode ringkas untuk peragaan --}}
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
```

---

### 5.5. Ilustrasi SVG Komoditas Bali (`<x-ui.commodity-icon>`)
File: `resources/views/components/ui/commodity-icon.blade.php`

Ilustrasi vektor ringan komoditas utama yang diatur dalam Pergub Bali No. 99/2018:
- `telur`: Telur di atas krat peternak lokal (`peternakan`)
- `sayur`: Daun pisang & sayuran segar (`hortikultura`)
- `beras`: Bulir padi subak Bali (`tanaman_pangan`)
- `ikan`: Ikan laut segar pesisir Bali (`perikanan`)
- `kopi`: Biji kopi & olahan lokal (`perkebunan` / `industri`)
- `nota`: Secarik nota belanja pasar

**Contoh pemakaian:**
```blade
<x-ui.commodity-icon name="telur" size="md" />
<x-ui.commodity-icon name="sayur" size="sm" />
<x-ui.commodity-icon name="ikan" size="lg" />
```
