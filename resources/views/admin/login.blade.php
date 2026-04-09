<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — SehatEdukasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #5EE9C7;
            --primary-dark: #3dc9a7;
            --border: #e5e7eb;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --text-light: #9ca3af;
            --bg: #f9fafb;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: #f4f7f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
        }

        .logo-icon {
            width: 56px; height: 56px;
            background: white;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
            border: 1.5px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .logo-name {
            font-size: 22px; font-weight: 800;
            color: var(--text-dark); letter-spacing: -0.5px;
        }

        .logo-sub {
            font-size: 13px; color: var(--text-muted); margin-top: 4px;
        }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #ecfdf5; color: #065f46;
            border: 1px solid #a7f3d0;
            font-size: 12px; font-weight: 600;
            padding: 4px 12px; border-radius: 20px;
            margin-top: 10px;
        }

        .badge svg { width: 12px; height: 12px; }

        .card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04);
            border: 1px solid var(--border);
        }

        .card-title {
            font-size: 18px; font-weight: 700;
            color: var(--text-dark); margin-bottom: 4px;
        }

        .card-sub {
            font-size: 13.5px; color: var(--text-muted); margin-bottom: 28px;
        }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block; font-weight: 600;
            font-size: 13.5px; margin-bottom: 7px;
            color: var(--text-dark);
        }

        .form-input {
            width: 100%; padding: 12px 15px;
            border: 1.5px solid var(--border); border-radius: 9px;
            font-family: inherit; font-size: 14.5px;
            color: var(--text-dark); background: var(--bg);
            outline: none; transition: all 0.15s;
        }

        .form-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(94,233,199,0.15);
        }

        .form-input::placeholder { color: var(--text-light); }

        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 13px;
            background: var(--text-dark); color: white;
            border: none; border-radius: 9px;
            font-family: inherit; font-size: 14.5px; font-weight: 700;
            cursor: pointer; transition: all 0.15s; margin-top: 8px;
        }

        .btn-primary:hover {
            background: #1f2937;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .btn-primary:active { transform: translateY(0); }
        .btn-primary svg { width: 17px; height: 17px; }

        .alert {
            padding: 12px 16px; border-radius: 8px;
            font-size: 13.5px; margin-bottom: 20px;
            background: #fef2f2; color: #991b1b;
            border: 1px solid #fecaca;
        }

        .divider {
            border: none; border-top: 1px solid var(--border);
            margin: 24px 0;
        }

        .back-link {
            text-align: center;
            font-size: 13.5px; color: var(--text-muted);
        }

        .back-link a {
            color: var(--text-dark); font-weight: 600;
            text-decoration: none; display: inline-flex;
            align-items: center; gap: 4px;
        }

        .back-link a:hover { text-decoration: underline; }
        .back-link svg { width: 14px; height: 14px; }

        .copyright {
            text-align: center; margin-top: 14px;
            font-size: 12px; color: var(--text-light);
        }
    </style>
</head>
<body>

    <div class="logo-wrap">
        <div class="logo-icon">
            <img src="{{ asset('kemen_icon.png') }}" alt="Logo" style="width:36px;height:36px;object-fit:contain;">
        </div>
        <div class="logo-name">SehatEdukasi</div>
        <div class="logo-sub">Platform Penyuluhan Kesehatan Digital</div>
        <div class="badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Portal Administrator
        </div>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif
        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <div class="card-title">Masuk sebagai Admin</div>
        <div class="card-sub">Akses terbatas untuk pengelola platform</div>

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-input"
                    placeholder="admin@example.com"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    autofocus
                >
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-input"
                    placeholder="••••••••"
                    autocomplete="current-password"
                >
            </div>
            <button type="submit" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Masuk ke Dashboard
            </button>
        </form>

        <hr class="divider">

        <div class="back-link">
            <a href="{{ route('login') }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke halaman responden
            </a>
        </div>
    </div>

    <div class="copyright">© {{ date('Y') }} SehatEdukasi — Akses Terbatas</div>

</body>
</html>