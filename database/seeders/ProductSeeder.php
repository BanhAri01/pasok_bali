<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAlias;
use App\Services\ProductCatalog;
use App\Support\Text;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public const AMBIGUOUS_UNITS = ['krat'];

    public const BASE = [
        ['Beras', 'tanaman_pangan', 'kg', 1, ['karung' => 25], ['baas', 'bras', 'beras putih', 'beras lokal']],
        ['Beras merah', 'tanaman_pangan', 'kg', 1, [], ['baas barak', 'beras mrh']],
        ['Jagung', 'tanaman_pangan', 'kg', 1, [], ['jagung manis', 'jgg']],
        ['Singkong', 'tanaman_pangan', 'kg', 1, [], ['ketela pohon', 'ubi kayu', 'ketela']],
        ['Ubi jalar', 'tanaman_pangan', 'kg', 1, [], ['ubi', 'ketela rambat']],
        ['Kacang tanah', 'tanaman_pangan', 'kg', 1, [], ['kc tanah']],
        ['Kedelai', 'tanaman_pangan', 'kg', 1, [], ['kacang kedelai']],
        ['Bawang merah', 'hortikultura', 'kg', 1, [], ['bawang barak', 'bw merah', 'bw mrh', 'bwg merah', 'bawang mrh', 'brambang']],
        ['Bawang putih', 'hortikultura', 'kg', 1, [], ['kesuna', 'bw putih', 'bw pth', 'bwg putih', 'bawang pth']],
        ['Cabai rawit', 'hortikultura', 'kg', 1, [], ['cabe rawit', 'cbe rawit', 'tabia', 'tabia kerinyi', 'lombok rawit', 'cabai kecil']],
        ['Cabai merah besar', 'hortikultura', 'kg', 1, [], ['cabe merah', 'cabe besar', 'cabai merah', 'tabia gede', 'cbe merah']],
        ['Tomat', 'hortikultura', 'kg', 1, [], ['tomat merah', 'tmt']],
        ['Kentang', 'hortikultura', 'kg', 1, [], ['ktg', 'kentang granola']],
        ['Wortel', 'hortikultura', 'kg', 1, [], ['wotel', 'wortle', 'wrtl']],
        ['Kubis', 'hortikultura', 'kg', 1, ['buah' => 1], ['kol', 'kobis', 'kol putih']],
        ['Sawi hijau', 'hortikultura', 'kg', 1, ['ikat' => 0.3], ['sawi', 'caisim', 'sawi manis']],
        ['Sawi putih', 'hortikultura', 'kg', 1, ['buah' => 1], ['petsai', 'sawi ptih']],
        ['Kangkung', 'hortikultura', 'kg', 1, ['ikat' => 0.25], ['kangkong']],
        ['Bayam', 'hortikultura', 'kg', 1, ['ikat' => 0.25], ['bayem']],
        ['Buncis', 'hortikultura', 'kg', 1, [], ['bncs']],
        ['Kacang panjang', 'hortikultura', 'kg', 1, ['ikat' => 0.25], ['kc panjang', 'kc pjg', 'kacang pjg']],
        ['Terong', 'hortikultura', 'kg', 1, [], ['terung', 'trg']],
        ['Mentimun', 'hortikultura', 'kg', 1, [], ['timun', 'ketimun']],
        ['Selada', 'hortikultura', 'kg', 1, [], ['lettuce', 'selada keriting']],
        ['Brokoli', 'hortikultura', 'kg', 1, [], ['brocoli', 'brokoli hijau']],
        ['Daun bawang', 'hortikultura', 'kg', 1, ['ikat' => 0.1], ['bawang daun', 'bw daun', 'loncang']],
        ['Seledri', 'hortikultura', 'kg', 1, ['ikat' => 0.05], ['sledri', 'daun seledri']],
        ['Jahe', 'hortikultura', 'kg', 1, [], ['jae']],
        ['Kunyit', 'hortikultura', 'kg', 1, [], ['kunir']],
        ['Lengkuas', 'hortikultura', 'kg', 1, [], ['laos', 'isen']],
        ['Kencur', 'hortikultura', 'kg', 1, [], ['cekuh']],
        ['Serai', 'hortikultura', 'kg', 1, ['ikat' => 0.2], ['sereh', 'sere']],
        ['Daun jeruk', 'hortikultura', 'kg', 1, ['ikat' => 0.05], ['daun limau']],
        ['Jeruk nipis', 'hortikultura', 'kg', 1, [], ['jeruk limau', 'limau']],
        ['Jeruk', 'hortikultura', 'kg', 1, [], ['juuk', 'jeruk manis', 'jeruk kintamani']],
        ['Pisang', 'hortikultura', 'kg', 1, ['sisir' => 1.5], ['biu']],
        ['Pepaya', 'hortikultura', 'kg', 1, ['buah' => 1.5], ['gedang', 'papaya']],
        ['Semangka', 'hortikultura', 'kg', 1, ['buah' => 4], ['watermelon']],
        ['Nanas', 'hortikultura', 'kg', 1, ['buah' => 1.2], ['nenas']],
        ['Salak', 'hortikultura', 'kg', 1, [], ['salak bali']],
        ['Mangga', 'hortikultura', 'kg', 1, [], ['poh']],
        ['Alpukat', 'hortikultura', 'kg', 1, [], ['avocado', 'apokat']],
        ['Buah naga', 'hortikultura', 'kg', 1, [], ['dragon fruit']],
        ['Jamur tiram', 'hortikultura', 'kg', 1, [], ['jamur']],
        ['Kelapa', 'perkebunan', 'kg', 1, ['buah' => 0.8], ['nyuh', 'kelapa tua', 'klp']],
        ['Kelapa muda', 'perkebunan', 'kg', 1, ['buah' => 1.5], ['nyuh muda', 'degan']],
        ['Kopi biji', 'perkebunan', 'kg', 1, [], ['kopi beras', 'green bean', 'biji kopi']],
        ['Biji kakao', 'perkebunan', 'kg', 1, [], ['kakao', 'coklat biji']],
        ['Cengkeh', 'perkebunan', 'kg', 1, [], ['cengkih']],
        ['Telur ayam ras', 'peternakan', 'kg', 1, ['butir' => 0.06, 'tray' => 1.8], ['telur', 'telor', 'tlr', 'taluh', 'taluh siap', 'telur ayam', 'telur negeri']],
        ['Telur bebek', 'peternakan', 'kg', 1, ['butir' => 0.07, 'tray' => 2.1], ['taluh bebek', 'telor bebek', 'tlr bebek']],
        ['Daging ayam', 'peternakan', 'kg', 1, ['ekor' => 1.2], ['ayam', 'ayam potong', 'be siap', 'ayam broiler', 'dada ayam', 'paha ayam']],
        ['Ayam kampung', 'peternakan', 'kg', 1, ['ekor' => 1], ['siap kampung', 'ayam kmpg']],
        ['Daging bebek', 'peternakan', 'kg', 1, ['ekor' => 1.5], ['bebek', 'be bebek']],
        ['Daging babi', 'peternakan', 'kg', 1, [], ['babi', 'be celeng', 'celeng', 'daging celeng', 'samcan', 'babi has']],
        ['Daging sapi', 'peternakan', 'kg', 1, [], ['sapi', 'be sampi', 'daging sampi', 'has dalam']],
        ['Susu sapi segar', 'peternakan', 'liter', 1.03, [], ['susu segar', 'susu murni']],
        ['Ikan tongkol', 'perikanan', 'kg', 1, ['ekor' => 0.5], ['tongkol', 'be tongkol', 'tkl']],
        ['Ikan tuna', 'perikanan', 'kg', 1, [], ['tuna', 'tuna loin']],
        ['Ikan cakalang', 'perikanan', 'kg', 1, [], ['cakalang']],
        ['Ikan kakap', 'perikanan', 'kg', 1, [], ['kakap', 'kakap merah', 'snapper']],
        ['Ikan kerapu', 'perikanan', 'kg', 1, [], ['kerapu', 'grouper']],
        ['Ikan lemuru', 'perikanan', 'kg', 1, [], ['lemuru']],
        ['Ikan mahi-mahi', 'perikanan', 'kg', 1, [], ['mahi mahi', 'lemadang']],
        ['Ikan nila', 'perikanan', 'kg', 1, [], ['nila']],
        ['Ikan lele', 'perikanan', 'kg', 1, [], ['lele']],
        ['Udang', 'perikanan', 'kg', 1, [], ['udang vaname', 'udang windu', 'udg', 'prawn']],
        ['Cumi-cumi', 'perikanan', 'kg', 1, [], ['cumi', 'sotong', 'squid']],
        ['Kepiting', 'perikanan', 'kg', 1, [], ['kepiting bakau', 'crab']],
        ['Rumput laut', 'perikanan', 'kg', 1, [], ['bulung', 'agar laut']],
        ['Minyak kelapa', 'industri', 'liter', 0.92, ['botol' => 0.6], ['lengis nyuh', 'lengis tanusan', 'minyak klp', 'vco']],
        ['Minyak goreng sawit', 'industri', 'liter', 0.9, ['jerigen' => 18], ['minyak goreng', 'migor', 'minyak sawit', 'bimoli', 'myk goreng']],
        ['Gula pasir', 'industri', 'kg', 1, ['karung' => 50], ['gula', 'gula putih', 'gls']],
        ['Gula aren', 'industri', 'kg', 1, [], ['gula bali', 'gula merah', 'gula kelapa', 'gula juruh']],
        ['Garam', 'industri', 'kg', 1, ['pack' => 0.25], ['uyah', 'garam laut', 'garam bali']],
        ['Kopi bubuk', 'industri', 'kg', 1, ['pack' => 0.25], ['kopi', 'kopi bali', 'kopi giling']],
        ['Arak Bali', 'industri', 'liter', 0.95, ['botol' => 0.6], ['arak']],
        ['Brem Bali', 'industri', 'liter', 1, ['botol' => 0.6], ['brem']],
        ['Tepung terigu', 'industri', 'kg', 1, ['karung' => 25], ['terigu', 'tepung']],
        ['Tepung beras', 'industri', 'kg', 1, ['pack' => 0.5], ['tepung baas']],
        ['Kecap manis', 'industri', 'liter', 1.2, ['botol' => 0.6], ['kecap', 'kcp']],
        ['Tahu', 'industri', 'kg', 1, ['buah' => 0.05, 'pack' => 0.5], ['tahu putih', 'tahu goreng']],
        ['Tempe', 'industri', 'kg', 1, ['buah' => 0.25, 'pack' => 0.25], ['tmp', 'tempe papan']],
        ['Roti', 'industri', 'kg', 1, ['buah' => 0.08, 'pack' => 0.4], ['roti tawar', 'bread']],
        ['Kerupuk', 'industri', 'kg', 1, ['pack' => 0.25], ['krupuk', 'kerupuk udang']],
        ['Base genep', 'industri', 'kg', 1, ['pack' => 0.25], ['bumbu bali', 'base gede', 'bumbu genep']],
        ['Air minum kemasan', 'industri', 'liter', 1, ['galon' => 19, 'botol' => 0.6, 'dus' => 14.4], ['air mineral', 'aqua', 'air galon']],
        ['Es batu', 'industri', 'kg', 1, ['karung' => 10, 'pack' => 1], ['es', 'es balok']],
    ];

    public function run(): void
    {
        $rows = collect(self::BASE)->map(fn ($row) => [
            'name' => $row[0], 'category' => $row[1], 'base_unit' => $row[2], 'kg_per_base' => $row[3], 'conversions' => $row[4], 'aliases' => $row[5],
        ]);

        $extra = database_path('data/produk.json');

        if (is_file($extra)) {
            $json = json_decode((string) file_get_contents($extra), true);

            foreach (is_array($json) ? $json : [] as $item) {
                if (! is_array($item) || empty($item['nama']) || ! array_key_exists($item['kategori'] ?? '', config('pasok.categories'))) {
                    continue;
                }

                $kgPerBase = (float) ($item['kg_per_satuan_dasar'] ?? 1) ?: 1;
                $conversions = collect($item['konversi'] ?? [])
                    ->filter(fn ($value, $unit) => is_numeric($value) && $value > 0 && ! in_array(Text::unit($unit), self::AMBIGUOUS_UNITS, true))
                    ->mapWithKeys(fn ($value, $unit) => [Text::unit($unit) => (float) $value]);
                $aliases = array_values(array_filter((array) ($item['alias'] ?? []), 'is_string'));
                $index = $rows->search(fn ($row) => Text::key($row['name']) === Text::key($item['nama']));

                if ($index !== false) {
                    $row = $rows[$index];
                    $row['aliases'] = array_merge($row['aliases'], $aliases);
                    $row['conversions'] = $conversions
                        ->map(fn ($value) => round($value * $kgPerBase / $row['kg_per_base'], 4))
                        ->merge($row['conversions'])
                        ->all();
                    $rows[$index] = $row;

                    continue;
                }

                $rows->push([
                    'name' => mb_substr($item['nama'], 0, 80),
                    'category' => $item['kategori'],
                    'base_unit' => in_array($item['satuan_dasar'] ?? 'kg', ['kg', 'liter', 'butir'], true) ? $item['satuan_dasar'] : 'kg',
                    'kg_per_base' => $kgPerBase,
                    'conversions' => $conversions->all(),
                    'aliases' => $aliases,
                ]);
            }
        }

        foreach ($rows as $row) {
            $product = Product::query()->updateOrCreate(
                ['name' => $row['name']],
                ['category' => $row['category'], 'base_unit' => $row['base_unit'], 'kg_per_base' => $row['kg_per_base'], 'conversions' => $row['conversions'] ?: null],
            );

            foreach (array_unique(array_map(fn ($alias) => Text::key($alias), $row['aliases'])) as $alias) {
                if ($alias === '' || $alias === Text::key($product->name)) {
                    continue;
                }

                $existing = ProductAlias::query()->whereNull('business_id')->where('alias', $alias)->first();

                if ($existing) {
                    continue;
                }

                ProductAlias::query()->create(['business_id' => null, 'alias' => $alias, 'product_id' => $product->id, 'source' => 'kamus']);
            }
        }

        app(ProductCatalog::class)->forget();
    }
}
