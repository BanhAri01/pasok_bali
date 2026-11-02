<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['owner_business_id', 'name', 'regency', 'phone', 'verification', 'is_demo', 'active', 'notes'])]
class Supplier extends Model
{
    public const VERIFIED = 'terverifikasi';

    public const DECLARED = 'pernyataan';

    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function scopeVisibleTo(Builder $query, int $businessId): void
    {
        $query->where(fn (Builder $inner) => $inner->whereNull('owner_business_id')->orWhere('owner_business_id', $businessId));
    }

    public function isVerified(): bool
    {
        return $this->verification === self::VERIFIED;
    }
}
