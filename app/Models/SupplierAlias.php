<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['business_id', 'alias', 'supplier_id'])]
class SupplierAlias extends Model
{
    use BelongsToBusiness;
}
