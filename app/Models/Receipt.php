<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['business_id', 'uploaded_by', 'supplier_id', 'supplier_name_raw', 'receipt_date', 'status', 'image_path', 'ai_confidence', 'ai_used', 'note', 'total', 'approved_at'])]
class Receipt extends Model
{
    use BelongsToBusiness;

    public const PROCESSING = 'diproses';

    public const NEEDS_CHECK = 'perlu_cek';

    public const APPROVED = 'disetujui';

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
            'ai_confidence' => 'float',
            'ai_used' => 'boolean',
            'total' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::PROCESSING => 'Sedang dibaca AI',
            self::NEEDS_CHECK => 'Perlu dicek',
            self::APPROVED => 'Disetujui',
            default => $this->status,
        };
    }
}
