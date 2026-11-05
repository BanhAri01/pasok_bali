# Pasok Bali

## Description

Pasok Bali membantu hotel, restoran, kafe, villa, dan katering di Bali **mengukur sendiri** kepatuhan terhadap Peraturan Gubernur Bali No. 99 Tahun 2018 tentang pemasaran dan pemanfaatan produk pertanian, perikanan, dan industri lokal Bali.

Pengguna cukup memotret nota belanja. AI membaca nota, mengenali istilah pasar dan bahasa Bali (misalnya *kesuna*, *bawang barak*, *taluh*), menyeragamkan satuan ke kilogram, lalu Pasok Bali menghitung persentase produk lokal per kelompok kuota. Bila kuota belum terpenuhi, aplikasi menyarankan petani, peternak, dan nelayan lokal, mensimulasikan kenaikan persentasenya, dan menyiapkan pesan permintaan pasokan lewat WhatsApp.

Pasok Bali adalah alat bantu pengukuran mandiri untuk usaha, **bukan** alat pengawasan pemerintah.

## Problem & Target User

**Masalah.** Pergub Bali No. 99/2018 meminta usaha akomodasi dan kuliner menyerap produk lokal Bali dalam kuota minimal per kelompok. Di lapangan, belanja dapur tercatat di nota yang tidak seragam (struk toko, nota tulisan tangan kios pasar, faktur pemasok) dengan istilah dan satuan yang beragam (ikat, krat, tray, ekor). Menghitung persentase volume lokal secara manual di spreadsheet memakan waktu, dan usaha sering tidak tahu pemasok lokal mana yang bisa mengisi kekurangan.

**Pengguna.**
1. Pemilik dan staf belanja hotel, restoran, kafe, villa, dan katering skala kecil–menengah di Bali (pengguna utama).
2. Pemasok lokal (petani, peternak, nelayan, industri rumahan) yang mendaftar dan menjawab permintaan lewat WhatsApp tanpa memasang aplikasi.
3. Admin platform yang memverifikasi pemasok dan mengatur angka kuota.

## Key Features

- **Baca nota dengan AI**: unggah foto nota (JPG/PNG/WEBP), AI mengekstrak pemasok, tanggal, dan baris barang (nama, jumlah, satuan, harga) ke layar koreksi. Jika AI tidak tersedia, formulir isian manual tetap bisa dipakai.
- **Pengelompokan barang dan konversi satuan**: nama barang mentah dicocokkan ke kamus produk (88 produk, 210 istilah termasuk istilah pasar Bali), lalu satuan dikonversi ke kg. Koreksi yang disetujui pengguna disimpan sebagai istilah khusus usaha itu, sehingga nota berikutnya dikenali tanpa memanggil AI.
- **Dasbor kepatuhan**: persentase lokal per kelompok (pertanian, peternakan, perikanan, industri lokal) dibandingkan kuota, dengan **label tingkat bukti**: terverifikasi admin, pernyataan pemasok, atau asal belum diketahui.
- **Rekomendasi pemasok lokal**: produk luar Bali yang paling banyak dibeli, pemasok lokal yang cocok, simulasi kenaikan persentase, dan draf pesan WhatsApp yang ditulis AI.
- **Pendaftaran pemasok lewat WhatsApp**: pemasok mengetik `DAFTAR …` dengan bahasa bebas, AI menyusun datanya, lalu pemasok mengonfirmasi dengan `YA`. Pemasok menjawab permintaan pasokan dengan `1` (terima) atau `2` (tolak).
- **Simulator WA**: mencoba alur pemasok langsung dari web tanpa nomor WhatsApp sungguhan.
- **Laporan bulanan**: tabel per kelompok dan ringkasan AI, siap dicetak atau disimpan sebagai PDF lewat browser.
- **Akun demo sekali klik**: sandbox per pengunjung berisi data fiktif, dihapus otomatis setelah 48 jam.
- **Panel admin**: verifikasi pemasok dan pengaturan angka kuota.

## Tech Stack

