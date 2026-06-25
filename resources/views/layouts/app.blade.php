<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengurusan Barangan Makmal JPP')</title>
    @livewireStyles
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite('resources/css/app.css')
    @endif
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f3f4f6; color: #1f2937; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; font-size: 14px; transition: all 0.2s; }
        .btn-primary { background: #003366; color: white; }
        .btn-primary:hover { background: #002244; }
        .btn-success { background: #006633; color: white; }
        .btn-success:hover { background: #004d26; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background: #d1d5db; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid { display: grid; gap: 20px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .stat-card { text-align: center; padding: 20px; }
        .stat-card .icon { font-size: 32px; margin-bottom: 8px; }
        .stat-card .value { font-size: 28px; font-weight: 700; color: #003366; }
        .stat-card .label { font-size: 14px; color: #6b7280; margin-top: 4px; }

        /* Dashboard ---------------------------------------------------------- */
        .welcome-hero { background: linear-gradient(135deg, #003366 0%, #006633 100%); color: white; padding: 28px 32px; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 8px 24px rgba(0,51,102,0.25); }
        .welcome-hero h1 { font-size: 24px; font-weight: 700; margin-bottom: 6px; }
        .welcome-hero p { opacity: 0.85; font-size: 14px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .metric { background: white; border-radius: 14px; padding: 20px 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 16px; border-left: 4px solid #003366; transition: transform 0.15s, box-shadow 0.15s; }
        .metric:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(0,0,0,0.1); }
        .metric .metric-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 26px; background: #eef2f7; flex-shrink: 0; }
        .metric .metric-value { font-size: 28px; font-weight: 700; color: #1f2937; line-height: 1; }
        .metric .metric-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .metric.accent-warning { border-left-color: #f59e0b; } .metric.accent-warning .metric-icon { background: #fef3c7; }
        .metric.accent-success { border-left-color: #006633; } .metric.accent-success .metric-icon { background: #d1fae5; }
        .metric.accent-info { border-left-color: #3b82f6; } .metric.accent-info .metric-icon { background: #dbeafe; }
        .section-title { font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 14px; }
        .empty-state { text-align: center; padding: 32px; color: #9ca3af; }
        .empty-state .emoji { font-size: 36px; display: block; margin-bottom: 8px; }

        .table-container, .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; font-size: 13px; text-transform: uppercase; }
        td { font-size: 14px; }
        tr:hover { background: #f9fafb; }
        .btn-sort { background: none; border: none; padding: 0; font: inherit; font-size: 13px; font-weight: 600; color: #374151; text-transform: uppercase; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
        .btn-sort:hover { color: #003366; }
        .sort-indicator { font-size: 14px; color: #003366; }
        .table-toolbar .form-control { height: 40px; }
        .item-row.selected { background: #ecfdf5; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: #e5e7eb; color: #374151; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px; color: #374151; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: #003366; box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }
        .form-control.error { border-color: #ef4444; }
        .form-row { display: flex; gap: 16px; flex-wrap: wrap; }
        .form-row .form-group { flex: 1; min-width: 180px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger, .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        /* Pagination --------------------------------------------------------- */
        .pagination { margin-top: 24px; }
        .pagination nav { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .pagination .flex.justify-between { display: flex; width: 100%; align-items: center; justify-content: space-between; }
        
        /* Specific fixes for default Laravel Tailwind pagination */
        .pagination nav span.relative, 
        .pagination nav a.relative {
            padding: 8px 14px;
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
        }

        .pagination nav a.relative:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .pagination nav span.relative[aria-current="page"] span {
            background: #003366;
            color: white;
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pagination nav span.relative[aria-current="page"] {
            background: #003366;
            color: white;
            border-color: #003366;
            position: relative;
        }

        /* Layout / sidebar --------------------------------------------------- */
        .sidebar { width: 260px; background: #003366; color: white; height: 100vh; position: fixed; top: 0; left: 0; z-index: 50; transition: transform 0.25s ease; display: flex; flex-direction: column; }
        .sidebar .logo { padding: 24px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
        .sidebar .logo h2 { font-size: 16px; font-weight: 700; }
        .sidebar .logo p { font-size: 11px; opacity: 0.7; margin-top: 4px; }
        .sidebar nav { padding: 16px 0; flex: 1; overflow-y: auto; }
        .sidebar nav::-webkit-scrollbar { width: 6px; }
        .sidebar nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 3px; }
        .sidebar nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
        .sidebar nav { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.25) transparent; }
        .sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; transition: all 0.2s; }
        .sidebar nav a:hover, .sidebar nav a.active { background: rgba(255,255,255,0.1); color: white; border-left: 3px solid #00a651; }
        .sidebar nav a .icon { font-size: 18px; width: 24px; text-align: center; }
        .main-content { margin-left: 260px; transition: margin-left 0.25s ease; }
        .topbar { background: white; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 30; }
        .topbar .topbar-left { display: flex; align-items: center; gap: 12px; }
        .menu-toggle { background: none; border: none; font-size: 22px; line-height: 1; cursor: pointer; color: #003366; padding: 6px 10px; border-radius: 8px; display: inline-flex; align-items: center; }
        .menu-toggle:hover { background: #f3f4f6; }
        .topbar .user-info { display: flex; align-items: center; gap: 12px; }
        .topbar .user-info .avatar { width: 36px; height: 36px; border-radius: 50%; background: #003366; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
        .content { padding: 32px; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 45; }

        /* User dropdown */
        .user-dropdown { position: relative; display: inline-flex; align-items: center; }
        .user-dropdown .user-btn { background: none; border: none; color: #1f2937; font: inherit; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 10px; padding: 4px 8px; border-radius: 8px; transition: background 0.2s; }
        .user-dropdown .user-btn:hover { background: #f3f4f6; }
        .user-dropdown .avatar { width: 36px; height: 36px; border-radius: 50%; background: #003366; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .user-dropdown .user-menu { display: none; position: absolute; right: 0; top: calc(100% + 8px); background: white; border: 1px solid #e5e7eb; border-radius: 10px; min-width: 160px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 200; padding: 6px 0; list-style: none; margin: 0; }
        .user-dropdown .user-menu.show { display: block; }
        .user-dropdown .user-menu li a { display: block; padding: 9px 16px; color: #374151; text-decoration: none; font-size: 14px; transition: background 0.15s; }
        .user-dropdown .user-menu li a:hover { background: #f3f4f6; color: #003366; }
        body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
        body.sidebar-collapsed .main-content { margin-left: 0; }

        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 12px; flex-wrap: wrap; }
        .page-header h1 { font-size: 24px; font-weight: 700; color: #1f2937; }
        .flex { display: flex; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .gap-2 { gap: 8px; }
        .gap-4 { gap: 16px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-3 { margin-top: 12px; }
        .mt-4 { margin-top: 16px; }
        .text-center { text-align: center; }
        .text-sm { font-size: 14px; }
        .text-xs { font-size: 12px; }
        .text-gray { color: #6b7280; }
        .w-full { width: 100%; }
        .inline-block { display: inline-block; }
        .search-box { display: flex; gap: 8px; margin-bottom: 16px; }
        .search-box input { flex: 1; }
        .export-buttons { display: flex; gap: 8px; }
        .toast { position: fixed; top: 20px; right: 20px; z-index: 1000; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .login-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #003366 0%, #004d80 100%); }
        .login-card { background: white; padding: 40px; border-radius: 16px; width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .login-card h2 { text-align: center; color: #003366; margin-bottom: 24px; font-size: 24px; }
        .login-card .logo-text { text-align: center; margin-bottom: 24px; }
        .login-card .logo-text h3 { font-size: 14px; color: #6b7280; }
        .login-card .btn { width: 100%; padding: 12px; font-size: 16px; }
        .login-card .form-group { margin-bottom: 20px; }
        .login-card .form-group label { font-size: 14px; }
        .checkbox-group { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .checkbox-group label { font-size: 14px; color: #6b7280; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-open .sidebar-overlay { display: block; }
            body.sidebar-collapsed .sidebar { transform: translateX(-100%); }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .content { padding: 16px; }
            .topbar { padding: 12px 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    @livewireScripts
    <div class="sidebar-overlay"></div>

    @if(session('success'))
    <div class="toast">
        <div class="alert alert-success">{{ session('success') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div class="toast">
        <div class="alert alert-danger">{{ session('error') }}</div>
    </div>
    @endif
    @if(session('info'))
    <div class="toast">
        <div class="alert alert-info">{{ session('info') }}</div>
    </div>
    @endif

    <script>
        // Sidebar toggle (collapse on desktop, slide-in drawer on mobile)
        (function () {
            var toggle = document.querySelector('.menu-toggle');
            var overlay = document.querySelector('.sidebar-overlay');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        document.body.classList.toggle('sidebar-open');
                    } else {
                        document.body.classList.toggle('sidebar-collapsed');
                    }
                });
            }
            if (overlay) {
                overlay.addEventListener('click', function () {
                    document.body.classList.remove('sidebar-open');
                });
            }
        })();


        // Auto-dismiss toasts
        setTimeout(function () {
            document.querySelectorAll('.toast').forEach(function (el) { el.remove(); });
        }, 5000);

        // User dropdown toggle
        function toggleUserMenu(e) {
            if (e) { e.stopPropagation(); }
            var menu = document.getElementById('user-menu');
            if (menu) {
                menu.classList.toggle('show');
            }
        }
        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            var dropdown = document.querySelector('.user-dropdown');
            var menu = document.getElementById('user-menu');
            if (menu && dropdown && !dropdown.contains(e.target)) {
                menu.classList.remove('show');
            }
        });

    </script>
    @stack('scripts')
</body>
</html>
