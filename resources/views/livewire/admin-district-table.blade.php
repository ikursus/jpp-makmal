<div>
    <!-- Search & Filter Bar -->
    <div class="table-toolbar" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 16px;">
        <!-- Search -->
        <div style="flex: 1; min-width: 200px;">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="🔍 Cari nama daerah, kod, telefon atau alamat..."
                class="form-control"
            >
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
                wire:click="$set('search', ''); $set('filterStatus', ''); $set('sortField', 'created_at'); $set('sortDirection', 'desc'); $set('perPage', 10)"
                class="btn btn-sm btn-secondary"
                title="Set semula penapis"
                @if(empty($search) && $filterStatus === '' && $sortField === 'created_at' && $sortDirection === 'desc' && $perPage === 10) disabled @endif
            >
                ⟳ Set Semula
            </button>
        </div>
    </div>

    <!-- Districts Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>
                        <button wire:click="sortBy('id')" class="btn-sort">
                            #
                            @if($sortField === 'id')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('name')" class="btn-sort">
                            Nama
                            @if($sortField === 'name')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('code')" class="btn-sort">
                            Kod
                            @if($sortField === 'code')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('phone')" class="btn-sort">
                            Telefon
                            @if($sortField === 'phone')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Pengguna</th>
                    <th>Permohonan</th>
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
                @forelse($districts as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->name }}</td>
                        <td><code>{{ $d->code }}</code></td>
                        <td>{{ $d->phone ?? '-' }}</td>
                        <td>{{ $d->users_count }}</td>
                        <td>{{ $d->loan_applications_count }}</td>
                        <td>
                            @if($d->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.districts.edit', $d) }}" class="btn btn-sm btn-primary">Kemaskini</a>
                            <button
                                wire:click="destroy({{ $d->id }})"
                                wire:confirm="Padam daerah ini?"
                                class="btn btn-sm btn-danger"
                            >
                                Padam
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                            Tiada daerah dijumpai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Info & Links -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 12px;">
        <div style="color: #6b7280; font-size: 0.875rem;">
            Menunjukkan {{ $districts->firstItem() ?? 0 }} - {{ $districts->lastItem() ?? 0 }} daripada {{ $districts->total() }} daerah
        </div>
        <div>
            {{ $districts->links() }}
        </div>
    </div>
</div>
