<?php

namespace App\Support;

class Rupiah
{
    public static function format(int|float|null $value): string
    {
        return 'Rp'.number_format((float) $value, 0, ',', '.');
    }

    public static function number(int|float|null $value, int $maxDecimals = 1): string
    {
        $value = (float) $value;
        $decimals = floor($value) == $value ? 0 : $maxDecimals;

        return number_format($value, $decimals, ',', '.');
    }
}
