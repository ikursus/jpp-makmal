<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            ['name' => 'Kota Kinabalu', 'code' => 'KK', 'address' => 'Ibu Pejabat JPP Sabah, Kota Kinabalu', 'phone' => '088-123456'],
            ['name' => 'Sandakan', 'code' => 'SDK', 'address' => 'Pejabat Daerah JPP Sandakan', 'phone' => '089-123456'],
            ['name' => 'Tawau', 'code' => 'TWU', 'address' => 'Pejabat Daerah JPP Tawau', 'phone' => '089-234567'],
            ['name' => 'Keningau', 'code' => 'KGU', 'address' => 'Pejabat Daerah JPP Keningau', 'phone' => '087-123456'],
            ['name' => 'Lahad Datu', 'code' => 'LDU', 'address' => 'Pejabat Daerah JPP Lahad Datu', 'phone' => '089-345678'],
            ['name' => 'Kudat', 'code' => 'KDT', 'address' => 'Pejabat Daerah JPP Kudat', 'phone' => '088-234567'],
            ['name' => 'Beaufort', 'code' => 'BFT', 'address' => 'Pejabat Daerah JPP Beaufort', 'phone' => '087-234567'],
        ];

        foreach ($districts as $district) {
            District::create($district);
        }
    }
}
