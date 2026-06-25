@extends('layouts.admin')
@section('title', 'Edit Barang')
@section('admin-content')
<div class="page-header"><h1>Edit Barang</h1><a href="{{ route('admin.items.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:700px;">
    <form method="POST" action="{{ route('admin.items.update', $item) }}">@csrf @method('PUT')
        <div class="form-group"><label>Nama Barang</label><input type="text" name="name" class="form-control" value="{{ $item->name }}" required></div>
        <div class="form-group"><label>Penerangan</label><textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Kuantiti</label><input type="number" name="quantity" class="form-control" value="{{ $item->quantity }}" min="0" required></div>
            <div class="form-group"><label>Keadaan</label><select name="condition" class="form-control" required>
                <option value="baik" {{ $item->condition == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rosak" {{ $item->condition == 'rosak' ? 'selected' : '' }}>Rosak</option>
                <option value="service" {{ $item->condition == 'service' ? 'selected' : '' }}>Service</option>
            </select></div>
            <div class="form-group"><label>Status</label><select name="status" class="form-control" required>
                <option value="tersedia" {{ $item->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="dipinjam" {{ $item->status == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="disimpan" {{ $item->status == 'disimpan' ? 'selected' : '' }}>Disimpan</option>
                <option value="rosak" {{ $item->status == 'rosak' ? 'selected' : '' }}>Rosak</option>
            </select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Kategori</label><select name="category_id" class="form-control" required>
                @foreach($categories as $c)<option value="{{ $c->id }}" {{ $item->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
            </select></div>
            <div class="form-group"><label>Lokasi Penyimpanan</label><select name="storage_location_id" class="form-control" required>
                @foreach($storageLocations as $l)<option value="{{ $l->id }}" {{ $item->storage_location_id == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>@endforeach
            </select></div>
        </div>
        <div class="form-group"><label>Tarikh Luput (pilihan)</label><input type="date" name="expiry_date" class="form-control" value="{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '' }}"></div>
        <button type="submit" class="btn btn-primary">Kemaskini</button>
    </form>
</div>
@endsection
