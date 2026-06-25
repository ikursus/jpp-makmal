<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCategoryTable extends Component
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
        $category = Category::findOrFail($id);

        if ($category->items()->count() > 0) {
            session()->flash('error', 'Kategori tidak boleh dipadam kerana masih mempunyai barang.');

            return;
        }

        $category->delete();
        session()->flash('success', 'Kategori berjaya dipadam.');
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

    public function getCategoriesProperty()
    {
        $query = Category::query()
            ->withCount('items')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
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
        return view('livewire.admin-category-table', [
            'categories' => $this->categories,
        ]);
    }
}
