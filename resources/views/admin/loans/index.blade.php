@extends('layouts.admin')
@section('title', 'Rekod Pinjaman')
@section('admin-content')
<div class="page-header"><h1>Rekod Pinjaman</h1></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
<div class="card">
<table class="table">
    <thead><tr><th>No. Pinjaman</th><th>Peminjam</th><th>Daerah</th><th>Barang</th><th>Tarikh Mula</th><th>Tarikh Akhir</th><th>Status</th><th>Tindakan</th></tr></thead>
    <tbody>
    @foreach($loans as $loan)
        <tr>
            <td>{{ $loan->loan_no }}</td>
            <td>{{ $loan->user->name ?? '-' }}</td>
            <td>{{ $loan->district->name ?? '-' }}</td>
            <td>{{ $loan->items->count() }} item</td>
            <td>{{ $loan->start_date->format('d/m/Y') }}</td>
            <td>{{ $loan->end_date->format('d/m/Y') }}</td>
            <td>
                @switch($loan->status)
                    @case('aktif') <span class="badge badge-success">Aktif</span> @break
                    @case('terlewat') <span class="badge badge-danger">Terlewat</span> @break
                    @case('dipulangkan') <span class="badge badge-secondary">Dipulangkan</span> @break
                    @default <span class="badge badge-secondary">{{ ucfirst($loan->status) }}</span>
                @endswitch
            </td>
            <td>
                @if(in_array($loan->status, ['aktif', 'terlewat']))
                    <a href="{{ route('admin.loans.return.form', $loan) }}" class="btn btn-sm btn-primary">Pulang</a>
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $loans->links() }}
</div>
@endsection
