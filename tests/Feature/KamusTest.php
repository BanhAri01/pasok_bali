<?php

use App\Models\Product;
use App\Models\ProductAlias;
use App\Services\UnitConverter;

it('memuat alias tambahan dari produk.json untuk produk bawaan', function () {
    $cabai = Product::query()->where('name', 'Cabai rawit')->sole();

    expect(ProductAlias::query()->whereNull('business_id')->where('alias', 'tabia krinyi')->value('product_id'))->toBe($cabai->id)
        ->and(ProductAlias::query()->where('alias', 'tabia')->value('product_id'))->toBe($cabai->id);
});

it('menghitung krat telur sebagai 30 butir dan tidak menebak isi peti', function () {
    $telur = Product::query()->where('name', 'Telur ayam ras')->sole();
    $kampung = Product::query()->where('name', 'Telur ayam kampung')->sole();
    $converter = app(UnitConverter::class);

    expect($telur->base_unit)->toBe('kg')
        ->and($converter->toKg($telur, 2, 'tray'))->toBe([3.6, false])
        ->and($converter->toKg($telur, 2, 'krt'))->toBe([3.6, false])
        ->and($converter->toKg($kampung, 1, 'krat'))->toBe([1.35, false])
        ->and($converter->toKg($telur, 1, 'peti'))->toBe([null, false])
        ->and($converter->toKg($telur, 1, 'peti', 15.0))->toBe([15.0, true]);
});

it('menambah produk baru dari kamus dengan kategori yang sah', function () {
    expect(Product::query()->count())->toBeGreaterThan(88)
        ->and(Product::query()->whereNotIn('category', array_keys(config('pasok.categories')))->exists())->toBeFalse();
});
