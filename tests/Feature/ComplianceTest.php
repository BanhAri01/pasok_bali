<?php

use App\Models\Product;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Services\ComplianceCalculator;
use App\Services\ReceiptPipeline;
use App\Services\RecommendationEngine;
use Carbon\CarbonImmutable;

function approved($user, ?string $supplierName, string $date, array $items): Receipt
{
    inBusiness($user->business_id);
    $receipt = Receipt::query()->create(['uploaded_by' => $user->id, 'status' => Receipt::NEEDS_CHECK, 'receipt_date' => $date]);
    $supplier = $supplierName ? Supplier::query()->where('name', $supplierName)->sole() : null;
    $products = Product::query()->pluck('id', 'name');

    app(ReceiptPipeline::class)->review($receipt, $supplier, $date, collect($items)->map(fn ($item) => [
        'name' => $item[0], 'product_id' => $products[$item[0]] ?? null, 'category' => $item[3] ?? null, 'quantity' => $item[1], 'unit' => $item[2], 'total_price' => 1000,
    ])->all(), true);
    inBusiness(null);

    return $receipt;
}

it('menghitung persentase lokal per kelompok berdasarkan berat dan tingkat bukti', function () {
    $user = owner();
    $month = CarbonImmutable::parse('2026-10-01');

    approved($user, 'Peternakan Taluh Mekar (contoh)', '2026-10-31', [['Telur ayam ras', 6, 'kg']]);
    approved($user, 'Ternak Sari Merta (contoh)', '2026-10-01', [['Daging babi', 4, 'kg']]);
    approved($user, 'Grosir Sumber Makmur (contoh)', '2026-10-15', [['Telur ayam ras', 30, 'butir'], ['Gula pasir', 10, 'kg']]);
    approved($user, null, '2026-10-20', [['Daging ayam', 2, 'ekor']]);
    approved($user, 'Peternakan Taluh Mekar (contoh)', '2026-11-01', [['Telur ayam ras', 100, 'kg']]);

    $report = app(ComplianceCalculator::class)->forMonth($user->business_id, $month);
    $peternakan = $report['groups']['peternakan'];

    expect($peternakan['total_kg'])->toBe(14.2)
        ->and($peternakan['local_kg'])->toBe(10.0)
        ->and($peternakan['verified_kg'])->toBe(6.0)
        ->and($peternakan['declared_kg'])->toBe(4.0)
        ->and($peternakan['outside_kg'])->toBe(1.8)
        ->and($peternakan['unknown_kg'])->toBe(2.4)
        ->and($peternakan['percent'])->toBe(70.4)
        ->and($peternakan['status'])->toBe('patuh')
        ->and($report['groups']['industri']['percent'])->toBe(0.0)
        ->and($report['groups']['industri']['gap_kg'])->toBe(2.0)
        ->and($report['groups']['industri']['status'])->toBe('kurang')
        ->and($report['groups']['perikanan']['status'])->toBe('kosong')
        ->and($report['approved_receipts'])->toBe(4);
});

it('tidak menghitung nota yang belum disetujui atau milik usaha lain', function () {
    $user = owner();
    $other = owner(['name' => 'Lain']);

    approved($other, 'Peternakan Taluh Mekar (contoh)', now()->toDateString(), [['Telur ayam ras', 50, 'kg']]);
    inBusiness($user->business_id);
    Receipt::query()->create(['uploaded_by' => $user->id, 'status' => Receipt::NEEDS_CHECK, 'receipt_date' => now()->toDateString()])
        ->items()->create(['business_id' => $user->business_id, 'name_raw' => 'Telur', 'category' => 'peternakan', 'quantity' => 5, 'unit' => 'kg', 'quantity_kg' => 5, 'origin' => 'bali', 'evidence' => 'terverifikasi']);
    inBusiness(null);

    $report = app(ComplianceCalculator::class)->forMonth($user->business_id, CarbonImmutable::now()->startOfMonth());

    expect($report['groups']['peternakan']['total_kg'])->toBe(0.0)
        ->and($report['pending_receipts'])->toBe(1);
});

it('memberi rekomendasi pemasok lokal beserta simulasi kenaikan dan alternatif bila produk sama tidak ada', function () {
    $user = owner();
    approved($user, 'Grosir Sumber Makmur (contoh)', now()->toDateString(), [['Telur ayam ras', 40, 'kg'], ['Minyak goreng sawit', 10, 'liter']]);
    approved($user, 'Peternakan Taluh Mekar (contoh)', now()->toDateString(), [['Telur ayam ras', 5, 'kg']]);

    $result = app(RecommendationEngine::class)->forMonth($user->business, CarbonImmutable::now()->startOfMonth());

    $telur = $result['peternakan']['items'][0];
    expect($telur['product']->name)->toBe('Telur ayam ras')
        ->and($telur['candidates'][0]['offer']->supplier->name)->toBe('Peternakan Taluh Mekar (contoh)')
        ->and($telur['candidates'][0]['switch_kg'])->toBe(40.0)
        ->and($telur['candidates'][0]['new_percent'])->toBe(100.0)
        ->and($telur['candidates'][0]['reaches_quota'])->toBeTrue()
        ->and($telur['candidates'][0]['alternative'])->toBeFalse();

    $minyak = $result['industri']['items'][0];
    expect($minyak['candidates'])->not->toBeEmpty()
        ->and($minyak['candidates'][0]['alternative'])->toBeTrue()
        ->and(collect($minyak['candidates'])->pluck('offer.product.name')->unique()->all())->toBe(['Minyak kelapa']);
});

it('tidak menyarankan pengganti yang tidak sejenis', function () {
    $user = owner();
    approved($user, 'Grosir Sumber Makmur (contoh)', now()->toDateString(), [['Tepung terigu', 10, 'kg'], ['Gula pasir', 8, 'kg']]);

    $items = collect(app(RecommendationEngine::class)->forMonth($user->business, CarbonImmutable::now()->startOfMonth())['industri']['items'])->keyBy('product.name');

    expect(collect($items['Gula pasir']['candidates'])->pluck('offer.product.name')->unique()->all())->toBe(['Gula aren'])
        ->and(collect($items['Tepung terigu']['candidates'])->pluck('offer.product.name')->diff(['Tepung beras', 'Tepung tapioka'])->all())->toBe([]);
});

it('semua nama di daftar pengganti ada di katalog produk', function () {
    $names = collect(config('pasok.substitutes'))->flatMap(fn ($list, $name) => [$name, ...$list])->unique();

    expect($names->diff(Product::query()->pluck('name'))->values()->all())->toBe([]);
});

it('menampilkan semua halaman utama untuk akun demo', function () {
    $this->post(route('demo'))->assertRedirect(route('dashboard'));

    foreach (['dashboard', 'receipts.index', 'receipts.create', 'recommendations.index', 'suppliers.index', 'report', 'simulator.index'] as $route) {
        $this->get(route($route))->assertOk();
    }

    $receipt = Receipt::query()->latest('id')->first();
    $this->get(route('receipts.show', $receipt))->assertOk()->assertSee('Periksa nota');
    $this->get(route('report'))->assertSee('DATA CONTOH FIKTIF');
});
