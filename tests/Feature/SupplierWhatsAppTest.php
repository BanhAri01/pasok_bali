<?php

use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\SupplyRequest;
use App\Models\WaMessage;
use App\Services\ReceiptPipeline;
use App\Services\SupplierBot;

it('mendaftarkan pemasok lewat WA dengan konfirmasi YA dan memperbarui asal nota lama', function () {
    $user = owner();
    $phone = '6281234567890';

    $supplier = Supplier::query()->create(['name' => 'Tani Mekar', 'phone' => $phone, 'regency' => 'Gianyar']);
    inBusiness($user->business_id);
    $receipt = Receipt::query()->create(['uploaded_by' => $user->id, 'status' => Receipt::NEEDS_CHECK, 'receipt_date' => now()->toDateString()]);
    app(ReceiptPipeline::class)->review($receipt, $supplier, now()->toDateString(), [['name' => 'Kangkung', 'product_id' => Product::query()->where('name', 'Kangkung')->value('id'), 'category' => null, 'quantity' => 10, 'unit' => 'ikat', 'total_price' => 50000]], true);
    inBusiness(null);
    expect(ReceiptItem::query()->sole()->origin)->toBe(ReceiptItem::UNKNOWN);

    $bot = app(SupplierBot::class);
    $replies = $bot->handle($phone, 'DAFTAR kangkung 50 ikat seminggu Gianyar');

    expect($replies[0])->toContain('Kangkung')->toContain('Gianyar')->toContain('Balas YA');

    $bot->handle($phone, 'ya');

    $offer = SupplierProduct::query()->where('supplier_id', $supplier->id)->sole();
    expect($offer->origin)->toBe('bali')
        ->and($offer->capacity_per_week)->toBe(50.0)
        ->and($supplier->fresh()->verification)->toBe('pernyataan')
        ->and($supplier->fresh()->owner_business_id)->toBeNull()
        ->and(ReceiptItem::query()->sole()->origin)->toBe('bali')
        ->and(ReceiptItem::query()->sole()->evidence)->toBe('pernyataan')
        ->and(WaMessage::query()->where('phone', $phone)->where('direction', 'out')->count())->toBe(2);
});

it('memakai hasil AI untuk pesan pendaftaran bebas dan menandai produk luar Bali', function () {
    $phone = '6281111111111';
    fakeAi(['daftar' => ['name' => 'UD Segara', 'regency' => 'luar_bali', 'products' => [
        ['product_name' => 'udang vaname', 'capacity_per_week' => 100, 'unit' => 'kg', 'price' => 90000, 'price_unit' => 'kg', 'produced_in_bali' => false],
    ]]]);

    $bot = app(SupplierBot::class);
    $bot->handle($phone, 'Daftar kami jual udang vaname dari Banyuwangi 100kg/minggu harga 90rb');
    $bot->handle($phone, 'YA');

    $supplier = Supplier::query()->where('phone', $phone)->sole();
    expect($supplier->name)->toBe('UD Segara')
        ->and($supplier->regency)->toBeNull()
        ->and($supplier->products()->sole()->origin)->toBe('luar');
});

it('pemasok menjawab permintaan dengan 1 atau 2', function () {
    $user = owner();
    $offer = SupplierProduct::query()->where('origin', 'bali')->with('supplier')->first();

    inBusiness($user->business_id);
    $request = SupplyRequest::query()->create(['supplier_id' => $offer->supplier_id, 'product_id' => $offer->product_id, 'quantity_per_week' => 5, 'unit' => 'kg', 'message' => 'Halo pemasok']);
    inBusiness(null);

    $replies = app(SupplierBot::class)->handle($offer->supplier->phone, '1');

    expect($request->fresh()->status)->toBe(SupplyRequest::ACCEPTED)
        ->and($replies[0])->toContain('DITERIMA')->toContain('Warung Uji');
});

it('mengirim permintaan dari halaman rekomendasi dengan draf cadangan saat AI mati', function () {
    $user = owner();
    $offer = SupplierProduct::query()->where('origin', 'bali')->with(['supplier', 'product'])->first();

    $this->actingAs($user)
        ->postJson(route('recommendations.draft'), ['offer_id' => $offer->id, 'quantity' => 12, 'unit' => 'kg'])
        ->assertOk()
        ->assertJson(['ai' => false])
        ->assertJsonPath('message', fn ($message) => str_contains($message, 'Warung Uji') && str_contains($message, $offer->product->name));

    $this->actingAs($user)
        ->post(route('recommendations.send'), ['offer_id' => $offer->id, 'quantity' => 12, 'unit' => 'kg', 'message' => 'Om Swastiastu, kami ingin berlangganan produk Anda tiap minggu.'])
        ->assertRedirect(route('recommendations.index'));

    expect(SupplyRequest::query()->withoutGlobalScopes()->sole()->business_id)->toBe($user->business_id)
        ->and(WaMessage::query()->where('phone', $offer->supplier->phone)->sole()->body)->toContain('Balas 1 untuk TERIMA')
        ->and(WaMessage::query()->where('phone', $offer->supplier->phone)->sole()->status)->toBe('logged');

    $this->actingAs($user)
        ->post(route('recommendations.send'), ['offer_id' => $offer->id, 'quantity' => 12, 'unit' => 'kg', 'message' => 'Om Swastiastu, kami ingin berlangganan produk Anda tiap minggu.'])
        ->assertSessionHasErrors('permintaan');
});

it('webhook WA memeriksa rahasia, mengabaikan grup, dan menolak pesan ganda', function () {
    config(['pasok.whatsapp.webhook_secret' => 'rahasia-webhook']);

    $this->post('/webhook/wa/salah', ['sender' => '6281234567890', 'message' => 'halo'])->assertNotFound();
    $this->post('/webhook/wa/rahasia-webhook', ['sender' => '120363@g.us', 'message' => 'halo', 'isgroup' => true])->assertJson(['reason' => 'group']);

    $this->post('/webhook/wa/rahasia-webhook', ['sender' => '081234567890', 'message' => 'halo', 'inboxid' => '77'])->assertJson(['status' => 'accepted']);
    $this->post('/webhook/wa/rahasia-webhook', ['sender' => '081234567890', 'message' => 'halo', 'inboxid' => '77'])->assertJson(['reason' => 'duplicate']);

    expect(WaMessage::query()->where('phone', '6281234567890')->where('direction', 'in')->count())->toBe(1)
        ->and(WaMessage::query()->where('phone', '6281234567890')->where('direction', 'out')->first()->body)->toContain('Cara daftar');
});

it('simulator di akun demo bisa mendaftar pemasok yang hanya terlihat oleh usaha itu', function () {
    $this->post(route('demo'));
    $this->post(route('simulator.send'), ['text' => 'DAFTAR bayam 30 ikat seminggu Tabanan']);
    $this->post(route('simulator.send'), ['text' => 'ya']);

    $supplier = Supplier::query()->where('phone', 'like', 'sim%')->sole();
    $business = auth()->user()->business_id;

    expect($supplier->owner_business_id)->toBe($business)
        ->and($supplier->is_demo)->toBeTrue();

    $this->get(route('suppliers.index'))->assertSee($supplier->name);
});
