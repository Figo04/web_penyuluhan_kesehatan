<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun Baru — SehatEdukasi</title>
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
            justify-content: flex-start;
            padding: 32px 24px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 70% 20%, rgba(255,255,255,0.2) 0%, transparent 60%),
                        radial-gradient(ellipse at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .page-header {
            width: 100%;
            max-width: 440px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .back-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: rgba(255,255,255,0.25);
            color: #111827;
            text-decoration: none;
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .back-btn:hover { background: rgba(255,255,255,0.4); }
        .back-btn svg { width: 18px; height: 18px; }

        .page-header h1 { font-size: 22px; font-weight: 800; color: #111827; }

        .card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08);
        }

        .card-avatar {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
        }

        .avatar-circle {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }
        .avatar-circle svg { width: 26px; height: 26px; color: var(--primary-dark); }
        .avatar-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; color: var(--text-dark); }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-family: inherit;
            font-size: 15px;
            color: var(--text-dark);
            background: var(--bg);
            outline: none;
            transition: all 0.15s;
        }
        .form-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(94,233,199,0.15);
        }
        .form-input::placeholder { color: var(--text-light); }

        /* RADIO */
        .radio-group { display: flex; gap: 20px; margin-top: 4px; }
        .radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            cursor: pointer;
            color: var(--text-dark);
        }
        .radio-label input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-dark);
        }

        /* CHECKBOXES */
        .checkbox-list { display: flex; flex-direction: column; gap: 10px; margin-top: 4px; }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            cursor: pointer;
            color: var(--text-dark);
        }
        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-dark);
            flex-shrink: 0;
        }

        .form-error { font-size: 13px; color: #dc2626; margin-top: 6px; }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .divider { height: 1px; background: var(--border); margin: 24px 0; }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
        }

        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #111827;
            border: none;
            border-radius: 9px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            margin-top: 8px;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(94,233,199,0.4);
        }
        .btn-primary:active { transform: translateY(0); }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }
        .footer-link a { color: #111827; font-weight: 600; text-decoration: underline; }

        /* Input number hide arrows */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
</head>
<body>
    <div class="page-header">
        <a href="{{ route('login') }}" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1>Daftar Akun Baru</h1>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <div class="card-avatar">
            <div class="avatar-circle">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <span class="avatar-label">Data Diri Responden</span>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- SECTION: Identitas --}}
            <p class="section-title">Identitas</p>

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input"
                    placeholder="Masukkan nama lengkap"
                    value="{{ old('name') }}" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Umur</label>
                <input type="number" name="age" class="form-input"
                    placeholder="Masukkan umur"
                    value="{{ old('age') }}"
                    min="10" max="120" required>
                @error('age') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Kelamin</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="gender" value="laki-laki"
                            {{ old('gender') == 'laki-laki' ? 'checked' : '' }} required>
                        Laki-laki
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="gender" value="perempuan"
                            {{ old('gender') == 'perempuan' ? 'checked' : '' }}>
                        Perempuan
                    </label>
                </div>
                @error('gender') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="divider"></div>

            {{-- SECTION: Status & Pekerjaan --}}
            <p class="section-title">Status & Pekerjaan</p>

            <div class="form-group">
                <label class="form-label">Status Perkawinan</label>
                <select name="marital_status" class="form-input" required>
                    <option value="" disabled {{ !old('marital_status') ? 'selected' : '' }}>Pilih status perkawinan</option>
                    <option value="belum menikah"  {{ old('marital_status') == 'belum menikah'  ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="menikah"        {{ old('marital_status') == 'menikah'        ? 'selected' : '' }}>Menikah</option>
                    <option value="cerai hidup"    {{ old('marital_status') == 'cerai hidup'    ? 'selected' : '' }}>Cerai Hidup</option>
                    <option value="cerai mati"     {{ old('marital_status') == 'cerai mati'     ? 'selected' : '' }}>Cerai Mati</option>
                </select>
                @error('marital_status') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Pekerjaan</label>
                <select name="occupation" class="form-input" required>
                    <option value="" disabled {{ !old('occupation') ? 'selected' : '' }}>Pilih pekerjaan</option>
                    <option value="PNS"           {{ old('occupation') == 'PNS'           ? 'selected' : '' }}>PNS</option>
                    <option value="Swasta"        {{ old('occupation') == 'Swasta'        ? 'selected' : '' }}>Karyawan Swasta</option>
                    <option value="Wiraswasta"    {{ old('occupation') == 'Wiraswasta'    ? 'selected' : '' }}>Wiraswasta</option>
                    <option value="Petani"        {{ old('occupation') == 'Petani'        ? 'selected' : '' }}>Petani</option>
                    <option value="IRT"           {{ old('occupation') == 'IRT'           ? 'selected' : '' }}>Ibu Rumah Tangga</option>
                    <option value="Pelajar"       {{ old('occupation') == 'Pelajar'       ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                    <option value="Tidak Bekerja" {{ old('occupation') == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                    <option value="Lainnya"       {{ old('occupation') == 'Lainnya'       ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('occupation') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="divider"></div>

            {{-- SECTION: Data Anak --}}
            <p class="section-title">Data Anak</p>

            <div class="form-group">
                <label class="form-label">Jumlah Anak</label>
                <input type="number" name="total_children" class="form-input"
                    placeholder="Masukkan jumlah anak"
                    value="{{ old('total_children') }}"
                    min="0" max="20" required>
                @error('total_children') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="divider"></div>

            {{-- SECTION: Riwayat Kesehatan --}}
            <p class="section-title">Riwayat Kesehatan</p>

            <div class="form-group">
                <label class="form-label">Riwayat Penyakit</label>
                <div class="checkbox-list">
                    @php
                        $diseases = [
                            'Hipertensi (Tekanan Darah Tinggi)',
                            'Diabetes Mellitus (Penyakit Gula)',
                            'Hiperkolesterolemia (Kolesterol Tinggi)',
                            'Stroke',
                            'Penyakit Jantung',
                            'Tidak Ada',
                        ];
                        $oldMedical = old('medical_history', []);
                    @endphp
                    @foreach($diseases as $disease)
                    <label class="checkbox-label">
                        <input type="checkbox" name="medical_history[]" value="{{ $disease }}"
                            {{ in_array($disease, $oldMedical) ? 'checked' : '' }}>
                        {{ $disease }}
                    </label>
                    @endforeach
                </div>
                @error('medical_history') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-primary">
                Daftar &amp; Mulai
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>
    </div>

    <div class="footer-link">
        Sudah punya kode akses? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</body>
</html>