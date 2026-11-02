<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['direction', 'phone', 'body', 'channel', 'status', 'external_id', 'error'])]
class WaMessage extends Model
{
    public const IN = 'in';

    public const OUT = 'out';
}
