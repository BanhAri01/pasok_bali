<?php

use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

function readNota(array $items, string $supplier = 'Peternakan Taluh Mekar', string $date = ''): array
{
    return ['legible' => true, 'supplier_name' => $supplier, 'receipt_date' => $date ?: now()->toDateString(), 'items' => $items];
}

it('membaca nota dengan AI lalu mengelompokkan, mengonversi satuan, dan menentukan asal', function () {
    $user = owner();
    $kangkung = Product::query()->where('name', 'Kangkung')->value('id');

    $ai = fakeAi([
        'nota' => readNota([
            ['name' => 'Taluh', 'quantity' => 10, 'unit' => 'kg', 'total_price' => 270000, 'confidence' => 0.95],
            ['name' => 'Kangkong', 'quantity' => 8, 'unit' => 'ikt', 'total_price' => 40000, 'confidence' => 0.9],
            ['name' => 'Telur bebek asin spesial', 'quantity' => 2, 'unit' => 'peti', 'total_price' => 500000, 'confidence' => 0.5],
            ['name' => 'Gas elpiji', 'quantity' => 1, 'unit' => 'tabung', 'total_price' => 25000, 'confidence' => 0.9],
        ]),
        'kelompok' => fn () => ['items' => [
            ['index' => 2, 'product_id' => Product::query()->where('name', 'Telur bebek')->value('id'), 'category' => 'peternakan', 'kg_per_unit' => 15, 'confidence' => 0.7],
            ['index' => 3, 'product_id' => 0, 'category' => 'lainnya', 'kg_per_unit' => 0, 'confidence' => 0.9],
        ]],
    ]);

    $this->actingAs($user)
        ->post(route('receipts.store'), ['foto' => UploadedFile::fake()->image('nota.jpg', 600, 900)])
        ->assertRedirect();

    $receipt = Receipt::query()->sole();
    $items = $receipt->items()->orderBy('id')->get()->keyBy('name_raw');

    expect($receipt->status)->toBe(Receipt::NEEDS_CHECK)
        ->and($receipt->supplier->name)->toBe('Peternakan Taluh Mekar (contoh)')
        ->and($receipt->total)->toBe(835000)
        ->and($ai->count('nota'))->toBe(1)
        ->and($ai->calls[0]['images'])->toBe(1)
        ->and($ai->count('kelompok'))->toBe(1)
        ->and($items['Taluh']->category)->toBe('peternakan')
        ->and($items['Taluh']->quantity_kg)->toBe(10.0)
        ->and($items['Taluh']->origin)->toBe('bali')
        ->and($items['Taluh']->evidence)->toBe('terverifikasi')
        ->and($items['Taluh']->needs_review)->toBeFalse()
        ->and($items['Kangkong']->product_id)->toBe($kangkung)
        ->and($items['Kangkong']->quantity_kg)->toBe(2.0)
        ->and($items['Kangkong']->origin)->toBe(ReceiptItem::UNKNOWN)
        ->and($items['Telur bebek asin spesial']->quantity_kg)->toBe(30.0)
        ->and($items['Telur bebek asin spesial']->kg_estimated)->toBeTrue()
        ->and($items['Telur bebek asin spesial']->needs_review)->toBeTrue()
        ->and($items['Gas elpiji']->category)->toBeNull()
        ->and($items['Gas elpiji']->needs_review)->toBeFalse();
});

it('tetap bisa dipakai manual saat AI belum aktif atau gagal', function () {
    $user = owner();

    $this->actingAs($user)->post(route('receipts.store'), ['foto' => UploadedFile::fake()->image('nota.jpg')]);
    expect(Receipt::query()->sole())->status->toBe(Receipt::NEEDS_CHECK)->note->toContain('belum aktif');

    fakeAi(['nota' => ['legible' => false, 'supplier_name' => '', 'receipt_date' => '', 'items' => []]]);
    $this->actingAs($user)->post(route('receipts.store'), ['foto' => UploadedFile::fake()->image('kucing.jpg')]);
    expect(Receipt::query()->latest('id')->first())->status->toBe(Receipt::NEEDS_CHECK)->note->toContain('tidak terbaca');

    fakeAi(['nota' => null]);
    $this->actingAs($user)->post(route('receipts.store'), ['foto' => UploadedFile::fake()->image('buram.jpg')]);
    expect(Receipt::query()->latest('id')->first())->note->toContain('belum berhasil');
});

it('menolak berkas yang bukan gambar', function () {
    $this->actingAs(owner())
        ->post(route('receipts.store'), ['foto' => UploadedFile::fake()->create('nota.pdf', 100, 'application/pdf')])
        ->assertSessionHasErrors('foto');

    expect(Receipt::query()->count())->toBe(0);
});

it('menyetujui nota hasil koreksi, menghitung ulang di server, dan mempelajari istilah untuk usaha itu saja', function () {
    $user = owner();
    inBusiness($user->business_id);
    $receipt = Receipt::query()->create(['uploaded_by' => $user->id, 'supplier_name_raw' => 'Bu Komang Pasar', 'status' => Receipt::NEEDS_CHECK, 'receipt_date' => now()->toDateString()]);
    inBusiness(null);

    $supplier = Supplier::query()->where('name', 'Kelompok Tani Sayur Pucak (contoh)')->sole();
    $kubis = Product::query()->where('name', 'Kubis')->value('id');

    $this->actingAs($user)->put(route('receipts.update', $receipt), [
        'supplier_id' => $supplier->id,
        'receipt_date' => now()->toDateString(),
        'action' => 'setujui',
        'items' => [
            ['name' => 'Kol bulat mantap', 'product_id' => $kubis, 'quantity' => 4, 'unit' => 'kg', 'total_price' => 28000, 'origin' => 'bali'],
            ['name' => 'Bumbu misteri', 'product_id' => '', 'category' => 'industri', 'quantity' => 1, 'unit' => 'kg', 'total_price' => 15000],
        ],
    ])->assertRedirect(route('dashboard', ['bulan' => now()->format('Y-m')]));

    $receipt->refresh();
    $items = $receipt->items()->get()->keyBy('name_raw');

    expect($receipt->status)->toBe(Receipt::APPROVED)
        ->and($receipt->approved_at)->not->toBeNull()
        ->and($items['Kol bulat mantap']->origin)->toBe('bali')
        ->and($items['Kol bulat mantap']->evidence)->toBe('terverifikasi')
        ->and($items['Bumbu misteri']->origin)->toBe(ReceiptItem::UNKNOWN)
        ->and(ProductAlias::query()->where('alias', 'kol bulat mantap')->where('business_id', $user->business_id)->value('product_id'))->toBe($kubis);

    $other = owner(['name' => 'Hotel Lain']);
    fakeAi(['nota' => readNota([['name' => 'Kol bulat mantap', 'quantity' => 1, 'unit' => 'kg', 'total_price' => 7000, 'confidence' => 0.9]]), 'kelompok' => ['items' => []]]);
    $this->actingAs($other)->post(route('receipts.store'), ['foto' => UploadedFile::fake()->image('nota.jpg')]);

    inBusiness($other->business_id);
    expect(ReceiptItem::query()->sole()->product_id)->toBeNull();
});

it('memproses nota contoh tanpa mengizinkan path sembarang', function () {
    $user = owner();

    $this->actingAs($user)->post('/nota/contoh/..%2F..%2F.env')->assertNotFound();
    $this->actingAs($user)->get('/nota/contoh/tidak-ada.jpg/gambar')->assertNotFound();
});
