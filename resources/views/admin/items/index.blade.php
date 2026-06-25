@extends('layouts.admin')
@section('title', 'Pengurusan Inventori')
@section('admin-content')
<div class="page-header"><h1>Pengurusan Inventori</h1><a href="{{ route('admin.items.create') }}" class="btn btn-success">+ Tambah Barang</a></div>
<div class="card">
    <div class="table-container">
        <table>
            <thead><tr><th>#</th><th>Nama Barang</th><th>Kuantiti</th><th>Status</th><th>Kondisi</th><th>Kategori</th><th>Lokasi</th><th>Tindakan</th></tr></thead>
            <tbody>
                @foreach($items as $i)
                <tr>
                    <td>{{ $i->id }}</td>
                    <td><a href="{{ route('admin.items.show', $i) }}">{{ $i->name }}</a></td>
                    <td>{{ $i->available_quantity }}/{{ $i->quantity }}</td>
                    <td>@switch($i->status) @case('tersedia')<span class="badge badge-success">Tersedia</span>@break @case('dipinjam')<span class="badge badge-warning">Dipinjam</span>@break @case('rosak')<span class="badge badge-danger">Rosak</span>@break @default<span class="badge badge-secondary">{{ $i->status }}</span>@endswitch</td>
                    <td>@switch($i->condition) @case('baik')<span class="badge badge-success">Baik</span>@break @case('rosak')<span class="badge badge-danger">Rosak</span>@break @case('service')<span class="badge badge-warning">Service</span>@break @endswitch</td>
                    <td>{{ $i->category->name ?? '-' }}</td>
                    <td>{{ $i->storageLocation->code ?? '-' }}</td>
                    <td class="flex gap-2">
                        <a href="{{ route('admin.items.edit', $i) }}" class="btn btn-sm btn-primary">Kemaskini</a>
                        <form method="POST" action="{{ route('admin.items.destroy', $i) }}" onsubmit="return confirm('Padam barang ini?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger">Padam</button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $items->links() }}</div>
</div>
@endsection
