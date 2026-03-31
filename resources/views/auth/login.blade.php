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
            --primary: #5EE9C7;
            --primary-dark: #3dc9a7;
            --border: #e5e7eb;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --text-light: #9ca3af;
            --bg: #f4f7f6;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #5EE9C7 0%, #3dc9a7 40%, #2aab8a 100%);
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

        .logo-icon svg { width: 32px; height: 32px; color: #111827; }
        .logo-name { font-size: 26px; font-weight: 800; color: #111827; letter-spacing: -0.5px; }
        .logo-sub { font-size: 14px; color: rgba(0,0,0,0.5); margin-top: 4px; }

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

        .tab-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            background: var(--bg);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 28px;
        }

        .tab-btn {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            padding: 10px; border-radius: 8px; border: none;
            font-family: inherit; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            color: var(--text-muted); background: transparent;
        }

        .tab-btn.active {
            background: white; color: var(--text-dark);
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        .tab-btn svg { width: 15px; height: 15px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; color: var(--text-dark); }
        .form-input {
            width: 100%; padding: 13px 16px;
            border: 1.5px solid var(--border); border-radius: 9px;
            font-family: inherit; font-size: 15px;
            color: var(--text-dark); background: var(--bg);
            outline: none; transition: all 0.15s;
        }
        .form-input:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 3px rgba(94,233,199,0.15); }
        .form-input::placeholder { color: var(--text-light); }
        .form-hint { font-size: 12.5px; color: var(--text-muted); margin-top: 6px; }

        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 14px;
            background: var(--primary); color: #111827;
            border: none; border-radius: 9px;
            font-family: inherit; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.15s; margin-top: 8px;
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(94,233,199,0.4); }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary svg { width: 18px; height: 18px; }

        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .form-panel { display: none; }
        .form-panel.active { display: block; }

        .footer-link {
            text-align: center; margin-top: 20px;
            font-size: 14px; color: rgba(0,0,0,0.5);
            position: relative; z-index: 1;
        }
        .footer-link a { color: #111827; font-weight: 600; text-decoration: underline; }

        .copyright {
            text-align: center; margin-top: 16px;
            font-size: 12px; color: rgba(0,0,0,0.35);
            position: relative; z-index: 1;
        }
    </style>
</head>
<body>
    <div class="logo-wrap">
        <div class="logo-icon">
    <img src="{{ asset('kemen_icon.png') }}" alt="Logo" style="width:40px; height:40px; object-fit:contain;">
</div>
        <div class="logo-name">SehatEdukasi</div>
        <div class="logo-sub">Platform Penyuluhan Kesehatan Digital</div>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif
        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <div class="tab-group">
            <button class="tab-btn active" id="tab-respondent" onclick="switchTab('respondent')" type="button">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Responden
            </button>
            <button class="tab-btn" id="tab-admin" onclick="switchTab('admin')" type="button">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Admin
            </button>
        </div>

        <div class="form-panel active" id="panel-respondent">
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Kode Akses</label>
                    <input type="text" name="access_code" class="form-input"
                        placeholder="Masukkan kode akses (cth: SEHAT001)"
                        value="{{ old('access_code') }}"
                        autocomplete="off">
                    <div class="form-hint">Kode akses diberikan oleh petugas kesehatan</div>
                </div>
                <button type="submit" class="btn-primary">
                    Masuk
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>
        </div>

        <div class="form-panel" id="panel-admin">
            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input"
                        placeholder="Masukkan email admin"
                        value="{{ old('email') }}"
                        autocomplete="email">
                </div>
                <div class="form-group">
                    <label class="form-label">Password Admin</label>
                    <input type="password" name="password" class="form-input"
                        placeholder="Masukkan password admin">
                </div>
                <button type="submit" class="btn-primary">
                    Masuk
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div class="footer-link">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </div>
    <div class="copyright">© {{ date('Y') }} SehatEdukasi — Penyuluhan Kesehatan</div>

    <script>
        function switchTab(tab) {
            document.getElementById('tab-respondent').classList.toggle('active', tab === 'respondent');
            document.getElementById('tab-admin').classList.toggle('active', tab === 'admin');
            document.getElementById('panel-respondent').classList.toggle('active', tab === 'respondent');
            document.getElementById('panel-admin').classList.toggle('active', tab === 'admin');
        }

        @if(session('active_tab') === 'admin' || old('email'))
            switchTab('admin');
        @endif
    </script>
</body>
</html>