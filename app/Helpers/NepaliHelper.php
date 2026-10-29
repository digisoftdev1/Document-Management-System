<?php

namespace App\Helpers;

class NepaliHelper
{
    public static function toNepaliNumber($number, $decimals = null)
    {
        if ($decimals !== null) {
            $number = number_format($number, $decimals, '.', '');
        }

        $number = (string) $number;

        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $nepali = ['0', '१', '२', '३', '४', '५', '६', '७', '८', '९'];

        return str_replace($english, $nepali, $number);
    }

    public static function toNepaliPercentage($number, $decimals = 2)
    {
        $formatted = number_format($number, $decimals, '.', '');
        return self::toNepaliNumber($formatted) . '%';
    }
    public static function number($number, $decimals = null)
    {
        if (app()->getLocale() === 'ne') {
            return self::toNepaliNumber($number, $decimals);
        }

        if ($decimals !== null) {
            return number_format($number, $decimals);
        }

        return $number;
    }
    public static function toEnglishNumber($number): string
    {
        return str_replace(
            ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'],
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            (string) $number
        );
    }

    public static function normalizeFiscalYear(string $name): string
    {
        $name = self::toEnglishNumber(trim($name));

        return preg_replace('/\s*[-\/]\s*/u', '-', $name);
    }
}
