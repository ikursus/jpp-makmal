@extends('layouts.admin')
@section('title', 'Laporan')
@section('admin-content')
<div class="page-header"><h1>Laporan & Analitik</h1></div>

@if(session('info'))<div class="alert alert-info">{{ session('info') }}</div>@endif

<div class="card mb-3">
    <h3>Eksport Laporan</h3>
    <div class="export-buttons">
        <a href="{{ route('admin.reports.export', 'pdf') }}" class="btn btn-danger">📄 Eksport PDF</a>
        <a href="{{ route('admin.reports.export', 'excel') }}" class="btn btn-success">📊 Eksport Excel</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak</button>
    </div>
</div>

<div class="card">
    <h3>Ringkasan Inventori</h3>
    <table class="table">
        <thead><tr><th>Metrik</th><th>Jumlah</th></tr></thead>
        <tbody>
            <tr><td>Jumlah Barang</td><td>{{ $totalItems ?? '-' }}</td></tr>
            <tr><td>Barang Tersedia</td><td>{{ $availableItems ?? '-' }}</td></tr>
            <tr><td>Barang Dipinjam</td><td>{{ $loanedItems ?? '-' }}</td></tr>
        </tbody>
    </table>
</div>

<div class="card mt-3">
    <h3>Permohonan & Pinjaman</h3>
    <table class="table">
        <thead><tr><th>Metrik</th><th>Jumlah</th></tr></thead>
        <tbody>
            <tr><td>Permohonan Menunggu</td><td>{{ $pendingApplications ?? '-' }}</td></tr>
            <tr><td>Permohonan Diluluskan</td><td>{{ $approvedApplications ?? '-' }}</td></tr>
            <tr><td>Pinjaman Aktif</td><td>{{ $activeLoans ?? '-' }}</td></tr>
        </tbody>
    </table>
</div>
@endsection
