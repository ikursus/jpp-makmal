@extends('layouts.admin')
@section('title', 'Edit Pengguna')
@section('admin-content')
<div class="page-header"><h1>Edit Pengguna</h1><a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
        <div class="form-group"><label>Nama</label><input type="text" name="name" class="form-control" value="{{ $user->name }}" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}" required></div>
        <div class="form-group"><label>Kata Laluan (kosongkan jika tidak ubah)</label><input type="password" name="password" class="form-control"></div>
        <div class="form-group"><label>Telefon</label><input type="text" name="phone" class="form-control" value="{{ $user->phone }}"></div>
        <div class="form-group"><label>Daerah</label><select name="district_id" class="form-control">
            <option value="">- Pilih Daerah -</option>
            @foreach($districts as $d)<option value="{{ $d->id }}" {{ $user->district_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach
        </select></div>
        <div class="form-group"><label>Peranan</label><select name="role" class="form-control" required>
            @foreach($roles as $r)<option value="{{ $r->name }}" {{ $user->roles->contains('name', $r->name) ? 'selected' : '' }}>{{ $r->name }}</option>@endforeach
        </select></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}> Aktif</label></div>
        <button type="submit" class="btn btn-primary">Kemaskini</button>
    </form>
</div>
@endsection
