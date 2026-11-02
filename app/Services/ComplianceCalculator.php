<?php

namespace App\Services;

use App\Models\QuotaRule;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ComplianceCalculator
{
    public function quotas(): array
    {
        $rules = Cache::remember('pasok:kuota', 600, fn () => QuotaRule::query()->pluck('min_percent', 'group')->all());

        return collect(config('pasok.quota_groups'))
            ->map(fn (array $group, string $key) => $group + ['quota' => (int) ($rules[$key] ?? $group['percent'])])
            ->all();
    }

    public function forMonth(int $businessId, CarbonImmutable $month): array
    {
        $rows = $this->baseQuery($businessId, $month)
            ->selectRaw('receipt_items.category, receipt_items.origin, receipt_items.evidence, sum(receipt_items.quantity_kg) as kg, sum(receipt_items.total_price) as rupiah, count(*) as lines, sum(case when receipt_items.quantity_kg is null then 1 else 0 end) as unmeasured')
            ->groupBy('receipt_items.category', 'receipt_items.origin', 'receipt_items.evidence')
            ->toBase()
            ->get();

        $groups = [];

        foreach ($this->quotas() as $key => $group) {
            $groupRows = $rows->whereIn('category', $group['categories']);
            $sum = fn (callable $filter, string $field = 'kg') => round((float) $groupRows->filter($filter)->sum($field), 2);

            $total = $sum(fn () => true);
            $local = $sum(fn ($row) => $row->origin === SupplierProduct::BALI);
            $verified = $sum(fn ($row) => $row->origin === SupplierProduct::BALI && $row->evidence === Supplier::VERIFIED);
            $percent = $total > 0 ? round($local / $total * 100, 1) : null;

            $groups[$key] = [
                'key' => $key,
                'label' => $group['label'],
                'quota' => $group['quota'],
                'total_kg' => $total,
                'local_kg' => $local,
                'verified_kg' => $verified,
                'declared_kg' => round($local - $verified, 2),
                'outside_kg' => $sum(fn ($row) => in_array($row->origin, [SupplierProduct::OUTSIDE, SupplierProduct::MIXED], true)),
                'unknown_kg' => $sum(fn ($row) => $row->origin === ReceiptItem::UNKNOWN),
                'total_rupiah' => (int) $groupRows->sum('rupiah'),
                'local_rupiah' => (int) $groupRows->where('origin', SupplierProduct::BALI)->sum('rupiah'),
                'unmeasured' => (int) $groupRows->sum('unmeasured'),
                'percent' => $percent,
                'verified_percent' => $total > 0 ? round($verified / $total * 100, 1) : null,
                'gap_kg' => $total > 0 ? round(max(0, $group['quota'] / 100 * $total - $local), 2) : 0.0,
                'status' => match (true) {
                    $total <= 0 => 'kosong',
                    $percent >= $group['quota'] => 'patuh',
                    default => 'kurang',
                },
            ];
        }

        $receipts = Receipt::query()
            ->where('business_id', $businessId)
            ->whereBetween('receipt_date', self::range($month))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'month' => $month,
            'groups' => $groups,
            'approved_receipts' => (int) ($receipts[Receipt::APPROVED] ?? 0),
            'pending_receipts' => (int) (($receipts[Receipt::NEEDS_CHECK] ?? 0) + ($receipts[Receipt::PROCESSING] ?? 0)),
            'compliant_groups' => collect($groups)->where('status', 'patuh')->count(),
            'measured_groups' => collect($groups)->where('status', '!=', 'kosong')->count(),
        ];
    }

    public function nonLocalProducts(int $businessId, CarbonImmutable $month, array $categories): Collection
    {
        return $this->baseQuery($businessId, $month)
            ->whereIn('receipt_items.category', $categories)
            ->whereNotNull('receipt_items.product_id')
            ->where('receipt_items.origin', '!=', SupplierProduct::BALI)
            ->whereNotNull('receipt_items.quantity_kg')
            ->selectRaw('receipt_items.product_id, sum(receipt_items.quantity_kg) as kg, sum(receipt_items.total_price) as rupiah')
            ->groupBy('receipt_items.product_id')
            ->orderByDesc('kg')
            ->toBase()
            ->get();
    }

    public static function range(CarbonImmutable $month): array
    {
        return [$month->startOfMonth()->format('Y-m-d 00:00:00'), $month->endOfMonth()->format('Y-m-d 23:59:59')];
    }

    private function baseQuery(int $businessId, CarbonImmutable $month)
    {
        return ReceiptItem::query()
            ->join('receipts', 'receipts.id', '=', 'receipt_items.receipt_id')
            ->where('receipt_items.business_id', $businessId)
            ->where('receipts.business_id', $businessId)
            ->where('receipts.status', Receipt::APPROVED)
            ->whereBetween('receipts.receipt_date', self::range($month))
            ->whereNotNull('receipt_items.category');
    }
}
