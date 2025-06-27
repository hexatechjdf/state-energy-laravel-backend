<?php

namespace App\Services;

use App\Models\Category;

class CartService
{
    public function calculatePrice1(Category $category, array $configValues, array $adders = [])
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
    public function calculatePrice(Category $category, array $configValues, array $adders = [])
    {
        $pricingRules = json_decode($category->pricing, true);
        $basePrice = 0;
        $baseUnitPrice = 0;
        switch ($category->name) {
            case 'Roof':
                $type = $configValues['category'];
                $rate = $pricingRules[$type]['price_per_sqft'];
                $basePrice += $rate * $configValues['square_footage'];
                break;

            case 'Solar':
                $baseUnitPrice = $pricingRules['price_per_watt'];
                $basePrice += $baseUnitPrice * (($configValues['number_of_panels'] * $configValues['panel_size']));
                if (isset($configValues['battery']) && $configValues['battery']) {
                    $basePrice += $pricingRules['battery'][$configValues['battery']];
                }
                break;

            case 'HVAC':
                $type = $configValues['sub_category'];
                $capacity = $configValues['capacity'];
                $basePrice = $pricingRules[$type][$capacity];
                break;

            case 'Windows':
                foreach($configValues['windows'] as $index => $window)
                {
                    $area = ($window['height'] * $window['width']) / 144;
                    $basePrice += $area * $pricingRules['price_per_sqft'] * $window['qty'];
                }
                break;

            case 'Doors':
                
                foreach($configValues['doors'] as $index => $door)
                {
                    $doorType = $door['type'];
                    $area = ($door['height'] * $door['width']) / 144;
                    $price = $pricingRules[$doorType]['price'];
                    $basePrice += $area * $price * $door['qty'];
                }
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
                $type = $configValues['sub_category'];
                $rValue = $configValues['r_value'];
                $basePrice = $pricingRules[$type][$rValue] * $configValues['square_footage'];
                break;

            case 'Other':
                $basePrice += $configValues['total_price'];
                break;
        }
        // dd($basePrice);  
       
        foreach ($adders as $adder) {
            $type = isset($adder['type']) ? $adder['type'] : 'linear';
            if ($type == 'linear') {
                $basePrice += $adder['price'];
            } else {
                $basePrice += $adder['price']*$baseUnitPrice;
            }
        }
        return $basePrice;
    }
}
