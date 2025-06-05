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

        foreach ($pricingRules as $pricingKey => $unitPrice)
        {
            $configKey = $configKeyMap[$pricingKey] ?? null;

            if ($configKey && isset($configValues[$configKey]))
            {
                $basePrice += $unitPrice * $configValues[$configKey];
            }
            elseif (in_array($pricingKey, ['flat_rate', 'installation_fee']))
            {
                $basePrice += $unitPrice;
            }
        }
        // Special: Tankless add-on
        if (isset($pricingRules->tankless_addon) && isset($configValues['type']) && $configValues['type']??'' === 'Tankless')
        {
            $basePrice += $pricingRules['tankless_addon'];
        }

        // Adders
        foreach ($adders as $adder) {
            $basePrice += $adder['price'];
        }

        return $basePrice;
    }
}
