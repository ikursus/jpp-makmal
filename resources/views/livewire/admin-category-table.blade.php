<div>
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="table-toolbar" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 16px;">
        <!-- Search -->
        <div style="flex: 1; min-width: 200px;">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="🔍 Cari nama atau penerangan kategori..."
                class="form-control"
            >
        </div>

        <!-- Filter Status -->
        <div style="min-width: 200px;">
            <select wire:model.live="filterStatus" class="form-control">
                <option value="">Semua Status</option>
                <option value="dipinjam">Dipinjam</option>
                <option value="dalam_proses_permohonan">Dalam Proses Permohonan</option>
                <option value="dikembalikan">Dikembalikan</option>
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

    <!-- Categories Table -->
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
                        <button wire:click="sortBy('description')" class="btn-sort">
                            Penerangan
                            @if($sortField === 'description')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Barang</th>
                    <th>
                        <button wire:click="sortBy('status')" class="btn-sort">
                            Status
                            @if($sortField === 'status')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->description ?? '-' }}</td>
                        <td>{{ $c->items_count }}</td>
                        <td>
                            @switch($c->status)
                                @case('dipinjam') <span class="badge badge-warning">Dipinjam</span> @break
                                @case('dalam_proses_permohonan') <span class="badge badge-info">Dalam Proses Permohonan</span> @break
                                @case('dikembalikan') <span class="badge badge-success">Dikembalikan</span> @break
                                @default <span class="badge badge-secondary">{{ $c->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-primary">Kemaskini</a>
                            <button
                                wire:click="destroy({{ $c->id }})"
                                wire:confirm="Padam kategori ini?"
                                class="btn btn-sm btn-danger"
                            >
                                Padam
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                            Tiada kategori dijumpai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Info & Links -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 12px;">
        <div style="color: #6b7280; font-size: 0.875rem;">
            Menunjukkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} daripada {{ $categories->total() }} kategori
        </div>
        <div>
            {{ $categories->links() }}
        </div>
    </div>
</div>