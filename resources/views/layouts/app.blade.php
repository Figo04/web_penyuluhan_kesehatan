<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SehatEdukasi') — Platform Penyuluhan Kesehatan Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1a7a5e;
            --primary-dark: #155f49;
            --primary-light: #e8f5f0;
            --primary-mid: #2d9b76;
            --accent: #34c68a;
            --text-dark: #111827;
            --text-mid: #374151;
            --text-muted: #6b7280;
            --text-light: #9ca3af;
            --bg: #f4f7f6;
            --surface: #ffffff;
            --border: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
        }

        /* NAV */
        .nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            height: 60px;
            gap: 8px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 16px;
            color: var(--text-dark);
            text-decoration: none;
            margin-right: 8px;
        }

        .nav-brand-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            flex: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 14px;
            transition: all 0.15s;
        }

        .nav-link:hover { background: var(--bg); color: var(--text-dark); }
        .nav-link.active { background: var(--primary); color: white; }
        .nav-link svg { width: 16px; height: 16px; }

        .nav-logout {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
        }
        .nav-logout:hover { color: var(--danger); background: #fef2f2; }

        /* MAIN CONTENT */
        .main { max-width: 1200px; margin: 0 auto; padding: 32px 24px; }

        /* CARDS */
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        /* BUTTONS */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            width: 100%;
            justify-content: center;
            padding: 14px 20px;
            font-size: 15px;
            border-radius: var(--radius-sm);
        }
        .btn-primary:hover { background: var(--primary-dark); }

        .btn-outline {
            background: transparent;
            color: var(--text-dark);
            border: 1px solid var(--border);
        }
        .btn-outline:hover { background: var(--bg); }

        .btn-sm { padding: 7px 14px; font-size: 13px; }

        /* FORM */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; color: var(--text-dark); }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 15px;
            color: var(--text-dark);
            background: var(--bg);
            transition: border-color 0.15s;
            outline: none;
        }
        .form-input:focus { border-color: var(--primary); background: white; }
        .form-input::placeholder { color: var(--text-light); }
        .form-hint { font-size: 13px; color: var(--text-muted); margin-top: 6px; }
        .form-error { font-size: 13px; color: var(--danger); margin-top: 6px; }

        /* ALERTS */
        .alert { padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; margin-bottom: 20px; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* BADGE */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #ecfdf5; color: #065f46; }
        .badge-muted { background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }
        .badge-primary { background: var(--primary-light); color: var(--primary); }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="nav">
        <div class="nav-inner">
            <a href="{{ route('respondent.home') }}" class="nav-brand">
                <div class="nav-brand-avatar">S</div>
                SehatEdukasi
            </a>
            <div class="nav-links">
                <a href="{{ route('respondent.home') }}" class="nav-link {{ request()->routeIs('respondent.home') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Beranda
                </a>
                <a href="{{ route('respondent.pretest') }}" class="nav-link {{ request()->routeIs('respondent.pretest') || request()->routeIs('respondent.posttest') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Buku Kerja
                </a>
                <a href="{{ route('respondent.material') }}" class="nav-link {{ request()->routeIs('respondent.material') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Materi
                </a>
                <a href="{{ route('respondent.profile') }}" class="nav-link {{ request()->routeIs('respondent.profile') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </a>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-logout">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    <main class="main">
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