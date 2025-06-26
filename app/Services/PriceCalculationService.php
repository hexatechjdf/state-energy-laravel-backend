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
    public function calculate1(Category $category, array $configuration, array $selectedAdders = []): float
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
    public function calculate(Category $category, array $config, array $selectedAdders = []): float
    {
        $pricing = json_decode($category->pricing, true);
        $total = 0;

        switch ($category->name) {
            case 'Roof':
                $type = $config['category'] ?? null;
                $sqft = $config['square_footage'] ?? 0;
                $total = isset($pricing[$type]['price_per_sqft'])
                    ? $pricing[$type]['price_per_sqft'] * $sqft
                    : 0;
                break;

            case 'Solar':
                $panelCount = $config['number_of_panels'] ?? 0;
                $panelSize  = $config['panel_size'] ?? 0;
                $wattage    = $panelCount * $panelSize;
                $total = $pricing['price_per_watt'] * $wattage;
                break;

            case 'HVAC':
                $type     = $config['sub_category'] ?? '';
                $capacity = $config['capacity'] ?? '';
                $total = collect($pricing[$type]['capacities'] ?? [])
                    ->firstWhere('label', $capacity)['price'] ?? 0;
                break;

            case 'Insulation':
                $cat = $config['category'] ?? '';
                $rval = $config['r_value'] ?? '';
                $sqft = $config['square_footage'] ?? 0;
                $unitPrice = collect($pricing[$cat]['R-Value'] ?? [])
                    ->firstWhere('label', $rval)['price'] ?? 0;
                $total = $unitPrice * $sqft;

                if (!empty($config['insulation_removal'])) {
                    $total += 500; // Add removal fee if needed
                }
                break;

            case 'Windows':
                $h   = $config['height'] ?? 0;
                $w   = $config['width'] ?? 0;
                $qty = $config['qty'] ?? 1;
                $sqft = ($h * $w) / 144;
                $unitPrice = $pricing['price_per_sqft'] ?? 0;
                $total = $unitPrice * $sqft * $qty;
                break;

            case 'Doors':
                $type = $config['type'] ?? '';
                $qty  = $config['qty'] ?? 1;
                $unitPrice = $pricing[$type]['msrp'] ?? 0;
                $total = $unitPrice * $qty;
                break;

            case 'Water Heater':
                $capacity = $config['capacity'] ?? 0;
                $type     = $config['type'] ?? 'Tank';
                $includeInstall = $config['include_installation'] ?? false;

                $base = $capacity * ($pricing['price_per_gallon'] ?? 0);
                $install = $includeInstall ? ($pricing['installation_fee'] ?? 0) : 0;
                $addon = ($type === 'Tankless') ? ($pricing['tankless_addon'] ?? 0) : 0;
                $total = $base + $install + $addon;
                break;

            case 'Other':
                $total = $config['total_price'] ?? 0;
                break;
        }

        // Add Adders
        $addersTotal = $category->adders()
            ->whereIn('id', $selectedAdders)
            ->sum('price');

        return round($total + $addersTotal, 2);
    }
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
        if (!preg_match('/^[0-9+\-*/().\s]+$/', $expression)) {
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
