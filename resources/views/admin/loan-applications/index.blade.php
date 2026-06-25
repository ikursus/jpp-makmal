@extends('layouts.admin')
@section('title', 'Pengurusan Permohonan')
@section('admin-content')
<div class="page-header"><h1>Pengurusan Permohonan</h1></div>
<div class="card">
    <div class="table-container">
        <table>
            <thead><tr><th>No.</th><th>Pemohon</th><th>Daerah</th><th>Tarikh</th><th>Status</th><th>Tindakan</th></tr></thead>
            <tbody>
                @foreach($applications as $app)
                <tr>
                    <td>{{ $app->application_no }}</td>
                    <td>{{ $app->user->name }}</td>
                    <td>{{ $app->district->name }}</td>
                    <td>{{ $app->start_date->format('d/m/Y') }} - {{ $app->end_date->format('d/m/Y') }}</td>
                    <td>@switch($app->status) @case('menunggu')<span class="badge badge-warning">Menunggu</span>@break @case('diluluskan')<span class="badge badge-success">Diluluskan</span>@break @case('ditolak')<span class="badge badge-danger">Ditolak</span>@break @case('dibatalkan')<span class="badge badge-secondary">Dibatalkan</span>@break @case('dipinjam')<span class="badge badge-info">Dipinjam</span>@break @case('dikembalikan')<span class="badge badge-secondary">Dikembalikan</span>@break @endswitch</td>
                    <td><a href="{{ route('admin.loan-applications.show', $app) }}" class="btn btn-sm btn-primary">Detail</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $applications->links() }}</div>
</div>
@endsection
