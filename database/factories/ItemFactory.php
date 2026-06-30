<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Item> */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'quantity' => 10,
            'available_quantity' => 10,
            'condition' => 'baik',
            'status' => 'tersedia',
            'category_id' => Category::factory(),
            'storage_location_id' => StorageLocation::factory(),
            'expiry_date' => null,
            'image' => null,
            'qr_code' => null,
            'is_active' => true,
        ];
    }
}
