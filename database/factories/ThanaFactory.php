<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Thana;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thana>
 */
class ThanaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'district_id' => District::factory(),
            'name' => $this->faker->streetName,
        ];
    }
}