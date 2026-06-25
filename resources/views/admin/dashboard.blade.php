@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('admin-content')

<div class="welcome-hero">
    <h1>Selamat Datang, {{ auth()->user()->name }} 👋</h1>
    <p>Ringkasan keseluruhan sistem inventori &amp; pinjaman barangan makmal</p>
</div>

<div class="stats-grid">
    <div class="metric">
        <div class="metric-icon">📦</div>
        <div>
            <div class="metric-value">{{ $totalItems }}</div>
            <div class="metric-label">Jumlah Barang</div>
        </div>
    </div>
    <div class="metric accent-success">
        <div class="metric-icon">✅</div>
        <div>
            <div class="metric-value">{{ $availableItems }}</div>
            <div class="metric-label">Tersedia</div>
        </div>
    </div>
    <div class="metric accent-info">
        <div class="metric-icon">📤</div>
        <div>
            <div class="metric-value">{{ $loanedItems }}</div>
            <div class="metric-label">Dipinjam</div>
        </div>
    </div>
    <div class="metric accent-warning">
        <div class="metric-icon">⏳</div>
        <div>
            <div class="metric-value">{{ $pendingApplications }}</div>
            <div class="metric-label">Menunggu Kelulusan</div>
        </div>
    </div>
    <div class="metric accent-success">
        <div class="metric-icon">👍</div>
        <div>
            <div class="metric-value">{{ $approvedApplications }}</div>
            <div class="metric-label">Permohonan Diluluskan</div>
        </div>
    </div>
    <div class="metric accent-info">
        <div class="metric-icon">📝</div>
        <div>
            <div class="metric-value">{{ $activeLoans }}</div>
            <div class="metric-label">Pinjaman Aktif</div>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h3 class="section-title">Permohonan Terkini</h3>
        @if($recentApplications->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>No.</th><th>Pemohon</th><th>Daerah</th><th>Status</th><th>Tindakan</th></tr>
                </thead>
                <tbody>
                    @foreach($recentApplications as $app)
                    <tr>
                        <td>{{ $app->application_no }}</td>
                        <td>{{ $app->user->name }}</td>
                        <td>{{ $app->district->name }}</td>
                        <td>
                            @switch($app->status)
                                @case('menunggu') <span class="badge badge-warning">Menunggu</span> @break
                                @case('diluluskan') <span class="badge badge-success">Diluluskan</span> @break
                                @case('ditolak') <span class="badge badge-danger">Ditolak</span> @break
                                @default <span class="badge badge-secondary">{{ ucfirst($app->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <a href="{{ route('admin.loan-applications.show', $app) }}" class="btn btn-sm btn-primary">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state"><span class="emoji">📭</span>Tiada permohonan terkini.</div>
        @endif
    </div>

    <div class="card">
        <h3 class="section-title">⚠️ Barangan Hampir Luput</h3>
        @if($expiringItems->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Barang</th><th>Tarikh Luput</th><th>Hari Lagi</th></tr>
                </thead>
                <tbody>
                    @foreach($expiringItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->expiry_date->format('d/m/Y') }}</td>
                        <td>{{ (int) now()->diffInDays($item->expiry_date) }} hari</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state"><span class="emoji">✅</span>Tiada barang hampir luput.</div>
        @endif
    </div>
</div>
@endsection
