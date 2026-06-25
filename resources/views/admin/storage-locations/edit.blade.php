@extends('layouts.admin')
@section('title', 'Edit Lokasi Penyimpanan')
@section('admin-content')
<div class="page-header"><h1>Edit Lokasi Penyimpanan</h1><a href="{{ route('admin.storage-locations.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.storage-locations.update', $storageLocation) }}">@csrf @method('PUT')
        <div class="form-group"><label>Nama Lokasi</label><input type="text" name="name" class="form-control" value="{{ $storageLocation->name }}" required></div>
        <div class="form-group"><label>Kod</label><input type="text" name="code" class="form-control" value="{{ $storageLocation->code }}" required></div>
        <div class="form-group"><label>Penerangan</label><textarea name="description" class="form-control" rows="3">{{ $storageLocation->description }}</textarea></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" value="1" {{ $storageLocation->is_active ? 'checked' : '' }}> Aktif</label></div>
        <button type="submit" class="btn btn-primary">Kemaskini</button>
    </form>
</div>
@endsection
