<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Adder;
use App\Models\Category;

class AdderSeeder extends Seeder
{
    public function run(): void
    {

        $solarCategory = Category::where('name', 'Solar')->first();
        $roofCategory  = Category::where('name', 'Roof')->first();
        $hvacCategory  = Category::where('name', 'HVAC')->first();
        if (!$solarCategory || !$roofCategory || !$hvacCategory) {
            $this->command->error('Required categories not found. Please run CategorySeeder first.');
            return;
        }
        $hvacAdders = [
            ['name' => 'New Slub', 'price' => 500.00],
            ['name' => 'New Ducts', 'price' => 1250.00],
            ['name' => 'New Breaker', 'price' => 3125.00],
            ['name' => 'New Air Handler stand', 'price' => 325.00],
            ['name' => 'New Disconnect', 'price' => 450.00],
            ['name' => 'New Planum', 'price' => 625.00],
            ['name' => 'Smart Thermostat', 'price' => 375.00],
            ['name' => 'New Drain Line', 'price' => 1250.00],
            ['name' => 'New Copper Line', 'price' => 2500.00],
        ];
        $solarAdders = [
            ['name' => 'MPU', 'price' => 350.00],
            ['name' => 'Panel Box Relocation', 'price' => 350.00],
            ['name' => 'Sub-Panel', 'price' => 350.00],
            ['name' => 'Stucco Repair', 'price' => 350.00],
        ];
        // Roof Adders
        $roofAdders = [
            ['name' => 'Fascia', 'price' => 350.00],
            ['name' => 'Rain Gutters', 'price' => 350.00],
        ];
        // Attach hvac Adders
        foreach ($hvacAdders as $data) {
            $adder = Adder::create($data);
            $hvacCategory->adders()->attach($adder->id);
        }
        // Attach Solar Adders
        foreach ($solarAdders as $data) {
            $adder = Adder::create($data);
            $solarCategory->adders()->attach($adder->id);
        }

        // Attach Roof Adders
        foreach ($roofAdders as $data) {
            $adder = Adder::create($data);
            $roofCategory->adders()->attach($adder->id);
        }
    }
}
