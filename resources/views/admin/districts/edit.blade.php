@extends('layouts.admin')
@section('title', 'Edit Daerah')
@section('admin-content')
<div class="page-header"><h1>Edit Daerah</h1><a href="{{ route('admin.districts.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.districts.update', $district) }}">@csrf @method('PUT')
        <div class="form-group"><label>Nama Daerah</label><input type="text" name="name" class="form-control" value="{{ $district->name }}" required></div>
        <div class="form-group"><label>Kod Daerah</label><input type="text" name="code" class="form-control" value="{{ $district->code }}" required maxlength="10"></div>
        <div class="form-group"><label>Alamat</label><textarea name="address" class="form-control" rows="3">{{ $district->address }}</textarea></div>
        <div class="form-group"><label>No Telefon</label><input type="text" name="phone" class="form-control" value="{{ $district->phone }}" maxlength="20"></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" value="1" {{ $district->is_active ? 'checked' : '' }}> Aktif</label></div>
        <button type="submit" class="btn btn-primary">Kemaskini</button>
    </form>
</div>
@endsection
