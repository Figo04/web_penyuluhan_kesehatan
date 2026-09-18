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
            --text-mid: #374151;
            --text-muted: #6b7280;
            --text-light: #9ca3af;
            --bg: #f4f7f6;
            --radius: 12px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text-dark);
        }

        /* ── DESKTOP LAYOUT: 2 kolom ── */
        .page-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* Kolom kiri — hero/branding */
        .hero-side {
            background: linear-gradient(145deg, #5EE9C7 0%, #3dc9a7 50%, #2aab8a 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 56px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
        }

        .hero-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 80% 20%, rgba(255,255,255,0.25) 0%, transparent 55%),
                radial-gradient(ellipse at 10% 80%, rgba(255,255,255,0.15) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
            position: relative;
            z-index: 1;
        }
        .hero-logo img {
            width: 40px; height: 40px;
            border-radius: 10px;
            object-fit: contain;
        }
        .hero-logo-name {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }

        .hero-content { position: relative; z-index: 1; }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.35);
            color: #0d3a2f;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 100px;
            margin-bottom: 20px;
            letter-spacing: 0.03em;
        }

        .hero-title {
            font-size: 36px;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .hero-title span {
            display: block;
            color: #0d5c47;
        }

        .hero-desc {
            font-size: 15px;
            color: rgba(0,0,0,0.55);
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 36px;
        }

        .hero-steps {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .hero-step {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #0d3a2f;
        }
        .hero-step-num {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: rgba(255,255,255,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800;
            flex-shrink: 0;
        }

        /* Dekorasi lingkaran */
        .hero-circle-1 {
            position: absolute;
            bottom: -60px; right: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            border: 40px solid rgba(255,255,255,0.12);
        }
        .hero-circle-2 {
            position: absolute;
            top: 40px; right: 40px;
            width: 120px; height: 120px;
            border-radius: 50%;
            border: 24px solid rgba(255,255,255,0.15);
        }

        /* Kolom kanan — form */
        .form-side {
            background: white;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .form-inner {
            max-width: 520px;
            width: 100%;
            margin: 0 auto;
            padding: 48px 48px;
            flex: 1;
        }

        .form-top {
            margin-bottom: 32px;
        }
        .form-heading {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 6px;
        }
        .form-subheading {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Section label */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        .form-section { margin-bottom: 28px; }

        /* Grid 2 kolom untuk field tertentu di desktop */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group { margin-bottom: 18px; }
        .form-group:last-child { margin-bottom: 0; }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 7px;
            color: var(--text-dark);
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-family: inherit;
            font-size: 14px;
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

        /* Radio */
        .radio-group { display: flex; gap: 20px; margin-top: 2px; }
        .radio-label {
            display: flex; align-items: center; gap: 7px;
            font-size: 14px; cursor: pointer; color: var(--text-dark);
            padding: 9px 16px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            transition: all 0.15s;
            flex: 1; justify-content: center;
        }
        .radio-label:has(input:checked) {
            border-color: var(--primary);
            background: #edfdf9;
            color: #0d5c47;
        }
        .radio-label input[type="radio"] {
            width: 16px; height: 16px;
            accent-color: var(--primary-dark);
        }

        /* Checkbox */
        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 2px;
        }
        .checkbox-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; cursor: pointer; color: var(--text-dark);
            padding: 8px 12px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            transition: all 0.15s;
            line-height: 1.4;
        }
        .checkbox-label:has(input:checked) {
            border-color: var(--primary);
            background: #edfdf9;
            color: #0d5c47;
        }
        .checkbox-label input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--primary-dark);
            flex-shrink: 0;
        }

        .form-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .btn-primary {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 13px;
            background: var(--primary); color: #111827;
            border: none; border-radius: 9px;
            font-family: inherit; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.15s;
            margin-top: 8px;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(94,233,199,0.35);
        }
        .btn-primary:active { transform: translateY(0); }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            padding-bottom: 48px;
            font-size: 14px;
            color: var(--text-muted);
        }
        .footer-link a {
            color: var(--text-dark);
            font-weight: 700;
            text-decoration: none;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1px;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        /* ── TABLET (768px - 1024px) ── */
        @media (max-width: 1024px) {
            .hero-title { font-size: 28px; }
            .hero-side  { padding: 48px 40px; }
            .form-inner { padding: 40px 36px; }
        }

        /* ── MOBILE: 1 kolom ── */
        @media (max-width: 768px) {
            .page-wrapper {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            /* Hero jadi header kecil di atas */
            .hero-side {
                position: static;
                height: auto;
                padding: 24px 20px;
                flex-direction: row;
                align-items: center;
                gap: 0;
                min-height: auto;
            }
            .hero-logo { margin-bottom: 0; }
            .hero-content { display: none; }
            .hero-circle-1, .hero-circle-2 { display: none; }

            /* Form full width */
            .form-side { background: var(--bg); }
            .form-inner {
                padding: 24px 20px 40px;
                max-width: 100%;
            }
            .form-top { margin-bottom: 24px; }
            .form-heading { font-size: 22px; }

            /* Form row jadi 1 kolom */
            .form-row { grid-template-columns: 1fr; gap: 0; }

            /* Checkbox grid jadi 1 kolom */
            .checkbox-grid { grid-template-columns: 1fr; }

            /* Radio full width */
            .radio-group { gap: 10px; }
        }

        @media (max-width: 480px) {
            .form-inner { padding: 20px 16px 40px; }
        }
    </style>
</head>
<body>
<div class="page-wrapper">

    {{-- ── KIRI: Hero / Branding ── --}}
    <div class="hero-side">
        <div class="hero-circle-1"></div>
        <div class="hero-circle-2"></div>

        <div class="hero-logo">
            <img src="{{ asset('kemen_icon.png') }}" alt="Logo">
            <span class="hero-logo-name">Kenali Stunting</span>
        </div>

        <div class="hero-content">
            <div class="hero-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Platform Edukasi Kesehatan
            </div>

            <h1 class="hero-title">
                Cegah Stunting
                <span>Sejak Dini</span>
            </h1>

            <p class="hero-desc">
                Ikuti program edukasi digital untuk meningkatkan pengetahuan dan sikap tentang pencegahan stunting bersama tim dokter Poltekkes.
            </p>

            <div class="hero-steps">
                <div class="hero-step">
                    <div class="hero-step-num">1</div>
                    Daftar & isi data diri
                </div>
                <div class="hero-step">
                    <div class="hero-step-num">2</div>
                    Kerjakan Pre-Test
                </div>
                <div class="hero-step">
                    <div class="hero-step-num">3</div>
                    Pelajari materi edukasi
                </div>
                <div class="hero-step">
                    <div class="hero-step-num">4</div>
                    Kerjakan Post-Test
                </div>
            </div>
        </div>
    </div>

    {{-- ── KANAN: Form ── --}}
    <div class="form-side">
        <div class="form-inner">

            <div class="form-top">
                <h2 class="form-heading">Buat Akun Baru</h2>
                <p class="form-subheading">Isi data diri Anda untuk memulai program edukasi</p>
            </div>

            @if($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- IDENTITAS --}}
                <div class="form-section">
                    <p class="section-title">Identitas</p>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-input"
                            placeholder="Masukkan nama lengkap"
                            value="{{ old('name') }}" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Umur</label>
                            <input type="number" name="age" class="form-input"
                                placeholder="Tahun"
                                value="{{ old('age') }}" min="10" max="120" required>
                            @error('age') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jumlah Anak</label>
                            <input type="number" name="total_children" class="form-input"
                                placeholder="0"
                                value="{{ old('total_children') }}" min="0" max="20" required>
                            @error('total_children') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
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
                </div>

                {{-- STATUS & PEKERJAAN --}}
                <div class="form-section">
                    <p class="section-title">Status &amp; Pekerjaan</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Status Perkawinan</label>
                            <select name="marital_status" class="form-input" required>
                                <option value="" disabled {{ !old('marital_status') ? 'selected' : '' }}>Pilih status</option>
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
                    </div>
                </div>

                {{-- RIWAYAT KESEHATAN --}}
                <div class="form-section">
                    <p class="section-title">Riwayat Kesehatan</p>

                    <div class="form-group">
                        <label class="form-label">Riwayat Penyakit <span style="font-weight:400;color:var(--text-muted);">(bisa pilih lebih dari satu)</span></label>
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
                        <div class="checkbox-grid">
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
                </div>

                <button type="submit" class="btn-primary">
                    Daftar &amp; Mulai
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="17" height="17">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="footer-link">
                Sudah punya kode akses? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>

</div>
</body>
</html>