@extends('layouts.admin')
@section('title', 'Pemulangan Pinjaman')
@section('admin-content')
<div class="page-header">
    <h1>Pemulangan Pinjaman #{{ $loan->loan_no }}</h1>
    <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
@if($errors->any())
<div class="alert alert-error">
    <ul style="margin:0; padding-left:18px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif

<div class="card mb-3">
    <table>
        <tr><td style="width:140px; font-weight:600;">Peminjam</td><td>{{ $loan->user->name ?? '-' }}</td></tr>
        <tr><td style="font-weight:600;">Daerah</td><td>{{ $loan->district->name ?? '-' }}</td></tr>
        <tr><td style="font-weight:600;">Tempoh</td><td>{{ $loan->start_date->format('d/m/Y') }} - {{ $loan->end_date->format('d/m/Y') }}</td></tr>
    </table>
</div>

<div class="card">
    <h3 style="margin-bottom:16px;">Barang Dipinjam</h3>
    <p class="text-gray" style="margin-bottom:16px;">Masukkan kuantiti yang dipulangkan dan pilih keadaan barang selepas pemulangan. Biarkan 0 untuk barang yang belum dipulang.</p>
    <form method="POST" action="{{ route('admin.loans.return', $loan) }}">
        @csrf @method('PUT')
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr><th>Barang</th><th>Dipinjam</th><th>Telah Dipulang</th><th>Baki</th><th>Kuantiti Pulang</th><th>Keadaan Selepas</th></tr>
                </thead>
                <tbody>
                @foreach($loan->items as $li)
                    @php $baki = $li->quantity_loaned - $li->quantity_returned; @endphp
                    <tr>
                        <td>{{ $li->item->name ?? '-' }}</td>
                        <td>{{ $li->quantity_loaned }}</td>
                        <td>{{ $li->quantity_returned }}</td>
                        <td>{{ $baki }}</td>
                        <td>
                            <input type="number" name="returns[{{ $li->id }}][quantity]" value="0" min="0" max="{{ $baki }}"
                                class="form-control" style="width:90px;" {{ $baki <= 0 ? 'readonly' : '' }}>
                        </td>
                        <td>
                            <select name="returns[{{ $li->id }}][condition]" class="form-control" style="width:130px;">
                                @foreach(['baik' => 'Baik', 'rosak' => 'Rosak', 'service' => 'Service'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($li->condition_after ?? $li->condition_before) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">
            <button type="submit" class="btn btn-success" onclick="return confirm('Sahkan pemulangan barang ini?')">✅ Sahkan Pulang</button>
        </div>
    </form>
</div>
@endsection
