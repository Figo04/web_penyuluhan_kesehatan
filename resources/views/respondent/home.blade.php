@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    .hero-banner {
        background: linear-gradient(135deg, #5EE9C7 0%, #3dc9a7 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .hero-banner::before {
        content: '';
        position: absolute;
        right: -20px; top: -20px;
        width: 160px; height: 160px;
        background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, transparent 70%);
        border-radius: 50%;
    }
    .hero-name { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .hero-sub { font-size: 13px; color: rgba(0,0,0,0.55); line-height: 1.5; }
    .hero-tags { display: flex; gap: 8px; margin-top: 14px; flex-wrap: wrap; }
    .hero-tag {
        display: flex; align-items: center; gap: 5px;
        background: rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.1);
        padding: 4px 12px; border-radius: 100px;
        font-size: 12px; font-weight: 600; color: #111827;
    }
    .hero-tag svg { width: 12px; height: 12px; }

    /* ── PROGRESS STEPS (compact) ──────────── */
    .section-label {
        font-size: 13px; font-weight: 700;
        color: var(--text-muted); text-transform: uppercase;
        letter-spacing: 0.06em; margin-bottom: 12px;
    }

    .progress-steps {
        display: flex;
        align-items: center;
        background: white;
        border-radius: 14px;
        border: 1px solid var(--border);
        padding: 16px 20px;
        margin-bottom: 28px;
        box-shadow: var(--shadow);
        gap: 0;
    }

    .progress-step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: inherit;
        position: relative;
    }

    /* Garis connector */
    .progress-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 16px;
        right: -50%;
        width: 100%;
        height: 2px;
        background: var(--border);
        z-index: 0;
    }
    .progress-step.done:not(:last-child)::after {
        background: #5EE9C7;
    }

    .step-dot {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--bg);
        border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800;
        color: var(--text-muted);
        position: relative; z-index: 1;
        transition: all 0.2s;
    }
    .progress-step.done .step-dot {
        background: #5EE9C7;
        border-color: #5EE9C7;
        color: #111827;
    }
    .progress-step.done .step-dot svg { width: 14px; height: 14px; }

    .step-label {
        font-size: 11px; font-weight: 700;
        color: var(--text-muted); text-align: center;
        white-space: nowrap;
    }
    .progress-step.done .step-label { color: #0d7a5f; }

    .progress-step.locked { cursor: not-allowed; opacity: 0.55; }
    .progress-step.locked .step-dot { background: #e5e7eb; border-color: #e5e7eb; color: #9ca3af; }
    .progress-step.locked .step-label { color: #9ca3af; }

    /* ── HIGHLIGHT SECTION ─────────────────── */
    .highlight-section {
        animation: fadeSlideUp 0.5s ease both;
    }
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .highlight-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 14px;
    }
    .highlight-title { font-size: 16px; font-weight: 800; color: var(--text-dark); }
    .highlight-badge {
        background: #5EE9C7; color: #111827;
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 100px;
    }

    /* Horizontal scroll container */
    .highlight-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 12px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .highlight-scroll::-webkit-scrollbar { display: none; }

    .highlight-card {
        flex-shrink: 0;
        width: 220px;
        background: white;
        border-radius: 14px;
        border: 1.5px solid #b2f5e8;
        padding: 18px;
        scroll-snap-align: start;
        text-decoration: none;
        display: block;
        transition: all 0.2s;
    }
    .highlight-card:hover, .highlight-card:active {
        border-color: #5EE9C7;
        box-shadow: 0 4px 16px rgba(94,233,199,0.3);
        transform: translateY(-2px);
    }

    .highlight-card-title {
        font-size: 13px; font-weight: 800;
        color: #0d7a5f; margin-bottom: 10px;
        line-height: 1.4;
    }
    .highlight-card-body {
        font-size: 12px; color: var(--text-mid);
        line-height: 1.6;
    }
    .highlight-card-body ul {
        padding-left: 14px; margin: 4px 0 0;
    }
    .highlight-card-body li { margin-bottom: 2px; }

    /* Scroll indicator dots */
    .scroll-dots {
        display: flex; justify-content: center;
        gap: 5px; margin-top: 10px;
    }
    .scroll-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--border);
        transition: all 0.2s;
    }
    .scroll-dot.active { background: #5EE9C7; width: 18px; border-radius: 3px; }

    .highlight-cta {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        margin-top: 16px; padding: 14px;
        background: #5EE9C7; color: #111827;
        border-radius: 12px; font-weight: 700; font-size: 15px;
        text-decoration: none; transition: all 0.15s;
    }
    .highlight-cta:hover { background: #3dc9a7; }
    .highlight-cta svg { width: 18px; height: 18px; }
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="hero-banner">
    <div class="hero-name">Halo, {{ $respondent->name }}! 👋</div>
    <div class="hero-sub">Ikuti 3 tahap program penyuluhan kesehatan berikut ini.</div>
    <div class="hero-tags">
        <div class="hero-tag">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            ~30 menit
        </div>
        <div class="hero-tag">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            3 tahap
        </div>
    </div>
</div>

{{-- PROGRESS STEPS (compact) --}}
<div class="section-label">Progres Kamu</div>
<div class="progress-steps">
    <a href="{{ route('respondent.pretest') }}" class="progress-step {{ $respondent->pre_test_done ? 'done' : '' }}">
        <div class="step-dot">
            @if($respondent->pre_test_done)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            @else
                1
            @endif
        </div>
        <div class="step-label">Pre-Test</div>
    </a>

    @php
        // Kunci yang sama persis dengan yang ditegakkan server di
        // MaterialController::index dan TestController::stageBlocked.
        $materiTerkunci   = !$respondent->pre_test_done;
        $postTestTerkunci = !$respondent->pre_test_done
            || (!$respondent->material_done && $totalMaterials > 0);
    @endphp

    <{{ $materiTerkunci ? 'div' : 'a' }}
        @if(!$materiTerkunci) href="{{ route('respondent.material') }}" @endif
        class="progress-step {{ $respondent->material_done ? 'done' : '' }} {{ $materiTerkunci ? 'locked' : '' }}"
        @if($materiTerkunci) title="Selesaikan Pre-Test terlebih dahulu" @endif>
        <div class="step-dot">
            @if($respondent->material_done)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            @elseif($materiTerkunci)
                🔒
            @else
                2
            @endif
        </div>
        <div class="step-label">Materi</div>
    </{{ $materiTerkunci ? 'div' : 'a' }}>

    <{{ $postTestTerkunci ? 'div' : 'a' }}
        @if(!$postTestTerkunci) href="{{ route('respondent.posttest') }}" @endif
        class="progress-step {{ $respondent->post_test_done ? 'done' : '' }} {{ $postTestTerkunci ? 'locked' : '' }}"
        @if($postTestTerkunci) title="Selesaikan Pre-Test dan baca semua materi terlebih dahulu" @endif>
        <div class="step-dot">
            @if($respondent->post_test_done)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            @elseif($postTestTerkunci)
                🔒
            @else
                3
            @endif
        </div>
        <div class="step-label">Post-Test</div>
    </{{ $postTestTerkunci ? 'div' : 'a' }}>
</div>

{{-- HIGHLIGHT CARDS — hanya muncul setelah pre-test selesai --}}
@if($respondent->pre_test_done)
<div class="highlight-section" id="highlightSection">
    <div class="highlight-header">
        <div class="highlight-title">Info Kesehatan Untukmu</div>
        <span class="highlight-badge">✨ Baru</span>
    </div>

    <div class="highlight-scroll" id="highlightScroll">
        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Tanda Anak Berisiko Stunting</div>
            <div class="highlight-card-body">
                Orang tua perlu waspada jika:
                <ul>
                    <li>Anak jarang naik berat badan</li>
                    <li>Tinggi badan tidak sesuai usia</li>
                    <li>Anak sering sakit</li>
                </ul>
            </div>
        </a>

        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Kesalahan yang Sering Dilakukan Orang Tua</div>
            <div class="highlight-card-body">
                <ul>
                    <li>Memberi makanan instan terlalu sering</li>
                    <li>MPASI tidak mengandung protein</li>
                    <li>Ayah tidak terlibat pengasuhan</li>
                </ul>
            </div>
        </a>

        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Contoh Menu Sehari untuk Balita</div>
            <div class="highlight-card-body">
                🌅 Pagi: Nasi + telur + sayur<br>
                ☀️ Siang: Nasi + ayam + tempe<br>
                🌙 Malam: Nasi + ikan + sayur<br>
                🍎 Snack: Buah atau susu
            </div>
        </a>

        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Peran Ayah yang Sering Diabaikan</div>
            <div class="highlight-card-body">
                Ayah bisa membantu dengan:
                <ul>
                    <li>Mengingatkan jadwal posyandu</li>
                    <li>Membantu menyiapkan makanan</li>
                    <li>Mendukung ibu merawat anak</li>
                </ul>
            </div>
        </a>

        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Pentingnya Posyandu</div>
            <div class="highlight-card-body">
                Manfaat posyandu:
                <ul>
                    <li>Memantau pertumbuhan anak</li>
                    <li>Edukasi kesehatan gratis</li>
                    <li>Deteksi dini stunting</li>
                </ul>
            </div>
        </a>

        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Lingkungan Rumah yang Sehat</div>
            <div class="highlight-card-body">
                Rumah sehat mencegah stunting:
                <ul>
                    <li>Air bersih & sanitasi baik</li>
                    <li>Anak aktif bermain</li>
                    <li>Kebersihan makanan terjaga</li>
                </ul>
            </div>
        </a>

        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Dampak Stunting Saat Dewasa</div>
            <div class="highlight-card-body">
                Jika tidak dicegah:
                <ul>
                    <li>Prestasi belajar menurun</li>
                    <li>Produktivitas rendah</li>
                    <li>Risiko penyakit meningkat</li>
                </ul>
            </div>
        </a>

        <a href="{{ route('respondent.material') }}" class="highlight-card">
            <div class="highlight-card-title">Langkah Mudah Mulai Hari Ini</div>
            <div class="highlight-card-body">
                Mulai dari hal sederhana:
                <ul>
                    <li>Sediakan makanan bergizi</li>
                    <li>Libatkan ayah dalam pengasuhan</li>
                    <li>Pantau tumbuh kembang anak</li>
                </ul>
            </div>
        </a>
    </div>

    {{-- Scroll indicator dots --}}
    <div class="scroll-dots" id="scrollDots">
        @for($i = 0; $i < 8; $i++)
            <div class="scroll-dot {{ $i === 0 ? 'active' : '' }}" id="dot-{{ $i }}"></div>
        @endfor
    </div>

    <a href="{{ route('respondent.material') }}" class="highlight-cta">
        Buka Materi Lengkap
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </a>
</div>
@endif

@endsection

@push('scripts')
<script>
    // ── Auto-scroll ke highlight section (pertama kali setelah pre-test selesai)
    @if($respondent->pre_test_done && session('show_highlight'))
        setTimeout(() => {
            const el = document.getElementById('highlightSection');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 500);
    @endif

    // ── Scroll indicator dots
    const scroll = document.getElementById('highlightScroll');
    if (scroll) {
        const cards = scroll.querySelectorAll('.highlight-card');
        const dots  = document.querySelectorAll('.scroll-dot');

        scroll.addEventListener('scroll', () => {
            const index = Math.round(scroll.scrollLeft / (scroll.scrollWidth / cards.length));
            dots.forEach((d, i) => d.classList.toggle('active', i === index));
        }, { passive: true });
    }
</script>
@endpush