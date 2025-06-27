<?php

namespace App\Services;

use App\Models\Category;

class PriceCalculationService
{
    /**
     * Calculate total price based on category pricing, user configuration, and selected adders.
     *
     * @param Category $category
     * @param array $configuration Key-value user config (e.g. ['square_footage' => 500])
     * @param array $selectedAdders Array of adder IDs
     * @return float
     */
    function calculateFinance(string $provider, float $subtotal, float $selectedApr): ?float
    {
        $financeOptions = config("finance.finance_options.$provider");

        if (!$financeOptions || !is_array($financeOptions)) {
            return null; // Invalid provider
        }

        // Find the set with matching APR
        $matchedOption = collect($financeOptions)->firstWhere('apr', $selectedApr);

        if (!$matchedOption || !isset($matchedOption['formula'])) {
            return null; // No matching APR found
        }
        $variables = [
            'subtotal' => $subtotal,
            'dealer_fee' => $matchedOption['dealer_fee'] ?? 0,
            'monthly_ntc' => $matchedOption['monthly_ntc'] ?? 1,
            'total_loan_value' => $matchedOption['total_loan_value'] ?? 1,
            'multiplier' => $matchedOption['multiplier'] ?? 1,
        ];

        $expression = $matchedOption['formula'];

        foreach ($variables as $key => $value) {
            $expression = str_replace($key, (string) $value, $expression);
        }
        if (!preg_match('#^[0-9+\-*/().\s]+$#', $expression)) {
            return null;
        }
        try {
            $result = eval("return $expression;");
        } catch (\Throwable $e) {
            return null;
        }
        return is_numeric($result) ? round($result, 2) : null;
    }
}
