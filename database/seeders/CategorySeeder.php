<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Roof Category
        Category::create([
            'name'           => 'Roof',
            'thumbnail'      => 'category/roof-thumb.png',
            'detail_photo'   => 'category/roof-detail.png',
            'pricing' => json_encode([
                'Shingle' => ['price_per_sqft' => 1375],
                'Flat'    => ['price_per_sqft' => 1500],
                'Metal'   => ['price_per_sqft' => 2625],
                'Tile'    => ['price_per_sqft' => 2750],
            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Choose Category',
                        'type'  => 'select',
                        'options' => ['Shingle', 'Flat', 'Metal', 'Tile'],
                        'name'  => 'category'
                    ],
                    [
                        'label' => 'Square Footage',
                        'type'  => 'number',
                        'unit'  => 'sqft',
                        'name'  => 'square_footage'
                    ],
                    [
                        'label' => 'Color',
                        'type'  => 'select',
                        'options' => ['Red', 'Grey', 'Black'],
                        'name'  => 'color'
                    ],
                ]
            ])
        ]);

        // Solar Category
        Category::create([
            'name'           => 'Solar',
            'thumbnail'      => 'category/solar-thumb.png',
            'detail_photo'   => 'category/solar-detail.png',
            'pricing' => json_encode([
                'price_per_watt' => 0.80,
            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Number of Panels',
                        'type'  => 'number',
                        'unit'  => 'pcs',
                        'name'  => 'number_of_panels'
                    ],
                    [
                        'label' => 'Size of Panel',
                        'type'  => 'number',
                        'unit'  => 'w',
                        'name'  => 'panel_size'
                    ],
                    [
                        'label' => 'Include Battery',
                        'type'  => 'boolean',
                        'name'  => 'battery_backup'
                    ],
                    [
                        'label' => 'Select Battery',
                        'type'  => 'select',
                        'options' => ['5.8 kilowatt', '7.6 kilowatt'],
                        'name'  => 'battery'
                    ],
                ]
            ])
        ]);

        // HVAC Category
        Category::create([
            'name'         => 'HVAC',
            'thumbnail'    => 'category/hvac-thumb.png',
            'detail_photo' => 'category/hvac-detail.png',

            // Pricing specific to each sub-category and capacity
            'pricing' => json_encode([
                'Central' => [
                    'capacities' => [
                        ['label' => '1 TON', 'price' => 7750],
                        ['label' => '2 TON', 'price' => 8500],
                        ['label' => '2.5 TON', 'price' => 9000],
                        ['label' => '3 TON', 'price' => 10000],
                        ['label' => '3.5 TON', 'price' => 10500],
                        ['label' => '4 TON', 'price' => 11250],
                        ['label' => '5 TON', 'price' => 12000],
                    ]
                ],
                'Mini Split' => [
                    'capacities' => [
                        ['label' => '1 TON', 'price' => 4500],
                        ['label' => '1.5 TON', 'price' => 4750],
                        ['label' => '2 TON', 'price' => 5750],
                        ['label' => '2 TON', 'price' => 6250],
                    ]
                ]
            ]),

            // Dynamic field config based on sub-category
            'configuration' => json_encode([
                'fields' => [
                    [
                        'label'   => 'Choose System Type',
                        'type'    => 'select',
                        'name'    => 'sub_category',
                        'options' => ['Central', 'Mini Split']
                    ],
                    [
                        'label'   => 'Capacity',
                        'type'    => 'dynamic_select',
                        'name'    => 'capacity',
                        'options_source' => 'pricing',
                    ]
                ]
            ])
        ]);

        // Windows Category
        Category::create([
            'name'           => 'Windows',
            'thumbnail'      => 'category/windows-thumb.png',
            'detail_photo'   => 'category/windows-detail.png',
            'pricing' => json_encode([
                'unit' => 'sqft',
                'formula' => '(height * width) / 144',
                'price_per_sqft' => 150.00,
            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Height',
                        'type'  => 'number',
                        'unit'  => 'sqft',
                        'name'  => 'height'
                    ],
                    [
                        'label' => 'Width',
                        'type'  => 'number',
                        'unit'  => 'sqft',
                        'name'  => 'width'
                    ],
                    [
                        'label' => 'Type',
                        'type'  => 'select',
                        'options' => ['Single Hung', 'French'],
                        'name'  => 'type'
                    ],
                    [
                        'label' => 'Frame Color',
                        'type'  => 'select',
                        'options' => ['White', 'Brown', 'Red'],
                        'name'  => 'frame_color'
                    ],
                    [
                        'label' => 'Tint Color',
                        'type'  => 'string',
                        'name'  => 'tint_color'
                    ],
                    [
                        'label' => 'Quantity',
                        'type'  => 'number',
                        'name'  => 'qty'
                    ],
                ]
            ])
        ]);

        // Doors Category
        Category::create([
            'name'           => 'Doors',
            'thumbnail'      => 'category/doors-thumb.png',
            'detail_photo'   => 'category/doors-detail.png',
            'pricing' => json_encode([
                'Single Front Door' => [
                    'cost'       => 1750.00,
                    'msrp'       => 3150.00,
                    'min_price'  => 2625.00,
                    'max_price'  => 4375.00
                ],
                'Double Front Door' => [
                    'cost'       => 3050.00,
                    'msrp'       => 5490.00,
                    'min_price'  => 4575.00,
                    'max_price'  => 7625.00
                ],
                'Sliding Door' => [
                    'cost'       => 2850.00,
                    'msrp'       => 5130.00,
                    'min_price'  => 4275.00,
                    'max_price'  => 7125.00
                ]

            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Height',
                        'type'  => 'number',
                        'unit'  => 'sqft',
                        'name'  => 'height'
                    ],
                    [
                        'label' => 'Width',
                        'type'  => 'number',
                        'unit'  => 'sqft',
                        'name'  => 'width'
                    ],
                    [
                        'label' => 'Type',
                        'type'  => 'select',
                        'options' => ['Single Front Door', 'Double Front Door', 'Sliding Door'],
                        'name'  => 'type'
                    ],
                    [
                        'label' => 'Frame Color',
                        'type'  => 'select',
                        'options' => ['White', 'Brown', 'Red'],
                        'name'  => 'frame_color'
                    ],
                    [
                        'label' => 'Tint Color',
                        'type'  => 'string',
                        'name'  => 'tint_color'
                    ],
                    [
                        'label' => 'Quantity',
                        'type'  => 'number',
                        'name'  => 'qty'
                    ],
                ]
            ])
        ]);

        // Water Heater Category
        Category::create([
            'name'           => 'Water Heater',
            'thumbnail'      => 'category/water-heater-thumb.png',
            'detail_photo'   => 'category/water-heater-detail.png',
            'pricing'        => json_encode([
                'price_per_gallon'  => 12,    // hypothetical per gallon rate
                'installation_fee'  => 200,
                'tankless_addon'    => 500
            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Type',
                        'type'  => 'select',
                        'options' => ['Tank', 'Tankless'],
                        'name'  => 'type'
                    ],
                    [
                        'label' => 'Capacity',
                        'type'  => 'number',
                        'unit'  => 'gallons',
                        'name'  => 'capacity'
                    ],
                    [
                        'label' => 'Energy Efficiency Rating',
                        'type'  => 'select',
                        'options' => ['Standard', 'High Efficiency'],
                        'name'  => 'efficiency_rating'
                    ],
                    [
                        'label' => 'Include Installation',
                        'type'  => 'boolean',
                        'name'  => 'include_installation'
                    ],
                ]
            ])
        ]);

        // Insulation Category
        Category::create([
            'name'           => 'Insulation',
            'thumbnail'      => 'category/insulation-thumb.png',
            'detail_photo'   => 'category/insulation-detail.png',
            'pricing' => json_encode([
                'Blown-in Insulation' => [
                    'R-Value' => [
                        ['label' => 'R-30', 'price' => 7750],
                        ['label' => 'R-38', 'price' => 8500],
                    ]
                ],
                'Batt Insulation' => [
                    'R-Value' => [
                        ['label' => 'R-30', 'price' => 750],
                        ['label' => 'R-35', 'price' => 800],
                    ]
                ]
            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Choose Category',
                        'type'  => 'select',
                        'options' => ['Blown-in Insulation', 'Batt Insulation'],
                        'name'  => 'category'
                    ],
                    [
                        'label' => 'R-Value',
                        'type'  => 'dynamic_select',
                        'name'  => 'r_value',
                        'options_source' => 'pricing',
                    ],
                    [
                        'label' => 'Square Footage',
                        'type'  => 'number',
                        'unit'  => 'sqft',
                        'name'  => 'square_footage'
                    ],
                    [
                        'label' => 'Include Insulation Removal',
                        'type'  => 'boolean',
                        'name'  => 'insulation_removal'
                    ],
                ]
            ])
        ]);

        // Other Category
        Category::create([
            'name'           => 'Other',
            'thumbnail'      => 'category/other-thumb.png',
            'detail_photo'   => 'category/other-detail.png',
            'pricing'        => json_encode(['unit_price' => 100]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Description',
                        'type'  => 'text-area',
                        'name'  => 'description',
                    ],
                    [
                        'label' => 'Total Price',
                        'type'  => 'number',
                        'name'  => 'total_price'
                    ],
                ]
            ])
        ]);
    }
}
