<?php

use App\Models\Product;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\SupplyRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function receiptFor($user): Receipt
{
    inBusiness($user->business_id);
    Storage::disk('local')->put('nota/'.$user->business_id.'/a.jpg', 'gambar');
    $receipt = Receipt::query()->create(['uploaded_by' => $user->id, 'status' => Receipt::NEEDS_CHECK, 'image_path' => 'nota/'.$user->business_id.'/a.jpg', 'receipt_date' => now()->toDateString()]);
    inBusiness(null);

    return $receipt;
}

beforeEach(function () {
    Storage::fake('local');
});

it('usaha lain tidak bisa membuka, mengubah, atau menghapus nota', function () {
    $a = owner(['name' => 'Usaha A']);
    $b = owner(['name' => 'Usaha B']);
    $receipt = receiptFor($b);

    $this->actingAs($a)->get(route('receipts.show', $receipt))->assertNotFound();
    $this->actingAs($a)->get(route('receipts.image', $receipt))->assertNotFound();
    $this->actingAs($a)->get(route('receipts.status', $receipt))->assertNotFound();
    $this->actingAs($a)->put(route('receipts.update', $receipt), ['receipt_date' => now()->toDateString(), 'action' => 'setujui', 'items' => [['name' => 'x', 'quantity' => 1, 'unit' => 'kg', 'total_price' => 1]]])->assertNotFound();
    $this->actingAs($a)->delete(route('receipts.destroy', $receipt))->assertNotFound();

    $this->actingAs($a)->get(route('receipts.index'))->assertOk()->assertDontSee(route('receipts.show', $receipt));
    $this->actingAs($b)->get(route('receipts.image', $receipt))->assertOk();
    expect(Receipt::query()->whereKey($receipt->id)->exists())->toBeTrue();
});

it('pemasok milik simulator usaha lain tidak bisa dipilih', function () {
    $a = owner(['name' => 'Usaha A']);
    $b = owner(['name' => 'Usaha B']);
    $hidden = Supplier::query()->create(['name' => 'Pemasok Simulator B', 'phone' => 'simrahasia', 'owner_business_id' => $b->business_id, 'regency' => 'Bangli', 'is_demo' => true]);
    $receipt = receiptFor($a);

    $this->actingAs($a)->put(route('receipts.update', $receipt), [
        'supplier_id' => $hidden->id,
        'receipt_date' => now()->toDateString(),
        'action' => 'setujui',
        'items' => [['name' => 'Telur', 'quantity' => 1, 'unit' => 'kg', 'total_price' => 1]],
    ])->assertSessionHasErrors('supplier_id');

    $this->actingAs($a)->get(route('suppliers.index'))->assertDontSee('Pemasok Simulator B');
});

it('usaha lain tidak bisa menjawab permintaan pasokan lewat simulator', function () {
    $a = owner(['name' => 'Usaha A']);
    $b = owner(['name' => 'Usaha B']);
    $offer = SupplierProduct::query()->where('origin', 'bali')->first();

    inBusiness($b->business_id);
    $request = SupplyRequest::query()->create(['supplier_id' => $offer->supplier_id, 'product_id' => $offer->product_id, 'quantity_per_week' => 5, 'unit' => 'kg', 'message' => 'Halo pemasok']);
    inBusiness(null);

    $this->actingAs($a)->post(route('simulator.reply', $request), ['answer' => '1'])->assertNotFound();
    expect($request->fresh()->status)->toBe(SupplyRequest::WAITING);
});

it('halaman usaha menolak tamu dan admin tanpa usaha diarahkan ke panel admin', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));

    $admin = User::query()->create(['name' => 'Admin', 'email' => 'admin@contoh.test', 'password' => 'rahasia-panjang', 'role' => 'admin']);
    $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.index'));
    $this->actingAs(owner())->get(route('admin.index'))->assertForbidden();
});

it('validasi produk menolak id yang tidak ada', function () {
    $user = owner();
    $receipt = receiptFor($user);

    $this->actingAs($user)->put(route('receipts.update', $receipt), [
        'receipt_date' => now()->toDateString(),
        'action' => 'simpan',
        'items' => [['name' => 'x', 'product_id' => Product::query()->max('id') + 100, 'quantity' => 1, 'unit' => 'kg', 'total_price' => 1]],
    ])->assertSessionHasErrors('items.0.product_id');
});

it('membalas di simulator hanya mengubah permintaan milik usaha sendiri dan tidak menampilkan pesan usaha lain', function () {
    $a = owner(['name' => 'Usaha A']);
    $b = owner(['name' => 'Usaha B']);
    $offer = SupplierProduct::query()->where('origin', 'bali')->where('supplier_id', fn ($query) => $query->select('id')->from('suppliers')->where('phone', 'like', \App\Support\Phone::SAMPLE_PREFIX.'%')->limit(1))->first();

    inBusiness($b->business_id);
    $older = SupplyRequest::query()->create(['supplier_id' => $offer->supplier_id, 'product_id' => $offer->product_id, 'quantity_per_week' => 5, 'unit' => 'kg', 'message' => 'Rahasia Usaha B']);
    inBusiness(null);
    app(\App\Services\Messenger::class)->send($offer->supplier->phone, 'Rahasia Usaha B');

    $this->travel(1)->minutes();

    inBusiness($a->business_id);
    $mine = SupplyRequest::query()->create(['supplier_id' => $offer->supplier_id, 'product_id' => $offer->product_id, 'quantity_per_week' => 3, 'unit' => 'kg', 'message' => 'Permintaan Usaha A']);
    inBusiness(null);

    $this->actingAs($a)->post(route('simulator.reply', $mine), ['answer' => '1'])->assertRedirect();

    expect($mine->fresh()->status)->toBe(SupplyRequest::ACCEPTED)
        ->and($older->fresh()->status)->toBe(SupplyRequest::WAITING);

    $this->actingAs($a)->get(route('simulator.index'))->assertOk()->assertSee('Permintaan Usaha A')->assertDontSee('Rahasia Usaha B');
});
