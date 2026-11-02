# Catatan Kamus Produk, Istilah Lokal Bali, dan Konversi Satuan

Dokumen ini menjelaskan sumber rujukan, alasan pengelompokan, daftar istilah bahasa Bali yang dimasukkan ke dalam `database/data/produk.json` dan `database/data/satuan.json`, serta daftar istilah/konversi yang **ditandai tidak pasti** agar dapat ditinjau oleh Ari dan tim.

---

## 1. Ringkasan Cakupan Data

1. **`database/data/produk.json` (124 produk):**
   - **Tanaman pangan (`tanaman_pangan`):** 11 produk (beras putih, beras merah, ketan hitam/*injin*, jagung, singkong/*kesela*, ubi jalar/*kesela bun*, talas/*keladi*, kacang tanah, kedelai, kacang hijau, kacang *undis*).
   - **Hortikultura (`hortikultura`):** 52 produk (bawang merah, bawang putih/*kesuna*, bawang bombay, cabai rawit/*tabia krinyi*, cabai merah besar/*tabia gede*, cabai merah keriting, tomat, kentang, wortel, kubis, sawi hijau, sawi putih, kangkung, bayam, sayur paku, daun singkong/*don kesela*, buncis, kacang panjang, tauge, terong/*tuung*, mentimun, labu siam/*jipang*, labu kuning/*waluh*, nangka muda, batang pisang muda/*ares*, jantung pisang/*pusuh biu*, selada, brokoli, daun bawang, seledri, daun kemangi, jahe/*jae*, kunyit/*kunir*, lengkuas/*isen*, kencur/*cekuh*, serai, daun jeruk, daun pandan, kecombrang/*bongkot*, jeruk limau/*lemo*, jeruk nipis, jeruk manis/*juuk*, pisang/*biu*, pepaya/*gedang*, semangka, nanas/*manas*, salak, mangga/*poh*, alpukat, buah naga, stroberi, jamur tiram).
   - **Perkebunan (`perkebunan`):** 11 produk (kelapa tua/*nyuh*, kelapa muda/*kuud*, kopi biji, biji kakao, cengkeh, lada putih/*mica putih*, lada hitam/*mica selem*, ketumbar biji, kemiri/*tingkih*, cabai jawa/*tabia bun*, asam jawa/*celagi*/*lunak*).
   - **Peternakan (`peternakan`):** 11 produk (telur ayam ras, telur ayam kampung, telur bebek, telur puyuh, daging ayam, ayam kampung, daging bebek, daging babi, daging sapi, susu sapi segar, madu murni).
   - **Perikanan (`perikanan`):** 15 produk (ikan tongkol, tuna, cakalang, kakap, kerapu, lemuru, mahi-mahi/*lemadang*, tenggiri, nila/mujair, lele, gurame, udang, cumi-cumi, kepiting, rumput laut/*bulung*).
   - **Industri lokal (`industri`):** 24 produk (minyak kelapa/*lengis nyuh*, minyak goreng sawit, santan kelapa, gula pasir, gula aren/*gula juruh*, garam/*uyah*, terasi/*sera*, kopi bubuk, arak Bali, brem Bali, tepung terigu, tepung beras, tepung tapioka, kecap manis, tahu, tempe, roti, kerupuk, *base genep*, *urutan* Bali, ikan asin/*gerang*, telur asin/*taluh uyah*, air minum kemasan, es batu).
2. **`database/data/satuan.json` (19 satuan):**
   - Mencakup seluruh satuan berat/volume metrik (`kg`, `ons`, `gram`, `liter`, `ml`) dan satuan kemasan/pasar (`butir`, `ikat`, `ikat kecil`, `karung`, `krat`, `tray`, `pack`, `btl`, `dus`, `ekor`, `sisir`, `buah`, `jerigen`, `galon`) beserta pemetaan ke fungsi normalisasi `App\Support\Text::unit()`.

---

## 2. Sumber & Alasan Pengelompokan

1. **Acuan Kategori Pergub Bali No. 99 Tahun 2018:**
   - Sesuai `config/pasok.php` dan `docs/RENCANA.md`, kuota serapan produk lokal dibagi menjadi 4 kelompok besar:
     - **Pertanian (kuota min. 30%):** menggabungkan `tanaman_pangan`, `hortikultura`, dan `perkebunan`.
     - **Peternakan (kuota min. 30%):** kategori `peternakan`.
     - **Perikanan (kuota min. 30%):** kategori `perikanan`.
     - **Industri lokal Bali (kuota bawaan 20%):** kategori `industri` (produk hasil olahan pertanian/perikanan/pangan seperti minyak kelapa, garam, gula aren, kopi bubuk, tahu, tempe, *base genep*, *urutan*, arak, brem).
2. **Alasan Pemisahan Bahan Mentah vs Olahan:**
   - **Kopi biji (`perkebunan`) vs Kopi bubuk (`industri`):** Biji kopi mentah (*green bean* / *kopi beras*) masuk hasil kebun, sedangkan kopi sangrai/bubuk kemasan yang dibeli kafe/hotel masuk hasil olahan industri lokal.
   - **Kelapa tua (`perkebunan`) vs Minyak kelapa & Santan kemasan (`industri`):** Kelapa butir masuk kebun, sedangkan *lengis nyuh/tanusan* (minyak kelapa) dan santan kemasan masuk industri olahan.
   - **Telur bebek segar (`peternakan`) vs Telur asin (`industri`):** Telur bebek mentah masuk peternakan, sedangkan telur asin (*taluh uyah*) masuk olahan pangan.

---

## 3. Istilah Bahasa Bali yang Dimasukkan (Yakin Benar)

Seluruh istilah bahasa Bali di bawah ini adalah kosakata pasar sehari-hari di Bali yang maknanya tunggal dan tidak ambigu dalam konteks belanja dapur:

| Istilah Bahasa Bali | Nama Baku di Kamus | Kategori | Keterangan Pemakaian di Bali |
|---|---|---|---|
| `baas`, `baas barak` | Beras, Beras merah | `tanaman_pangan` | Istilah baku beras di seluruh pasar Bali. |
| `injin`, `baas injin` | Beras ketan hitam | `tanaman_pangan` | Bahan utama *bubuh injin* (bubur ketan hitam Bali). |
| `kesela`, `kesela bun`, `don kesela` | Singkong, Ubi jalar, Daun singkong | `tanaman_pangan` / `hortikultura` | *Kesela* = ketela pohon; *kesela bun* = ubi rambat; *don kesela* = daun singkong. |
| `keladi` | Talas | `tanaman_pangan` | Umbi talas pasar Bali. |
| `undis` | Kacang undis | `tanaman_pangan` | Kacang gude hitam khas Bali untuk *jukut undis*. |
| `bawang barak` | Bawang merah | `hortikultura` | Bawang merah lokal (sering disingkat `bw mrh` di nota). |
| `kesuna` | Bawang putih | `hortikultura` | Kosakata baku bahasa Bali untuk bawang putih. |
| `tabia krinyi` / `tabia kerinyi` / `tabia cenik` | Cabai rawit | `hortikultura` | Cabai rawit untuk sambal matah / sambal embe. |
| `tabia gede` / `tabia lombok` | Cabai merah besar | `hortikultura` | Cabai merah besar untuk bumbu dasar (*base genep*). |
| `paku` / `jukut paku` | Sayur paku | `hortikultura` | Pucuk pakis sayur khas Bali. |
| `tuung` | Terong | `hortikultura` | Bahasa Bali baku untuk buah terong. |
| `jipang` | Labu siam | `hortikultura` | Sebutan umum labu siam di pasar Bali dan Jawa Timur. |
| `waluh` | Labu kuning | `hortikultura` | Bahasa Bali untuk labu kuning/parang. |
| `ares` / `ares biu` | Batang pisang muda | `hortikultura` | Bahan utama *jukut ares* pada katering dan upacara di Bali. |
| `pusuh biu` / `pusuh` | Jantung pisang | `hortikultura` | Bahasa Bali untuk jantung pisang. |
| `jae`, `kunir`, `isen`, `cekuh` | Jahe, Kunyit, Lengkuas, Kencur | `hortikultura` | Empat rimpang utama (*tetukon* / *base genep*) dalam kuliner Bali. |
| `bongkot` / `kecicang` | Kecombrang | `hortikultura` | Batang/bunga kecombrang untuk *sambal bongkot* (*kecicang* umum di Bali Timur/Karangasem). |
| `lemo` / `juuk lemo` / `juuk lengis` | Jeruk limau | `hortikultura` | Jeruk sambal wajib untuk *sambal matah* dan *lawar*. |
| `juuk`, `juuk nipis` | Jeruk, Jeruk nipis | `hortikultura` | *Juuk* = jeruk dalam bahasa Bali. |
| `biu`, `gedang`, `manas`, `poh` | Pisang, Pepaya, Nanas, Mangga | `hortikultura` | Empat nama buah harian dalam bahasa Bali. |
| `nyuh`, `kuud` | Kelapa, Kelapa muda | `perkebunan` | *Nyuh* = kelapa tua; *kuud* = kelapa muda. |
| `mica putih`, `mica selem` | Lada putih, Lada hitam | `perkebunan` | *Mica* = merica/lada dalam bahasa Bali. |
| `tingkih` | Kemiri | `perkebunan` | Bahasa Bali baku untuk kemiri. |
| `tabia bun` | Cabai jawa | `perkebunan` | Rempah cabai jawa/puyang yang wajib ada dalam *base genep* jangkep. |
| `celagi`, `lunak` | Asam jawa | `perkebunan` | *Celagi* = buah asam jawa; *lunak* = daging asam jawa kupas. |
| `taluh`, `taluh siap`, `taluh bebek`, `taluh puyuh` | Telur ayam ras/kampung, Telur bebek, Telur puyuh | `peternakan` | *Taluh* = telur; *siap* = ayam. |
| `be siap`, `be bebek`, `be celeng`, `be sampi` | Daging ayam, bebek, babi, sapi | `peternakan` | *Be* = daging/ikan; *celeng* = babi; *sampi* = sapi. |
| `bulung`, `bulung boni`, `bulung rambut` | Rumput laut | `perikanan` | Rumput laut segar khas pesisir Bali (Serangan/Nusa Penida). |
| `lengis nyuh`, `lengis tanusan` | Minyak kelapa | `industri` | Minyak kelapa murni/tradisional Bali. |
| `uyah`, `sera`, `gerang`, `taluh uyah` | Garam, Terasi, Ikan asin, Telur asin | `industri` | Bahan bumbu & lauk awetan di dapur Bali. |
| `base genep`, `urutan` | Base genep, Urutan Bali | `industri` | Bumbu rajang Bali dan sosis fermentasi tradisional Bali. |

---

## 4. Bagian yang Ditandai TIDAK PASTI / Perlu Keputusan

Sesuai aturan tugas G2 (*"Istilah bahasa Bali hanya yang yakin benar; kalau ragu, jangan dimasukkan... tandai yang tidak pasti"*), butir-butir di bawah ini **sengaja tidak dimasukkan sebagai alias otomatis** atau **ditandai memiliki rentang konversi lebar**:

### A. Istilah Lokal yang Sengaja Tidak Dimasukkan karena Ambigu (`[TIDAK PASTI / AMBIGU]`)

1. **`don kayu manis` (Daun katuk vs Kayu manis rempah) — `[TIDAK DIMASUKKAN]`:**
   - **Alasan:** Dalam bahasa Bali sehari-hari, sayur daun katuk disebut *don kayu manis* (untuk membuat *jukut kayu manis*). Namun di nota belanja restoran/hotel, tulisan `"kayu manis"` hampir selalu merujuk pada rempah kulit kayu manis (*cinnamon*, kategori `perkebunan`). Memasukkan `"kayu manis"` ke sayuran akan menyebabkan salah kelompok.
2. **Kata tunggal `bawang` (tanpa `barak` / `merah` / `putih`) — `[TIDAK DIMASUKKAN]`:**
   - **Alasan:** Dalam bahasa Bali murni, kata *bawang* berarti bawang merah (bawang putih = *kesuna*). Akan tetapi pada nota berbahasa Indonesia yang ditulis terburu-buru, kata `"bawang"` saja bisa berarti bawang merah, bawang putih, atau bawang bombay. Kamus hanya memetakan `"bawang barak"`, `"bw merah"`, `"bw mrh"`, dan `"kesuna"`.
3. **Kata `gedang` di luar konteks Bali — `[CATATAN HATI-HATI]`:**
   - **Status:** Dimasukkan ke `Pepaya` (sesuai `ProductSeeder::BASE` baris 50).
   - **Catatan:** Dalam bahasa Bali, *gedang* = pepaya (sedangkan pisang = *biu*). Namun dalam bahasa Jawa/Sunda, *gedang* berarti pisang. Karena aplikasi ini khusus beroperasi untuk pasar Bali, `gedang` tetap mengacu ke `Pepaya`.
4. **Istilah `jukut` dan `be` tanpa nama spesifik — `[TIDAK DIMASUKKAN]`:**
   - **Alasan:** Kata *jukut* (sayur) dan *be* (daging/ikan) terlalu umum bila berdiri sendiri. Hanya gabungan kata yang spesifik (mis. `jukut paku`, `be siap`, `be celeng`, `be sampi`, `be tongkol`) yang didaftarkan.
5. **Istilah lokal ikan karang yang berbeda tiap pesisir (`bojog`, `katambak`, `lencam`) — `[TIDAK DIMASUKKAN]`:**
   - **Alasan:** Penyebutan ikan karang di Kedonganan, Kusamba, dan Buleleng sering berbeda untuk spesies yang sama. Untuk menghindari salah petakan, kamus perikanan hanya memuat nama ikan yang sudah baku di tempat pelelangan ikan dan dapur horeka Bali.

### B. Nilai Konversi Satuan yang Bervariasi di Lapangan (`[TIDAK PASTI / TAKSIRAN RATA-RATA]`)

1. **Satuan `krat` vs `peti` pada Telur (`[PERLU PERHATIAN]`):**
   - Di kode `App\Support\Text::unit()`, kata `"krat"`, `"krt"`, dan `"peti"` dinormalisasi menjadi satu kunci yang sama: `'krat'`.
   - Di nota dapur restoran/warung di Bali, `"1 krat telur"` lazim berarti **1 rak/tray isi 30 butir** (±1,8 kg untuk telur ayam ras).
   - Namun pada transaksi kandang/pengepul telur partai besar, istilah `"1 peti"` bisa berarti peti kayu/plastik berisi **10 kg atau 15 kg**.
   - **Keputusan di data:** Mengikuti arahan tugas G2 (`{"krat": 30, "tray": 30}` untuk telur bersatuan `butir`, sehingga `30 × 0,06 kg = 1,8 kg` untuk telur ayam ras dan `30 × 0,045 kg = 1,35 kg` untuk telur ayam kampung). Pada `ProductSeeder::BASE`, `Telur ayam ras` dan `Telur bebek` bawaan tetap dipertahankan oleh seeder.
2. **Satuan `ikat` dan `ikat kecil` pada Sayuran (`[TAKSIRAN RATA-RATA]`):**
   - Berat 1 ikat sayur tidak pernah ditimbang baku di pasar tradisional: ikat pengepul sayur di Pasar Badung/Baturiti berkisar `0,20–0,30 kg` (kangkung, bayam, sawi, kacang panjang), sedangkan ikat daun bumbu (seledri, kemangi, daun jeruk, daun salam) berkisar `0,05–0,10 kg`.
   - Untuk `"ikat kecil"`, beratnya bervariasi antara `0,03–0,15 kg` tergantung penjual eceran, sehingga tidak dipatok angka tunggal di produk dan dicatat di `satuan.json` agar masuk pemeriksaan manual bila muncul di nota.
3. **Satuan `ekor` pada Ikan Tangkap Laut (`[TAKSIRAN RATA-RATA]`):**
   - Ikan nila (`0,3 kg/ekor`), lele (`0,15 kg/ekor`), dan ayam potong (`1,2 kg/ekor`) memiliki ukuran panen yang relatif seragam.
   - Sebaliknya, ikan laut seperti `Ikan tuna` (`2,5 kg/ekor`), `Ikan mahi-mahi` (`2,0 kg/ekor`), `Ikan tenggiri` (`1,5 kg/ekor`), dan `Ikan kakap` (`0,8 kg/ekor`) memiliki rentang ukuran tangkapan yang sangat lebar di lapangan. Nilai di `produk.json` adalah ukuran rata-rata ikan utuh yang dibeli dapur restoran; untuk pembelian fillet/loin umumnya nota sudah tertulis dalam `kg`.