| Lapisan | Teknologi |
|---|---|
| Frontend | Blade, Tailwind CSS 4, Alpine.js 3, Vite |
| Backend | PHP 8.3+, Laravel 13 |
| Database | SQLite (lokal) / MySQL atau MariaDB (produksi) |
| AI | Google Gemini (`gemini-3.5-flash-lite`, cadangan otomatis `gemini-flash-lite-latest` dan `gemini-3.6-flash` saat server sibuk) lewat endpoint kompatibel OpenAI; cadangan opsional Anthropic Claude (`claude-haiku-5-5`) |
| WhatsApp | Fonnte (opsional; tanpa token, pesan dicatat ke log dan dapat dicoba lewat Simulator WA) |
| Pengujian | Pest 5 |
| Hosting | Shared hosting cPanel (Rapidplex), PHP 8.5, MariaDB, HTTPS AutoSSL |

## AI Implementation

| Tahap | Peran AI | Masukan ke AI | Jika AI gagal |
|---|---|---|---|
| Baca nota | Model vision mengekstrak pemasok, tanggal, dan baris barang ke JSON terstruktur | Foto nota + daftar produk kamus | Nota ditandai *perlu dicek*, isian manual |
| Kelompokkan barang | Memetakan nama mentah (termasuk istilah Bali/pasar) ke produk kamus dan kelompok Pergub, beserta konversi satuan | Nama barang yang tidak ditemukan di kamus | Barang dibiarkan tanpa produk dan ditandai untuk dikoreksi |
| Pendaftaran pemasok | Mengurai pesan WhatsApp bebas menjadi nama, kabupaten, produk, kapasitas, harga, dan asal produksi | Teks pesan pemasok | Pengurai pola sederhana (regex) |
| Draf pesan pasokan | Menulis pesan WhatsApp sopan dengan salam Bali | Nama usaha, pemasok, produk, jumlah per minggu | Templat pesan baku |
| Ringkasan laporan | Merangkum angka kepatuhan bulanan menjadi 3–5 kalimat | Angka hasil perhitungan saja | Ringkasan otomatis berbasis aturan |

Prinsip yang dipakai:
- **Angka kepatuhan tidak dihitung oleh AI.** AI hanya membaca dan mengelompokkan; persentase dihitung deterministik dari data yang sudah disetujui pengguna.
- **Manusia tetap memutuskan.** Setiap nota melewati layar koreksi sebelum masuk hitungan.
- **Belajar dari koreksi.** Istilah yang disetujui disimpan per usaha untuk mengurangi panggilan AI berikutnya.
- **Keluaran terstruktur.** Semua panggilan meminta JSON sesuai skema, lalu divalidasi ulang di server.
- **Hemat dan aman.** Ada batas panggilan harian per usaha dan global; setiap panggilan dicatat di tabel `ai_calls` (fitur, model, durasi, status) tanpa menyimpan isi permintaan. Teks dari nota atau pesan diperlakukan sebagai data, bukan perintah.

## Architecture / Data Flow

```
Pengguna (browser) ──> Laravel (Blade + Alpine)
                          │
                          ├─ Unggah foto ─> penyimpanan privat ─> Job ProcessReceipt
                          │                                         ├─> AI vision: baca nota (JSON)
                          │                                         ├─> Kamus produk + AI: kelompokkan & konversi kg
                          │                                         └─> Pencocokan pemasok
                          │
                          ├─ Layar koreksi ─> setujui ─> Database (terisolasi per usaha)
                          │
                          ├─ ComplianceCalculator (tanpa AI) ─> Dasbor & laporan (+ ringkasan AI)
                          │
                          └─ RecommendationEngine ─> draf AI ─> WhatsApp (Fonnte / log)

Pemasok (WhatsApp) ──> /webhook/wa/{rahasia} ──> SupplierBot ──> AI pengurai pendaftaran ──> Database
```

Data tiap usaha dipisahkan dengan *global scope* `business_id` dan middleware yang berjalan sebelum *route model binding*; isolasi ini diuji di `tests/Feature/IsolationTest.php`.

## Installation / Run Instructions

