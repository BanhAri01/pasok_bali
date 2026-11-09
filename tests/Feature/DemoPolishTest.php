<?php

use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

it('mencatat sumber pencocokan tiap baris dan meringkasnya tanpa klaim keyakinan rata-rata', function () {
    $user = owner();

    fakeAi([
        'nota' => readNota([
            ['name' => 'Taluh', 'quantity' => 10, 'unit' => 'kg', 'total_price' => 270000, 'confidence' => 1],
            ['name' => 'Telur bebek asin spesial', 'quantity' => 2, 'unit' => 'peti', 'total_price' => 500000, 'confidence' => 0.8],
        ]),
        'kelompok' => fn () => ['items' => [
            ['index' => 1, 'product_id' => Product::query()->where('name', 'Telur bebek')->value('id'), 'category' => 'peternakan', 'kg_per_unit' => 15, 'confidence' => 0.7],
        ]],
    ]);

    $this->actingAs($user)->post(route('receipts.store'), ['foto' => UploadedFile::fake()->image('nota.jpg')]);

    $receipt = Receipt::query()->sole();
    $items = $receipt->items()->get()->keyBy('name_raw');

    expect($items['Taluh']->match_source)->toBe(ReceiptItem::MATCH_DICTIONARY)
        ->and($items['Telur bebek asin spesial']->match_source)->toBe(ReceiptItem::MATCH_AI);

    $this->actingAs($user)->get(route('receipts.show', $receipt))
        ->assertOk()
        ->assertDontSee('keyakinan rata-rata')
        ->assertSee('1 dikenali kamus istilah')
        ->assertSee('1 ditebak AI')
        ->assertSee($receipt->items()->where('origin', 'bali')->count().' lokal Bali');

    $kubis = Product::query()->where('name', 'Kubis')->value('id');

    $this->actingAs($user)->put(route('receipts.update', $receipt), [
        'supplier_id' => $receipt->supplier_id,
        'receipt_date' => now()->toDateString(),
        'action' => 'simpan',
        'items' => [
            ['id' => $items['Taluh']->id, 'name' => 'Taluh', 'product_id' => $items['Taluh']->product_id, 'quantity' => 10, 'unit' => 'kg', 'total_price' => 270000],
            ['id' => $items['Telur bebek asin spesial']->id, 'name' => 'Telur bebek asin spesial', 'product_id' => $kubis, 'quantity' => 2, 'unit' => 'kg', 'total_price' => 500000],
        ],
    ]);

    $items = $receipt->items()->get()->keyBy('name_raw');

    expect($items['Taluh']->match_source)->toBe(ReceiptItem::MATCH_DICTIONARY)
        ->and($items['Telur bebek asin spesial']->match_source)->toBe(ReceiptItem::MATCH_USER);
});

it('menaruh nota contoh dari pemasok terdaftar di urutan pertama', function () {
    $user = owner();

    $samples = $this->actingAs($user)->get(route('receipts.create'))->assertOk()->viewData('samples');

    $registered = collect($samples)->takeWhile(fn ($sample) => $sample['supplier'] !== null);

    expect($registered)->not->toBeEmpty()
        ->and($samples[0]['file'])->toBe('nota-02-peternakan-taluh-mekar.png')
        ->and($samples[0]['supplier'])->toBe('Peternakan Taluh Mekar (contoh)')
        ->and(collect($samples)->slice($registered->count())->every(fn ($sample) => $sample['supplier'] === null))->toBeTrue()
        ->and(collect($samples)->pluck('file')->sort()->values()->all())->toBe(collect(\App\Http\Controllers\ReceiptController::samples())->sort()->values()->all());
});

it('menampilkan dampak persetujuan nota pada bulan sesuai tanggal nota', function () {
    $user = owner();
    $supplier = Supplier::query()->where('name', 'Peternakan Taluh Mekar (contoh)')->sole();
    $telur = Product::query()->where('name', 'Telur ayam ras')->value('id');
    $lastMonth = now()->subMonthNoOverflow()->startOfMonth()->addDays(4);

    fakeAi(['nota' => readNota([['name' => 'Taluh', 'quantity' => 10, 'unit' => 'kg', 'total_price' => 270000, 'confidence' => 0.9]], 'Peternakan Taluh Mekar', $lastMonth->toDateString())]);
    $this->actingAs($user)->post(route('receipts.store'), ['foto' => UploadedFile::fake()->image('nota.jpg')]);
    $receipt = Receipt::query()->sole();

    $this->actingAs($user)->put(route('receipts.update', $receipt), [
        'supplier_id' => $supplier->id,
        'receipt_date' => $lastMonth->toDateString(),
        'action' => 'setujui',
        'items' => [['name' => 'Taluh', 'product_id' => $telur, 'quantity' => 10, 'unit' => 'kg', 'total_price' => 270000]],
    ])->assertRedirect(route('dashboard', ['bulan' => $lastMonth->format('Y-m')]))
        ->assertSessionHas('impact', fn ($impact) => $impact['groups'][0]['label'] === config('pasok.quota_groups.peternakan.label')
            && $impact['groups'][0]['before'] === null
            && (float) $impact['groups'][0]['after'] === 100.0
            && (float) $impact['groups'][0]['added_local_kg'] === 10.0
            && $impact['unknown'] === 0);

    $this->actingAs($user)
        ->withSession(['impact' => ['month' => $lastMonth->format('Y-m'), 'groups' => [['label' => 'Peternakan', 'quota' => 30, 'before' => null, 'after' => 100.0, 'added_kg' => 10.0, 'added_local_kg' => 10.0]], 'unknown' => 0, 'supplier' => $supplier->name]])
        ->get(route('dashboard', ['bulan' => $lastMonth->format('Y-m')]))
        ->assertSee('Dampak nota ini')
        ->assertSee('Kembali ke '.now()->translatedFormat('F Y'));
});

it('menyebut masa berlaku demo yang sama di semua halaman', function () {
    config(['pasok.demo.ttl_hours' => 48]);

    $this->get('/')->assertSee('terhapus sendiri setelah 48 jam');

    $this->post(route('demo'))->assertRedirect();

    $this->get(route('dashboard'))->assertSee('dihapus otomatis 48 jam setelah dibuat')->assertDontSee('dari sekarang');
    $this->get(route('simulator.index'))->assertSee('ikut terhapus bersama akun demo');
});
