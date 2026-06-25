@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('admin-content')
<div class="page-header"><h1>Edit Kategori</h1><a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">← Kembali</a></div>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">@csrf @method('PUT')
        <div class="form-group"><label>Nama Kategori</label><input type="text" name="name" class="form-control" value="{{ $category->name }}" required></div>
        <div class="form-group"><label>Penerangan</label><textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea></div>
        <div class="form-group"><label>Status</label><select name="status" class="form-control" required>
            <option value="dipinjam" {{ $category->status == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            <option value="dalam_proses_permohonan" {{ $category->status == 'dalam_proses_permohonan' ? 'selected' : '' }}>Dalam Proses Permohonan</option>
            <option value="dikembalikan" {{ $category->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
        </select></div>
        <button type="submit" class="btn btn-primary">Kemaskini</button>
    </form>
</div>
@endsection
