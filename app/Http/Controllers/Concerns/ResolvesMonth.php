<?php

namespace App\Http\Controllers\Concerns;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Throwable;

trait ResolvesMonth
{
    protected function month(Request $request): CarbonImmutable
    {
        $value = (string) $request->query('bulan', '');

        if (preg_match('/^\d{4}-\d{2}$/', $value)) {
            try {
                $month = CarbonImmutable::createFromFormat('!Y-m', $value)->startOfMonth();

                if ($month->year >= 2015 && $month->lte(now()->startOfMonth())) {
                    return $month;
                }
            } catch (Throwable) {
            }
        }

        return CarbonImmutable::now()->startOfMonth();
    }
}
