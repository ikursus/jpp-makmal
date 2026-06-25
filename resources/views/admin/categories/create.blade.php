@extends('layouts.admin')
@section('title', 'Tambah Kategori')
@section('admin-content')
<div class="page-header"><h1>Tambah Kategori</h1><a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.categories.store') }}">@csrf
        <div class="form-group"><label>Nama Kategori</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Penerangan</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        <div class="form-group"><label>Status</label><select name="status" class="form-control" required>
            <option value="dipinjam">Dipinjam</option>
            <option value="dalam_proses_permohonan">Dalam Proses Permohonan</option>
            <option value="dikembalikan">Dikembalikan</option>
        </select></div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
