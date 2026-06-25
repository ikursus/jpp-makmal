<?php

namespace App\Exports\Users;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\DB;

class UsersExport extends DefaultValueBinder implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    ShouldAutoSize,
    WithEvents,
    WithTitle,
    WithProperties,
    WithColumnFormatting,
    WithStrictNullComparison
{
    /**
     * Optional filters passed from controller
     */
    protected array $filters;
    protected string $sortField;
    protected string $sortDirection;

    public function __construct(array $filters = [], string $sortField = 'created_at', string $sortDirection = 'desc')
    {
        $this->filters = $filters;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
    }

    /**
     * Sheet title (WithTitle)
     */
    public function title(): string
    {
        return 'Senarai Pengguna';
    }

    /**
     * Document properties (WithProperties)
     */
    public function properties(): array
    {
        return [
            'creator'        => 'JPP Makmal - Sistem Pengurusan Aset',
            'lastModifiedBy' => auth()->user()->name ?? 'System',
            'title'          => 'Senarai Pengguna',
            'description'    => 'Eksport data pengguna daripada Sistem Pengurusan Aset JPP Makmal',
            'subject'        => 'Data Pengguna',
            'keywords'       => 'pengguna,users,export,senarai',
            'category'       => 'Pengguna',
            'manager'        => 'JPP Makmal',
            'company'        => 'Jabatan Pertanian',
        ];
    }

    /**
     * Collection of data (FromCollection)
     * Uses query with filters, sorting, and eager loading
     */
    public function collection()
    {
        $query = User::with(['district', 'roles']);

        // Apply filters
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
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->filters['role']);
            });
        }

        if (isset($this->filters['is_active']) && $this->filters['is_active'] !== '') {
            $query->where('is_active', filter_var($this->filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        // Apply sorting
        $sortField = $this->sortField;
        $sortDir = $this->sortDirection;

        // Handle special sort fields that are on relationships
        if ($sortField === 'role_name') {
            // Default sort by created_at if sorting by relationship
            $query->orderBy('created_at', $sortDir);
        } elseif ($sortField === 'district_name') {
            // Can't sort directly by relationship field in collection export
            $query->orderBy('district_id', $sortDir);
        } else {
            $query->orderBy($sortField, $sortDir);
        }

        return $query->get();
    }

    /**
     * Headings row (WithHeadings)
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'No. Telefon',
            'Daerah',
            'Peranan',
            'Status',
            'Log Masuk Terakhir',
            'Tarikh Daftar',
        ];
    }

    /**
     * Map each row (WithMapping)
     * Transform model data into array format
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone ?? '-',
            $user->district->name ?? '-',
            $user->roles->pluck('name')->implode(', '),
            $user->is_active ? 'Aktif' : 'Tidak Aktif',
            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : '-',
            $user->created_at->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Column formatting (WithColumnFormatting)
     */
    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DATETIME,
            'I' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }

    /**
     * Column widths (WithColumnWidths)
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
     * Styles for the sheet (WithStyles)
     */
    public function styles($sheet)
    {
        return [
            // Style the first row (headings) as bold with background
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Style the ID column
            'A' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // Style the status column
            'G' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Register events (WithEvents)
     * - BeforeSheet: Run before sheet is created
     * - AfterSheet: Auto-filter, freeze panes, additional formatting
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                // Log export activity (optional)
                logger()->info('Exporting users data', [
                    'user' => auth()->user()->name ?? 'System',
                    'filters' => $this->filters,
                    'timestamp' => now()->toDateTimeString(),
                ]);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Auto-filter on all columns
                $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");

                // Freeze the top row
                $sheet->freezePane('A2');

                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Add borders to all cells in the data range
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BDC3C7'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];
                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray($styleArray);

                // Alternate row colors for better readability
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->setStartColor(new Color('FFF8F9FA'));
                    }
                }
            },
        ];
    }
}
