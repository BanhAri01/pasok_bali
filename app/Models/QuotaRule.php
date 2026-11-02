<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['group', 'min_percent', 'source'])]
class QuotaRule extends Model
{
    protected function casts(): array
    {
        return ['min_percent' => 'integer'];
    }
}
