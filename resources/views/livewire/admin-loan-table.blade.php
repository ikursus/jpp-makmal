<div>
    <!-- Search & Filter Bar -->
    <div class="table-toolbar" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 16px;">
        <!-- Search -->
        <div style="flex: 1; min-width: 200px;">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="🔍 Cari no. pinjaman, peminjam atau daerah..."
                class="form-control"
            >
        </div>

        <!-- Filter Status -->
        <div style="min-width: 150px;">
            <select wire:model.live="filterStatus" class="form-control">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="terlewat">Terlewat</option>
                <option value="dipulangkan">Dipulangkan</option>
            </select>
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
                wire:click="$set('search', ''); $set('filterStatus', ''); $set('filterDistrict', ''); $set('sortField', 'created_at'); $set('sortDirection', 'desc'); $set('perPage', 10)"
                class="btn btn-sm btn-secondary"
                title="Set semula penapis"
                @if(empty($search) && empty($filterStatus) && empty($filterDistrict) && $sortField === 'created_at' && $sortDirection === 'desc' && $perPage === 10) disabled @endif
            >
                ⟳ Set Semula
            </button>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>
                        <button wire:click="sortBy('loan_no')" class="btn-sort">
                            No. Pinjaman
                            @if($sortField === 'loan_no')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('user_name')" class="btn-sort">
                            Peminjam
                            @if($sortField === 'user_name')
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
                        <button wire:click="sortBy('items_count')" class="btn-sort">
                            Barang
                            @if($sortField === 'items_count')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('start_date')" class="btn-sort">
                            Tarikh Mula
                            @if($sortField === 'start_date')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('end_date')" class="btn-sort">
                            Tarikh Akhir
                            @if($sortField === 'end_date')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
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
                @forelse($loans as $loan)
                    <tr>
                        <td><strong>{{ $loan->loan_no }}</strong></td>
                        <td>{{ $loan->user->name ?? '-' }}</td>
                        <td>{{ $loan->district->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $loan->items_count }} item</span>
                        </td>
                        <td>{{ $loan->start_date->format('d/m/Y') }}</td>
                        <td>{{ $loan->end_date->format('d/m/Y') }}</td>
                        <td>
                            @switch($loan->status)
                                @case('aktif')
                                    <span class="badge badge-success">Aktif</span>
                                    @break
                                @case('terlewat')
                                    <span class="badge badge-danger">Terlewat</span>
                                    @break
                                @case('dipulangkan')
                                    <span class="badge badge-secondary">Dipulangkan</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ ucfirst($loan->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if(in_array($loan->status, ['aktif', 'terlewat']))
                                <a href="{{ route('admin.loans.return.form', $loan) }}" class="btn btn-sm btn-primary">Pulang</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                            Tiada rekod pinjaman dijumpai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Info & Links -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 12px;">
        <div style="color: #6b7280; font-size: 0.875rem;">
            Menunjukkan {{ $loans->firstItem() ?? 0 }} - {{ $loans->lastItem() ?? 0 }} daripada {{ $loans->total() }} rekod
        </div>
        <div>
            {{ $loans->links() }}
        </div>
    </div>
</div>
