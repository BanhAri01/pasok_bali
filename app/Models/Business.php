<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'regency', 'is_demo', 'demo_expires_at'])]
class Business extends Model
{
    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
            'demo_expires_at' => 'datetime',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function typeLabel(): string
    {
        return config('pasok.business_types.'.$this->type, $this->type);
    }

    public function demoExpired(): bool
    {
        return $this->is_demo && $this->demo_expires_at?->isPast();
    }
}
