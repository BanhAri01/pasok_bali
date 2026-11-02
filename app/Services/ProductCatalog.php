<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAlias;
use App\Support\Text;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductCatalog
{
    private ?Collection $products = null;

    public function all(): Collection
    {
        return $this->products ??= Product::hydrate(
            Cache::remember('pasok:produk', 600, fn () => Product::query()->orderBy('name')->toBase()->get()->map(fn ($row) => (array) $row)->all())
        );
    }

    public function find(?int $id): ?Product
    {
        return $id ? $this->all()->firstWhere('id', $id) : null;
    }

    public function forget(): void
    {
        $this->products = null;
        Cache::forget('pasok:produk');
    }

    public function lookup(int $businessId, array $names): array
    {
        $keys = collect($names)->map(fn ($name) => Text::key($name))->filter()->unique()->values();

        if ($keys->isEmpty()) {
            return [];
        }

        $byName = $this->all()->keyBy(fn (Product $product) => Text::key($product->name));

        $aliases = ProductAlias::query()
            ->whereIn('alias', $keys)
            ->where(fn ($query) => $query->whereNull('business_id')->orWhere('business_id', $businessId))
            ->orderByRaw('business_id is null')
            ->get(['alias', 'product_id', 'business_id']);

        $found = [];

        foreach ($keys as $key) {
            $alias = $aliases->firstWhere('alias', $key);
            $product = $alias ? $this->find($alias->product_id) : $byName->get($key);

            if ($product) {
                $found[$key] = $product;
            }
        }

        return $found;
    }

    public function learn(int $businessId, string $name, int $productId, string $source): void
    {
        $key = Text::key($name);

        if ($key === '' || ! $this->find($productId)) {
            return;
        }

        $global = ProductAlias::query()->whereNull('business_id')->where('alias', $key)->value('product_id');

        if ($global === $productId) {
            return;
        }

        ProductAlias::query()->updateOrCreate(
            ['business_id' => $businessId, 'alias' => mb_substr($key, 0, 100)],
            ['product_id' => $productId, 'source' => $source],
        );
    }

    public function promptList(): string
    {
        return $this->all()
            ->map(fn (Product $product) => "{$product->id}|{$product->name}|{$product->category}|{$product->base_unit}")
            ->implode("\n");
    }
}
