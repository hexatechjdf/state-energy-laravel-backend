<?php

namespace App\Services;

use App\Models\Category;

class CartService
{
    public function calculatePrice(Category $category, array $configValues, array $adders = [])
    {
        $pricingRules = json_decode($category->pricing);
        $basePrice = 0;

        $configKeyMap = [
            'price_per_panel'     => 'number_of_panels',
            'price_per_sqft'      => 'square_footage',
            'price_per_gallon'    => 'capacity',
            'price_per_capacity'  => 'capacity',
            'flat_rate'           => null,
            'installation_fee'    => null,
            'tankless_addon'      => null
        ];

        foreach ($pricingRules as $pricingKey => $unitPrice) {
            $configKey = $configKeyMap[$pricingKey] ?? null;

            if ($configKey && isset($configValues[$configKey])) {
                $basePrice += $unitPrice * $configValues[$configKey];
            } elseif (in_array($pricingKey, ['flat_rate', 'installation_fee'])) {
                $basePrice += $unitPrice;
            }
        }
        // Special: Tankless add-on
        if (isset($pricingRules->tankless_addon) && isset($configValues['type']) && $configValues['type'] ?? '' === 'Tankless') {
            $basePrice += $pricingRules['tankless_addon'];
        }

        // Adders
        foreach ($adders as $adder) {
            $basePrice += $adder['price'];
        }

        return $basePrice;
    }
    public function calculatePrice1(Category $category, array $configValues, array $adders = [])
    {
        $pricingRules = json_decode($category->pricing, true);
        $basePrice = 0;
        $map = config('pricing_mappings')[$category->name] ?? [];

        switch ($category->name) {
            case 'Roof':
                $type = $configValues[$map['pricing_field']];
                $rate = $pricingRules[$type]['price_per_sqft'];
                $basePrice += $rate * $configValues[$map['multiplier']];
                break;

            case 'Solar':
                $basePrice += $pricingRules['price_per_watt'] * ($configValues['number_of_panels'] * $configValues['panel_size']);
                if (isset($configValues['battery']) && $configValues['battery']) {
                    $basePrice += $pricingRules['battery'][$configValues['battery']];
                }
                break;

            case 'HVAC':
                $sub = $configValues[$map['pricing_field']];
                $capacity = $configValues[$map['dynamic_field']];
                $basePrice += $pricingRules[$sub][$capacity];
                break;

            case 'Windows':
                $area = ($configValues['height'] * $configValues['width']) / 144;
                $basePrice += $area * $pricingRules['price_per_sqft'] * $configValues[$map['qty_field']];
                break;

            case 'Doors':
                $doorType = $configValues[$map['pricing_field']];
                $price = $pricingRules[$doorType]['msrp'];
                $basePrice += $price * $configValues[$map['qty_field']];
                break;

            case 'Water Heater':
                $basePrice += $pricingRules['price_per_gallon'] * $configValues['capacity'];
                if (!empty($configValues['include_installation'])) {
                    $basePrice += $pricingRules['installation_fee'];
                }
                if (!empty($configValues['type']) && $configValues['type'] === 'Tankless') {
                    $basePrice += $pricingRules['tankless_addon'];
                }
                break;

            case 'Insulation':
                $sub = $configValues[$map['pricing_field']];
                $rVal = $configValues[$map['dynamic_field']];
                $basePrice += $pricingRules[$sub][$rVal] * $configValues[$map['multiplier']];
                break;

            case 'Other':
                $basePrice += $configValues[$map['price_field']];
                break;
        }

        foreach ($adders as $adder) {
            $basePrice += $adder['price'];
        }

        return $basePrice;
    }
}
