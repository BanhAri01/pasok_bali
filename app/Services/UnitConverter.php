<?php

namespace App\Services;

use App\Models\Product;
use App\Support\Text;

class UnitConverter
{
    public function toKg(?Product $product, float $quantity, string $unit, ?float $aiKgPerUnit = null): array
    {
        $unit = Text::unit($unit);

        if ($quantity <= 0) {
            return [null, false];
        }

        $generic = match ($unit) {
            'kg' => 1.0,
            'gram' => 0.001,
            'ons' => 0.1,
            default => null,
        };

        if ($generic !== null) {
            return [round($quantity * $generic, 3), false];
        }

        if ($product) {
            $kgPerBase = $product->kg_per_base > 0 ? $product->kg_per_base : 1.0;

            if ($unit === $product->base_unit) {
                return [round($quantity * $kgPerBase, 3), false];
            }

            if ($product->base_unit === 'liter' && $unit === 'ml') {
                return [round($quantity / 1000 * $kgPerBase, 3), false];
            }

            $conversion = (float) (($product->conversions ?? [])[$unit] ?? 0);

            if ($conversion > 0) {
                return [round($quantity * $conversion * $kgPerBase, 3), false];
            }
        }

        if ($unit === 'liter') {
            return [round($quantity, 3), true];
        }

        if ($aiKgPerUnit !== null && $aiKgPerUnit > 0 && $aiKgPerUnit <= 1000) {
            return [round($quantity * $aiKgPerUnit, 3), true];
        }

        return [null, false];
    }
}
