@extends('layouts.admin')
@section('title', 'Lokasi Penyimpanan')
@section('admin-content')
<div class="page-header"><h1>Lokasi Penyimpanan</h1><a href="{{ route('admin.storage-locations.create') }}" class="btn btn-primary">+ Tambah Lokasi</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
<div class="card">
<table class="table">
    <thead><tr><th>Nama</th><th>Kod</th><th>Penerangan</th><th>Status</th><th>Tindakan</th></tr></thead>
    <tbody>
    @foreach($locations as $loc)
        <tr>
            <td>{{ $loc->name }}</td><td>{{ $loc->code }}</td><td>{{ $loc->description }}</td>
            <td>{{ $loc->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
            <td><a href="{{ route('admin.storage-locations.edit', $loc) }}" class="btn btn-sm">Kemaskini</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $locations->links() }}
</div>
@endsection
