<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format number to Indonesian Rupiah
     */
    public static function formatRupiah($amount, $showSymbol = true): string
    {
        if ($amount === null || $amount === '') {
            return $showSymbol ? 'Rp 0' : '0';
        }

        $formatted = number_format((float) $amount, 0, ',', '.');

        return $showSymbol ? "Rp {$formatted}" : $formatted;
    }

    /**
     * Format for input fields (without symbol)
     */
    public static function formatForInput($amount): string
    {
        return self::formatRupiah($amount, false);
    }

    /**
     * Parse rupiah string back to number
     */
    public static function parseRupiah(string $rupiah): float
    {
        // Remove Rp, spaces, and dots, replace comma with dot for decimal
        $cleaned = str_replace(['Rp', ' ', '.'], '', $rupiah);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }
}
