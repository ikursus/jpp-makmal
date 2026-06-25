<?php

namespace App\Exports\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Export using a Blade view template (FromView).
 * Ideal when you need fine-grained control over the layout,
 * or want to reuse an existing HTML table as the export template.
 *
 * Laravel Excel options demonstrated:
 * - FromView: render a Blade view as the export
 * - WithStyles, WithColumnWidths, ShouldAutoSize, WithEvents
 */
class UsersExportWithView extends DefaultValueBinder implements
    FromView,
    WithProperties,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Pengguna (Template)';
    }

    /**
     * Document properties
     */
    public function properties(): array
    {
        return [
            'creator'        => 'JPP Makmal - Sistem Pengurusan Aset',
            'lastModifiedBy' => auth()->user()->name ?? 'System',
            'title'          => 'Senarai Pengguna (View Based)',
            'description'    => 'Eksport data pengguna menggunakan Blade template',
            'subject'        => 'Data Pengguna',
            'keywords'       => 'pengguna,users,export,view,template',
            'category'       => 'Pengguna',
            'manager'        => 'JPP Makmal',
            'company'        => 'Jabatan Pertanian',
        ];
    }

    /**
     * FromView: Return a View instance
     */
    public function view(): View
    {
        $query = User::with(['district', 'roles']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['district_id'])) {
            $query->where('district_id', $this->filters['district_id']);
        }

        if (!empty($this->filters['role'])) {
            $query->whereHas('roles', fn($q) => $q->where('name', $this->filters['role']));
        }

        if (isset($this->filters['is_active']) && $this->filters['is_active'] !== '') {
            $query->where('is_active', filter_var($this->filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('exports.users-table', compact('users'));
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 35,
            'D' => 18,
            'E' => 20,
            'F' => 25,
            'G' => 15,
            'H' => 22,
            'I' => 22,
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

                $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");
                $sheet->freezePane('A2');

                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
