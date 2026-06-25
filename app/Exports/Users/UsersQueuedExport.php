<?php

namespace App\Exports\Users;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithCustomQuerySize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\DB;

/**
 * This export uses FromQuery instead of FromCollection.
 * - Data is chunked automatically (config: excel.php chunk_size = 1000)
 * - Can be queued (ShouldQueue) for background processing
 * - Supports WithCustomQuerySize to show export progress
 */
class UsersQueuedExport extends DefaultValueBinder implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithTitle,
    WithProperties,
    WithStrictNullComparison,
    ShouldQueue,
    WithCustomQuerySize
{
    protected array $filters;

    public function __construct(protected ?int $districtId = null, protected ?string $role = null)
    {
        $this->filters = array_filter(compact('districtId', 'role'));
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Pengguna (Queued)';
    }

    /**
     * Document properties
     */
    public function properties(): array
    {
        return [
            'creator'        => 'JPP Makmal - Sistem Pengurusan Aset',
            'lastModifiedBy' => auth()->user()->name ?? 'System',
            'title'          => 'Eksport Pengguna (Queued)',
            'description'    => 'Eksport data pengguna beramai-ramai menggunakan queue',
            'subject'        => 'Data Pengguna',
            'keywords'       => 'pengguna,users,export,queue,large',
            'category'       => 'Pengguna',
            'manager'        => 'JPP Makmal',
            'company'        => 'Jabatan Pertanian',
        ];
    }

    /**
     * FromQuery: Use query builder - data is chunked automatically
     * Ideal for large datasets (thousands of records)
     */
    public function query()
    {
        $query = User::with(['district', 'roles']);

        if ($this->districtId) {
            $query->where('district_id', $this->districtId);
        }

        if ($this->role) {
            $query->whereHas('roles', fn($q) => $q->where('name', $this->role));
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Optional: Return approximate query size for progress tracking
     */
    public function querySize(): int
    {
        return User::count();
    }

    /**
     * Headings row
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
     * Map each row
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
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                logger()->info('[QUEUED EXPORT] Starting users export', [
                    'filters' => $this->filters,
                    'time' => now()->toDateTimeString(),
                ]);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");
                $sheet->freezePane('A2');

                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => new Color('FF34495E')],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                ]);
            },
        ];
    }
}
