@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    .hero-banner {
        background: linear-gradient(135deg, #1a7a5e 0%, #0f4035 100%);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        right: -20px;
        top: -20px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(52,198,138,0.2) 0%, transparent 70%);
        border-radius: 50%;
    }

    .hero-name { font-size: 28px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 8px; }
    .hero-sub { font-size: 15px; color: rgba(255,255,255,0.75); max-width: 500px; line-height: 1.6; }

    .hero-tags { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
    .hero-tag {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.15);
        backdrop-filter: blur(4px);
        padding: 6px 14px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 500;
    }
    .hero-tag svg { width: 14px; height: 14px; }

    .section-title { font-size: 18px; font-weight: 800; margin-bottom: 16px; color: var(--text-dark); }

    .steps-list { display: flex; flex-direction: column; gap: 12px; }

    .step-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
    }

    .step-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .step-card.completed { border-color: #a7f3d0; background: #f0fdf9; }

    .step-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--bg);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .step-icon.done { background: #ecfdf5; }
    .step-icon svg { width: 20px; height: 20px; }
    .step-icon.done svg { color: var(--success); }

    .step-body { flex: 1; }
    .step-meta { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; display: flex; gap: 8px; align-items: center; }
    .step-name { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 2px; }
    .step-desc { font-size: 13px; color: var(--text-muted); }

    .step-action {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
        background: white;
        white-space: nowrap;
    }

    .badge-done { background: #ecfdf5; color: #065f46; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 100px; }
    .badge-todo { background: var(--bg); color: var(--text-muted); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 100px; border: 1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="hero-banner">
    <div class="hero-name">Selamat Datang, {{ $respondent->name }}! 👋</div>
    <div class="hero-sub">Selamat mengikuti program penyuluhan kesehatan. Ikuti langkah-langkah di bawah ini untuk menyelesaikan program.</div>
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

<div class="section-title">Alur Program</div>
<div class="steps-list">
    <!-- Pre-Test -->
    <div class="step-card {{ $respondent->pre_test_done ? 'completed' : '' }}">
        <div class="step-icon {{ $respondent->pre_test_done ? 'done' : '' }}">
            @if($respondent->pre_test_done)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            @endif
        </div>
        <div class="step-body">
            <div class="step-meta">
                Tahap 1
                @if($respondent->pre_test_done)
                    <span class="badge-done">Selesai</span>
                @else
                    <span class="badge-todo">Belum</span>
                @endif
            </div>
            <div class="step-name">Pre-Test</div>
            <div class="step-desc">Jawab pertanyaan sebelum mempelajari materi</div>
        </div>
        <a href="{{ route('respondent.pretest') }}" class="step-action">
            Lihat
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>

    <!-- Materi -->
    <div class="step-card {{ $respondent->material_done ? 'completed' : '' }}">
        <div class="step-icon {{ $respondent->material_done ? 'done' : '' }}">
            @if($respondent->material_done)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            @endif
        </div>
        <div class="step-body">
            <div class="step-meta">
                Tahap 2
                @if($respondent->material_done)
                    <span class="badge-done">Selesai</span>
                @else
                    <span class="badge-todo">Belum</span>
                @endif
            </div>
            <div class="step-name">Materi Edukasi</div>
            <div class="step-desc">Pelajari materi kesehatan yang disediakan</div>
        </div>
        <a href="{{ route('respondent.material') }}" class="step-action">
            Lihat
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>

    <!-- Post-Test -->
    <div class="step-card {{ $respondent->post_test_done ? 'completed' : '' }}">
        <div class="step-icon {{ $respondent->post_test_done ? 'done' : '' }}">
            @if($respondent->post_test_done)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            @endif
        </div>
        <div class="step-body">
            <div class="step-meta">
                Tahap 3
                @if($respondent->post_test_done)
                    <span class="badge-done">Selesai</span>
                @else
                    <span class="badge-todo">Belum</span>
                @endif
            </div>
            <div class="step-name">Post-Test</div>
            <div class="step-desc">Jawab pertanyaan setelah mempelajari materi</div>
        </div>
        <a href="{{ route('respondent.posttest') }}" class="step-action">
            Lihat
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</div>
@endsection