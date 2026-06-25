@extends('layouts.admin')
@section('title', 'Lokasi Penyimpanan')
@section('admin-content')
<div class="page-header">
    <h1>Lokasi Penyimpanan</h1>
    <a href="{{ route('admin.storage-locations.create') }}" class="btn btn-primary">+ Tambah Lokasi</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card">
    @livewire('admin-storage-location-table')
</div>
@endsection
