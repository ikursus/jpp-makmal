<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\District;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class AdminUserTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $filterDistrict = '';
    public string $filterRole = '';
    public string $filterStatus = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDistrict' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDistrict()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
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

    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('super_admin')) {
            session()->flash('error', 'Super Admin tidak boleh dipadam.');
            return;
        }

        $user->delete();
        session()->flash('success', 'Pengguna berjaya dipadam.');
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

    #[Computed]
    public function roles()
    {
        return \Spatie\Permission\Models\Role::orderBy('name')->get();
    }

    public function getUsersProperty()
    {
        $query = User::query()
            ->with(['district', 'roles'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDistrict, function ($q) {
                $q->where('district_id', $this->filterDistrict);
            })
            ->when($this->filterRole, function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', $this->filterRole);
                });
            })
            ->when($this->filterStatus !== '', function ($q) {
                $q->where('is_active', $this->filterStatus === 'active' ? 1 : 0);
            });

        // Apply sorting
        $sortField = $this->sortField;
        $sortDirection = $this->sortDirection;

        // Handle sorting on relationship fields
        if ($sortField === 'district_name') {
            $query->orderBy(
                District::select('name')
                    ->whereColumn('districts.id', 'users.district_id'),
                $sortDirection
            );
        } elseif ($sortField === 'role_name') {
            // For role sorting, use primary sort as name fallback
            $query->orderBy('name', $sortDirection);
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
        return view('livewire.admin-user-table', [
            'users' => $this->users,
        ]);
    }
}
