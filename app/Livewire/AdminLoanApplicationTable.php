<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\LoanApplication;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class AdminLoanApplicationTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterCategory = '';

    public string $filterStatus = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
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
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    public function getApplicationsProperty()
    {
        $query = LoanApplication::query()
            ->with(['user', 'district', 'items.item.category'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('application_no', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($qu) {
                            $qu->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->filterCategory, function ($q) {
                $q->whereHas('items.item', function ($query) {
                    $query->where('category_id', $this->filterCategory);
                });
            })
            ->when($this->filterStatus !== '', function ($q) {
                $q->where('status', $this->filterStatus);
            });

        $query->orderBy($this->sortField, $this->sortDirection);

        if ($this->perPage === 0) {
            return $query->paginate($query->count() ?: 1);
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin-loan-application-table', [
            'applications' => $this->applications,
        ]);
    }
}
