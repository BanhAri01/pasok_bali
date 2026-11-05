# Skrip Video Demo — Pasok Bali (Bali DigiFest 2026)

- **Target Durasi Total:** ±4 menit 30 detik (270 detik, di bawah batas maksimal 5 menit)
- **Gaya Penyampaian:** Santai, jernih, mengalir seperti bercerita langsung ke pengelola dapur usaha di Bali, tanpa klaim berlebihan dan tanpa angka hasil uji atau testimoni karangan.
- **Resolusi Rekaman yang Disarankan:** `1920×1080` (1080p, 60 fps), skala peramban `100%` atau `110%` agar teks tabel dan nota terlihat tajam.

---

## Ringkasan Alur Adegan

| Adegan | Judul Adegan | Rentang Waktu | Durasi | Halaman / Fitur yang Ditampilkan |
|---|---|---|---|---|
| **1** | Masalah di Dapur & Perkenalan Pasok Bali | `00:00 – 00:45` | 45 dtk | Landing page (`/`), judul hero, & **Nota Dagang Men Sari** interaktif |
| **2** | Masuk ke Dapur Contoh & Cek Dasbor Kuota | `00:45 – 01:25` | 40 dtk | Klik **Coba dengan dapur contoh** → Dasbor (`/dasbor`), sorot kelompok Peternakan yang masih kurang |
| **3** | Unggah Nota Tulisan Tangan Pasar | `01:25 – 02:00` | 35 dtk | Halaman **Foto nota belanja** (`/nota/unggah`), pilih/unggah nota tulisan tangan 8 item |
| **4** | AI Membaca Nota & Koreksi 1 Baris oleh Manusia | `02:00 – 02:55` | 55 dtk | Halaman **Periksa nota** (`/nota/{id}`), AI ekstrak 8 item, koreksi 1 item, klik **Setujui nota** |
| **5** | Rekomendasi Peternak Lokal Tabanan & Kirim Draf WA | `02:55 – 03:40` | 45 dtk | Halaman **Rekomendasi** (`/rekomendasi`), simulasi kenaikan %, draf WA AI ke **Peternakan Taluh Mekar** |
| **6** | Pemasok Membalas `1` di Simulator WA | `03:40 – 04:10` | 30 dtk | Halaman **Simulator WA** (`/simulator-wa`), balas `1 (terima)` & contoh singkat `DAFTAR` pemasok |
| **7** | Laporan Bulanan Siap Cetak & Penutup | `04:10 – 04:30` | 20 dtk | Halaman **Laporan bulanan** (`/laporan`), ringkasan AI, tombol **Cetak / simpan PDF** |

---

## Rincian Per Adegan

### Adegan 1 — Masalah di Dapur & Perkenalan Pasok Bali
- **Waktu & Durasi:** `00:00 – 00:45` (45 detik)
- **Yang Tampil di Layar:**
  1. Buka halaman depan **Pasok Bali** (`/`). Tampil panggung kuning *atal* Kamasan dengan judul besar **"Dari kesuna sampai kuota."** dan kartu **Nota Dagang Men Sari** (`contoh fiktif`) di sebelah kanan.
  2. Arahkan kursor ke baris nota: ketuk **`kesuna 2 kg`**, **`bawang barak 3 kg`**, dan **`taluh 1 krat`**, lalu klik tombol biru **`Baca semua`**.
  3. Sorot 4 blok kuota di bawah hero (**Pertanian**, **Peternakan**, **Perikanan**, **Industri lokal**) yang angkanya langsung terisi dan batangnya bergerak.
  4. Gulir sedikit ke bagian **"Angka yang jujur soal asal barangnya"** untuk memperlihatkan tiga tingkat bukti (*Terverifikasi*, *Pernyataan pemasok*, *Belum diketahui*), lalu kembali ke tombol **`Coba dengan dapur contoh`**.