Kebutuhan: PHP 8.3+ (ekstensi `pdo_sqlite`, `fileinfo`), Composer, Node.js 20+.

```bash
git clone <url-repo> pasok-bali
cd pasok-bali
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
php artisan serve
```

Buka `http://localhost:8000`, lalu klik **Coba demo**.

Variabel lingkungan penting (lihat `.env.example`):

| Variabel | Fungsi |
|---|---|
| `PASOK_AI_KEY` | Kunci API Gemini (atau Anthropic bila `PASOK_AI_DRIVER=anthropic`). Tanpa kunci, aplikasi tetap jalan dengan isian manual. |
| `PASOK_AI_DRIVER` | `openai` (Gemini lewat endpoint kompatibel OpenAI) atau `anthropic` |
| `PASOK_AI_MODEL` | Opsional, mengganti model bawaan |
| `PASOK_AI_FALLBACK_MODELS` | Opsional, daftar model cadangan dipisah koma (bawaan `gemini-flash-lite-latest,gemini-3.6-flash`) |
| `PASOK_CA_BUNDLE` | Opsional, path berkas sertifikat CA bila PHP lokal (mis. XAMPP di Windows) gagal membuka HTTPS |
| `PASOK_WA_DRIVER` | `log` (bawaan) atau `fonnte` |
| `FONNTE_TOKEN`, `FONNTE_DEVICE` | Kredensial Fonnte bila memakai WhatsApp sungguhan |
| `PASOK_WEBHOOK_SECRET` | Rahasia di URL webhook `/webhook/wa/{rahasia}` |
| `PASOK_DEMO` | `true` untuk mengaktifkan tombol Coba demo |
| `PASOK_ADMIN_EMAIL` | Bila diisi, seeder membuat akun admin dan menampilkan sandi awal di terminal |

Perawatan terjadwal (hapus sandbox demo kedaluwarsa dan foto nota lama) berjalan lewat `php artisan schedule:run` yang dipanggil cron setiap menit di server produksi.

Menjalankan tes:

```bash
php artisan test
```

## Deployment URL

https://pasokbali.arisantika.tech

## Demo Account

Tidak perlu akun. Klik **Coba demo** di halaman depan untuk membuat sandbox pribadi berisi data fiktif (berlaku 48 jam, maksimal 15 sandbox per alamat IP per hari).

## Known Limitations

- Semua data di repositori dan mode demo adalah **data fiktif**. Aplikasi belum diuji dengan nota atau usaha sungguhan. Uji internal 3 Nov 2026 pada 10 nota demo fiktif (gambar digital, bukan foto kamera) dengan `gemini-3.5-flash-lite`: 10/10 nota terbaca dan 69/69 baris benar (nama, jumlah, satuan, harga, produk kamus), rata-rata ±2,7 detik per nota. Akurasi pada foto nota asli yang miring, buram, atau kusut belum diukur.
- Angka kuota mengikuti pengaturan admin (bawaan 30% untuk pertanian, peternakan, dan perikanan, serta 20% untuk industri lokal) dan perlu dicocokkan dengan teks resmi Pergub Bali No. 99/2018 sebelum dipakai untuk pelaporan resmi.
- Status "lokal" bergantung pada data pemasok: label *pernyataan* berarti berdasarkan pengakuan pemasok, belum diverifikasi di lapangan.
- Konversi satuan non-baku (ikat, sisir, ekor) memakai perkiraan berat rata-rata dari kamus.
- Kualitas pembacaan bergantung pada kejelasan foto; tulisan tangan yang buram perlu dikoreksi manual.
- Pengiriman WhatsApp sungguhan membutuhkan akun Fonnte; tanpa itu dipakai Simulator WA.
- Laporan PDF memakai fitur cetak browser, belum berupa berkas PDF yang dibuat server.

## Third-party Resources / Credits

Lihat [docs/DISCLOSURE.md](docs/DISCLOSURE.md) untuk daftar lengkap teknologi, model AI, pustaka, aset, dan alat bantu pengembangan.

## Team Members

| Nama | Peran |
|---|---|
| [ISI ARI] | [ISI ARI] |
