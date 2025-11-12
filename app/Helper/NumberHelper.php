<?php

namespace ilhamrhmtkbr\App\Helper;

class NumberHelper
{
    public static function convertNumberToRupiah(float|string $value): string
    {
        $value = (float) $value;

        return "Rp " . number_format($value, 0, ',', '.');
    }
}
