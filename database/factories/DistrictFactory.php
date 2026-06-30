<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<District> */
class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'code' => strtoupper(fake()->unique()->bothify('D###')),
            'address' => fake()->address(),
            'phone' => fake()->numerify('01#-#######'),
            'is_active' => true,
        ];
    }
}
