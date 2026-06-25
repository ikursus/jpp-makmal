<div>
    <!-- Search & Filter Bar -->
    <div class="table-toolbar" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 16px;">
        <!-- Search -->
        <div style="flex: 1; min-width: 200px;">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="🔍 Cari nama, email atau telefon..."
                class="form-control"
            >
        </div>

        <!-- Filter Daerah -->
        <div style="min-width: 160px;">
            <select wire:model.live="filterDistrict" class="form-control">
                <option value="">Semua Daerah</option>
                @foreach($this->districts as $district)
                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Peranan -->
        <div style="min-width: 140px;">
            <select wire:model.live="filterRole" class="form-control">
                <option value="">Semua Peranan</option>
                @foreach($this->roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status -->
        <div style="min-width: 130px;">
            <select wire:model.live="filterStatus" class="form-control">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
            </select>
        </div>

        <!-- Per Page -->
        <div style="min-width: 100px;">
            <select wire:model.live="perPage" class="form-control">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="0">Semua</option>
            </select>
        </div>

        <!-- Reset Filter -->
        <div>
            <button
                wire:click="$set('search', ''); $set('filterDistrict', ''); $set('filterRole', ''); $set('filterStatus', ''); $set('sortField', 'created_at'); $set('sortDirection', 'desc'); $set('perPage', 10)"
                class="btn btn-sm btn-secondary"
                title="Set semula penapis"
                @if(empty($search) && empty($filterDistrict) && empty($filterRole) && $filterStatus === '' && $sortField === 'created_at' && $sortDirection === 'desc' && $perPage === 10) disabled @endif
            >
                ⟳ Set Semula
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>
                        <button wire:click="sortBy('name')" class="btn-sort">
                            Nama
                            @if($sortField === 'name')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('email')" class="btn-sort">
                            Email
                            @if($sortField === 'email')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('district_name')" class="btn-sort">
                            Daerah
                            @if($sortField === 'district_name')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('role_name')" class="btn-sort">
                            Peranan
                            @if($sortField === 'role_name')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('is_active')" class="btn-sort">
                            Status
                            @if($sortField === 'is_active')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->district->name ?? '-' }}</td>
                        <td>{{ $u->roles->pluck('name')->join(', ') }}</td>
                        <td>
                            @if($u->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-primary">Kemaskini</a>
                            <a href="{{ route('admin.users.pdf', $u) }}" class="btn btn-sm btn-success" target="_blank" title="Muat Turun PDF">PDF</a>
                            @if(!$u->hasRole('super_admin'))
                                <button
                                    wire:click="deleteUser({{ $u->id }})"
                                    wire:confirm="Padam pengguna ini?"
                                    class="btn btn-sm btn-danger"
                                >
                                    Padam
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                            Tiada pengguna dijumpai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Info & Links -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 12px;">
        <div style="color: #6b7280; font-size: 0.875rem;">
            Menunjukkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} daripada {{ $users->total() }} pengguna
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>
