<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\StorageLocation;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');
        $locations = StorageLocation::pluck('id', 'code');

        // [name, description, category name, storage code, quantity, available, condition, status, expiry_date]
        $items = [
            ['Mikroskop Cahaya Majmuk', 'Mikroskop majmuk untuk pemerhatian sel dan mikroorganisma', 'Mikroskop & Optik', 'BK-OPTIK', 10, 10, 'baik', 'tersedia', null],
            ['Mikroskop Stereo', 'Mikroskop stereo dua mata untuk objek tiga dimensi', 'Mikroskop & Optik', 'BK-OPTIK', 6, 6, 'baik', 'tersedia', null],
            ['Kanta Pembesar Bertangkai', 'Kanta pembesar 10x bertangkai', 'Mikroskop & Optik', 'BK-OPTIK', 25, 25, 'baik', 'tersedia', null],
            ['Spektrofotometer UV-Vis', 'Spektrofotometer julat gelombang 190-1100nm', 'Peralatan Analitik', 'RAK-A', 3, 2, 'baik', 'tersedia', null],
            ['Set Pipet Mikro', 'Set pipet mikro boleh laras 2-1000 mikroliter', 'Peralatan Analitik', 'RAK-A', 15, 15, 'baik', 'tersedia', null],
            ['Buret Kaca 50ml', 'Buret kaca kelas A 50ml untuk pentitratan', 'Peralatan Analitik', 'RAK-A', 30, 28, 'baik', 'tersedia', null],
            ['Pemusing Meja (Centrifuge)', 'Centrifuge meja sehingga 4000 rpm', 'Peralatan Pemusing', 'RAK-B', 4, 4, 'baik', 'tersedia', null],
            ['Mikro-pemusing', 'Micro-centrifuge untuk tiub 1.5ml', 'Peralatan Pemusing', 'RAK-B', 5, 5, 'baik', 'tersedia', null],
            ['Autoklaf 50L', 'Autoklaf wap untuk sterilisasi peralatan makmal', 'Peralatan Sterilisasi', 'RAK-C', 2, 2, 'baik', 'tersedia', null],
            ['Oven Sterilisasi', 'Oven udara panas sterilisasi sehingga 250 darjah Celsius', 'Peralatan Sterilisasi', 'RAK-C', 3, 3, 'baik', 'tersedia', null],
            ['pH Meter Digital', 'pH meter mudah alih ketepatan 0.01', 'Peralatan Ukur', 'RAK-A', 12, 12, 'baik', 'tersedia', null],
            ['Conductivity Meter', 'Meter kekonduksian elektrik larutan', 'Peralatan Ukur', 'RAK-A', 8, 8, 'baik', 'tersedia', null],
            ['Termometer Digital', 'Termometer digital julat -50 hingga 300 darjah Celsius', 'Peralatan Ukur', 'RAK-A', 20, 20, 'baik', 'tersedia', null],
            ['Hot Plate Magnetic Stirrer', 'Plat panas dengan pengacau magnet', 'Peralatan Pemanas', 'RAK-B', 7, 7, 'baik', 'tersedia', null],
            ['Inkubator 80L', 'Inkubator suhu terkawal untuk kultur', 'Peralatan Pemanas', 'RAK-C', 3, 3, 'baik', 'tersedia', null],
            ['Penunu Bunsen', 'Penunu Bunsen gas untuk pemanasan', 'Peralatan Pemanas', 'GUDANG', 18, 18, 'baik', 'tersedia', null],
            ['Neraca Analitik', 'Neraca analitik ketepatan 0.0001g', 'Peralatan Timbang', 'RAK-A', 5, 5, 'baik', 'tersedia', null],
            ['Neraca Elektronik', 'Neraca digital ketepatan 0.01g', 'Peralatan Timbang', 'RAK-A', 10, 10, 'baik', 'tersedia', null],
            ['Larutan Penimbal pH 7 (500ml)', 'Larutan penimbal piawai pH 7.00', 'Bahan Kimia & Reagen', 'BK-KIMIA', 20, 20, 'baik', 'tersedia', '2027-06-30'],
            ['Sarung Tangan Nitril (Kotak 100)', 'Sarung tangan nitril pakai buang, kotak 100 helai', 'Peralatan Keselamatan', 'GUDANG', 50, 45, 'baik', 'tersedia', '2027-12-31'],
        ];

        foreach ($items as [$name, $description, $categoryName, $storageCode, $quantity, $available, $condition, $status, $expiry]) {
            Item::firstOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'quantity' => $quantity,
                    'available_quantity' => $available,
                    'condition' => $condition,
                    'status' => $status,
                    'category_id' => $categories[$categoryName] ?? $categories->first(),
                    'storage_location_id' => $locations[$storageCode] ?? $locations->first(),
                    'expiry_date' => $expiry,
                    'is_active' => true,
                ]
            );
        }
    }
}
