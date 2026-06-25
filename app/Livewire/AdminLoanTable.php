<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\District;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class AdminLoanTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterStatus = '';
    public string $filterDistrict = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDistrict' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterDistrict()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function districts()
    {
        return District::where('is_active', true)->orderBy('name')->get();
    }

    public function getLoansProperty()
    {
        $query = Loan::query()
            ->with(['user', 'district', 'items.item'])
            ->withCount('items')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('loan_no', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('district', function ($dq) {
                            $dq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterDistrict, function ($q) {
                $q->where('district_id', $this->filterDistrict);
            });

        // Apply sorting
        $sortField = $this->sortField;
        $sortDirection = $this->sortDirection;

        if (in_array($sortField, ['user_name', 'district_name'])) {
            $relation = $sortField === 'user_name' ? 'user' : 'district';
            $query->orderBy(
                \App\Models\User::select('name')
                    ->whereColumn('users.id', 'loans.user_id'),
                $sortDirection
            );
        } elseif ($sortField === 'items_count') {
            $query->orderBy('items_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        if ($this->perPage === 0) {
            return $query->paginate($query->count() ?: 1);
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin-loan-table', [
            'loans' => $this->loans,
        ]);
    }
}
