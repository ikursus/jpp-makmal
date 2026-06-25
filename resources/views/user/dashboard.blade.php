@extends('layouts.user')
@section('title', 'Dashboard')
@section('user-content')

<div class="welcome-hero">
    <h1>Selamat Datang, {{ Auth::user()->name }} 👋</h1>
    <p>{{ Auth::user()->district->name ?? 'Ibu Pejabat' }} · Ringkasan aktiviti pinjaman anda</p>
</div>

<div class="stats-grid">
    <div class="metric accent-warning">
        <div class="metric-icon">📋</div>
        <div>
            <div class="metric-value">{{ $pendingCount }}</div>
            <div class="metric-label">Permohonan Menunggu</div>
        </div>
    </div>
    <div class="metric accent-success">
        <div class="metric-icon">✅</div>
        <div>
            <div class="metric-value">{{ $approvedCount }}</div>
            <div class="metric-label">Permohonan Diluluskan</div>
        </div>
    </div>
    <div class="metric accent-info">
        <div class="metric-icon">📦</div>
        <div>
            <div class="metric-value">{{ $activeLoans->count() }}</div>
            <div class="metric-label">Pinjaman Aktif</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="flex-between mb-3">
        <h3 class="section-title" style="margin:0;">Pinjaman Aktif</h3>
        <a href="{{ route('user.loans.index') }}" class="text-sm">Lihat Semua →</a>
    </div>
    @if($activeLoans->isNotEmpty())
    <div class="table-container">
        <table class="table">
            <thead><tr><th>No. Pinjaman</th><th>Barang</th><th>Tarikh Akhir</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($activeLoans as $loan)
                <tr>
                    <td>{{ $loan->loan_no }}</td>
                    <td>{{ $loan->items->count() }} item</td>
                    <td>{{ $loan->end_date->format('d/m/Y') }}</td>
                    <td><span class="badge badge-info">{{ ucfirst($loan->status) }}</span></td>
                    <td><a href="{{ route('user.loans.show', $loan->id) }}" class="btn btn-sm btn-primary">Lihat</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state"><span class="emoji">📭</span>Tiada pinjaman aktif buat masa ini.</div>
    @endif
</div>

<div class="card">
    <div class="flex-between mb-3">
        <h3 class="section-title" style="margin:0;">Permohonan Terkini</h3>
        <a href="{{ route('user.loan-applications.index') }}" class="text-sm">Lihat Semua →</a>
    </div>
    @if($recentApplications->isNotEmpty())
    <div class="table-container">
        <table class="table">
            <thead><tr><th>No. Permohonan</th><th>Tarikh</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($recentApplications as $app)
                <tr>
                    <td><a href="{{ route('user.loan-applications.show', $app->id) }}">{{ $app->application_no }}</a></td>
                    <td>{{ $app->created_at->format('d/m/Y') }}</td>
                    <td>
                        @switch($app->status)
                            @case('menunggu') <span class="badge badge-warning">Menunggu</span> @break
                            @case('diluluskan') <span class="badge badge-success">Diluluskan</span> @break
                            @case('ditolak') <span class="badge badge-danger">Ditolak</span> @break
                            @default <span class="badge badge-secondary">{{ ucfirst($app->status) }}</span>
                        @endswitch
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state"><span class="emoji">📝</span>Belum ada permohonan. Klik "Mohon Pinjaman" untuk bermula.</div>
    @endif
</div>
@endsection
