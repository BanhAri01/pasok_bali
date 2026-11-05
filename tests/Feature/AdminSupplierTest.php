<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

function adminUser(): User
{
    return User::query()->create(['name' => 'Admin', 'email' => 'admin@contoh.test', 'password' => 'rahasia-panjang', 'role' => User::ADMIN]);
}

it('admin menambah pemasok terverifikasi beserta produknya', function () {
    $eggs = Product::query()->where('name', 'Telur ayam ras')->sole();
    $kangkung = Product::query()->where('name', 'Kangkung')->sole();

    $this->actingAs(adminUser())->post(route('admin.suppliers.store'), [
        'name' => 'Peternakan Sari',
        'phone' => '0812-3456-0001',
        'regency' => 'Tabanan',
        'verification' => 'terverifikasi',
        'products' => [
            ['product_id' => $eggs->id, 'capacity' => 200, 'unit' => 'krat', 'price' => 52000, 'origin' => 'bali'],
            ['product_id' => $kangkung->id, 'capacity' => '', 'unit' => '', 'price' => '', 'origin' => 'bali'],
            ['product_id' => '', 'capacity' => '', 'unit' => '', 'price' => '', 'origin' => 'bali'],
        ],
    ])->assertRedirect(route('admin.index'));

    $supplier = Supplier::query()->where('phone', '6281234560001')->sole();
    $offers = $supplier->products()->get()->keyBy('product_id');

    expect($supplier->verification)->toBe(Supplier::VERIFIED)
        ->and($supplier->owner_business_id)->toBeNull()
        ->and($supplier->is_demo)->toBeFalse()
        ->and($offers)->toHaveCount(2)
        ->and($offers[$eggs->id]->capacity_unit)->toBe('krat')
        ->and($offers[$eggs->id]->price_per_unit)->toBe(52000)
        ->and($offers[$kangkung->id]->capacity_unit)->toBe($kangkung->base_unit)
        ->and($offers[$kangkung->id]->price_per_unit)->toBeNull();
});

it('menolak nomor WA ganda, tanpa produk, dan bukan admin', function () {
    Supplier::query()->create(['name' => 'Lama', 'phone' => '6281234560002', 'regency' => 'Badung']);
    $product = Product::query()->first();

    $this->actingAs(adminUser())->post(route('admin.suppliers.store'), [
        'name' => 'Ganda', 'phone' => '0812 3456 0002', 'regency' => 'Badung', 'verification' => 'terverifikasi',
        'products' => [['product_id' => $product->id, 'origin' => 'bali']],
    ])->assertSessionHasErrors('phone');

    $this->post(route('admin.suppliers.store'), [
        'name' => 'Ganda', 'phone' => 'bukan nomor', 'regency' => 'Badung', 'verification' => 'terverifikasi',
        'products' => [['product_id' => $product->id, 'origin' => 'bali']],
    ])->assertSessionHasErrors('phone');

    $this->post(route('admin.suppliers.store'), [
        'name' => 'Kosong', 'regency' => 'Badung', 'verification' => 'terverifikasi',
        'products' => [['product_id' => '', 'origin' => 'bali']],
    ])->assertSessionHasErrors('products');

    $this->actingAs(owner())->post(route('admin.suppliers.store'), [
        'name' => 'Penyusup', 'regency' => 'Badung', 'verification' => 'terverifikasi',
        'products' => [['product_id' => $product->id, 'origin' => 'bali']],
    ])->assertForbidden();

    expect(Supplier::query()->whereIn('name', ['Ganda', 'Kosong', 'Penyusup'])->count())->toBe(0);
});

it('halaman admin menampilkan form tambah pemasok', function () {
    $this->actingAs(adminUser())->get(route('admin.index'))->assertOk()->assertSee('Tambah pemasok')->assertSee('Telur ayam ras');
});
