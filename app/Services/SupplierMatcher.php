<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierAlias;
use App\Support\Text;

class SupplierMatcher
{
    public function match(int $businessId, ?string $rawName): ?Supplier
    {
        $key = Text::key($rawName);

        if ($key === '') {
            return null;
        }

        $aliasSupplierId = SupplierAlias::query()->where('business_id', $businessId)->where('alias', $key)->value('supplier_id');

        $candidates = Supplier::query()->visibleTo($businessId)->where('active', true)->get(['id', 'name', 'regency', 'verification', 'owner_business_id', 'is_demo']);

        if ($aliasSupplierId) {
            $found = $candidates->firstWhere('id', $aliasSupplierId);

            if ($found) {
                return $found;
            }
        }

        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $supplier) {
            $candidate = Text::key($supplier->name);

            if ($candidate === $key) {
                return $supplier;
            }

            similar_text($candidate, $key, $score);

            $contains = strlen($key) >= 6 && strlen($candidate) >= 6 && (str_contains($candidate, $key) || str_contains($key, $candidate));

            if ($contains) {
                $score = max($score, 90.0);
            }

            if ($score > $bestScore) {
                [$best, $bestScore] = [$supplier, $score];
            }
        }

        return $bestScore >= 85 ? $best : null;
    }

    public function learn(int $businessId, ?string $rawName, int $supplierId): void
    {
        $key = Text::key($rawName);

        if ($key === '') {
            return;
        }

        SupplierAlias::query()->updateOrCreate(
            ['business_id' => $businessId, 'alias' => mb_substr($key, 0, 120)],
            ['supplier_id' => $supplierId],
        );
    }
}
