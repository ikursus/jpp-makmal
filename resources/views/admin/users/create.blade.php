@extends('layouts.admin')
@section('title', 'Tambah Pengguna')
@section('admin-content')
<div class="page-header"><h1>Tambah Pengguna</h1><a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.users.store') }}">@csrf
        <div class="form-group"><label>Nama</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="form-group"><label>Kata Laluan</label><input type="password" name="password" class="form-control" required></div>
        <div class="form-group"><label>Telefon</label><input type="text" name="phone" class="form-control"></div>
        <div class="form-group"><label>Daerah</label><select name="district_id" class="form-control">
            <option value="">- Pilih Daerah -</option>
            @foreach($districts as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
        </select></div>
        <div class="form-group"><label>Peranan</label><select name="role" class="form-control" required>
            @foreach($roles as $r)<option value="{{ $r->name }}">{{ $r->name }}</option>@endforeach
        </select></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" value="1" checked> Aktif</label></div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
