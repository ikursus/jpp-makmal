@extends('layouts.user')
@section('title', 'Mohon Pinjaman')
@section('user-content')
<div class="page-header">
    <h1>Mohon Pinjaman Barang</h1>
    <a href="{{ route('user.inventory') }}" class="btn btn-secondary">← Senarai Inventori</a>
</div>

@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())
<div class="alert alert-danger">
    <ul style="margin:0; padding-left:18px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('user.loan-applications.store') }}">

    @csrf
    {{ csrf_field() }}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="card">
        <div class="form-row">
            <div class="form-group"><label>Tarikh Mula</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required></div>
            <div class="form-group"><label>Tarikh Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required></div>
        </div>
        <div class="form-group"><label>Tujuan Pinjaman</label>
            <textarea name="purpose" class="form-control" rows="3" required minlength="10" placeholder="Nyatakan tujuan pinjaman (minimum 10 aksara)">{{ old('purpose') }}</textarea></div>
    </div>

    <div class="card">
        <div class="flex-between mb-3">
            <h3 class="section-title" style="margin:0;">Pilih Barang</h3>
            <input type="text" id="item-search" class="form-control" placeholder="🔍 Cari barang..." style="max-width:260px;">
        </div>

        @if($items->isEmpty())
            <div class="empty-state"><span class="emoji">📦</span>Tiada barang tersedia untuk dipinjam buat masa ini.</div>
        @else
        <p class="text-gray text-sm mb-3">Tandakan barang atau masukkan kuantiti yang ingin dipinjam.</p>
        <div class="table-container">
            <table class="table" id="items-table">
                <thead>
                    <tr><th style="width:60px;">Pilih</th><th>Barang</th><th>Kategori</th><th>Stok Tersedia</th><th style="width:120px;">Kuantiti</th></tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    @php $isPre = $preselectId === $item->id; @endphp
                    <tr class="item-row {{ $isPre ? 'selected' : '' }}" data-name="{{ strtolower($item->name) }}">
                        <td><input type="checkbox" class="item-check" {{ $isPre ? 'checked' : '' }}></td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td>{{ $item->available_quantity }}</td>
                        <td>
                            <input type="number" name="items[{{ $item->id }}]" class="form-control item-qty"
                                min="0" max="{{ $item->available_quantity }}"
                                value="{{ old('items.' . $item->id, $isPre ? 1 : 0) }}" style="width:90px;">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <button type="submit" class="btn btn-success">Hantar Permohonan</button>
</form>

<script>
(function () {
    // Quantity is the source of truth; the checkbox is a convenience that syncs with it.
    document.querySelectorAll('.item-row').forEach(function (row) {
        var check = row.querySelector('.item-check');
        var qty = row.querySelector('.item-qty');
        if (!check || !qty) return;

        function reflect() { row.classList.toggle('selected', (parseInt(qty.value, 10) || 0) > 0); }

        check.addEventListener('change', function () {
            if (check.checked && (parseInt(qty.value, 10) || 0) < 1) { qty.value = 1; }
            else if (!check.checked) { qty.value = 0; }
            reflect();
        });
        qty.addEventListener('input', function () {
            check.checked = (parseInt(qty.value, 10) || 0) > 0;
            reflect();
        });
        reflect();
    });

    var search = document.getElementById('item-search');
    if (search) {
        search.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('#items-table tbody .item-row').forEach(function (row) {
                row.style.display = row.dataset.name.indexOf(q) > -1 ? '' : 'none';
            });
        });
    }
})();
</script>
@endsection
