<?php

namespace App\Support;

final class Bytes
{
    public static function format(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
