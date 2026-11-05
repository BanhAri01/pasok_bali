<?php

use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\SupplyRequest;
use App\Models\WaMessage;
use App\Services\SupplierBot;

beforeEach(fn () => config(['pasok.whatsapp.webhook_secret' => 'rahasia-webhook', 'pasok.whatsapp.device' => null]));

it('mengklaim pesan berawalan PASOK dan menyimpannya', function () {
    $this->post('/webhook/wa/rahasia-webhook/teruskan', ['sender' => '081234567890', 'message' => 'PASOK DAFTAR kangkung 50 ikat Gianyar', 'inboxid' => 'f1'])
        ->assertExactJson(['claimed' => true]);

    expect(WaMessage::query()->where('phone', '6281234567890')->where('direction', 'in')->count())->toBe(1);
});

it('tidak mengklaim pesan biasa milik Harlok', function () {
    $this->post('/webhook/wa/rahasia-webhook/teruskan', ['sender' => '081234567890', 'message' => 'cabai 45rb Badung', 'inboxid' => 'f2'])
        ->assertExactJson(['claimed' => false]);

    $this->post('/webhook/wa/rahasia-webhook/teruskan', ['sender' => '081234567890', 'message' => '1', 'inboxid' => 'f3'])
        ->assertExactJson(['claimed' => false]);

    expect(WaMessage::query()->count())->toBe(0);
});

it('menolak rahasia penerus yang salah', function () {
    $this->post('/webhook/wa/salah/teruskan', ['sender' => '081234567890', 'message' => 'PASOK DAFTAR'])->assertNotFound();
});

it('mengklaim balasan YA saat percakapan pendaftaran masih berjalan', function () {
    $phone = '6281234567890';
    app(SupplierBot::class)->handle($phone, 'PASOK DAFTAR kangkung 50 ikat seminggu Gianyar');

    expect(app(SupplierBot::class)->claims($phone, 'ya'))->toBeTrue()
        ->and(app(SupplierBot::class)->claims('6289999999999', 'ya'))->toBeFalse();
});

it('mengklaim jawaban 1 dari pemasok yang punya permintaan menunggu', function () {
    $user = owner();
    $offer = SupplierProduct::query()->where('origin', 'bali')->with('supplier')->first();
    $phone = $offer->supplier->phone;

    expect(app(SupplierBot::class)->claims($phone, '1'))->toBeFalse();

    inBusiness($user->business_id);
    SupplyRequest::query()->create(['supplier_id' => $offer->supplier_id, 'product_id' => $offer->product_id, 'quantity_per_week' => 5, 'unit' => 'kg', 'message' => 'Halo pemasok']);
    inBusiness(null);

    expect(app(SupplierBot::class)->claims($phone, '1'))->toBeTrue()
        ->and(app(SupplierBot::class)->claims($phone, 'harga cabai'))->toBeFalse();
});

it('membuang kata kunci PASOK sebelum membaca pendaftaran', function () {
    expect(SupplierBot::stripKeyword('PASOK DAFTAR kangkung'))->toBe('DAFTAR kangkung')
        ->and(SupplierBot::stripKeyword('pasok bali: daftar telur'))->toBe('daftar telur')
        ->and(SupplierBot::stripKeyword('pasokan cabai'))->toBe('pasokan cabai');

    $phone = '6281333333333';
    $bot = app(SupplierBot::class);
    $bot->handle($phone, 'PASOK DAFTAR kangkung 50 ikat seminggu Gianyar');
    $bot->handle($phone, 'PASOK YA');

    expect(Supplier::query()->where('phone', $phone)->sole()->products()->sole()->capacity_per_week)->toBe(50.0);
});
