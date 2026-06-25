@extends('layouts.user')
@section('title', 'Maklumat Permohonan')
@section('user-content')
<div class="page-header"><h1>Permohonan {{ $application->application_no }}</h1></div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="card">
    <p><strong>Status:</strong>
        @if($application->status == 'menunggu')<span class="badge badge-warning">{{ ucfirst($application->status) }}</span>
        @elseif($application->status == 'diluluskan')<span class="badge badge-success">{{ ucfirst($application->status) }}</span>
        @else<span class="badge badge-danger">{{ ucfirst($application->status) }}</span>@endif
    </p>
    <p><strong>Daerah:</strong> {{ $application->district->name ?? '-' }}</p>
    <p><strong>Tarikh Mula:</strong> {{ $application->start_date->format('d/m/Y') }}</p>
    <p><strong>Tarikh Akhir:</strong> {{ $application->end_date->format('d/m/Y') }}</p>
    <p><strong>Tujuan:</strong> {{ $application->purpose }}</p>
</div>

<div class="card mt-3">
    <h3>Barang Dimohon</h3>
    <table class="table">
        <thead><tr><th>Barang</th><th>Kuantiti</th></tr></thead>
        <tbody>
        @foreach($application->items as $appItem)
            <tr>
                <td>{{ $appItem->item->name ?? 'Barang telah dipadam' }}</td>
                <td>{{ $appItem->quantity_requested }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@if($application->status == 'ditolak' && $application->rejection_reason)
<div class="card mt-3">
    <h3>Sebab Penolakan</h3>
    <p>{{ $application->rejection_reason }}</p>
</div>
@endif

<a href="{{ route('user.loan-applications.index') }}" class="btn btn-secondary mt-3">← Senarai Permohonan</a>
@endsection
