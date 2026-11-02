<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class RecommendationEngine
{
    public const WEEKS_PER_MONTH = 4.33;

    public function __construct(private ComplianceCalculator $compliance, private ProductCatalog $catalog, private UnitConverter $units) {}

    public function forMonth(Business $business, CarbonImmutable $month, ?array $report = null, int $productsPerGroup = 3): array
    {
        $report ??= $this->compliance->forMonth($business->id, $month);
        $result = [];

        foreach ($report['groups'] as $key => $group) {
            if ($group['status'] !== 'kurang') {
                continue;
            }

            $categories = config("pasok.quota_groups.{$key}.categories");
            $products = $this->compliance->nonLocalProducts($business->id, $month, $categories)->take($productsPerGroup);

            if ($products->isEmpty()) {
                continue;
            }

            $offers = SupplierProduct::query()
                ->with(['supplier:id,name,regency,verification,is_demo,phone', 'product:id,name,category'])
                ->where('origin', SupplierProduct::BALI)
                ->whereHas('product', fn (Builder $query) => $query->whereIn('category', $categories))
                ->whereHas('supplier', fn (Builder $query) => $query->visibleTo($business->id)->where('active', true))
                ->get();

            $items = [];

            foreach ($products as $row) {
                $product = $this->catalog->find((int) $row->product_id);

                if (! $product) {
                    continue;
                }

                $exact = $offers->where('product_id', $product->id);
                $alternative = $exact->isEmpty();
                $pool = $alternative ? $offers->where('product.category', $product->category) : $exact;

                $candidates = $pool
                    ->sortBy(fn (SupplierProduct $offer) => [
                        $offer->supplier->verification === Supplier::VERIFIED ? 0 : 1,
                        $offer->supplier->regency === $business->regency ? 0 : 1,
                        $offer->price_per_unit ?? PHP_INT_MAX,
                    ])
                    ->take($alternative ? 2 : 3)
                    ->map(function (SupplierProduct $offer) use ($group, $row, $alternative) {
                        $offerProduct = $this->catalog->find($offer->product_id);
                        $capacityKg = $offer->capacity_per_week
                            ? $this->units->toKg($offerProduct, $offer->capacity_per_week * self::WEEKS_PER_MONTH, (string) $offer->capacity_unit)[0]
                            : null;
                        $switchKg = round(min((float) $row->kg, $capacityKg ?? (float) $row->kg), 2);
                        $newPercent = round(($group['local_kg'] + $switchKg) / $group['total_kg'] * 100, 1);

                        return [
                            'offer' => $offer,
                            'capacity_kg_month' => $capacityKg,
                            'switch_kg' => $switchKg,
                            'new_percent' => $newPercent,
                            'reaches_quota' => $newPercent >= $group['quota'],
                            'weekly_quantity' => round($switchKg / self::WEEKS_PER_MONTH, 1),
                            'alternative' => $alternative,
                        ];
                    })
                    ->values()
                    ->all();

                $items[] = [
                    'product' => $product,
                    'non_local_kg' => round((float) $row->kg, 2),
                    'non_local_rupiah' => (int) $row->rupiah,
                    'candidates' => $candidates,
                ];
            }

            $result[$key] = ['group' => $group, 'items' => $items];
        }

        return $result;
    }
}
