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
    public function calculatePrice(Category $category, array $configValues, array $adders = [], $upsellPrice = 0)
    {
        $pricingRules = json_decode($category->pricing, true);
        $basePrice = 0;
        $baseUnitPrice = 0;
        $totalSizeWatts = 0;
        switch ($category->name) {
            case 'Roof':

                $type = $configValues['category'];
                $rate = ($upsellPrice > 0) ? $upsellPrice : $pricingRules[$type]['price_per_sqft'];
                $baseUnitPrice = $rate;
                $basePrice += $rate * $configValues['square_footage'];
                break;

            case 'Solar':
                $baseUnitPrice = ($upsellPrice > 0) ? $upsellPrice : $pricingRules['price_per_watt'];
                if (!empty($configValues['number_of_panels']) && !empty($configValues['panel_size'])) {
                    $totalSizeWatts = $configValues['number_of_panels'] * $configValues['panel_size'];
                    $basePrice += $baseUnitPrice * $totalSizeWatts;
                }
                if (!empty($configValues['battery'])) {
                    if (is_array($configValues['battery'])) {
                        foreach ($configValues['battery'] as $battery) {

                            if (isset($battery['name'], $battery['quantity'])) {
                                $batteryName = $battery['name'];
                                $quantity    = (int) $battery['quantity'];

                                if (isset($pricingRules['battery'][$batteryName])) {
                                    $basePrice += $pricingRules['battery'][$batteryName] * $quantity;
                                }
                            }
                        }
                    } else {
                        if (isset($pricingRules['battery'][$configValues['battery']])) {
                            $basePrice += $pricingRules['battery'][$configValues['battery']];
                        }
                    }
                }
                break;

            case 'HVAC':

                $type = $configValues['sub_category'];
                $capacity = $configValues['capacity'];
                $priceData = $pricingRules[$type][$capacity];
                if (is_array($priceData) && isset($priceData['msrp'])) {
                    $basePrice = (float) $priceData['msrp'];
                } else {
                    $basePrice = (float) $priceData;
                }
                if ($upsellPrice > 0) {
                    $basePrice = $upsellPrice;
                }
                $baseUnitPrice = $basePrice;
                break;

            case 'Windows':
                $baseUnitPrice = ($upsellPrice > 0) ? $upsellPrice : $pricingRules['price_per_sqft'];
                foreach ($configValues['windows'] as $index => $window) {
                    $area = ($window['height'] * $window['width']) / 144;
                    $basePrice += $area * $baseUnitPrice * $window['qty'];
                }
                break;

            case 'Doors':

                foreach ($configValues['doors'] as $index => $door) {
                    $doorType = $door['type'];
                    $area = ($door['height'] * $door['width']) / 144;
                    $price = ($upsellPrice > 0) ? $upsellPrice : $pricingRules[$doorType]['price'];
                    $baseUnitPrice = $price;
                    $basePrice += $area * $price * $door['qty'];
                }
                break;

            case 'Water Heater':
                $type = $configValues['capacity'];
                if (is_int($type)) {
                    $type = (string)$type . ' kW';
                }
                $ratePerGallon = ($upsellPrice > 0) ? $upsellPrice : $pricingRules[$type]['msrp'];
                $baseUnitPrice = $ratePerGallon;
                $basePrice = $baseUnitPrice;
                // $basePrice += $ratePerGallon * $configValues['capacity'];
                // if (!empty($configValues['include_installation'])) {
                //     $basePrice += $pricingRules['installation_fee'];
                // }
                // if (!empty($configValues['type']) && $configValues['type'] === 'Tankless') {
                //     $basePrice += $pricingRules['tankless_addon'];
                // }
                break;

            case 'Insulation':
                $type = $configValues['sub_category'];
                $rValue = $configValues['r_value'];
                $priceData =  $pricingRules[$type][$rValue];
                if (is_array($priceData) && isset($priceData['msrp'])) {
                    $baseUnitPrice = (float) $priceData['msrp'];
                } else {
                    $baseUnitPrice = (float) $priceData;
                }
                $rate = ($upsellPrice > 0) ? $upsellPrice : $baseUnitPrice;
                $basePrice = $rate * $configValues['square_footage'];
                break;

            case 'Other':
                $basePrice = ($upsellPrice > 0) ? $upsellPrice : $configValues['total_price'];
                $baseUnitPrice = $basePrice;
                break;
        }

        foreach ($adders as $adder) {
            $type = isset($adder['type']) ? $adder['type'] : 'linear';
            $qty  = isset($adder['qty']) && $adder['qty'] > 0 ? $adder['qty'] : 1;
            if ($category->name == 'Solar') {
                if ($type == 'linear') {
                    $basePrice += $adder['price'] * $qty;
                } else {
                    if ($totalSizeWatts > 0) {
                        $basePrice += $totalSizeWatts * ($adder['price'] * $qty);
                    }
                }
            } else if ($type == 'linear') {
                $basePrice += $adder['price'] * $qty;
            } else {
                $basePrice += $adder['price'] * $baseUnitPrice * $qty;
            }
        }
        return $basePrice;
    }
    function hasUserAlreadyCheckIn($userId, $appointmentId = null)
    {
        $query = \App\Models\Order::where('user_id', $userId);
           // ->whereNotNull('checkin_completed_at');

        if ($appointmentId) {
            $query->where('appointment_id', $appointmentId);
        }

        return $query->exists();
    }
}
