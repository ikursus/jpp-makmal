<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\District;
use App\Models\Loan;
use App\Models\LoanApplication;
use Livewire\Component;
use Illuminate\Support\Carbon;

class AdminStatistics extends Component
{
    public int $selectedYear;
    public string $selectedMonth = '';
    public array $availableYears = [];
    public string $activeTab = 'loans'; // 'loans' atau 'applications'

    public function mount()
    {
        if (!auth()->user()->can('view-reports')) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk melihat halaman ini.');
        }

        $this->availableYears = $this->getAvailableYears();
        
        $currentYear = (int) now()->year;
        if (!in_array($currentYear, $this->availableYears) && !empty($this->availableYears)) {
            $this->selectedYear = end($this->availableYears);
        } else {
            $this->selectedYear = $currentYear;
        }
    }

    public function resetFilters()
    {
        $this->selectedYear = (int) now()->year;
        $this->selectedMonth = '';
        $this->dispatch('chart-data-updated', $this->getChartData());
    }

    private function getAvailableYears(): array
    {
        $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';

        if ($isSqlite) {
            $yearsInApps = LoanApplication::selectRaw("strftime('%Y', created_at) as year")->pluck('year');
            $yearsInLoans = Loan::selectRaw("strftime('%Y', start_date) as year")->pluck('year');
        } else {
            $yearsInApps = LoanApplication::selectRaw('YEAR(created_at) as year')->pluck('year');
            $yearsInLoans = Loan::selectRaw('YEAR(start_date) as year')->pluck('year');
        }
        
        return $yearsInApps->merge($yearsInLoans)
            ->push((int) now()->year)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->map(fn($y) => (int) $y)
            ->toArray();
    }

    public function getKpiDataProperty(): array
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        $usersCount = User::where('is_active', true)->count();

        $appQuery = LoanApplication::query()
            ->when($year, fn($q) => $q->whereYear('created_at', $year))
            ->when($month !== '', fn($q) => $q->whereMonth('created_at', $month));
        
        $loanQuery = Loan::query()
            ->when($year, fn($q) => $q->whereYear('start_date', $year))
            ->when($month !== '', fn($q) => $q->whereMonth('start_date', $month));

        return [
            'total_users' => $usersCount,
            'total_applications' => $appQuery->count(),
            'total_approved_loans' => $loanQuery->clone()->count(),
            'active_loans' => $loanQuery->clone()->where('status', 'aktif')->count(),
        ];
    }

    public function getUserLoanStatsProperty()
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        return User::query()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'user');
            })
            ->with('district')
            ->withCount(['loans' => function ($q) use ($year, $month) {
                $q->when($year, fn($query) => $query->whereYear('start_date', $year))
                  ->when($month !== '', fn($query) => $query->whereMonth('start_date', $month));
            }])
            ->orderBy('loans_count', 'desc')
            ->take(10)
            ->get();
    }

    public function getUserAppStatsProperty()
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        return User::query()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'user');
            })
            ->with('district')
            ->withCount(['loanApplications' => function ($q) use ($year, $month) {
                $q->when($year, fn($query) => $query->whereYear('created_at', $year))
                  ->when($month !== '', fn($query) => $query->whereMonth('created_at', $month));
            }])
            ->orderBy('loan_applications_count', 'desc')
            ->take(10)
            ->get();
    }

    public function getDistrictStatsProperty()
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        return District::query()
            ->withCount([
                'users' => function ($q) {
                    $q->where('is_active', true);
                },
                'loanApplications' => function ($q) use ($year, $month) {
                    $q->when($year, fn($query) => $query->whereYear('created_at', $year))
                      ->when($month !== '', fn($query) => $query->whereMonth('created_at', $month));
                },
                'loans' => function ($q) use ($year, $month) {
                    $q->when($year, fn($query) => $query->whereYear('start_date', $year))
                      ->when($month !== '', fn($query) => $query->whereMonth('start_date', $month));
                }
            ])
            ->orderBy('users_count', 'desc')
            ->get();
    }

    public function getChartData(): array
    {
        $year = $this->selectedYear;
        $month = $this->selectedMonth;

        $labels = [];
        $applicationsData = [];
        $loansData = [];

        if ($month === '') {
            // Trend Bulanan (12 Bulan)
            $shortMonthsMalay = ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogos', 'Sept', 'Okt', 'Nov', 'Dis'];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $shortMonthsMalay[$m - 1];
                
                $applicationsData[] = LoanApplication::whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->count();

                $loansData[] = Loan::whereYear('start_date', $year)
                    ->whereMonth('start_date', $m)
                    ->count();
            }
        } else {
            // Trend Harian dalam Bulan terpilih
            $daysInMonth = Carbon::create($year, (int)$month, 1)->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = (string)$d;

                $applicationsData[] = LoanApplication::whereYear('created_at', $year)
                    ->whereMonth('created_at', (int)$month)
                    ->whereDay('created_at', $d)
                    ->count();

                $loansData[] = Loan::whereYear('start_date', $year)
                    ->whereMonth('start_date', (int)$month)
                    ->whereDay('start_date', $d)
                    ->count();
            }
        }

        // Statistik Daerah (hanya daerah aktif)
        $districts = District::withCount(['users' => function($q) {
            $q->where('is_active', true);
        }])->orderBy('users_count', 'desc')->get();
        $districtNames = $districts->pluck('name')->toArray();
        $districtCounts = $districts->pluck('users_count')->toArray();

        // Pecahan status permohonan dalam tempoh ditapis
        $appStatuses = ['menunggu', 'diluluskan', 'ditolak', 'dibatalkan', 'dipinjam', 'dikembalikan'];
        $statusLabels = ['Menunggu', 'Diluluskan', 'Ditolak', 'Dibatalkan', 'Dipinjam', 'Dikembalikan'];
        $statusCounts = [];
        
        foreach ($appStatuses as $status) {
            $statusCounts[] = LoanApplication::where('status', $status)
                ->when($year, fn($q) => $q->whereYear('created_at', $year))
                ->when($month !== '', fn($q) => $q->whereMonth('created_at', $month))
                ->count();
        }

        return [
            'labels' => $labels,
            'applications' => $applicationsData,
            'loans' => $loansData,
            'districts' => $districtNames,
            'districtCounts' => $districtCounts,
            'statusLabels' => $statusLabels,
            'statusCounts' => $statusCounts,
        ];
    }

    public function render()
    {
        // Hantar data carta terkini kepada JavaScript selepas setiap kitaran render
        $this->dispatch('chart-data-updated', $this->getChartData());

        return view('livewire.admin-statistics', [
            'kpi' => $this->kpiData,
            'userLoanStats' => $this->userLoanStats,
            'userAppStats' => $this->userAppStats,
            'districtStats' => $this->districtStats,
        ]);
    }
}
