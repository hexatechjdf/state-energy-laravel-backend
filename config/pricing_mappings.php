<?php
// config/pricing_mappings.php

return [
    'Roof' => [
        'pricing_field' => 'category',  // field whose value is the pricing key
        'multiplier'    => 'square_footage',
    ],
    'Solar' => [
        'price_per_watt' => [
            'multiplier' => function($config) {
                return $config['number_of_panels'] * $config['panel_size'];
            }
        ],
        'battery' => [
            'field' => 'battery',
            'type'  => 'select'
        ]
    ],
    'HVAC' => [
        'pricing_field' => 'sub_category',
        'dynamic_field' => 'capacity'
    ],
    'Windows' => [
        'formula'   => '(height * width) / 144',
        'price_key' => 'price_per_sqft',
        'qty_field' => 'qty'
    ],
    'Doors' => [
        'pricing_field' => 'type',
        'qty_field'     => 'qty'
    ],
    'Water Heater' => [
        'price_per_gallon' => [
            'multiplier' => 'capacity'
        ],
        'installation_fee' => 'include_installation',
        'tankless_addon'   => [
            'field' => 'type',
            'value' => 'Tankless'
        ]
    ],
    'Insulation' => [
        'pricing_field' => 'sub_category',
        'dynamic_field' => 'r_value',
        'multiplier'    => 'square_footage'
    ],
    'Other' => [
        'price_field' => 'total_price'
    ]
];

?>