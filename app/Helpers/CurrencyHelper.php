<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * USD to PHP conversion rate
     * 1 USD = 56 PHP (approximate current rate)
     */
    const USD_TO_PHP_RATE = 56.0;

    /**
     * Threshold to determine if a price is in USD
     * Prices below this threshold are assumed to be in PHP already
     * Prices above this threshold might be in USD and need conversion
     */
    const USD_THRESHOLD = 1000;

    /**
     * Convert USD to Philippine Peso
     *
     * @param float $usdAmount
     * @return float
     */
    public static function usdToPhp(float $usdAmount): float
    {
        return $usdAmount * self::USD_TO_PHP_RATE;
    }

    /**
     * Convert PHP to USD
     *
     * @param float $phpAmount
     * @return float
     */
    public static function phpToUsd(float $phpAmount): float
    {
        return $phpAmount / self::USD_TO_PHP_RATE;
    }

    /**
     * Convert price to PHP if it's in USD
     * Automatically detects if price needs conversion based on threshold
     *
     * @param float|null $price
     * @param bool $forceConvert Force conversion regardless of threshold
     * @return float
     */
    public static function toPhp(?float $price, bool $forceConvert = false): float
    {
        if ($price === null || $price == 0) {
            return 0.0;
        }

        // If force convert, always convert
        if ($forceConvert) {
            return self::usdToPhp($price);
        }

        // If price is above threshold (1000+), assume it's already in PHP
        // Typical PHP prices for gadgets are 500+ PHP
        if ($price >= self::USD_THRESHOLD) {
            return $price;
        }

        // If price is below threshold, check if it looks like USD
        // Typical USD gadget prices: $1 - $5000
        // If it's in this range, convert it to PHP
        if ($price >= 1 && $price <= 5000) {
            // Additional check: if converted price makes sense (not too high)
            $convertedPrice = self::usdToPhp($price);
            // If converted price is reasonable (less than 500,000 PHP), it's likely USD
            if ($convertedPrice < 500000) {
                return $convertedPrice;
            }
        }

        // Otherwise, assume it's already in PHP
        return $price;
    }

    /**
     * Format price as Philippine Peso
     *
     * @param float|null $price
     * @param int $decimals
     * @return string
     */
    public static function formatPhp(?float $price, int $decimals = 2): string
    {
        $convertedPrice = self::toPhp($price);
        return '₱' . number_format($convertedPrice, $decimals);
    }

    /**
     * Get PHP price (converted if needed)
     *
     * @param float|null $price
     * @return float
     */
    public static function getPhpPrice(?float $price): float
    {
        return self::toPhp($price);
    }
}

