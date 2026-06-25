@extends('layouts.admin')
@section('title', 'Kategori')
@section('admin-content')
<div class="page-header">
    <h1>Kategori Barang</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-success">+ Tambah Kategori</a>
</div>

<div class="card">
    @livewire('admin-category-table')
</div>
@endsection
