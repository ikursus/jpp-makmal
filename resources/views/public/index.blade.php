@extends('layouts.app')

@section('title', 'Selamat Datang - JPP Makmal')

@section('content')
<div class="landing">
    <h1>🏛️ Sistem Pengurusan Barangan Makmal</h1>
    <p>Jabatan Perkhidmatan Pembetungan Sabah (JPP) — Mengurus inventori dan permohonan pinjaman barangan makmal antara Ibu Pejabat dan Pejabat Daerah.</p>
    <a href="{{ route('login') }}" class="btn btn-success" style="padding: 14px 40px; font-size: 18px;">Log Masuk</a>

    <div class="features">
        <div class="feature">
            <div class="icon">📦</div>
            <h3>Urus Inventori</h3>
            <p>Pengurusan stok barangan makmal secara berpusat</p>
        </div>
        <div class="feature">
            <div class="icon">📋</div>
            <h3>Pinjam Barang</h3>
            <p>Permohonan pinjaman dalam talian</p>
        </div>
        <div class="feature">
            <div class="icon">✅</div>
            <h3>Status Real-time</h3>
            <p>Semak status permohonan 24/7</p>
        </div>
        <div class="feature">
            <div class="icon">📊</div>
            <h3>Laporan</h3>
            <p>Analitik dan laporan pengurusan</p>
        </div>
    </div>

    <p style="margin-top: 60px; font-size: 12px; opacity: 0.5;">© <?php echo config('jpp-config.general.site_year') ?> <?php echo config('jpp-config.general.site_copyright'); ?></p>
</div>

@php

    $welcome = '<script>alert("Selamat Datang");</script>'

@endphp


{{ $welcome }}

@endsection