- **Narasi Santai:**
  > "Halo rekan-rekan juri Bali DigiFest 2026. Sejak tahun 2019, Pergub Bali Nomor 99 Tahun 2018 meminta hotel, restoran, dan katering di Bali menyerap produk pertanian, peternakan, perikanan, dan industri lokal Bali dengan kuota minimal per kelompok.
  >
  > Masalahnya di lapangan, belanja dapur itu dicatat di nota kertas yang macam-macam bentuknya. Bahasanya campur istilah pasar Bali—ada *kesuna*, *bawang barak*, *taluh*, *be siap*—satuannya juga campur antara ikat, krat, dan ekor. Kalau direkap manual di spreadsheet tiap bulan, tentu makan waktu.
  >
  > Karena itu kami membangun **Pasok Bali**: alat bantu bagi usaha kuliner dan akomodasi untuk mengukur sendiri serapan produk lokalnya dari foto nota belanja, lalu terhubung langsung dengan pemasok lokal lewat WhatsApp."

---

### Adegan 2 — Masuk ke Dapur Contoh & Cek Dasbor Kuota
- **Waktu & Durasi:** `00:45 – 01:25` (40 detik)
- **Yang Tampil di Layar:**
  1. Klik tombol **`Coba dengan dapur contoh`**.
  2. Masuk ke halaman **Dasbor** (`/dasbor`). Sorot pita pemberitahuan di bagian atas bahwa akun demo ini berisi data contoh fiktif dan terhapus otomatis setelah 48 jam.
  3. Arahkan kursor ke 4 kartu meter kuota. Tunjukkan kelompok yang sudah memenuhi kuota (hijau), lalu berhenti di kartu **Peternakan** yang berstatus merah **`Belum memenuhi`** (persentase lokal masih di bawah kuota 30%, misalnya sekitar 18% pada skenario restoran contoh karena sebagian besar telur dan daging ayam masih dibeli dari grosir non-lokal).
  4. Sorot juga perbedaan warna pada batang meter: hijau untuk **Lokal terverifikasi** dan kuning untuk **Lokal pernyataan**.
- **Narasi Santai:**
  > "Kita langsung coba tanpa perlu daftar akun. Begitu klik *Coba dengan dapur contoh*, kita masuk ke dasbor dapur simulasi. Semua data di sini fiktif untuk peragaan.
  >
  > Di dasbor ini, kita langsung melihat posisi empat kelompok kuota bulan berjalan. Setiap angka dihitung dari berat kilogram, bukan nilai rupiah, dan diberi label bukti yang jujur: hijau artinya pemasok lokal sudah diverifikasi admin, sedangkan kuning artinya berdasarkan pernyataan pemasok.
  >
  > Nah, terlihat di sini kelompok **Peternakan** masih merah—belum memenuhi kuota 30 persen karena belanja telur dan ayam bulan ini masih banyak dari pemasok non-lokal."

---

### Adegan 3 — Unggah Nota Tulisan Tangan Pasar
- **Waktu & Durasi:** `01:25 – 02:00` (35 detik)
- **Yang Tampil di Layar:**
  1. Klik tombol **`+ Foto nota`** di kanan atas dasbor untuk membuka halaman **Foto nota belanja** (`/nota/unggah`).
  2. Tunjukkan area unggah foto (`Ketuk untuk memotret atau memilih foto`), lalu gulir sedikit ke bagian **`Tidak punya nota? Pakai nota contoh`** yang menampilkan 10 nota fiktif berlabel `CONTOH`.
  3. Pilih nota kios pasar tulisan tangan 8 item (misalnya **Kios Bumbu Bali Mbok Komang** / **Kios Sayur & Buah Pak Kadek**, atau unggah berkas nota tulisan tangan 8 item).
  4. Layar menampilkan kartu tunggu **`AI sedang membaca nota…`** selama beberapa detik sebelum berpindah otomatis ke layar pemeriksaan.
- **Narasi Santai:**
  > "Sekarang, bagaimana cara staf dapur mencatat nota baru? Kita klik *Foto nota*. Di HP, staf tinggal memotret struk toko, faktur pemasok, atau nota tulisan tangan dari pasar.
  >
  > Untuk demo ini, kita pilih salah satu nota kios pasar bertulisan tangan dengan 8 baris belanjaan yang memakai istilah lokal Bali dan satuan campuran. Saat dikirim, AI vision membaca gambar nota tersebut di latar belakang."

---

