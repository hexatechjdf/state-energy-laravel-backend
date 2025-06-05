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
            'thumbnail'      => 'roof-thumb.jpg',
            'detail_photo'   => 'roof-detail.jpg',
            'pricing'        => json_encode(['price_per_sqft' => 50]),
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
            'thumbnail'      => 'solar-thumb.jpg',
            'detail_photo'   => 'solar-detail.jpg',
            'pricing'        => json_encode(['price_per_panel' => 300]),
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
            'name'           => 'HVAC',
            'thumbnail'      => 'hvac-thumb.jpg',
            'detail_photo'   => 'hvac-detail.jpg',
            'pricing'        => json_encode(['price_per_capacity' => 300, 'base_price' => 100]),
            'configuration'  => json_encode([
                'fields' => [
                    [
                        'label' => 'Choose Category',
                        'type'  => 'select',
                        'options' => ['Central', 'Mini Split'],
                        'name'  => 'sub_category'
                    ],
                    [
                        'label' => 'Capacity',
                        'type'  => 'number',
                        'unit'  => 'TON',
                        'name'  => 'capacity'
                    ],
                ]
            ])
        ]);

        // Windows Category
        Category::create([
            'name'           => 'Windows',
            'thumbnail'      => 'windows-thumb.jpg',
            'detail_photo'   => 'windows-detail.jpg',
            'pricing'        => json_encode(['price_per_sqft' => 300, 'base_price' => 100]),
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
            'thumbnail'      => 'doors-thumb.jpg',
            'detail_photo'   => 'doors-detail.jpg',
            'pricing'        => json_encode(['price_per_sqft' => 300, 'base_price' => 100]),
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

        // Water Heater Category
        Category::create([
            'name'           => 'Water Heater',
            'thumbnail'      => 'water-heater-thumb.jpg',
            'detail_photo'   => 'water-heater-detail.jpg',
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
            'thumbnail'      => 'insulation-thumb.jpg',
            'detail_photo'   => 'insulation-detail.jpg',
            'pricing'        => json_encode(['price_per_sqft' => 300, 'base_price' => 100]),
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
                        'type'  => 'select',
                        'options' => ['R-38', 'R-39'],
                        'name'  => 'r_value'
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
            'thumbnail'      => 'other-thumb.jpg',
            'detail_photo'   => 'other-detail.jpg',
            'pricing'        => json_encode(['price_per_sqft' => 300, 'base_price' => 100]),
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
