<?php

namespace App\Exports\Loans;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LoansExport extends DefaultValueBinder implements
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
    protected array $filters;
    protected string $sortField;
    protected string $sortDirection;

    public function __construct(array $filters = [], string $sortField = 'created_at', string $sortDirection = 'desc')
    {
        $this->filters = $filters;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
    }

    public function title(): string
    {
        return 'Rekod Pinjaman';
    }

    public function properties(): array
    {
        return [
            'creator'        => 'JPP Makmal - Sistem Pengurusan Aset',
            'lastModifiedBy' => auth()->user()->name ?? 'System',
            'title'          => 'Rekod Pinjaman',
            'description'    => 'Eksport data pinjaman daripada Sistem Pengurusan Aset JPP Makmal',
            'subject'        => 'Data Pinjaman',
            'keywords'       => 'pinjaman,loans,export,rekod',
            'category'       => 'Pinjaman',
            'manager'        => 'JPP Makmal',
            'company'        => 'Jabatan Pertanian',
        ];
    }

    public function collection()
    {
        $query = Loan::with(['user', 'district', 'items.item'])->withCount('items');

        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('loan_no', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('district', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['district_id'])) {
            $query->where('district_id', $this->filters['district_id']);
        }

        $sortField = $this->sortField;
        $sortDir = $this->sortDirection;

        if (in_array($sortField, ['user_name', 'district_name'])) {
            $query->orderBy('created_at', $sortDir);
        } elseif ($sortField === 'items_count') {
            $query->orderBy('items_count', $sortDir);
        } else {
            $query->orderBy($sortField, $sortDir);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No. Pinjaman',
            'Peminjam',
            'Daerah',
            'Jumlah Barang',
            'Tarikh Mula',
            'Tarikh Akhir',
            'Tarikh Pulang',
            'Status',
            'Catatan',
            'Tarikh Rekod',
        ];
    }

    public function map($loan): array
    {
        return [
            $loan->loan_no,
            $loan->user->name ?? '-',
            $loan->district->name ?? '-',
            $loan->items_count,
            $loan->start_date ? $loan->start_date->format('d/m/Y') : '-',
            $loan->end_date ? $loan->end_date->format('d/m/Y') : '-',
            $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y') : '-',
            $this->mapStatus($loan->status),
            $loan->notes ?? '-',
            $loan->created_at->format('d/m/Y H:i:s'),
        ];
    }

    protected function mapStatus(string $status): string
    {
        return match ($status) {
            'aktif' => 'Aktif',
            'terlewat' => 'Terlewat',
            'dipulangkan' => 'Dipulangkan',
            default => ucfirst($status),
        };
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DMYSLASH,
            'F' => NumberFormat::FORMAT_DATE_DMYSLASH,
            'G' => NumberFormat::FORMAT_DATE_DMYSLASH,
            'J' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 22,
            'D' => 16,
            'E' => 16,
            'F' => 16,
            'G' => 16,
            'H' => 16,
            'I' => 30,
            'J' => 22,
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // Header styling
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E79'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Body
        if ($lastRow > 1) {
            $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);

            // Alternating row colors
            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F3F4F6'],
                        ],
                    ]);
                }
            }
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = $sheet->getHighestColumn();

                // Freeze header row
                $sheet->freezePane('A2');

                // Auto-filter
                $sheet->setAutoFilter("A1:{$lastCol}{$lastRow}");

                // Center-align status column
                $sheet->getStyle("H2:H{$lastRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
