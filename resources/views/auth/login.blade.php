<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — SehatEdukasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #14503E;
            --primary-dark: #0E3D2F;
            --primary-mid: #2C7A5F;
            --on-primary: #ffffff;
            --border: #EFE6DD;
            --text-dark: #14332A;
            --text-muted: #8A9A93;
            --text-light: #B6ABA1;
            --bg: #FBF6F1;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #2C7A5F 0%, #1C6249 45%, #14503E 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 70% 20%, rgba(255,255,255,0.2) 0%, transparent 60%),
                        radial-gradient(ellipse at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
            position: relative;
            z-index: 1;
        }

        .logo-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .logo-name { font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; }
        .logo-sub { font-size: 14px; color: rgba(255,255,255,0.75); margin-top: 4px; }

        .card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08);
        }

        .card-title { font-size: 20px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .card-sub { font-size: 14px; color: var(--text-muted); margin-bottom: 28px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; color: var(--text-dark); }

        .form-input {
            width: 100%; padding: 13px 16px;
            border: 1.5px solid var(--border); border-radius: 9px;
            font-family: inherit; font-size: 15px;
            color: var(--text-dark); background: var(--bg);
            outline: none; transition: all 0.15s;
        }
        .form-input:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 3px rgba(20,80,62,0.12); }
        .form-input::placeholder { color: var(--text-light); }
        .form-hint { font-size: 12.5px; color: var(--text-muted); margin-top: 6px; }

        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 14px;
            background: var(--primary); color: var(--on-primary);
            border: none; border-radius: 9px;
            font-family: inherit; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.15s; margin-top: 8px;
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(20,80,62,0.25); }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary svg { width: 18px; height: 18px; }

        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .footer-link {
            text-align: center; margin-top: 20px;
            font-size: 14px; color: rgba(255,255,255,0.75);
            position: relative; z-index: 1;
        }
        .footer-link a { color: #ffffff; font-weight: 600; text-decoration: underline; }

        .copyright {
            text-align: center; margin-top: 16px;
            font-size: 12px; color: rgba(255,255,255,0.55);
            position: relative; z-index: 1;
        }
    </style>
</head>
<body>

    <div class="logo-wrap">
        <div class="logo-icon">
            <img src="{{ asset('kemen_icon.png') }}" alt="Logo" style="width:40px; height:40px; object-fit:contain;">
        </div>
        <div class="logo-name">Kenali Stunting</div>
        <div class="logo-sub">Platform Penyuluhan Kesehatan Digital</div>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif
        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <div class="card-title">Selamat datang 👋</div>
        <div class="card-sub">Daftar terlebih dahulu jika belum punya kode akses</div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Kode Akses</label>
                <input
                    type="text"
                    name="access_code"
                    class="form-input"
                    placeholder="Contoh: SEHAT001"
                    value="{{ old('access_code') }}"
                    autocomplete="off"
                    autofocus
                >
                <div class="form-hint">Daftar terlebih dahulu jika belum punya kode akses</div>
            </div>
            <button type="submit" class="btn-primary">
                Masuk
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>
    </div>

    <div class="footer-link">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </div>
    <div class="copyright">© {{ date('Y') }} SehatEdukasi — Penyuluhan Kesehatan</div>

</body>
</html>