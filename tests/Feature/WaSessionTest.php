<?php

use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\SupplyRequest;
use App\Models\WaConversation;
use App\Models\WaMessage;
use App\Services\SupplierBot;

it('meminta mengetik PASOK sekali bila belum ada sesi dan tidak memanggil AI', function () {
    $ai = fakeAi();
    $bot = app(SupplierBot::class);

    expect($bot->handle('6281200000001', 'halo kak'))->toBe([SupplierBot::CLOSED])
        ->and($bot->handle('6281200000001', 'halo lagi'))->toBe([])
        ->and($bot->handle('6281200000001', 'DAFTAR kangkung 50 ikat Gianyar'))->toBe([])
        ->and($ai->calls)->toBe([])
        ->and(WaConversation::query()->count())->toBe(0);
});

it('kata PASOK membuka sesi lalu pertanyaan bebas dijawab AI dengan data pemasok sendiri', function () {
    $ai = fakeAi(['tanya' => ['intent' => 'tanya', 'answer' => 'Pendaftaran **Bapak** masih menunggu verifikasi admin.']]);
    $phone = '6281200000002';
    Supplier::query()->create(['name' => 'Tani Mekar', 'phone' => $phone, 'regency' => 'Gianyar']);
    $bot = app(SupplierBot::class);

    expect($bot->handle($phone, 'PASOK')[0])->toContain('Cara daftar')
        ->and($bot->handle($phone, 'kapan akun saya diverifikasi?'))->toBe(['Pendaftaran Bapak masih menunggu verifikasi admin.'])
        ->and($ai->calls[0]['input'])->toBe('kapan akun saya diverifikasi?')
        ->and(WaConversation::query()->where('phone', $phone)->sole()->expires_at->isFuture())->toBeTrue();
});

it('AI mengenali niat daftar dari kalimat bebas lalu meneruskan ke pendaftaran', function () {
    fakeAi([
        'tanya' => ['intent' => 'daftar', 'answer' => ''],
        'daftar' => ['name' => 'Kebun Ayu', 'regency' => 'Bangli', 'products' => [
            ['product_name' => 'kangkung', 'capacity_per_week' => 40, 'unit' => 'ikat', 'price' => 0, 'price_unit' => '', 'produced_in_bali' => true],
        ]],
    ]);
    $phone = '6281200000003';
    $bot = app(SupplierBot::class);

    $bot->handle($phone, 'pasok');
    expect($bot->handle($phone, 'saya punya kebun kangkung di Bangli 40 ikat seminggu')[0])->toContain('Balas YA');

    $bot->handle($phone, 'ya');

    expect(Supplier::query()->where('phone', $phone)->sole()->name)->toBe('Kebun Ayu')
        ->and(WaConversation::query()->where('phone', $phone)->sole()->expires_at->isFuture())->toBeTrue();
});

it('memberi jawaban cadangan bila AI tidak tersedia dan SELESAI menutup sesi', function () {
    $phone = '6281200000004';
    $bot = app(SupplierBot::class);

    $bot->handle($phone, 'PASOK');
    expect($bot->handle($phone, 'berapa harga cabai?')[0])->toContain('belum bisa kami jawab')
        ->and($bot->handle($phone, 'selesai'))->toBe([SupplierBot::ENDED])
        ->and(WaConversation::query()->count())->toBe(0);
});

it('pemasok dengan permintaan menunggu bisa membalas 1 tanpa membuka sesi', function () {
    $user = owner();
    $offer = SupplierProduct::query()->where('origin', 'bali')->with('supplier')->first();

    inBusiness($user->business_id);
    $request = SupplyRequest::query()->create(['supplier_id' => $offer->supplier_id, 'product_id' => $offer->product_id, 'quantity_per_week' => 5, 'unit' => 'kg', 'message' => 'Halo pemasok']);
    inBusiness(null);

    app(SupplierBot::class)->handle($offer->supplier->phone, '1');

    expect($request->fresh()->status)->toBe(SupplyRequest::ACCEPTED);
});

it('membatasi pertanyaan AI per nomor per hari', function () {
    config(['pasok.whatsapp.ai_per_phone' => 1]);
    $ai = fakeAi(['tanya' => ['intent' => 'tanya', 'answer' => 'Jawaban.']]);
    $phone = '6281200000005';
    $bot = app(SupplierBot::class);

    $bot->handle($phone, 'PASOK');
    $bot->handle($phone, 'pertanyaan satu');
    expect($bot->handle($phone, 'pertanyaan dua')[0])->toContain('belum bisa kami jawab')
        ->and($ai->count('tanya'))->toBe(1);
});

it('webhook asli memproses pesan PASOK dari nomor WA', function () {
    config(['pasok.whatsapp.webhook_secret' => 'rahasia-webhook', 'pasok.whatsapp.device' => null]);

    $this->post('/webhook/wa/rahasia-webhook', ['sender' => '081200000006', 'message' => 'Pasok Bali', 'inboxid' => 's1'])->assertJson(['status' => 'accepted']);

    expect(WaMessage::query()->where('phone', '6281200000006')->where('direction', 'out')->sole()->body)->toContain('Cara daftar');
});
