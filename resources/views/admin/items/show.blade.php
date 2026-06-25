@extends('layouts.admin')
@section('title', 'Maklumat Barang')
@section('admin-content')
<div class="page-header"><h1>{{ $item->name }}</h1><a href="{{ route('admin.items.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card">
    <p><strong>Penerangan:</strong> {{ $item->description ?? '-' }}</p>
    <p><strong>Kategori:</strong> {{ $item->category->name ?? '-' }}</p>
    <p><strong>Lokasi:</strong> {{ $item->storageLocation->name ?? '-' }}</p>
    <p><strong>Kuantiti:</strong> {{ $item->quantity }} (Tersedia: {{ $item->available_quantity }})</p>
    <p><strong>Keadaan:</strong> {{ ucfirst($item->condition) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($item->status) }}</p>
    <p><strong>Tarikh Luput:</strong> {{ $item->expiry_date ? $item->expiry_date->format('d/m/Y') : '-' }}</p>
</div>
@if($item->itemConditions->isNotEmpty())
<div class="card mt-3">
    <h3>Sejarah Keadaan</h3>
    <table class="table">
        <thead><tr><th>Tarikh</th><th>Keadaan</th><th>Catatan</th><th>Dikemaskini Oleh</th></tr></thead>
        <tbody>
        @foreach($item->itemConditions as $cond)
            <tr>
                <td>{{ $cond->created_at->format('d/m/Y') }}</td>
                <td>{{ ucfirst($cond->condition_from) }} → {{ ucfirst($cond->condition_to) }}</td>
                <td>{{ $cond->notes }}</td>
                <td>{{ $cond->changedBy->name ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
