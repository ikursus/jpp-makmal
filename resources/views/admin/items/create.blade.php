@extends('layouts.admin')
@section('title', 'Tambah Barang')
@section('admin-content')
<div class="page-header"><h1>Tambah Barang</h1><a href="{{ route('admin.items.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:700px;">
    <form method="POST" action="{{ route('admin.items.store') }}">@csrf
        <div class="form-group"><label>Nama Barang</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Penerangan</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Kuantiti</label><input type="number" name="quantity" class="form-control" min="0" required></div>
            <div class="form-group"><label>Keadaan</label><select name="condition" class="form-control" required>
                <option value="baik">Baik</option><option value="rosak">Rosak</option><option value="service">Service</option>
            </select></div>
            <div class="form-group"><label>Status</label><select name="status" class="form-control" required>
                <option value="tersedia">Tersedia</option><option value="dipinjam">Dipinjam</option>
                <option value="disimpan">Disimpan</option><option value="rosak">Rosak</option>
            </select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Kategori</label><select name="category_id" class="form-control" required>
                @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select></div>
            <div class="form-group"><label>Lokasi Penyimpanan</label><select name="storage_location_id" class="form-control" required>
                @foreach($storageLocations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach
            </select></div>
        </div>
        <div class="form-group"><label>Tarikh Luput (pilihan)</label><input type="date" name="expiry_date" class="form-control"></div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
