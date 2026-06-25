@extends('layouts.user')
@section('title', 'Permohonan Saya')
@section('user-content')
<div class="page-header">
    <h1>Permohonan Saya</h1>
    <a href="{{ route('user.loan-applications.create') }}" class="btn btn-success">+ Mohon Pinjaman</a>
</div>

<div class="card">
    @if($applications->isNotEmpty())
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>No. Permohonan</th><th>Tarikh Pinjaman</th><th>Bil. Barang</th><th>Tarikh Mohon</th><th>Status</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
            @foreach($applications as $app)
                <tr>
                    <td>{{ $app->application_no }}</td>
                    <td>{{ $app->start_date->format('d/m/Y') }} - {{ $app->end_date->format('d/m/Y') }}</td>
                    <td>{{ $app->items_count }} item</td>
                    <td>{{ $app->created_at->format('d/m/Y') }}</td>
                    <td>
                        @switch($app->status)
                            @case('menunggu') <span class="badge badge-warning">Menunggu</span> @break
                            @case('diluluskan') <span class="badge badge-success">Diluluskan</span> @break
                            @case('ditolak') <span class="badge badge-danger">Ditolak</span> @break
                            @case('dibatalkan') <span class="badge badge-secondary">Dibatalkan</span> @break
                            @default <span class="badge badge-secondary">{{ ucfirst($app->status) }}</span>
                        @endswitch
                    </td>
                    <td>
                        <a href="{{ route('user.loan-applications.show', $app->id) }}" class="btn btn-sm btn-primary">Lihat Detail</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $applications->links() }}</div>
    @else
    <div class="empty-state">
        <span class="emoji">📭</span>
        Belum ada permohonan pinjaman.
        <div style="margin-top:12px;"><a href="{{ route('user.loan-applications.create') }}" class="btn btn-success">Buat Permohonan</a></div>
    </div>
    @endif
</div>
@endsection
