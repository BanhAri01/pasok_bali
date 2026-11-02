<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['receipt_id', 'business_id', 'name_raw', 'product_id', 'category', 'quantity', 'unit', 'quantity_kg', 'kg_estimated', 'ai_kg_per_unit', 'total_price', 'origin', 'evidence', 'confidence', 'needs_review'])]
class ReceiptItem extends Model
{
    use BelongsToBusiness;

    public const UNKNOWN = 'tidak_diketahui';

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'quantity_kg' => 'float',
            'kg_estimated' => 'boolean',
            'ai_kg_per_unit' => 'float',
            'total_price' => 'integer',
            'confidence' => 'float',
            'needs_review' => 'boolean',
        ];
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
