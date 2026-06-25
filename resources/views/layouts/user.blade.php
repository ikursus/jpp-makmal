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
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 45; }

        /* User dropdown styles */
        .user-dropdown { position: relative; display: inline-block; }
        .user-dropdown button.user-btn { background: none; border: none; color: inherit; font: inherit; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .user-dropdown .user-menu { position: absolute; right: 0; top: 100%; background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px 0; min-width: 150px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: none; z-index: 100; }
        .user-dropdown .user-menu.show { display: block; }
        .user-dropdown .user-menu a { display: block; padding: 6px 16px; color: #374151; text-decoration: none; }
        .user-dropdown .user-menu a:hover { background: #f3f4f6; }margin: 16px 24px;">
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
<div class="user-dropdown">
    <button class="user-btn" onclick="toggleUserMenu()">
        <span>{{ auth()->user()->name }} ({{ auth()->user()->district?->code ?? 'HQ' }})</span>
        <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
    </button>
    <ul class="user-menu" id="user-menu">
        <li><a href="{{ route('user.profile.edit') }}">Profil</a></li>
        <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Keluar</a></li>
    </ul>
</div>
        </div>
        <div class="content">
            @yield('user-content')
        </div>
    </div>
</div>
@endsection
