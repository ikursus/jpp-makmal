@extends('layouts.user')
@section('title', 'Pinjaman Saya')
@section('user-content')
<div class="page-header"><h1>Pinjaman Saya</h1></div>

<div class="card">
    @if($loans->isNotEmpty())
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>No. Pinjaman</th><th>Tarikh</th><th>Bil. Barang</th><th>Tarikh Pulang</th><th>Status</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
            @foreach($loans as $loan)
                <tr>
                    <td>{{ $loan->loan_no }}</td>
                    <td>{{ $loan->start_date->format('d/m/Y') }} - {{ $loan->end_date->format('d/m/Y') }}</td>
                    <td>{{ $loan->items_count }} item</td>
                    <td>{{ $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y') : '-' }}</td>
                    <td>
                        @switch($loan->status)
                            @case('aktif') <span class="badge badge-info">Aktif</span> @break
                            @case('terlewat') <span class="badge badge-danger">Terlewat</span> @break
                            @case('dipulangkan') <span class="badge badge-success">Dipulangkan</span> @break
                            @default <span class="badge badge-secondary">{{ ucfirst($loan->status) }}</span>
                        @endswitch
                    </td>
                    <td><a href="{{ route('user.loans.show', $loan->id) }}" class="btn btn-sm btn-primary">Lihat Detail</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $loans->links() }}</div>
    @else
    <div class="empty-state"><span class="emoji">📦</span>Tiada rekod pinjaman lagi.</div>
    @endif
</div>
@endsection
