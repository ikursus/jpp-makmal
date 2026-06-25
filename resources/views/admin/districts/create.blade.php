@extends('layouts.admin')
@section('title', 'Tambah Daerah')
@section('admin-content')
<div class="page-header"><h1>Tambah Daerah</h1><a href="{{ route('admin.districts.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.districts.store') }}">@csrf
        <div class="form-group"><label>Nama Daerah</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Kod Daerah</label><input type="text" name="code" class="form-control" required maxlength="10"></div>
        <div class="form-group"><label>Alamat</label><textarea name="address" class="form-control" rows="3"></textarea></div>
        <div class="form-group"><label>No Telefon</label><input type="text" name="phone" class="form-control" maxlength="20"></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" value="1" checked> Aktif</label></div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
