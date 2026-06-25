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
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

/**
 * Individual sheet for each district in the multi-sheet export
 */
class UsersPerDistrictSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected District $district;

    public function __construct(District $district)
    {
        $this->district = $district;
    }

    /**
     * Sheet title = district name (truncated to 31 chars for Excel limit)
     */
    public function title(): string
    {
        return mb_substr($this->district->name, 0, 31);
    }

    /**
     * Get users for this district only
     */
    public function collection()
    {
        return User::with('roles')
            ->where('district_id', $this->district->id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'No. Telefon',
            'Peranan',
            'Status',
            'Log Masuk Terakhir',
            'Tarikh Daftar',
        ];
    }

    /**
     * Map each row
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone ?? '-',
            $user->roles->pluck('name')->implode(', '),
            $user->is_active ? 'Aktif' : 'Tidak Aktif',
            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : '-',
            $user->created_at->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Styles
     */
    public function styles($sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
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

                $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");
                $sheet->freezePane('A2');

                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BDC3C7'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
