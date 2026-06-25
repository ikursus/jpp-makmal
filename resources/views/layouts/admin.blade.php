@extends('layouts.app')

@section('content')
<div class="admin-layout">
    <div class="sidebar">
        <div class="logo">
            <h2>🏛️ JPP Makmal</h2>
            <p>Sistem Pengurusan Barangan</p>
        </div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                <span class="icon">🏠</span> Dashboard
            </a>
            @can('manage-districts')
            <a href="{{ route('admin.districts.index') }}" class="{{ request()->routeIs('admin.districts*') ? 'active' : '' }}">
                <span class="icon">🌍</span> Daerah
            </a>
            @endcan
            @can('manage-categories')
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <span class="icon">📂</span> Kategori
            </a>
            @endcan
            @can('manage-items')
            <a href="{{ route('admin.items.index') }}" class="{{ request()->routeIs('admin.items*') ? 'active' : '' }}">
                <span class="icon">📦</span> Inventori
            </a>
            @endcan
            @can('manage-storage-locations')
            <a href="{{ route('admin.storage-locations.index') }}" class="{{ request()->routeIs('admin.storage-locations*') ? 'active' : '' }}">
                <span class="icon">📍</span> Lokasi
            </a>
            @endcan
            @can('manage-users')
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <span class="icon">👥</span> Pengguna
            </a>
            @endcan
            @can('manage-loan-applications')
            <a href="{{ route('admin.loan-applications.index') }}" class="{{ request()->routeIs('admin.loan-applications*') ? 'active' : '' }}">
                <span class="icon">📋</span> Permohonan
            </a>
            <a href="{{ route('admin.loans.index') }}" class="{{ request()->routeIs('admin.loans*') ? 'active' : '' }}">
                <span class="icon">📝</span> Pinjaman
            </a>
            @endcan
            @can('view-reports')
            <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <span class="icon">📊</span> Laporan
            </a>
            <a href="{{ route('admin.statistics') }}" class="{{ request()->routeIs('admin.statistics*') ? 'active' : '' }}">
                <span class="icon">📈</span> Statistik
            </a>
            @endcan
            <hr style="border-color: rgba(255,255,255,0.1); margin: 16px 24px;">
            <a href="{{ route('user.profile.edit') }}">
                <span class="icon">👤</span> Profil
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="icon">🚪</span> Log Keluar
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </nav>
    </div>
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button type="button" class="menu-toggle" aria-label="Togol menu">☰</button>
            </div>
            <div class="user-info">
                <span>{{ auth()->user()->name }}</span>
                <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            </div>
        </div>
        <div class="content">
            @yield('admin-content')
        </div>
    </div>
</div>
@endsection
