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
                'Shingle' => ['price_per_sqft' => '1375.00'],
                'Flat'    => ['price_per_sqft' => '1500.00'],
                'Metal'   => ['price_per_sqft' => '2625.00'],
                'Tile'    => ['price_per_sqft' => '2750.00'],
            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Choose Category',
                        'type'  => 'select',
                        'options' => ['Shingle', 'Flat', 'Metal', 'Tile'],
                        'pricing' => 'true',
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
                        'name'  => 'color',
                        'pricing' => 'false',
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
                'price_per_watt' => '5.50 ',
                'battery' => [
                    '5.8 kilowatt' => '4500.00',
                    '7.6 kilowatt' => '5500.00',
                ]
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
                        'name'  => 'panel_size',
                    ],
                    [
                        'label' => 'Include Battery',
                        'type'  => 'boolean',
                        'name'  => 'battery_backup'
                    ],
                    [
                        'label'   => 'Select Battery',
                        'type'    => 'select',
                        'options' => ['5.8 kilowatt', '7.6 kilowatt'],
                        'name'    => 'battery',
                        'pricing' => 'true', // enable pricing input for this select field
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
                    '1 TON'   => '7750.00',
                    '2 TON'   => '8500.00',
                    '2.5 TON' => '9000.00',
                    '3 TON'   => '10000.00',
                    '3.5 TON' => '10500.00',
                    '4 TON'   => '11250.00',
                    '5 TON'   => '12000.00',
                ],
                'Mini Split' => [
                    '1 TON'   => '4500.00',
                    '1.5 TON' => '4750.00',
                    '2 TON'   => '5750.00',
                    '2.5 TON' => '6250.00'
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
                        'label'         => 'Capacity',
                        'type'          => 'dynamic_select',
                        'name'          => 'capacity',
                        'options_source' => 'pricing',
                        'options'       => [
                            'Central'    => ['1 TON', '2 TON', '2.5 TON', '3 TON', '3.5 TON', '4 TON', '5 TON'],
                            'Mini Split' => ['1 TON', '1.5 TON', '2 TON', '2.5 TON']
                        ],
                        'pricing'       => 'true',
                        "depends_on"=> "sub_category",
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
                'price_per_sqft' => '150.00',
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
                    'price'       => '3150.00',
                ],
                'Double Front Door' => [
                    'price'       => '5490.00',
                ],
                'Sliding Door' => [
                    'price'       => '5130.00',
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
                        'name'  => 'type',
                        'pricing' => 'true',
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
                'price_per_gallon'  => '12.00',    // hypothetical per gallon rate
                'installation_fee'  => '200.00',
                'tankless_addon'    => '500.00'
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
                    'R-30' => '7750.00',
                    'R-38' => '8500.00'
                ],
                'Batt Insulation' => [
                    'R-30' => '750.00',
                    'R-35' => '800.00'
                ]
            ]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label'   => 'Choose Insulation Type',
                        'type'    => 'select',
                        'name'    => 'sub_category',
                        'options' => ['Blown-in Insulation', 'Batt Insulation']
                    ],
                    [
                        'label'          => 'R-Value',
                        'type'           => 'dynamic_select',
                        'name'           => 'r_value',
                        'options_source' => 'pricing',
                        'options'        => [
                            'Blown-in Insulation' => ['R-30', 'R-38'],
                            'Batt Insulation'     => ['R-30', 'R-35']
                        ],
                        "depends_on"=> "sub_category",
                        'pricing'        => 'true'
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
            'pricing'        => json_encode([]),
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
