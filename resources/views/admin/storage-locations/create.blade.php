@extends('layouts.admin')
@section('title', 'Tambah Lokasi Penyimpanan')
@section('admin-content')
<div class="page-header"><h1>Tambah Lokasi Penyimpanan</h1><a href="{{ route('admin.storage-locations.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.storage-locations.store') }}">@csrf
        <div class="form-group"><label>Nama Lokasi</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Kod</label><input type="text" name="code" class="form-control" required></div>
        <div class="form-group"><label>Penerangan</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" value="1" checked> Aktif</label></div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
