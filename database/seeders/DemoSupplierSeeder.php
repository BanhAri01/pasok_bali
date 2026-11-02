<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use Illuminate\Database\Seeder;

class DemoSupplierSeeder extends Seeder
{
    public const SUPPLIERS = [
        'taluh' => ['Peternakan Taluh Mekar (contoh)', 'Tabanan', 'terverifikasi', [
            ['Telur ayam ras', 'bali', 300, 'kg', 27000, 'kg'],
            ['Telur bebek', 'bali', 40, 'kg', 36000, 'kg'],
        ]],
        'tani' => ['Kelompok Tani Sayur Pucak (contoh)', 'Tabanan', 'terverifikasi', [
            ['Kubis', 'bali', 150, 'kg', 7000, 'kg'],
            ['Wortel', 'bali', 120, 'kg', 12000, 'kg'],
            ['Kentang', 'bali', 100, 'kg', 15000, 'kg'],
            ['Tomat', 'bali', 80, 'kg', 11000, 'kg'],
            ['Sawi hijau', 'bali', 60, 'kg', 9000, 'kg'],
            ['Selada', 'bali', 30, 'kg', 25000, 'kg'],
            ['Brokoli', 'bali', 30, 'kg', 28000, 'kg'],
        ]],
        'abian' => ['Subak Abian Kintamani Asri (contoh)', 'Bangli', 'pernyataan', [
            ['Bawang merah', 'bali', 60, 'kg', 32000, 'kg'],
            ['Cabai rawit', 'bali', 25, 'kg', 45000, 'kg'],
            ['Jeruk', 'bali', 100, 'kg', 14000, 'kg'],
            ['Kopi biji', 'bali', 20, 'kg', 90000, 'kg'],
        ]],
        'beras' => ['Penggilingan Beras Subak Asri (contoh)', 'Tabanan', 'terverifikasi', [
            ['Beras', 'bali', 500, 'kg', 14500, 'kg'],
            ['Beras merah', 'bali', 80, 'kg', 19000, 'kg'],
        ]],
        'ternak' => ['Ternak Sari Merta (contoh)', 'Gianyar', 'pernyataan', [
            ['Daging babi', 'bali', 80, 'kg', 75000, 'kg'],
            ['Daging ayam', 'bali', 120, 'kg', 38000, 'kg'],
            ['Ayam kampung', 'bali', 40, 'ekor', 85000, 'ekor'],
        ]],
        'nelayan' => ['Nelayan Segara Asri (contoh)', 'Badung', 'pernyataan', [
            ['Ikan tongkol', 'bali', 80, 'kg', 32000, 'kg'],
            ['Ikan kakap', 'bali', 30, 'kg', 85000, 'kg'],
            ['Cumi-cumi', 'bali', 25, 'kg', 70000, 'kg'],
            ['Udang', 'bali', 20, 'kg', 95000, 'kg'],
            ['Ikan lemuru', 'bali', 60, 'kg', 18000, 'kg'],
        ]],
        'garam' => ['Garam Tradisional Pesisir (contoh)', 'Klungkung', 'terverifikasi', [
            ['Garam', 'bali', 50, 'kg', 25000, 'kg'],
        ]],
        'lengis' => ['Lengis Nyuh Mekar (contoh)', 'Karangasem', 'pernyataan', [
            ['Minyak kelapa', 'bali', 60, 'liter', 45000, 'liter'],
            ['Gula aren', 'bali', 40, 'kg', 40000, 'kg'],
        ]],
        'kopi' => ['Kopi Abian Asri (contoh)', 'Bangli', 'pernyataan', [
            ['Kopi bubuk', 'bali', 20, 'kg', 160000, 'kg'],
            ['Base genep', 'bali', 15, 'kg', 60000, 'kg'],
        ]],
        'grosir' => ['Grosir Sumber Makmur (contoh)', 'Denpasar', 'pernyataan', [
            ['Telur ayam ras', 'luar', null, null, 26000, 'kg'],
            ['Bawang putih', 'luar', null, null, 38000, 'kg'],
            ['Minyak goreng sawit', 'luar', null, null, 18000, 'liter'],
            ['Gula pasir', 'luar', null, null, 17500, 'kg'],
            ['Tepung terigu', 'luar', null, null, 12000, 'kg'],
            ['Beras', 'campuran', null, null, 14000, 'kg'],
        ]],
        'segar' => ['CV Segar Lestari Distribusi (contoh)', 'Badung', 'pernyataan', [
            ['Bawang merah', 'luar', null, null, 30000, 'kg'],
            ['Kentang', 'luar', null, null, 14000, 'kg'],
            ['Wortel', 'luar', null, null, 11000, 'kg'],
            ['Daging ayam', 'campuran', null, null, 36000, 'kg'],
        ]],
        'beku' => ['Laut Biru Beku (contoh)', 'Denpasar', 'pernyataan', [
            ['Udang', 'luar', null, null, 90000, 'kg'],
            ['Cumi-cumi', 'luar', null, null, 65000, 'kg'],
            ['Ikan tuna', 'luar', null, null, 80000, 'kg'],
        ]],
    ];

    public function run(): void
    {
        $products = Product::query()->pluck('id', 'name');
        $index = 0;

        foreach (self::SUPPLIERS as $key => [$name, $regency, $verification, $items]) {
            $index++;
            $supplier = Supplier::query()->updateOrCreate(
                ['phone' => 'cth'.str_pad((string) $index, 4, '0', STR_PAD_LEFT).$key],
                ['name' => $name, 'regency' => $regency, 'verification' => $verification, 'is_demo' => true, 'active' => true, 'owner_business_id' => null],
            );

            foreach ($items as [$product, $origin, $capacity, $unit, $price, $priceUnit]) {
                if (! isset($products[$product])) {
                    continue;
                }

                SupplierProduct::query()->updateOrCreate(
                    ['supplier_id' => $supplier->id, 'product_id' => $products[$product]],
                    ['origin' => $origin, 'capacity_per_week' => $capacity, 'capacity_unit' => $unit, 'price_per_unit' => $price, 'price_unit' => $priceUnit],
                );
            }
        }
    }

    public static function phone(string $key): string
    {
        $index = array_search($key, array_keys(self::SUPPLIERS), true) + 1;

        return 'cth'.str_pad((string) $index, 4, '0', STR_PAD_LEFT).$key;
    }
}
