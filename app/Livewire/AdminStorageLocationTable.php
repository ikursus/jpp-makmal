<?php

namespace App\Livewire;

use App\Models\StorageLocation;
use Livewire\Component;
use Livewire\WithPagination;

class AdminStorageLocationTable extends Component
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
        $location = StorageLocation::findOrFail($id);

        if ($location->items()->count() > 0) {
            session()->flash('error', 'Lokasi tidak boleh dipadam kerana masih mempunyai barang.');

            return;
        }

        $location->delete();
        session()->flash('success', 'Lokasi penyimpanan berjaya dipadam.');
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

    public function getLocationsProperty()
    {
        $query = StorageLocation::query()
            ->withCount(['items'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('code', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
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
        return view('livewire.admin-storage-location-table', [
            'locations' => $this->locations,
        ]);
    }
}
