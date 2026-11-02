<?php

namespace App\Models\Concerns;

use App\Models\Business;
use App\Support\CurrentBusiness;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBusiness
{
    public static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope('usaha', function (Builder $query) {
            $businessId = app(CurrentBusiness::class)->id();

            if ($businessId !== null) {
                $query->where($query->qualifyColumn('business_id'), $businessId);
            }
        });

        static::creating(function ($model) {
            $model->business_id ??= app(CurrentBusiness::class)->id();
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $businessId = app(CurrentBusiness::class)->id();

        if ($businessId === null) {
            return null;
        }

        return static::query()
            ->where($this->qualifyColumn('business_id'), $businessId)
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->first();
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
