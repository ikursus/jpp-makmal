<?php

namespace App\Exports\Users;

use App\Models\District;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Export with multiple sheets — one sheet per district, plus a summary sheet.
 *
 * Laravel Excel options demonstrated:
 * - WithMultipleSheets: create multiple sheets in one file
 * - Exportables: each sheet is its own export class
 */
class UsersMultipleSheetsExport implements WithMultipleSheets, WithProperties, WithTitle
{
    /**
     * Return an array of sheet export classes.
     * First sheet is a summary, then one sheet per active district.
     */
    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Summary of all users
        $sheets[] = new UsersSummarySheet();

        // Sheets 2+: Per district
        $districts = District::where('is_active', true)
            ->orderBy('name')
            ->get();

        foreach ($districts as $district) {
            $sheets[] = new UsersPerDistrictSheet($district);
        }

        return $sheets;
    }

    /**
     * Main document title
     */
    public function title(): string
    {
        return 'Pengguna Mengikut Daerah';
    }

    /**
     * Document properties
     */
    public function properties(): array
    {
        return [
            'creator'        => 'JPP Makmal - Sistem Pengurusan Aset',
            'lastModifiedBy' => auth()->user()->name ?? 'System',
            'title'          => 'Senarai Pengguna Mengikut Daerah',
            'description'    => 'Eksport data pengguna yang dikumpulkan mengikut daerah',
            'subject'        => 'Data Pengguna',
            'keywords'       => 'pengguna,users,export,daerah,district,multiple',
            'category'       => 'Pengguna',
            'manager'        => 'JPP Makmal',
            'company'        => 'Jabatan Pertanian',
        ];
    }
}
