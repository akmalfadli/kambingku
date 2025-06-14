<?php

if (!function_exists('rupiah')) {
    /**
     * Format number as Indonesian Rupiah
     */
    function rupiah($amount, $showSymbol = true): string
    {
        return \App\Helpers\CurrencyHelper::formatRupiah($amount, $showSymbol);
    }
}

if (!function_exists('parseRupiah')) {
    /**
     * Parse rupiah string back to number
     */
    function parseRupiah(string $rupiah): float
    {
        return \App\Helpers\CurrencyHelper::parseRupiah($rupiah);
    }
}
