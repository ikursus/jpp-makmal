@extends('layouts.user')
@section('title', 'Detail Pinjaman')
@section('user-content')
<div class="page-header">
    <h1>Pinjaman #{{ $loan->loan_no }}</h1>
    <a href="{{ route('user.loans.index') }}" class="btn btn-secondary">← Senarai Pinjaman</a>
</div>

<div class="card">
    <p><strong>Status:</strong>
        @switch($loan->status)
            @case('aktif') <span class="badge badge-info">Aktif</span> @break
            @case('terlewat') <span class="badge badge-danger">Terlewat</span> @break
            @case('dipulangkan') <span class="badge badge-success">Dipulangkan</span> @break
            @default <span class="badge badge-secondary">{{ ucfirst($loan->status) }}</span>
        @endswitch
    </p>
    <p><strong>Daerah:</strong> {{ $loan->district->name ?? '-' }}</p>
    <p><strong>Tempoh Pinjaman:</strong> {{ $loan->start_date->format('d/m/Y') }} - {{ $loan->end_date->format('d/m/Y') }}</p>
    @if($loan->actual_return_date)
    <p><strong>Tarikh Pulang Penuh:</strong> {{ $loan->actual_return_date->format('d/m/Y') }}</p>
    @endif
</div>

<div class="card">
    <h3 class="section-title">Rekod Barang &amp; Pemulangan</h3>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>Barang</th><th>Dipinjam</th><th>Dipulang</th><th>Baki</th><th>Keadaan Selepas</th><th>Tarikh Pulang</th></tr>
            </thead>
            <tbody>
            @foreach($loan->items as $li)
                @php $baki = $li->quantity_loaned - $li->quantity_returned; @endphp
                <tr>
                    <td>{{ $li->item->name ?? 'Barang telah dipadam' }}</td>
                    <td>{{ $li->quantity_loaned }}</td>
                    <td>{{ $li->quantity_returned }}</td>
                    <td>
                        @if($baki > 0)
                            <span class="badge badge-warning">{{ $baki }}</span>
                        @else
                            <span class="badge badge-success">0</span>
                        @endif
                    </td>
                    <td>{{ $li->condition_after ? ucfirst($li->condition_after) : '-' }}</td>
                    <td>{{ $li->returned_at ? $li->returned_at->format('d/m/Y') : '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p class="text-gray text-sm mt-3">Baki menunjukkan kuantiti yang masih belum dipulangkan.</p>
</div>
@endsection