### Adegan 4 — AI Membaca Nota & Koreksi 1 Baris oleh Manusia
- **Waktu & Durasi:** `02:00 – 02:55` (55 detik)
- **Yang Tampil di Layar:**
  1. Halaman **Periksa nota** (`/nota/{id}`) terbuka. Di sisi kiri tampil foto nota asli berlabel `CONTOH`; di sisi kanan tampil formulir hasil bacaan AI lengkap dengan nama pemasok, tanggal nota, dan 8 baris barang.
  2. Gulir perlahan melewati ke-8 baris barang. Tunjukkan bahwa istilah lokal Bali seperti *jae*, *kunir*, *isen*, *cekuh*, *bongkot*, *sereh*, *don salam*, dan *juuk lemo* (atau *tuung*, *pusuh biu*, *gedang*, *poh*) otomatis terpetakan ke produk katalog dan terkonversi dari satuan *ons*, *ikat*, atau *buah* menjadi kilogram (`≈ ... kg`).
  3. Berhenti pada **1 baris item** untuk memperagakan peran koreksi manusia (*human-in-the-loop*): ubah jumlah/satuan atau pilih pemasok/produk yang sesuai pada baris tersebut, dan perhatikan total harga di bagian bawah ikut terhitung ulang.
  4. Klik tombol **`Setujui nota`**. Layar kembali ke halaman Nota / Dasbor dengan pesan status bahwa nota telah disetujui dan dasbor kuota ikut diperbarui.
- **Narasi Santai:**
  > "Dalam hitungan detik, ke-8 item dari nota tulisan tangan tadi sudah tersalin rapi. Istilah pasar Bali langsung dikenali oleh kamus Pasok Bali yang memuat 124 produk dan lebih dari 500 alias, lalu satuan seperti ikat, ons, atau buah otomatis diubah ke kilogram.
  >
  > Tapi kami memegang prinsip: **AI tidak boleh langsung memutuskan angka kepatuhan tanpa diperiksa manusia**. Kalau ada tulisan yang kurang jelas atau perlu disesuaikan, kita tinggal mengoreksi barisnya di layar ini—misalnya kita perbaiki satu baris ini dan memilih pemasoknya.
  >
  > Begitu kita klik *Setujui nota*, koreksi tadi langsung diingat sebagai kamus khusus usaha ini, sehingga bulan depan tidak perlu memanggil AI lagi untuk istilah yang sama. Hitungan kilogram di dasbor pun langsung diperbarui."

---

### Adegan 5 — Rekomendasi Peternak Lokal Tabanan & Kirim Draf WA
- **Waktu & Durasi:** `02:55 – 03:40` (45 detik)
- **Yang Tampil di Layar:**
  1. Klik menu **`Rekomendasi`** (`/rekomendasi`) di bilah navigasi atas.
  2. Sorot kelompok **Peternakan** yang masih di bawah kuota 30%. Tampil komoditas **`Telur ayam ras`** yang bulan ini masih dibeli dari grosir non-lokal.
  3. Sorot kartu pemasok lokal pertama: **`Peternakan Taluh Mekar`** (`Tabanan · Terverifikasi`), kapasitas mingguan, harga acuan, dan teks simulasi hijau: **`Naik ke ...% · memenuhi kuota`**.
  4. Klik tombol **`Minta pasokan ±... kg/minggu →`** pada kartu Peternakan Taluh Mekar.
  5. Kotak formulir terbuka dan AI secara otomatis menyusun draf pesan WhatsApp yang sopan dengan salam Bali (*Om Swastiastu* ... *Suksma*).
  6. Klik tombol **`Kirim lewat WhatsApp`**. Muncul notifikasi permintaan pasokan berhasil dikirim dan tercatat di tabel **Riwayat permintaan** dengan status `Menunggu`.
- **Narasi Santai:**
  > "Lalu bagaimana dengan kelompok Peternakan yang masih kurang tadi? Kita buka menu *Rekomendasi*.
  >
  > Sistem otomatis mencari barang luar Bali yang paling banyak kita beli bulan ini, yaitu *Telur ayam ras*, lalu mencarikan pemasok lokal Bali yang kapasitasnya mencukupi. Di sini disarankan **Peternakan Taluh Mekar** di Tabanan yang sudah terverifikasi.
  >
  > Bukan cuma nama pemasok, Pasok Bali juga menghitung simulasinya: kalau kebutuhan telur ini dialihkan ke Peternakan Taluh Mekar, persentase peternakan kita langsung naik melewati batas kuota 30 persen.
  >
  > Kita klik *Minta pasokan*, AI langsung membuatkan draf pesan WhatsApp yang sopan lengkap dengan salam Bali dan rincian jumlah mingguan. Kita tinggal klik *Kirim lewat WhatsApp*."

