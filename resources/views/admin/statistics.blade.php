@extends('layouts.admin')
@section('title', 'Statistik')
@section('admin-content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="page-header">
        <h1>Statistik & Analitis</h1>
    </div>
    @livewire('admin-statistics')
@endsection
