# Rencana Pasok Bali — Bali DigiFest 2026 (AI Web Innovation Challenge)

## Satu kalimat
Aplikasi yang membantu hotel dan restoran di Bali mengukur sendiri kepatuhan mereka terhadap Pergub Bali No. 99/2018 dengan membaca nota belanja memakai AI, lalu menghubungkan mereka dengan petani dan peternak lokal agar kuota produk lokalnya terpenuhi.

## Keputusan Ari (2 Nov 2026)
- Nama: **Pasok Bali**. serapbali/pasokbali .com & .id kosong per 2 Nov; tidak ada aplikasi bernama sama di Google/Play Store.
- Pengganti Harlok untuk DigiFest. Repo baru; modul AI & WA dari Harlok dipakai ulang dan diungkapkan di disclosure.
- **Hanya data demo** (fiktif, ditandai jelas). Tidak memakai nota/data klien nyata. Hasil uji pengguna tidak boleh dikarang.
- Tenggat Tahap 1 belum diumumkan → rencana disusun untuk 3–5 hari.

## Aturan yang diukur (Pergub 99/2018, cocokkan ke teks resmi JDIH sebelum final)
| Kategori | Kuota minimal hotel/restoran/katering |
|---|---|
| Tanaman pangan, hortikultura, perkebunan | 30% volume |
| Peternakan | 30% volume |
| Perikanan | 30% volume |
| Industri lokal Bali | 20% volume (ada sumber menyebut 30%) |
Kuota disimpan di tabel `quota_rules`, bukan angka mati di kode.

## Pengguna
1. Restoran/kafe/villa/hotel kecil-menengah — pengguna utama (pemilik, staf belanja).
2. Pemasok lokal — mendaftar & menerima permintaan lewat WA.
3. Admin platform — verifikasi pemasok, kamus produk.

## Alur AI
1. **Baca nota** (vision): foto → JSON {pemasok, tanggal, item[nama, jumlah, satuan, harga]} + keyakinan → layar koreksi.
2. **Kelompokkan barang**: nama mentah (termasuk istilah Bali/pasar) → produk induk + kategori Pergub + konversi ke satuan dasar. Hasil yang disetujui masuk kamus `product_aliases` → nota berikutnya tanpa panggilan AI.
3. **Cocokkan pemasok**: nama di nota → pemasok terdaftar (alias per usaha).
4. **Hitung kepatuhan** (tanpa AI): % volume lokal per kategori vs kuota; tiap angka berlabel tingkat bukti (terverifikasi / pernyataan pemasok / belum diketahui).
5. **Rekomendasi**: peringkat pemasok lokal (aturan) + penjelasan & simulasi kenaikan % + draf pesan WA (AI, cadangan templat).
6. **Laporan bulanan**: angka + ringkasan AI.
Gagal/ragu → isian manual, tanda kuning. Semua panggilan AI tercatat di `ai_calls` tanpa isi.

## Penyedia AI
- Utama: Gemini Flash via endpoint OpenAI-compatible (`LAPOR_AI_DRIVER=openai`, base URL generativelanguage). Mendukung gambar.
- Cadangan: Anthropic (jika kunci tersedia).
- Data yang dikirim ke AI: hanya gambar nota demo/teks item. Tidak ada nama orang/telepon.

## Skema data (ringkas)
- `businesses` (nama, jenis, kabupaten, is_demo)
- `users` (business_id, role: pemilik|staf|admin)
- `products` (nama, kategori, satuan_dasar) · `product_aliases` (istilah → produk) · `unit_conversions`
- `suppliers` (nama, kabupaten, telepon, tingkat_bukti, is_demo) · `supplier_products` (asal: bali|luar|campuran, kapasitas/minggu, harga acuan)
- `supplier_aliases` (business_id, nama_mentah → supplier)
- `receipts` (business_id, supplier_id, tanggal, status, foto privat, keyakinan) · `receipt_items` (nama_mentah, product_id, kategori, jumlah, satuan, jumlah_dasar, harga, asal, tingkat_bukti)
- `quota_rules` (kategori, persen_min, berlaku_mulai, sumber)
- `supply_requests` (business_id, supplier_id, produk, jumlah/minggu, harga_tawar, status)
- `ai_calls`, `inbound_messages`, `outbound_messages` (dari Harlok)
Isolasi antar-usaha: global scope `business_id` + tes isolasi.

## MVP (wajib jalan saat submit)
1. Unggah foto nota → AI baca → koreksi → simpan.
2. Kelompokkan & seragamkan satuan.
3. Dashboard kepatuhan per kategori + label tingkat bukti.
4. Rekomendasi pemasok lokal + simulasi + draf WA.
5. Pemasok daftar lewat WA (simulator di web bila WA tak aktif).
Tambahan bila sempat: laporan PDF, peringatan harga wajar (biaya produksi +20%), rekap dinas.

## Alur video demo
Restoran contoh (peternakan 18%, merah) → foto nota tulisan tangan → AI baca 8 item → koreksi 1 → dashboard naik/turun → rekomendasi peternak telur Tabanan → "hubungi" → WA pemasok balas 1 → laporan bulanan.

## Pembagian kerja
Claude: fondasi, skema & migrasi, login/peran, isolasi usaha, unggah foto, seluruh AI, WA/webhook, hitung kepatuhan, keamanan, deploy.
Gemini: sistem desain & landing, tampilan halaman dari data yang disiapkan Claude, kamus produk & istilah Bali (data, direview), nota demo fiktif, draf proposal/README/disclosure/diagram.
