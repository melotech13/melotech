<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmFactory extends Factory
{
    protected $model = Farm::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'farm_name' => $this->faker->company . ' Farm',
            'province_name' => $this->faker->randomElement(['Metro Manila', 'Cebu', 'Davao', 'Batangas', 'Pampanga']),
            'city_municipality_name' => $this->faker->randomElement(['Manila', 'Cebu City', 'Davao City', 'Batangas City', 'Angeles City']),
            'barangay_name' => $this->faker->randomElement(['Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay 4', 'Barangay 5']),
            'watermelon_variety' => $this->faker->randomElement(['Watermelon', 'Cantaloupe / Muskmelon', 'Bitter Melon - Ampalaya']),
            'planting_date' => $this->faker->dateTimeBetween('-30 days', '+30 days'),
            'land_size' => $this->faker->randomFloat(2, 0.5, 10.0),
            'land_size_unit' => $this->faker->randomElement(['m2', 'ha']),
        ];
    }
}
