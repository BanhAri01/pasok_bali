<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Models\User;
use App\Support\CurrentBusiness;
use Carbon\CarbonImmutable;
use Database\Seeders\DemoSupplierSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoSandbox
{
    private const THIS_MONTH = [
        ['grosir', 'Grosir Sumber Makmur', 1, [['Telor ayam', 'Telur ayam ras', 30, 'kg', 780000], ['Minyak goreng', 'Minyak goreng sawit', 10, 'liter', 180000], ['Gula pasir', 'Gula pasir', 8, 'kg', 140000], ['Bw putih', 'Bawang putih', 3, 'kg', 114000]]],
        ['tani', 'Kelompok Tani Sayur Pucak', 2, [['Kol', 'Kubis', 12, 'kg', 84000], ['Wortel', 'Wortel', 8, 'kg', 96000], ['Tomat', 'Tomat', 6, 'kg', 66000], ['Sawi', 'Sawi hijau', 5, 'kg', 45000]]],
        ['beras', 'Penggilingan Beras Subak Asri', 3, [['Beras', 'Beras', 50, 'kg', 725000]]],
        ['segar', 'CV Segar Lestari', 4, [['Ayam potong', 'Daging ayam', 20, 'kg', 720000], ['Kentang', 'Kentang', 15, 'kg', 210000], ['Bawang merah', 'Bawang merah', 6, 'kg', 180000]]],
        ['beku', 'Laut Biru Beku', 5, [['Udang vaname', 'Udang', 8, 'kg', 720000], ['Cumi', 'Cumi-cumi', 6, 'kg', 390000]]],
        ['nelayan', 'Nelayan Segara Asri', 6, [['Tongkol', 'Ikan tongkol', 4, 'kg', 128000]]],
        ['taluh', 'Peternakan Taluh Mekar', 7, [['Telur', 'Telur ayam ras', 8, 'kg', 216000]]],
        ['ternak', 'Ternak Sari Merta', 8, [['Be celeng', 'Daging babi', 4, 'kg', 300000]]],
        ['garam', 'Garam Tradisional Pesisir', 9, [['Uyah', 'Garam', 2, 'kg', 50000]]],
        ['grosir', 'Grosir Sumber Makmur', 10, [['Telor', 'Telur ayam ras', 25, 'kg', 650000], ['Terigu', 'Tepung terigu', 10, 'kg', 120000], ['Minyak goreng', 'Minyak goreng sawit', 10, 'liter', 180000]]],
        [null, 'Pasar Badung - Dagang Sayur', 11, [['Kangkung', 'Kangkung', 10, 'ikat', 50000], ['Cabe rawit', 'Cabai rawit', 2, 'kg', 96000], ['Daun bawang', 'Daun bawang', 5, 'ikat', 25000]]],
    ];

    private const LAST_MONTH = [
        ['grosir', 'Grosir Sumber Makmur', 3, [['Telor ayam', 'Telur ayam ras', 60, 'kg', 1560000], ['Minyak goreng', 'Minyak goreng sawit', 20, 'liter', 360000], ['Gula pasir', 'Gula pasir', 15, 'kg', 262500], ['Beras', 'Beras', 50, 'kg', 700000]]],
        ['segar', 'CV Segar Lestari', 7, [['Ayam potong', 'Daging ayam', 35, 'kg', 1260000], ['Kentang', 'Kentang', 20, 'kg', 280000], ['Wortel', 'Wortel', 10, 'kg', 110000], ['Bawang merah', 'Bawang merah', 8, 'kg', 240000]]],
        ['tani', 'Kelompok Tani Sayur Pucak', 12, [['Kol', 'Kubis', 15, 'kg', 105000], ['Tomat', 'Tomat', 10, 'kg', 110000]]],
        ['beku', 'Laut Biru Beku', 15, [['Udang', 'Udang', 12, 'kg', 1080000], ['Tuna loin', 'Ikan tuna', 8, 'kg', 640000]]],
        ['nelayan', 'Nelayan Segara Asri', 20, [['Tongkol', 'Ikan tongkol', 5, 'kg', 160000]]],
        ['taluh', 'Peternakan Taluh Mekar', 24, [['Telur', 'Telur ayam ras', 6, 'kg', 162000]]],
    ];

    public function __construct(private ReceiptPipeline $pipeline, private CurrentBusiness $current) {}

    public function create(): User
    {
        return DB::transaction(function () {
            $business = Business::query()->create([
                'name' => 'Warung Demo '.strtoupper(Str::random(4)).' (contoh)',
                'type' => 'restoran',
                'regency' => 'Badung',
                'is_demo' => true,
                'demo_expires_at' => now()->addHours((int) config('pasok.demo.ttl_hours')),
            ]);

            $user = User::query()->create([
                'business_id' => $business->id,
                'name' => 'Pemilik Demo',
                'email' => 'demo-'.Str::lower(Str::random(16)).'@demo.pasokbali.invalid',
                'password' => Str::random(40),
                'role' => User::OWNER,
            ]);

            $this->current->run($business->id, function () use ($business, $user) {
                $today = CarbonImmutable::today();
                $this->fill($business, $user, self::THIS_MONTH, fn (int $day) => $today->startOfMonth()->addDays(min($day - 1, $today->day - 1)));
                $this->fill($business, $user, self::LAST_MONTH, fn (int $day) => $today->subMonthNoOverflow()->startOfMonth()->addDays($day - 1));
            });

            return $user;
        });
    }

    private function fill(Business $business, User $user, array $receipts, callable $date): void
    {
        $products = Product::query()->pluck('id', 'name');
        $suppliers = Supplier::query()->whereIn('phone', collect(DemoSupplierSeeder::SUPPLIERS)->keys()->map(fn ($key) => DemoSupplierSeeder::phone($key)))->get()->keyBy('phone');

        foreach ($receipts as [$supplierKey, $rawName, $day, $items]) {
            $supplier = $supplierKey ? $suppliers->get(DemoSupplierSeeder::phone($supplierKey)) : null;

            $receipt = Receipt::query()->create([
                'business_id' => $business->id,
                'uploaded_by' => $user->id,
                'supplier_name_raw' => $rawName,
                'receipt_date' => $date($day)->toDateString(),
                'status' => Receipt::NEEDS_CHECK,
            ]);

            $rows = collect($items)->map(fn ($item) => [
                'name' => $item[0],
                'product_id' => $products[$item[1]] ?? null,
                'category' => null,
                'quantity' => $item[2],
                'unit' => $item[3],
                'total_price' => $item[4],
            ])->all();

            $this->pipeline->review($receipt, $supplier, $receipt->receipt_date->toDateString(), $rows, true);
        }
    }

    public function purgeExpired(): int
    {
        $count = 0;

        Business::query()
            ->where('is_demo', true)
            ->where('demo_expires_at', '<', now())
            ->select(['id'])
            ->chunkById(50, function ($businesses) use (&$count) {
                foreach ($businesses as $business) {
                    Storage::disk('local')->deleteDirectory('nota/'.$business->id);
                    $business->delete();
                    $count++;
                }
            });

        return $count;
    }
}
