<?php

namespace App\Services;

use App\Ai\AiGateway;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Support\CurrentBusiness;
use App\Support\Text;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReceiptPipeline
{
    public const REVIEW_CONFIDENCE = 0.6;

    public function __construct(
        private AiGateway $ai,
        private ReceiptReader $reader,
        private ItemClassifier $classifier,
        private ProductCatalog $catalog,
        private SupplierMatcher $suppliers,
        private UnitConverter $units,
        private CurrentBusiness $current,
    ) {}

    public function process(Receipt $receipt): void
    {
        $this->current->run($receipt->business_id, fn () => $this->processInBusiness($receipt));
    }

    private function processInBusiness(Receipt $receipt): void
    {
        if (! $this->ai->available()) {
            $this->fail($receipt, 'AI pembaca nota belum aktif. Isi barang secara manual di bawah.');

            return;
        }

        $disk = Storage::disk('local');

        if (! $receipt->image_path || ! $disk->exists($receipt->image_path)) {
            $this->fail($receipt, 'Foto nota tidak ditemukan. Isi barang secara manual.');

            return;
        }

        $read = $this->reader->read($receipt->business_id, $disk->get($receipt->image_path), $disk->mimeType($receipt->image_path) ?: 'image/jpeg');

        if ($read === null) {
            $this->fail($receipt, 'AI belum berhasil membaca nota ini. Coba foto ulang yang lebih terang dan lurus, atau isi manual.');

            return;
        }

        if (! $read['legible']) {
            $this->fail($receipt, 'Gambar tidak terbaca sebagai nota belanja. Periksa fotonya atau isi manual.');

            return;
        }

        $supplier = $this->suppliers->match($receipt->business_id, $read['supplier_name']);

        DB::transaction(function () use ($receipt, $read, $supplier) {
            $receipt->fill([
                'supplier_id' => $supplier?->id,
                'supplier_name_raw' => $read['supplier_name'] ?: null,
                'receipt_date' => $read['receipt_date'] ?? $receipt->receipt_date ?? now()->toDateString(),
                'ai_used' => true,
                'ai_confidence' => round(collect($read['items'])->avg('confidence') ?? 0, 3),
                'note' => null,
            ]);

            $this->replaceItems($receipt, $this->resolve($receipt->business_id, $read['items'], $supplier, true));
            $receipt->status = Receipt::NEEDS_CHECK;
            $receipt->save();
        });
    }

    public function review(Receipt $receipt, ?Supplier $supplier, string $date, array $rows, bool $approve): void
    {
        $previous = $receipt->items()->get()->keyBy('id');

        $resolved = collect($rows)->values()->map(function (array $row) use ($previous) {
            $old = isset($row['id']) ? $previous->get((int) $row['id']) : null;
            $product = $this->catalog->find($row['product_id'] ?? null);
            $category = $product?->category ?? (($row['category'] ?? '') ?: null);

            return [
                'name' => mb_substr(trim((string) $row['name']), 0, 100),
                'quantity' => (float) $row['quantity'],
                'unit' => Text::unit($row['unit']),
                'total_price' => (int) $row['total_price'],
                'confidence' => $old && $old->product_id === $product?->id ? $old->confidence : 1.0,
                'match_source' => $product ? ($old && $old->product_id === $product->id ? $old->match_source : ReceiptItem::MATCH_USER) : null,
                'product_id' => $product?->id,
                'category' => $category,
                'ai_kg_per_unit' => $old?->ai_kg_per_unit,
            ];
        })->all();

        DB::transaction(function () use ($receipt, $supplier, $date, $resolved, $approve) {
            $receipt->fill([
                'supplier_id' => $supplier?->id,
                'receipt_date' => $date,
                'status' => $approve ? Receipt::APPROVED : Receipt::NEEDS_CHECK,
                'approved_at' => $approve ? now() : null,
                'note' => null,
            ]);

            $this->replaceItems($receipt, $this->resolve($receipt->business_id, $resolved, $supplier, false));
            $receipt->save();

            if (! $approve) {
                return;
            }

            if ($supplier && $receipt->supplier_name_raw) {
                $this->suppliers->learn($receipt->business_id, $receipt->supplier_name_raw, $supplier->id);
            }

            foreach ($resolved as $row) {
                if ($row['product_id']) {
                    $this->catalog->learn($receipt->business_id, $row['name'], $row['product_id'], 'pengguna');
                }
            }
        });
    }

    private function resolve(int $businessId, array $rows, ?Supplier $supplier, bool $useAi): array
    {
        $known = $this->catalog->lookup($businessId, array_column($rows, 'name'));

        $unknown = [];

        foreach ($rows as $index => $row) {
            if (! array_key_exists('product_id', $row) && ! isset($known[Text::key($row['name'])])) {
                $unknown[$index] = $row;
            }
        }

        $classified = $useAi && $unknown !== [] ? $this->classifier->classify($businessId, $unknown) : [];

        $origins = $this->origins($supplier);

        return collect($rows)->map(function (array $row, int $index) use ($known, $classified, $origins, $supplier) {
            if (array_key_exists('product_id', $row)) {
                $product = $this->catalog->find($row['product_id']);
                $category = $row['category'];
                $aiKg = $row['ai_kg_per_unit'];
                $confidence = (float) $row['confidence'];
                $source = $row['match_source'] ?? null;
                $ignored = false;
            } elseif ($hit = $known[Text::key($row['name'])] ?? null) {
                $product = $hit;
                $category = $hit->category;
                $aiKg = null;
                $confidence = (float) $row['confidence'];
                $source = ReceiptItem::MATCH_DICTIONARY;
                $ignored = false;
            } else {
                $guess = $classified[$index] ?? null;
                $product = $this->catalog->find($guess['product_id'] ?? null);
                $category = $guess['category'] ?? null;
                $aiKg = $guess['kg_per_unit'] ?? null;
                $confidence = min((float) $row['confidence'], (float) ($guess['confidence'] ?? 0));
                $source = $product ? ReceiptItem::MATCH_AI : null;
                $ignored = (bool) ($guess['ignored'] ?? false);
            }

            [$kg, $estimated] = $this->units->toKg($product, (float) $row['quantity'], (string) $row['unit'], $aiKg);
            [$origin, $evidence] = $this->originFor($supplier, $product?->id, $origins);

            return [
                'name_raw' => $row['name'],
                'product_id' => $product?->id,
                'match_source' => $source,
                'category' => $category,
                'quantity' => (float) $row['quantity'],
                'unit' => Text::unit($row['unit']),
                'quantity_kg' => $kg,
                'kg_estimated' => $estimated,
                'ai_kg_per_unit' => $aiKg,
                'total_price' => (int) $row['total_price'],
                'origin' => $origin,
                'evidence' => $evidence,
                'confidence' => round($confidence, 3),
                'needs_review' => ! $ignored && ($category === null || $kg === null || $estimated || $confidence < self::REVIEW_CONFIDENCE),
            ];
        })->all();
    }

    private function origins(?Supplier $supplier): Collection
    {
        if (! $supplier) {
            return collect();
        }

        return SupplierProduct::query()->where('supplier_id', $supplier->id)->pluck('origin', 'product_id');
    }

    private function originFor(?Supplier $supplier, ?int $productId, Collection $origins): array
    {
        $origin = $supplier && $productId ? $origins->get($productId) : null;

        if (! $origin) {
            return [ReceiptItem::UNKNOWN, ReceiptItem::UNKNOWN];
        }

        return [$origin, $supplier->verification];
    }

    private function replaceItems(Receipt $receipt, array $items): void
    {
        $receipt->items()->delete();

        foreach ($items as $item) {
            $receipt->items()->create($item + ['business_id' => $receipt->business_id]);
        }

        $receipt->total = array_sum(array_column($items, 'total_price'));
    }

    private function fail(Receipt $receipt, string $note): void
    {
        $receipt->update([
            'status' => Receipt::NEEDS_CHECK,
            'note' => $note,
            'receipt_date' => $receipt->receipt_date ?? now()->toDateString(),
        ]);
    }

    public function refreshSupplierOrigins(Supplier $supplier): void
    {
        $origins = $this->origins($supplier);
        $receiptIds = Receipt::query()->where('supplier_id', $supplier->id)->pluck('id');

        if ($receiptIds->isEmpty()) {
            return;
        }

        ReceiptItem::query()
            ->whereIn('receipt_id', $receiptIds)
            ->whereNotNull('product_id')
            ->select(['id', 'product_id'])
            ->chunkById(500, function ($items) use ($origins, $supplier) {
                foreach ($items->groupBy(fn ($item) => $origins->get($item->product_id) ?? ReceiptItem::UNKNOWN) as $origin => $group) {
                    ReceiptItem::query()->whereIn('id', $group->pluck('id'))->update([
                        'origin' => $origin,
                        'evidence' => $origin === ReceiptItem::UNKNOWN ? ReceiptItem::UNKNOWN : $supplier->verification,
                    ]);
                }
            });
    }
}
