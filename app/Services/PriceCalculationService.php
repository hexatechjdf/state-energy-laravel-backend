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
    public function calculate(Category $category, array $configuration, array $selectedAdders = []): float
    {
        $basePrice = 0;

        // Pricing is JSON: key => price per unit
        foreach ($category->pricing ?? [] as $key => $pricePerUnit) {
            if (isset($configuration[$key])) {
                $basePrice += $configuration[$key] * $pricePerUnit;
            }
        }

        // Sum prices of selected adders
        $addersTotal = $category->adders()
            ->whereIn('id', $selectedAdders)
            ->sum('price');

        return round($basePrice + $addersTotal, 2);
    }
}
