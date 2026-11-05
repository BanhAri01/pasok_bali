# AI & Technology Disclosure — Pasok Bali

| Komponen | Isi |
|---|---|
| Nama Produk | Pasok Bali |
| Framework / Frontend | Laravel Blade, Tailwind CSS 4, Alpine.js 3, Vite 8 |
| Backend / Runtime | PHP 8.3+, Laravel 13 |
| Database / Storage | SQLite (pengembangan), MySQL/MariaDB (produksi); foto nota disimpan di penyimpanan privat server dan dihapus otomatis 30 hari setelah nota disetujui |
| Hosting / Deployment | Shared hosting cPanel (Rapidplex) dengan PHP 8.5 dan MariaDB, sertifikat HTTPS AutoSSL; aplikasi dapat dibuka di https://pasokbali.arisantika.tech |
| AI Model / API / Method | Google Gemini `gemini-3.5-flash-lite` melalui endpoint Gemini yang kompatibel dengan OpenAI (vision + keluaran JSON), dengan cadangan otomatis `gemini-flash-lite-latest` lalu `gemini-3.6-flash` bila server sibuk (HTTP 429/5xx) atau model ditutup (404). Dipakai dengan kunci API tingkat gratis Google AI Studio. Cadangan opsional: Anthropic Claude `claude-haiku-5-5` melalui Messages API. Perhitungan kepatuhan, rekomendasi pemasok, dan konversi satuan memakai aturan deterministik, bukan AI. |
| Peran AI dalam Produk | (1) Membaca foto nota belanja menjadi data terstruktur; (2) memetakan nama barang mentah dan istilah pasar/bahasa Bali ke produk dan kelompok kuota Pergub; (3) mengurai pesan pendaftaran pemasok di WhatsApp; (4) menulis draf pesan permintaan pasokan; (5) merangkum laporan bulanan dari angka yang sudah dihitung. Semua keluaran AI melewati validasi server dan koreksi pengguna. |
| Dataset / Data Source | Kamus produk dan istilah lokal disusun tim (`database/seeders/ProductSeeder.php`, `database/data/`). Kuota mengacu pada Peraturan Gubernur Bali No. 99 Tahun 2018 dan dapat diubah admin. Seluruh usaha, pemasok, dan nota demo adalah **data fiktif** buatan tim; tidak ada data pribadi atau data pelanggan sungguhan. |
| Open-source Libraries | Laravel Framework (MIT), Laravel Tinker (MIT), Tailwind CSS (MIT), Alpine.js (MIT), Vite (MIT), laravel-vite-plugin (MIT), Pest (MIT), Laravel Pint (MIT), Mockery (BSD-3-Clause), Faker (MIT), Collision (MIT) |
| Template / Boilerplate | Kerangka aplikasi baru Laravel 13 (`laravel new`). Semua halaman aplikasi, sistem desain, dan landing page dibuat tim. |
| AI Coding Assistant | Claude Code (Anthropic, model Claude Opus 5.5) dan Google Gemini melalui Antigravity, dipakai untuk membantu menulis kode, tes, desain, dan dokumentasi. Seluruh kode diuji (`php artisan test`) dan ditinjau oleh tim. |
| Pre-existing Components | Pola gateway AI (pembatas anggaran, pencatatan panggilan, driver kompatibel OpenAI/Anthropic) dan modul pengirim WhatsApp Fonnte diadaptasi dari proyek tim sebelumnya, **Harlok**. Selain itu semua fitur dibangun baru untuk kompetisi ini. |
| Third-party Assets / Credits | Huruf Bricolage Grotesque dan Plus Jakarta Sans (SIL Open Font License 1.1, dibundel lokal dari paket @fontsource). Arah visual terinspirasi palet pigmen lukisan Kamasan dan warna Tridatu Bali. Ikon dan ilustrasi komoditas berupa SVG buatan tim. Layanan pihak ketiga: Google Gemini API, Anthropic API (opsional), Fonnte WhatsApp API (opsional). |

## Data yang dikirim ke layanan AI

- Foto nota yang diunggah pengguna, daftar nama produk kamus, nama barang mentah, teks pesan pemasok, serta angka ringkasan kepatuhan.
- Tidak mengirim kata sandi, alamat email, atau token. Isi permintaan AI tidak disimpan di database; yang dicatat hanya fitur, model, durasi, dan status.

## Pernyataan

Kami menyatakan bahwa informasi di atas benar dan lengkap. Kami bertanggung jawab atas penggunaan teknologi, source code, data, library, API, model AI, dan aset yang digunakan pada produk.
