@extends('layouts.app')

@section('content')
<div class="admin-layout">
    <div class="sidebar">
        <div class="logo">
            <h2>🏛️ JPP Makmal</h2>
            <p>Sistem Pengurusan Barangan</p>
        </div>
        <nav>
            <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard*') ? 'active' : '' }}">
                <span class="icon">🏠</span> Dashboard
            </a>
            @can('view-inventory')
            <a href="{{ route('user.inventory') }}" class="{{ request()->routeIs('user.inventory*') ? 'active' : '' }}">
                <span class="icon">📦</span> Inventori
            </a>
            @endcan
            @can('create-loan-application')
            <a href="{{ route('user.loan-applications.create') }}" class="{{ request()->routeIs('user.loan-applications.create') ? 'active' : '' }}">
                <span class="icon">📝</span> Permohonan Baru
            </a>
            @endcan
            @can('view-own-applications')
            <a href="{{ route('user.loan-applications.index') }}" class="{{ request()->routeIs('user.loan-applications.index', 'user.loan-applications.show') ? 'active' : '' }}">
                <span class="icon">📋</span> Permohonan Saya
            </a>
            <a href="{{ route('user.loans.index') }}" class="{{ request()->routeIs('user.loans.*') ? 'active' : '' }}">
                <span class="icon">🤝</span> Pinjaman Saya
            </a>
            @endcan
            <hr style="border-color: rgba(255,255,255,0.1); margin: 16px 24px;">
            <a href="{{ route('user.profile.edit') }}">
                <span class="icon">👤</span> Profil
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-user').submit();">
                <span class="icon">🚪</span> Log Keluar
            </a>
            <form id="logout-form-user" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </nav>
    </div>
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button type="button" class="menu-toggle" aria-label="Togol menu">☰</button>
            </div>
            <div class="user-dropdown">
                <button class="user-btn" onclick="toggleUserMenu(event)">
                    <span>{{ auth()->user()->name }} ({{ auth()->user()->district?->code ?? 'HQ' }})</span>
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                </button>
                <ul class="user-menu" id="user-menu">
                    <li><a href="{{ route('user.profile.edit') }}">👤 Profil</a></li>
                    <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-user').submit();">🚪 Log Keluar</a></li>
                </ul>
            </div>
        </div>

        <div class="content">
            @yield('user-content')
        </div>
    </div>
</div>
@endsection
