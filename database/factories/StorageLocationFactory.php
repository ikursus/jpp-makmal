<?php

namespace Database\Factories;

use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StorageLocation> */
class StorageLocationFactory extends Factory
{
    protected $model = StorageLocation::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Store',
            'code' => strtoupper(fake()->unique()->bothify('SL###')),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
