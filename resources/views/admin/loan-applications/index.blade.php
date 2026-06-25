@extends('layouts.admin')
@section('title', 'Senarai Permohonan Pinjaman')

@section('admin-content')
<div class="page-header">
    <h1>Senarai Permohonan Pinjaman</h1>
</div>

<div class="card">
    <livewire:admin-loan-application-table />
</div>
@endsection
