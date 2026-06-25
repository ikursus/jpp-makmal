<?php

namespace App\Exports\Users;

use App\Models\User;
use App\Models\District;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

/**
 * Summary sheet for the multi-sheet export.
 * Shows aggregated user counts per district and per role.
 */
class UsersSummarySheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Ringkasan';
    }

    /**
     * Collection: aggregated summary data
     */
    public function collection()
    {
        $districts = District::where('is_active', true)
            ->orderBy('name')
            ->get();

        $summary = collect();

        foreach ($districts as $district) {
            $total = User::where('district_id', $district->id)->count();
            $active = User::where('district_id', $district->id)->where('is_active', true)->count();
            $inactive = $total - $active;

            $summary->push((object) [
                'district_name' => $district->name,
                'total_users'   => $total,
                'active_users'  => $active,
                'inactive_users' => $inactive,
            ]);
        }

        // Add grand total row as a separate entry
        $summary->push((object) [
            'district_name'  => 'JUMLAH KESELURUHAN',
            'total_users'    => User::count(),
            'active_users'   => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
        ]);

        return $summary;
    }

    /**
     * Headings
     */
    public function headings(): array
    {
        return [
            'Daerah',
            'Jumlah Pengguna',
            'Aktif',
            'Tidak Aktif',
        ];
    }

    /**
     * Map rows
     */
    public function map($row): array
    {
        return [
            $row->district_name,
            $row->total_users,
            $row->active_users,
            $row->inactive_users,
        ];
    }

    /**
     * Styles
     */
    public function styles($sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    /**
     * Events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Bold the last row (grand total)
                $sheet->getStyle("A{$lastRow}:{$lastColumn}{$lastRow}")
                    ->getFont()
                    ->setBold(true);
                $sheet->getStyle("A{$lastRow}:{$lastColumn}{$lastRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color('FFD5F5E3'));

                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'AAAAAA'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
