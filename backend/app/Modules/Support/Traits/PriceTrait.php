<?php

namespace App\Modules\Support\Traits;

trait PriceTrait
{
    public static function grossPrice(float $amount): float
    {
        $amountInCents = $amount * 100;

        return $amountInCents * static::$vatAmount / 10000;
    }

    public static function formatPrice(float $amount, int $decimals = 2, string $decimalPoint = ',', string $thousandsPoint = ' '): string
    {
        return number_format($amount, $decimals, $decimalPoint, $thousandsPoint);
    }
}
