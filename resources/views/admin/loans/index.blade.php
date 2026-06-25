@extends('layouts.admin')
@section('title', 'Rekod Pinjaman')
@section('admin-content')
<div class="page-header">
    <h1>Rekod Pinjaman</h1>
    <div class="page-actions" style="display: flex; gap: 8px; align-items: flex-start;">
        <div class="export-dropdown" style="position: relative; display: inline-block;">
            <button class="btn btn-success" onclick="toggleExportMenu()" type="button">
                ⬇ Eksport ▾
            </button>
            <div id="exportMenu" class="export-menu" style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; min-width: 260px; background: white; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); padding: 8px 0; margin-top: 4px;">
                <div style="padding: 4px 16px; font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 700;">Format Spreadsheet</div>
                <a href="{{ route('admin.loans.export.xlsx') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">📊 Excel (XLSX)</a>
                <a href="{{ route('admin.loans.export.csv') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">📄 CSV</a>
                <a href="{{ route('admin.loans.export.ods') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">📗 OpenDocument (ODS)</a>
                <hr style="margin: 4px 0; border: 0; border-top: 1px solid #e5e7eb;">
                <div style="padding: 4px 16px; font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 700;">Format Dokumen</div>
                <a href="{{ route('admin.loans.export.pdf') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">📕 PDF (DOMPDF)</a>
                <a href="{{ route('admin.loans.export.tcpdf') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">📕 PDF (TCPDF)</a>
                <a href="{{ route('admin.loans.export.mpdf') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">📕 PDF (MPDF)</a>
                <a href="{{ route('admin.loans.export.html') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">🌐 HTML</a>
                <hr style="margin: 4px 0; border: 0; border-top: 1px solid #e5e7eb;">
                <div style="padding: 4px 16px; font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 700;">Eksport Khas</div>
                <a href="{{ route('admin.loans.export.view') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">🖼 Guna Template View</a>
                <a href="{{ route('admin.loans.export.multiple-sheets') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">📚 Berbilang Sheet (Mengikut Daerah)</a>
                <a href="{{ route('admin.loans.export.stream') }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #1f2937; font-size: 14px;">🌊 Stream (Fail Besar)</a>
                <hr style="margin: 4px 0; border: 0; border-top: 1px solid #e5e7eb;">
                <div style="padding: 4px 16px; font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 700;">Eksport ke Pelayan</div>
                <form action="{{ route('admin.loans.export.store') }}" method="POST" style="padding: 4px 16px;">
                    @csrf
                    <select name="format" style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; margin-bottom: 6px;">
                        <option value="xlsx">XLSX</option>
                        <option value="csv">CSV</option>
                        <option value="ods">ODS</option>
                        <option value="pdf">PDF</option>
                        <option value="html">HTML</option>
                    </select>
                    <button type="submit" style="width: 100%; padding: 6px 12px; background: #e5e7eb; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 13px;">💾 Simpan ke Pelayan</button>
                </form>
                <hr style="margin: 4px 0; border: 0; border-top: 1px solid #e5e7eb;">
                <form action="{{ route('admin.loans.export.queue') }}" method="POST" style="padding: 4px 16px;">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 6px 12px; background: #dbeafe; border: 1px solid #bfdbfe; border-radius: 6px; cursor: pointer; font-size: 13px; color: #1e40af;">⏳ Eksport Beratur (Queue)</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleExportMenu() {
    var menu = document.getElementById('exportMenu');
    menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
}

document.addEventListener('click', function(event) {
    var menu = document.getElementById('exportMenu');
    var button = document.querySelector('.export-dropdown .btn');
    if (menu && button && !button.contains(event.target) && !menu.contains(event.target)) {
        menu.style.display = 'none';
    }
});
</script>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
@if(session('info'))<div class="alert alert-info">{{ session('info') }}</div>@endif

<div class="card">
    @livewire('admin-loan-table')
</div>
@endsection
