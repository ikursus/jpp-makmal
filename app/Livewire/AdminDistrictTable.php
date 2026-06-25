<?php

namespace App\Livewire;

use App\Models\District;
use Livewire\Component;
use Livewire\WithPagination;

class AdminDistrictTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterStatus = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function destroy(int $id)
    {
        $district = District::findOrFail($id);

        if ($district->users()->count() > 0 || $district->loanApplications()->count() > 0) {
            session()->flash('error', 'Daerah tidak boleh dipadam kerana masih mempunyai rekod berkaitan.');
            return;
        }

        $district->delete();
        session()->flash('success', 'Daerah berjaya dipadam.');
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

    public function getDistrictsProperty()
    {
        $query = District::query()
            ->withCount(['users', 'loanApplications'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus !== '', function ($q) {
                $q->where('is_active', $this->filterStatus === 'active' ? 1 : 0);
            });

        $query->orderBy($this->sortField, $this->sortDirection);

        if ($this->perPage === 0) {
            return $query->paginate($query->count() ?: 1);
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin-district-table', [
            'districts' => $this->districts,
        ]);
    }
}
