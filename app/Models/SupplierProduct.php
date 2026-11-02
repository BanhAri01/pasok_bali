<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['supplier_id', 'product_id', 'origin', 'capacity_per_week', 'capacity_unit', 'price_per_unit', 'price_unit'])]
class SupplierProduct extends Model
{
    public const BALI = 'bali';

    public const OUTSIDE = 'luar';

    public const MIXED = 'campuran';

    protected function casts(): array
    {
        return [
            'capacity_per_week' => 'float',
            'price_per_unit' => 'integer',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
