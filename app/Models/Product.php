<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'category', 'base_unit', 'kg_per_base', 'conversions'])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'kg_per_base' => 'float',
            'conversions' => 'array',
        ];
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(ProductAlias::class);
    }

    public function categoryLabel(): string
    {
        return config('pasok.categories.'.$this->category, $this->category);
    }
}
