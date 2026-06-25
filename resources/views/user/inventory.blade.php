@extends('layouts.user')
@section('title', 'Senarai Inventori')
@section('user-content')
<div class="page-header"><h1>Senarai Inventori</h1></div>

<div class="card mb-3">
    <form method="GET" action="{{ route('user.inventory') }}" class="form-row" style="align-items:flex-end;">
        <div class="form-group" style="flex:2;">
            <label>Cari Barang</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nama barang...">
        </div>
        <div class="form-group" style="flex:1;">
            <label>Kategori</label>
            <select name="category_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="{{ route('user.inventory') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>Nama Barang</th><th>Kategori</th><th>Lokasi</th><th>Keadaan</th><th>Status</th><th>Stok Tersedia</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td>{{ $item->storageLocation->name ?? '-' }}</td>
                    <td>{{ ucfirst($item->condition) }}</td>
                    <td>
                        @switch($item->status)
                            @case('tersedia') <span class="badge badge-success">Tersedia</span> @break
                            @case('dipinjam') <span class="badge badge-info">Dipinjam</span> @break
                            @case('disimpan') <span class="badge badge-secondary">Disimpan</span> @break
                            @case('rosak') <span class="badge badge-danger">Rosak</span> @break
                            @default <span class="badge badge-secondary">{{ ucfirst($item->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ $item->available_quantity }}</td>
                    <td>
                        @can('create-loan-application')
                            @if($item->status === 'tersedia' && $item->available_quantity > 0)
                                <a href="{{ route('user.loan-applications.create', ['item' => $item->id]) }}" class="btn btn-sm btn-success">Mohon</a>
                            @else
                                <span class="text-gray text-xs">Tidak tersedia</span>
                            @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-gray" style="text-align:center; padding:24px;">Tiada barang dijumpai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $items->links() }}</div>
</div>
@endsection
