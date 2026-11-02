<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'supplier_id', 'product_id', 'quantity_per_week', 'unit', 'offered_price', 'status', 'message', 'responded_at'])]
class SupplyRequest extends Model
{
    use BelongsToBusiness;

    public const WAITING = 'menunggu';

    public const ACCEPTED = 'diterima';

    public const REJECTED = 'ditolak';

    protected function casts(): array
    {
        return [
            'quantity_per_week' => 'float',
            'offered_price' => 'integer',
            'responded_at' => 'datetime',
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
