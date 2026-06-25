@extends('layouts.admin')
@section('title', 'Kategori')
@section('admin-content')
<div class="page-header"><h1>Kategori Barang</h1><a href="{{ route('admin.categories.create') }}" class="btn btn-success">+ Tambah Kategori</a></div>
<div class="card">
    <div class="table-container"><table><thead><tr><th>#</th><th>Nama</th><th>Penerangan</th><th>Status</th><th>Tindakan</th></tr></thead>
            <tbody>@foreach($categories as $c)<tr><td>{{ $c->id }}</td><td>{{ $c->name }}</td><td>{{ $c->description ?? '-' }}</td><td>{!! $c->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>' !!}</td>
                    <td class="flex gap-2"><a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-primary">Kemaskini</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Padam kategori ini?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger">Padam</button></form></td></tr>@endforeach</tbody></table></div>
    <div class="pagination">{{ $categories->links() }}</div>
</div>
@endsection
