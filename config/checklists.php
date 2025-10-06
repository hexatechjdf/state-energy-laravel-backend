<?php

return [
    'roof' => [
        'front_of_home' => 'required|file|mimes:jpg,jpeg,png',
        'all_roof_slopes' => 'required|array|min:1',
        'all_roof_slopes.*' => 'file|mimes:jpg,jpeg,png',
        'visible_damage' => 'nullable|file|mimes:jpg,jpeg,png',
        'attic_access' => 'nullable|file|mimes:jpg,jpeg,png',
    ],

    'windows_doors' => [
        'front_of_home' => 'required|file|mimes:jpg,jpeg,png',
        'window_sets' => 'required|array|min:1',
        'window_sets.*' => 'file|mimes:jpg,jpeg,png',
        'exterior_doors' => 'required|array|min:1',
        'exterior_doors.*' => 'file|mimes:jpg,jpeg,png',
        'damage_closeups' => 'nullable|array',
        'damage_closeups.*' => 'file|mimes:jpg,jpeg,png',
    ],

    'hvac' => [
        'outdoor_condenser_zoomout' => 'required|file|mimes:jpg,jpeg,png',
        'outdoor_condenser_zoomin' => 'required|file|mimes:jpg,jpeg,png',
        'thermostat' => 'required|file|mimes:jpg,jpeg,png',
        'inside_air_handler' => 'required|file|mimes:jpg,jpeg,png',
        'electrical_panel' => 'required|file|mimes:jpg,jpeg,png',
    ],

    'tankless_water_heater' => [
        'heater_view' => 'required|file|mimes:jpg,jpeg,png',
        'serial_label' => 'required|file|mimes:jpg,jpeg,png',
        'gas_line' => 'nullable|file|mimes:jpg,jpeg,png',
        'mount_location' => 'required|file|mimes:jpg,jpeg,png',
    ],

    'water_filtration' => [
        'install_location' => 'required|file|mimes:jpg,jpeg,png',
    ],

    'solar' => [
        'front_of_home' => 'required|file|mimes:jpg,jpeg,png',
        'electrical_meter' => 'required|file|mimes:jpg,jpeg,png',
        'main_panel' => 'required|file|mimes:jpg,jpeg,png',
        'sub_panel' => 'nullable|file|mimes:jpg,jpeg,png',
        'roof_planes' => 'required|array|min:1',
        'roof_planes.*' => 'file|mimes:jpg,jpeg,png',
        'house_sides' => 'required|array|min:1',
        'house_sides.*' => 'file|mimes:jpg,jpeg,png',
        'shade_obstructions' => 'nullable|array',
        'shade_obstructions.*' => 'file|mimes:jpg,jpeg,png',
    ],

    'insulation' => [
        'attic_access_open' => 'required|file|mimes:jpg,jpeg,png',
    ],
];
