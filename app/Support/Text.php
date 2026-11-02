<?php

namespace App\Support;

use Illuminate\Support\Str;

class Text
{
    public static function key(?string $value): string
    {
        $value = Str::lower(Str::ascii((string) $value));
        $value = preg_replace('/[^a-z0-9 ]+/', ' ', $value);

        return trim((string) preg_replace('/\s+/', ' ', (string) $value));
    }

    public static function unit(?string $value): string
    {
        $key = str_replace(' ', '', self::key($value));

        return match ($key) {
            'kg', 'kgs', 'kilo', 'kilogram', 'kilogr', 'kl' => 'kg',
            'g', 'gr', 'grm', 'gram' => 'gram',
            'ons', 'on' => 'ons',
            'l', 'lt', 'ltr', 'liter', 'litre' => 'liter',
            'ml', 'mili', 'mililiter' => 'ml',
            'btr', 'butir', 'bj', 'biji', 'btir' => 'butir',
            'ekr', 'ekor', 'ek' => 'ekor',
            'ikt', 'ikat', 'iket' => 'ikat',
            'krt', 'krat', 'peti' => 'krat',
            'try', 'tray', 'trai', 'rak' => 'tray',
            'pak', 'pack', 'pck', 'pk', 'bungkus', 'bks', 'pax' => 'pack',
            'btl', 'botol' => 'botol',
            'dus', 'ds', 'box', 'kardus', 'karton' => 'dus',
            'krg', 'karung', 'sak', 'zak' => 'karung',
            'sisir', 'ssr' => 'sisir',
            'bh', 'buah', 'pcs', 'pc', 'biji2', 'unit' => 'buah',
            'jrg', 'jerigen', 'jirigen' => 'jerigen',
            default => $key === '' ? 'buah' : mb_substr($key, 0, 20),
        };
    }
}
