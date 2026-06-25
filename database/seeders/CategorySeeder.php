<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Mikroskop & Optik', 'description' => 'Peralatan mikroskop dan optik makmal'],
            ['name' => 'Peralatan Analitik', 'description' => 'Peralatan analisis makmal'],
            ['name' => 'Peralatan Pemusing', 'description' => 'Centrifuge dan peralatan pemusing'],
            ['name' => 'Peralatan Sterilisasi', 'description' => 'Autoklaf dan peralatan sterilisasi'],
            ['name' => 'Peralatan Ukur', 'description' => 'pH meter, conductivity meter, dll'],
            ['name' => 'Peralatan Pemanas', 'description' => 'Oven, incubator, hot plate, dll'],
            ['name' => 'Peralatan Timbang', 'description' => 'Timbangan analitik dan precision'],
            ['name' => 'Bahan Kimia & Reagen', 'description' => 'Bahan kimia dan reagen makmal'],
            ['name' => 'Peralatan Keselamatan', 'description' => 'Alat keselamatan makmal'],
            ['name' => 'Lain-lain', 'description' => 'Peralatan makmal lain'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
