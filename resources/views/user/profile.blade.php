@extends('layouts.user')
@section('title', 'Profil Saya')
@section('user-content')
<div class="page-header"><h1>Profil Saya</h1></div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('user.profile.update') }}">@csrf @method('PUT')
        <div class="form-group"><label>Nama</label><input type="text" name="name" class="form-control" value="{{ $user->name }}" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}" required></div>
        <div class="form-group"><label>Telefon</label><input type="text" name="phone" class="form-control" value="{{ $user->phone }}"></div>
        <div class="form-group"><label>Daerah</label><input type="text" class="form-control" value="{{ $user->district->name ?? '-' }}" disabled></div>
        <hr>
        <h3>Tukar Kata Laluan (pilihan)</h3>
        <div class="form-group"><label>Kata Laluan Baru</label><input type="password" name="password" class="form-control"></div>
        <div class="form-group"><label>Sahkan Kata Laluan</label><input type="password" name="password_confirmation" class="form-control"></div>
        <button type="submit" class="btn btn-primary">Kemaskini Profil</button>
        <a href="{{ route('user.profile.pdf') }}" class="btn btn-success" target="_blank" style="margin-left: 8px;">Muat Turun PDF</a>
    </form>
</div>
@endsection
