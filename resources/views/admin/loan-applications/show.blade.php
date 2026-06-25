@extends('layouts.admin')
@section('title', 'Detail Permohonan')
@section('admin-content')
<div class="page-header">
    <h1>Permohonan #{{ $loanApplication->application_no }}</h1>
    <a href="{{ route('admin.loan-applications.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div class="card">
    <div style="margin-bottom: 16px;">
        Status:
        @switch($loanApplication->status)
            @case('menunggu') <span class="badge badge-warning" style="font-size:16px; padding:8px 16px;">Menunggu Kelulusan</span> @break
            @case('diluluskan') <span class="badge badge-success" style="font-size:16px; padding:8px 16px;">Diluluskan</span> @break
            @case('ditolak') <span class="badge badge-danger" style="font-size:16px; padding:8px 16px;">Ditolak</span> @break
            @case('dipinjam') <span class="badge badge-info" style="font-size:16px; padding:8px 16px;">Dipinjam</span> @break
            @case('dikembalikan') <span class="badge badge-secondary" style="font-size:16px; padding:8px 16px;">Dikembalikan</span> @break
        @endswitch
    </div>

    <div class="grid grid-2">
        <div>
            <h3>Maklumat Pemohon</h3>
            <table>
                <tr><td style="width:120px; font-weight:600;">Nama</td><td>{{ $loanApplication->user->name }}</td></tr>
                <tr><td style="font-weight:600;">Daerah</td><td>{{ $loanApplication->district->name }}</td></tr>
                <tr><td style="font-weight:600;">Emel</td><td>{{ $loanApplication->user->email }}</td></tr>
            </table>
        </div>
        <div>
            <h3>Maklumat Pinjaman</h3>
            <table>
                <tr><td style="width:120px; font-weight:600;">Tarikh</td><td>{{ $loanApplication->start_date->format('d/m/Y') }} - {{ $loanApplication->end_date->format('d/m/Y') }}</td></tr>
                <tr><td style="font-weight:600;">Tujuan</td><td>{{ $loanApplication->purpose }}</td></tr>
                @if($loanApplication->approvedBy)
                <tr><td style="font-weight:600;">Dilulus oleh</td><td>{{ $loanApplication->approvedBy->name }}</td></tr>
                @endif
                @if($loanApplication->rejection_reason)
                <tr><td style="font-weight:600;">Sebab Ditolak</td><td style="color:#ef4444;">{{ $loanApplication->rejection_reason }}</td></tr>
                @endif
            </table>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom:16px;">Barang Dipohon</h3>
    <div class="table-container">
        <table>
            <thead><tr><th>#</th><th>Barang</th><th>Kuantiti</th><th>Stok Ada</th></tr></thead>
            <tbody>
                @foreach($loanApplication->items as $idx => $appItem)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $appItem->item->name }}</td>
                    <td>{{ $appItem->quantity_requested }}</td>
                    <td>{{ $appItem->item->available_quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($loanApplication->status === 'menunggu')
<div class="card">
    <h3 style="margin-bottom:16px;">Tindakan</h3>
    <div class="flex gap-4">
        <form method="POST" action="{{ route('admin.loan-applications.approve', $loanApplication) }}" onsubmit="return confirm('Luluskan permohonan ini? Stok akan dikurangkan secara automatik.')">
            @csrf @method('PUT')
            <button type="submit" class="btn btn-success">✅ Luluskan</button>
        </form>
        <form method="POST" action="{{ route('admin.loan-applications.reject', $loanApplication) }}" onsubmit="return confirm('Tolak permohonan ini?')">
            @csrf @method('PUT')
            <div class="flex gap-2" style="align-items:flex-start;">
                <input type="text" name="rejection_reason" class="form-control" placeholder="Sebab penolakan..." required style="width:300px;">
                <button type="submit" class="btn btn-danger">❌ Tolak</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
