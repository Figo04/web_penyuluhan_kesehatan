<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — SehatEdukasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a7a5e 0%, #155f49 40%, #0f4035 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 70% 20%, rgba(52,198,138,0.15) 0%, transparent 60%);
        }
        .logo-wrap { display: flex; flex-direction: column; align-items: center; margin-bottom: 28px; }
        .logo-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .logo-icon svg { width: 32px; height: 32px; color: white; }
        .logo-name { font-size: 26px; font-weight: 800; color: white; }
        .logo-sub { font-size: 14px; color: rgba(255,255,255,0.6); margin-top: 4px; }

        .wrap { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 1; width: 100%; max-width: 400px; }

        .card {
            background: white; border-radius: 20px; padding: 32px; width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .card-title { font-size: 17px; font-weight: 800; margin-bottom: 6px; }
        .card-sub { font-size: 13px; color: #6b7280; margin-bottom: 24px; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 7px; }
        .form-input {
            width: 100%; padding: 12px 14px;
            border: 1.5px solid #e5e7eb; border-radius: 9px;
            font-family: inherit; font-size: 15px;
            background: #f4f7f6; outline: none; transition: all 0.15s;
        }
        .form-input:focus { border-color: #1a7a5e; background: white; box-shadow: 0 0 0 3px rgba(26,122,94,0.08); }
        .form-input::placeholder { color: #9ca3af; }

        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 14px;
            background: #1a7a5e; color: white; border: none; border-radius: 9px;
            font-family: inherit; font-size: 15px; font-weight: 700; cursor: pointer;
            transition: all 0.15s; margin-top: 8px;
        }
        .btn-primary:hover { background: #155f49; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,122,94,0.3); }

        .alert { padding: 11px 14px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .tab-group {
            display: grid; grid-template-columns: 1fr 1fr; gap: 4px;
            background: #f4f7f6; border-radius: 10px; padding: 4px; margin-bottom: 24px;
        }
        .tab-btn {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            padding: 10px; border-radius: 8px; border: none;
            font-family: inherit; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            color: #6b7280; background: transparent; text-decoration: none;
        }
        .tab-btn.active { background: white; color: #111827; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .tab-btn svg { width: 15px; height: 15px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo-wrap">
            <div class="logo-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
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
                <a href="{{ route('login') }}" class="tab-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    Responden
                </a>
                <button class="tab-btn active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Admin
                </button>
            </div>

            <div class="card-title">Login Admin</div>
            <div class="card-sub">Masuk sebagai administrator sistem</div>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input"
                        placeholder="admin@example.com"
                        value="{{ old('email') }}" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input"
                        placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-primary">
                    Masuk sebagai Admin
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>
    </div>
</body>
</html>