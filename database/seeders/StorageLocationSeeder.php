<?php

namespace Database\Seeders;

use App\Models\StorageLocation;
use Illuminate\Database\Seeder;

class StorageLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Makmal A - HQ', 'code' => 'RAK-A', 'description' => 'Rak penyimpanan A di Ibu Pejabat'],
            ['name' => 'Makmal B - HQ', 'code' => 'RAK-B', 'description' => 'Rak penyimpanan B di Ibu Pejabat'],
            ['name' => 'Makmal C - HQ', 'code' => 'RAK-C', 'description' => 'Rak penyimpanan C di Ibu Pejabat'],
            ['name' => 'Bilik Bahan Kimia', 'code' => 'BK-KIMIA', 'description' => 'Bilik khas untuk bahan kimia'],
            ['name' => 'Bilik Alat Optik', 'code' => 'BK-OPTIK', 'description' => 'Bilik khas untuk alat optik'],
            ['name' => 'Gudang Utama', 'code' => 'GUDANG', 'description' => 'Gudang penyimpanan utama'],
        ];

        foreach ($locations as $loc) {
            StorageLocation::create($loc);
        }
    }
}
