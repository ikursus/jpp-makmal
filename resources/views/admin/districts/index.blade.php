@extends('layouts.admin')
@section('title', 'Pengurusan Daerah')
@section('admin-content')
<div class="page-header">
    <h1>Pengurusan Daerah</h1>
    <a href="{{ route('admin.districts.create') }}" class="btn btn-primary">+ Tambah Daerah</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card">
    @livewire('admin-district-table')
</div>
@endsection
