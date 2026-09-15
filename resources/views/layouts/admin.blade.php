<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — SehatEdukasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #5EE9C7;
            --primary-dark: #3dc9a7;
            --primary-light: #edfdf9;
            --accent: #5EE9C7;
            --sidebar-bg: #0a2a26;
            --sidebar-hover: #0d3530;
            --sidebar-active: #5EE9C7;
            --sidebar-text: #94a3a0;
            --sidebar-text-active: #ffffff;
            --text-dark: #111827;
            --text-mid: #374151;
            --text-muted: #6b7280;
            --bg: #f4f7f6;
            --surface: #ffffff;
            --border: #e5e7eb;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.04);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            font-size: 15px;
        }

        .sidebar {
            width: 240px;
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            padding: 0 12px 24px;
        }

        .sidebar-brand {
            padding: 20px 8px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 12px;
        }

        .sidebar-brand-logo {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: white; font-size: 15px;
            margin-bottom: 12px;
        }

        .sidebar-brand-name { font-size: 15px; font-weight: 700; color: white; }
        .sidebar-brand-sub { font-size: 12px; color: var(--sidebar-text); margin-top: 2px; }

        .sidebar-section-label {
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.25);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 12px 12px 6px;
        }

        .sidebar-nav { flex: 1; }

        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: 14px; font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.15s;
        }
        .sidebar-link:hover { background: var(--sidebar-hover); color: white; }
        .sidebar-link.active { background: var(--sidebar-active); color: #111827; }
        .sidebar-link svg { width: 17px; height: 17px; flex-shrink: 0; }

        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 12px;
        }

        .sidebar-logout {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            font-size: 14px; font-weight: 500;
            background: none; border: none;
            cursor: pointer; font-family: inherit;
            transition: all 0.15s;
        }
        .sidebar-logout:hover { background: rgba(239,68,68,0.1); color: #f87171; }

        .admin-content { margin-left: 240px; flex: 1; padding: 32px; min-height: 100vh; }

        .page-header { margin-bottom: 28px; display: flex; align-items: flex-start; justify-content: space-between; }
        .page-title { font-size: 24px; font-weight: 800; color: var(--text-dark); }
        .page-sub { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

        .card { background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); padding: 20px; box-shadow: var(--shadow); }
        .stat-icon { width: 40px; height: 40px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-icon-teal { background: #edfdf9; color: var(--primary); }
        .stat-icon-blue { background: #eff6ff; color: #3b82f6; }
        .stat-icon-purple { background: #f5f3ff; color: #8b5cf6; }
        .stat-icon-amber { background: #fffbeb; color: #f59e0b; }
        .stat-number { font-size: 28px; font-weight: 800; color: var(--text-dark); line-height: 1; }
        .stat-label { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        tbody td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid var(--border); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--bg); }

        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 18px; border-radius: var(--radius-sm); font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
        .btn svg { width: 15px; height: 15px; }
        .btn-primary { background: var(--primary); color: #111827; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; color: var(--text-dark); border: 1px solid var(--border); }
        .btn-outline:hover { background: var(--bg); }
        .btn-danger { background: #fef2f2; color: var(--danger); border: 1px solid #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; font-size: 14px; color: var(--text-dark); background: var(--bg); outline: none; transition: border-color 0.15s; }
        .form-input:focus { border-color: var(--primary); background: white; }
        .form-hint { font-size: 12px; color: var(--text-muted); margin-top: 5px; }

        .alert { padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; margin-bottom: 20px; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 100px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #ecfdf5; color: #065f46; }
        .badge-muted { background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }

        .dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; }
        .dot-green { background: var(--success); }
        .dot-gray { background: var(--border); }

        .search-wrap { position: relative; margin-bottom: 20px; }
        .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 16px; height: 16px; }
        .search-input { width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; font-size: 14px; outline: none; transition: border-color 0.15s; background: var(--bg); }
        .search-input:focus { border-color: var(--primary); background: white; }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-logo">S</div>
            <div class="sidebar-brand-name">SehatEdukasi</div>
            <div class="sidebar-brand-sub">Admin Panel</div>
        </div>

        <nav class="sidebar-nav">

            {{-- DATA --}}
            <div class="sidebar-section-label">Data</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.respondents') }}" class="sidebar-link {{ request()->routeIs('admin.respondents') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Responden
            </a>
            <a href="{{ route('admin.hasil-test') }}" class="sidebar-link {{ request()->routeIs('admin.hasil-test') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Hasil Test
            </a>

            {{-- KONTEN --}}
            <div class="sidebar-section-label">Konten</div>
            <a href="{{ route('admin.questions.index') }}" class="sidebar-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kelola Soal
            </a>
            <a href="{{ route('admin.materials.index') }}" class="sidebar-link {{ request()->routeIs('admin.materials.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Kelola Materi
            </a>
            <a href="{{ route('admin.locations.index') }}" class="sidebar-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Kelola Lokasi
            </a>

        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>