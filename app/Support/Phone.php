<?php

namespace App\Support;

use Illuminate\Support\Str;

class Phone
{
    public const SIMULATOR_PREFIX = 'sim';

    public const SAMPLE_PREFIX = 'cth';

    public static function channel(string $phone): string
    {
        return match (true) {
            str_starts_with($phone, self::SIMULATOR_PREFIX) => 'simulator',
            str_starts_with($phone, self::SAMPLE_PREFIX) => 'contoh',
            default => 'wa',
        };
    }

    public static function normalize(?string $raw): ?string
    {
        $digits = (string) preg_replace('/\D+/', '', (string) $raw);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return preg_match('/^62\d{8,13}$/', $digits) ? $digits : null;
    }

    public static function simulator(): string
    {
        return self::SIMULATOR_PREFIX.Str::lower(Str::random(12));
    }

    public static function display(string $phone): string
    {
        return match (self::channel($phone)) {
            'simulator' => 'Simulator',
            'contoh' => 'Nomor contoh',
            default => '0'.substr($phone, 2),
        };
    }
}
