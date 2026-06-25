<div>
    <!-- Search & Filter Bar -->
    <div class="table-toolbar" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 16px;">
        <!-- Search -->
        <div style="flex: 1; min-width: 200px;">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="🔍 Cari no. permohonan, nama atau emel pemohon..."
                class="form-control"
            >
        </div>

        <!-- Filter Category -->
        <div style="min-width: 160px;">
            <select wire:model.live="filterCategory" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($this->categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status -->
        <div style="min-width: 150px;">
            <select wire:model.live="filterStatus" class="form-control">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="diluluskan">Diluluskan</option>
                <option value="ditolak">Ditolak</option>
                <option value="dipinjam">Dipinjam</option>
                <option value="dikembalikan">Dikembalikan</option>
                <option value="dibatalkan">Dibatalkan</option>
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
                wire:click="$set('search', ''); $set('filterCategory', ''); $set('filterStatus', ''); $set('sortField', 'created_at'); $set('sortDirection', 'desc'); $set('perPage', 10)"
                class="btn btn-sm btn-secondary"
                title="Set semula penapis"
                @if(empty($search) && $filterCategory === '' && $filterStatus === '' && $sortField === 'created_at' && $sortDirection === 'desc' && $perPage === 10) disabled @endif
            >
                ⟳ Set Semula
            </button>
        </div>
    </div>

    <!-- Loan Applications Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>
                        <button wire:click="sortBy('application_no')" class="btn-sort">
                            No. Permohonan
                            @if($sortField === 'application_no')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Pemohon</th>
                    <th>Daerah</th>
                    <th>
                        <button wire:click="sortBy('start_date')" class="btn-sort">
                            Tempoh Pinjaman
                            @if($sortField === 'start_date')
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
                    <th>
                        <button wire:click="sortBy('created_at')" class="btn-sort">
                            Tarikh Mohon
                            @if($sortField === 'created_at')
                                <span class="sort-indicator">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </button>
                    </th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td><strong>{{ $app->application_no }}</strong></td>
                        <td>
                            {{ $app->user->name }}
                            <br><small style="color: #6b7280;">{{ $app->user->email }}</small>
                        </td>
                        <td>{{ $app->district->name }}</td>
                        <td>
                            <small>
                                <span style="display: block;">Mula: {{ $app->start_date->format('d/m/Y') }}</span>
                                <span style="display: block; color: #6b7280;">Tamat: {{ $app->end_date->format('d/m/Y') }}</span>
                            </small>
                        </td>
                        <td>
                            @switch($app->status)
                                @case('menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                    @break
                                @case('diluluskan')
                                    <span class="badge badge-success">Diluluskan</span>
                                    @break
                                @case('ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                    @break
                                @case('dipinjam')
                                    <span class="badge badge-info">Dipinjam</span>
                                    @break
                                @case('dikembalikan')
                                    <span class="badge badge-secondary">Dikembalikan</span>
                                    @break
                                @case('dibatalkan')
                                    <span class="badge badge-secondary">Dibatalkan</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ ucfirst($app->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <small>{{ $app->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.loan-applications.show', $app) }}" class="btn btn-sm btn-primary">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                            Tiada permohonan dijumpai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Info & Links -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 12px;">
        <div style="color: #6b7280; font-size: 0.875rem;">
            Menunjukkan {{ $applications->firstItem() ?? 0 }} - {{ $applications->lastItem() ?? 0 }} daripada {{ $applications->total() }} permohonan
        </div>
        <div>
            {{ $applications->links() }}
        </div>
    </div>
</div>