---

### Adegan 6 — Pemasok Membalas `1` di Simulator WA
- **Waktu & Durasi:** `03:40 – 04:10` (30 detik)
- **Yang Tampil di Layar:**
  1. Klik menu **`Simulator WA`** (`/simulator-wa`) di navigasi atas.
  2. Sorot sebentar panel kiri (**Daftar sebagai pemasok baru**) tempat petani/peternak cukup mengetik `DAFTAR ...` lalu membalas `YA` tanpa perlu memasang aplikasi.
  3. Arahkan fokus ke panel kanan (**Permintaan yang masuk ke pemasok**). Tampil kartu **Peternakan Taluh Mekar** dengan pesan permintaan telur yang baru saja kita kirim, berstatus kuning `Menunggu`.
  4. Klik tombol **`Balas 1 (terima)`**.
  5. Tunjukkan perubahan langsung di layar: muncul balasan otomatis bot *"Suksma! Permintaan Telur ayam ras dari ... ditandai DITERIMA"*, dan badge status di kanan atas kartu berubah menjadi hijau **`Diterima`**.
- **Narasi Santai:**
  > "Di sisi petani atau peternak lokal, mereka sama sekali tidak perlu mengunduh aplikasi atau membuka web. Semua dilakukan lewat WhatsApp.
  >
  > Agar juri bisa mencoba langsung tanpa nomor telepon sungguhan, kami menyediakan **Simulator WA** ini. Di sebelah kiri, petani bisa mendaftar cukup dengan satu chat *DAFTAR* lalu konfirmasi *YA*.
  >
  > Sedangkan di sebelah kanan adalah pesan permintaan telur yang baru saja diterima oleh **Peternakan Taluh Mekar**. Peternak cukup membalas angka **1** untuk menerima. Begitu diklik, status permintaan langsung berubah menjadi *Diterima* dan pihak dapur bisa melanjutkan jadwal pengiriman."

---

### Adegan 7 — Laporan Bulanan Siap Cetak & Penutup
- **Waktu & Durasi:** `04:10 – 04:30` (20 detik)
- **Yang Tampil di Layar:**
  1. Klik menu **`Laporan`** (`/laporan`).
  2. Tunjukkan bagian **Ringkasan** (3–5 kalimat naratif yang disusun dari angka capaian), tabel rekap 4 kelompok kuota (`Total (kg)`, `Lokal (kg)`, `Terverifikasi`, `% lokal`, `Kuota`, `Status`), serta tabel **Rincian per pemasok** lengkap dengan badge tingkat buktinya.
  3. Klik tombol **`Cetak / simpan PDF`** untuk memperlihatkan pratinjau cetak laporan yang rapi dan bersih.
- **Narasi Santai:**
  > "Terakhir, di akhir bulan pengelola usaha tinggal membuka menu *Laporan*. Di sini sudah tersedia ringkasan eksekutif, tabel capaian per kelompok kuota, hingga rincian berat dan tingkat bukti per pemasok yang siap dicetak atau disimpan sebagai PDF untuk evaluasi internal usaha.
  >
  > Dari secarik nota pasar berbahasa Bali, menjadi angka kuota yang jujur, dan menyambungkan dapur hotel serta restoran dengan petani dan peternak lokal kita. Terima kasih, *Om Santih, Santih, Santih Om*."

---

## Catatan Persiapan Sebelum Merekam
1. Pastikan aplikasi berjalan dengan `npm run build` terbaru agar huruf lokal `Bricolage Grotesque Variable` dan palet Kamasan tampil penuh.
2. Jika ingin menampilkan pembacaan AI *live* pada Adegan 3–4, pastikan variabel `PASOK_AI_KEY` di `.env` terisi kunci Gemini yang aktif; seluruh nota contoh di `database/data/nota-demo/` sudah diuji 100% cocok dengan kamus produk.
3. Hindari menampilkan data pribadi, token `.env`, atau tab peramban lain selama perekaman layar.
