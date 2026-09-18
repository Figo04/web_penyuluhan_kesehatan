@extends('layouts.app')

@section('title', 'Pre-Test')

@push('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-title  { font-size: 24px; font-weight: 800; }
    .page-sub    { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

    /* ── TAB ── */
    .tab-group {
        display: grid; grid-template-columns: 1fr 1fr;
        background: white; border-radius: 12px; border: 1px solid var(--border);
        padding: 6px; margin-bottom: 24px; gap: 4px; box-shadow: var(--shadow);
    }
    .tab-link {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600;
        text-decoration: none; color: var(--text-muted); transition: all 0.15s;
    }
    .tab-link.active { background: var(--primary); color: #111827; }
    .tab-link svg { width: 16px; height: 16px; flex-shrink: 0; }

    /* ── INSTRUCTION ── */
    .instruction-box {
        background: white; border: 1px solid var(--border);
        border-radius: 12px; margin-bottom: 20px;
        overflow: hidden; box-shadow: var(--shadow);
    }
    .instruction-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; background: #f0fdf9;
        border-bottom: 1px solid #d1fae5; cursor: pointer; user-select: none;
    }
    .instruction-header-left { display: flex; align-items: center; gap: 10px; }
    .instruction-header-left span { font-size: 14px; font-weight: 700; color: #065f46; }
    .instruction-icon {
        width: 32px; height: 32px; background: #d1fae5; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;
    }
    .instruction-toggle {
        width: 28px; height: 28px; border-radius: 7px; background: #d1fae5;
        border: none; cursor: pointer; display: flex; align-items: center;
        justify-content: center; transition: all 0.2s; flex-shrink: 0;
    }
    .instruction-toggle svg { width: 14px; height: 14px; color: #065f46; transition: transform 0.25s; }
    .instruction-toggle.open svg { transform: rotate(180deg); }
    .instruction-body { padding: 18px; display: none; }
    .instruction-body.open { display: block; }
    .instruction-section { margin-bottom: 16px; }
    .instruction-section:last-child { margin-bottom: 0; }
    .instruction-section-title {
        font-size: 12px; font-weight: 800; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px;
    }
    .instruction-item {
        display: flex; align-items: flex-start; gap: 8px;
        font-size: 13.5px; color: var(--text-dark); line-height: 1.5; margin-bottom: 6px;
    }
    .instruction-item:last-child { margin-bottom: 0; }
    .instruction-bullet {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--primary); flex-shrink: 0; margin-top: 7px;
    }

    /* Likert legend di petunjuk */
    .likert-legend { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .likert-chip {
        display: flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 7px; font-size: 12px; font-weight: 700;
        border: 1.5px solid transparent;
    }
    .chip-sts { background: #fef2f2; color: #991b1b; border-color: #fca5a5; }
    .chip-ts  { background: #fffbeb; color: #92400e; border-color: #fcd34d; }
    .chip-r   { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
    .chip-s   { background: #eff6ff; color: #1e40af; border-color: #93c5fd; }
    .chip-ss  { background: #ecfdf5; color: #065f46; border-color: #6ee7b7; }

    /* ── PROGRESS ── */
    .progress-wrap { margin-bottom: 20px; }
    .progress-header { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600; }
    .progress-bar { height: 6px; background: var(--bg); border-radius: 100px; overflow: hidden; border: 1px solid var(--border); }
    .progress-fill { height: 100%; background: var(--primary); border-radius: 100px; transition: width 0.3s ease; }

    /* ── QUESTION CARD ── */
    .question-card {
        background: white; border-radius: 14px; border: 1px solid var(--border);
        padding: 22px 24px; margin-bottom: 16px; box-shadow: var(--shadow);
        transition: border-color 0.2s;
    }
    .question-card.answered { border-color: #a7f3d0; }

    .question-top { margin-bottom: 14px; }
    .question-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .question-num { font-size: 12px; font-weight: 700; color: var(--text-muted); }
    .question-format-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; font-weight: 700; padding: 2px 8px;
        border-radius: 100px;
    }
    .badge-mc     { background: #f3f4f6; color: #374151; }
    .badge-likert { background: #eff6ff; color: #1d4ed8; }
    .badge-child  { background: #fdf2f8; color: #9d174d; }

    /* ── DATA ANAK & JUDUL BAGIAN ── */
    .child-summary {
        display: flex; align-items: center; gap: 14px;
        background: white; border: 1px solid var(--border); border-radius: 12px;
        padding: 14px 18px; margin-bottom: 20px; box-shadow: var(--shadow);
    }
    .child-summary-icon { font-size: 28px; }
    .child-summary-name { font-size: 15px; font-weight: 700; }
    .child-summary-meta { font-size: 13px; color: var(--text-muted); margin-top: 2px; }
    .section-heading {
        font-size: 13px; font-weight: 800; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.06em; margin: 24px 0 12px;
    }
    .question-text { font-size: 15px; font-weight: 600; color: var(--text-dark); line-height: 1.55; }

    /* ── MC OPTIONS ── */
    .options-list { display: flex; flex-direction: column; gap: 8px; }
    .option-label {
        display: flex; align-items: center; gap: 12px; padding: 13px 16px;
        border-radius: 9px; border: 1.5px solid var(--border);
        cursor: pointer; transition: all 0.15s; font-size: 14px; color: var(--text-dark);
    }
    .option-label:hover { border-color: var(--primary); background: var(--primary-light); }
    .option-label input[type="radio"] { display: none; }
    .option-radio {
        width: 20px; height: 20px; border-radius: 50%; border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.15s;
    }
    .option-label:has(input[type="radio"]:checked) { border-color: var(--primary); background: var(--primary-light); }
    .option-label:has(input[type="radio"]:checked) .option-radio { border-color: var(--primary-dark); background: var(--primary); }
    .option-label:has(input[type="radio"]:checked) .option-radio::after {
        content: ''; width: 8px; height: 8px; background: white; border-radius: 50%; display: block;
    }

    /* ── LIKERT HORIZONTAL GRID ── */
    .likert-scale {
        margin-top: 4px;
    }

    /* Label atas: teks penuh — hanya desktop */
    .likert-labels {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        margin-bottom: 6px;
        text-align: center;
    }
    .likert-label-text {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        line-height: 1.3;
        padding: 0 2px;
    }

    /* Grid 5 tombol */
    .likert-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
    }

    .likert-btn {
        position: relative;
        cursor: pointer;
    }
    .likert-btn input[type="radio"] { display: none; }

    .likert-btn-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 14px 8px;
        border-radius: 10px;
        border: 2px solid var(--border);
        background: var(--bg);
        transition: all 0.18s;
        cursor: pointer;
        min-height: 72px;
    }

    .likert-btn:hover .likert-btn-inner {
        border-color: var(--primary);
        background: var(--primary-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(94,233,199,0.2);
    }

    .likert-abbr {
        font-size: 13px;
        font-weight: 800;
        color: var(--text-muted);
        transition: color 0.15s;
        letter-spacing: 0.02em;
    }

    .likert-dot-indicator {
        width: 10px; height: 10px;
        border-radius: 50%;
        border: 2px solid var(--border);
        background: white;
        transition: all 0.15s;
    }

    /* State: selected — per nilai */
    .likert-btn.selected-STS .likert-btn-inner { background: #fef2f2; border-color: #f87171; box-shadow: 0 4px 12px rgba(239,68,68,0.15); transform: translateY(-2px); }
    .likert-btn.selected-STS .likert-abbr      { color: #991b1b; }
    .likert-btn.selected-STS .likert-dot-indicator { background: #ef4444; border-color: #ef4444; }

    .likert-btn.selected-TS .likert-btn-inner  { background: #fffbeb; border-color: #fbbf24; box-shadow: 0 4px 12px rgba(245,158,11,0.15); transform: translateY(-2px); }
    .likert-btn.selected-TS .likert-abbr       { color: #92400e; }
    .likert-btn.selected-TS .likert-dot-indicator { background: #f59e0b; border-color: #f59e0b; }

    .likert-btn.selected-R .likert-btn-inner   { background: #f9fafb; border-color: #9ca3af; box-shadow: 0 4px 12px rgba(107,114,128,0.12); transform: translateY(-2px); }
    .likert-btn.selected-R .likert-abbr        { color: #374151; }
    .likert-btn.selected-R .likert-dot-indicator { background: #6b7280; border-color: #6b7280; }

    .likert-btn.selected-S .likert-btn-inner   { background: #eff6ff; border-color: #60a5fa; box-shadow: 0 4px 12px rgba(59,130,246,0.15); transform: translateY(-2px); }
    .likert-btn.selected-S .likert-abbr        { color: #1e40af; }
    .likert-btn.selected-S .likert-dot-indicator { background: #3b82f6; border-color: #3b82f6; }

    .likert-btn.selected-SS .likert-btn-inner  { background: #ecfdf5; border-color: #34d399; box-shadow: 0 4px 12px rgba(16,185,129,0.15); transform: translateY(-2px); }
    .likert-btn.selected-SS .likert-abbr       { color: #065f46; }
    .likert-btn.selected-SS .likert-dot-indicator { background: #10b981; border-color: #10b981; }

    /* Skala visual di bawah grid */
    .likert-scale-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
        padding: 0 4px;
    }
    .likert-scale-left  { font-size: 11px; color: #991b1b; font-weight: 600; }
    .likert-scale-line  {
        flex: 1; height: 2px; margin: 0 8px;
        background: linear-gradient(to right, #f87171, #fbbf24, #9ca3af, #60a5fa, #34d399);
        border-radius: 2px; opacity: 0.5;
    }
    .likert-scale-right { font-size: 11px; color: #065f46; font-weight: 600; }

    /* ── BANNER ── */
    .error-banner {
        background: #fef2f2; border: 1px solid #fca5a5;
        border-radius: 10px; padding: 14px 18px;
        margin-bottom: 16px; color: #991b1b; font-size: 14px; font-weight: 600;
    }
    .done-banner {
        background: #ecfdf5; border: 1px solid #a7f3d0;
        border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px;
    }
    .done-icon  { font-size: 40px; margin-bottom: 12px; }
    .done-title { font-size: 18px; font-weight: 800; color: #065f46; margin-bottom: 6px; }
    .done-sub   { font-size: 14px; color: #047857; }

    /* ── SUBMIT BUTTON ── */
    .btn-submit {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 15px; background: var(--primary); color: #111827;
        border: none; border-radius: 10px; font-family: inherit; font-size: 16px;
        font-weight: 700; cursor: pointer; transition: all 0.15s; margin-top: 24px;
    }
    .btn-submit:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(94,233,199,0.35); }

    /* ── RESULT MODAL ── */
    .result-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.6); z-index: 1000;
        align-items: center; justify-content: center; padding: 16px;
    }
    .result-overlay.active { display: flex; }
    .result-box {
        background: white; border-radius: 20px;
        width: 100%; max-width: 480px; overflow: hidden;
        box-shadow: 0 24px 64px rgba(0,0,0,0.25);
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .result-header {
        padding: 28px 24px 20px; text-align: center;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-bottom: 1px solid #a7f3d0;
    }
    .result-emoji  { font-size: 52px; margin-bottom: 10px; line-height: 1; }
    .result-title  { font-size: 20px; font-weight: 800; color: #065f46; margin-bottom: 4px; }
    .result-sub    { font-size: 14px; color: #047857; }
    .result-body   { padding: 20px 24px; }
    .result-section-title {
        font-size: 11px; font-weight: 800; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.06em;
        margin-bottom: 10px; margin-top: 16px;
    }
    .result-section-title:first-child { margin-top: 0; }
    .result-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; border-radius: 9px; margin-bottom: 6px;
        background: var(--bg); border: 1px solid var(--border);
    }
    .result-row-label { font-size: 14px; color: var(--text-dark); font-weight: 500; }
    .result-row-value { font-size: 15px; font-weight: 800; }
    .value-correct { color: #059669; }
    .value-wrong   { color: #ef4444; }
    .value-score   { color: var(--primary-dark); }
    .value-likert  { color: #3b82f6; }
    .result-score-big {
        background: linear-gradient(135deg, var(--primary-light), #d1fae5);
        border: 2px solid var(--primary); border-radius: 12px;
        padding: 16px; text-align: center; margin: 16px 0 0;
    }
    .result-score-big .score-num   { font-size: 40px; font-weight: 800; color: #065f46; line-height: 1; }
    .result-score-big .score-label { font-size: 13px; color: #047857; margin-top: 4px; font-weight: 600; }
    .result-footer { padding: 16px 24px 24px; }
    .btn-result-ok {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 13px; background: var(--primary); color: #111827;
        border: none; border-radius: 10px; font-family: inherit;
        font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.15s;
    }
    .btn-result-ok:hover { background: var(--primary-dark); }

    /* ── RESPONSIVE ── */
    @media (max-width: 640px) {
        /* Di HP, sembunyikan label teks penuh di atas — cukup abbr di dalam tombol */
        .likert-labels { display: none; }

        .likert-btn-inner {
            padding: 12px 4px;
            min-height: 60px;
            gap: 5px;
        }
        .likert-abbr { font-size: 12px; }
        .likert-dot-indicator { width: 8px; height: 8px; }

        .likert-scale-bar { display: none; }

        .question-card { padding: 16px; }
    }

    @media (max-width: 380px) {
        .likert-grid { gap: 4px; }
        .likert-btn-inner { padding: 10px 2px; min-height: 54px; }
        .likert-abbr { font-size: 11px; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">Buku Kerja</div>
    <div class="page-sub">Kerjakan Pre-Test dan Post-Test di sini</div>
</div>

<div class="tab-group">
    <a href="{{ route('respondent.pretest') }}" class="tab-link active">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Pre-Test
        @if($respondent->pre_test_done)
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
    </a>
    <a href="{{ route('respondent.posttest') }}" class="tab-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Post-Test
        @if($respondent->post_test_done)
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="color:var(--success)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
    </a>
</div>

@php
    $scored      = $questions->where('category', '!=', 'kondisi_anak');
    $childCount  = $questions->count() - $scored->count();
    $hasMc       = $scored->where('question_format', 'multiple_choice')->count() > 0;
    $hasLikert   = $scored->where('question_format', 'likert')->count() > 0;
    $mcCount     = $scored->where('question_format', 'multiple_choice')->count();
    $likertCount = $scored->where('question_format', 'likert')->count();
    $prevCategory = null;
    $likertOrder = ['STS' => 1, 'TS' => 2, 'R' => 3, 'S' => 4, 'SS' => 5];
    $answeredCount  = $answers->count();
    $totalQuestions = $questions->count();
@endphp

{{-- PETUNJUK --}}
<div class="instruction-box">
    <div class="instruction-header" onclick="toggleInstruction()">
        <div class="instruction-header-left">
            <div class="instruction-icon">📋</div>
            <span>Petunjuk Pengisian</span>
        </div>
        <button class="instruction-toggle open" id="instrToggleBtn" type="button">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    </div>
    <div class="instruction-body open" id="instrBody">
        <div class="instruction-section">
            <div class="instruction-section-title">Umum</div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Baca setiap soal dengan teliti sebelum menjawab.</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Jawaban tersimpan <strong>otomatis</strong> setiap kali Anda memilih pilihan.</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Pastikan semua soal dijawab sebelum menekan <strong>Selesaikan Pre-Test</strong>.</span></div>
        </div>
        @if($childCount)
        <div class="instruction-section">
            <div class="instruction-section-title">Kondisi Anak <span style="font-weight:500;text-transform:none;font-size:11px;">({{ $childCount }} soal)</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Pilih jawaban yang <strong>sesuai kondisi anak Anda</strong>. Bagian ini tidak dinilai.</span></div>
        </div>
        @endif
        @if($hasMc)
        <div class="instruction-section">
            <div class="instruction-section-title">Bagian A — Pengetahuan <span style="font-weight:500;text-transform:none;font-size:11px;">({{ $mcCount }} soal)</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Pilih <strong>satu jawaban paling benar</strong> dari pilihan A, B, C, D, atau E.</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Setiap jawaban benar bernilai <strong>10 poin</strong>.</span></div>
        </div>
        @endif
        @if($hasLikert)
        <div class="instruction-section">
            <div class="instruction-section-title">Bagian B — Sikap <span style="font-weight:500;text-transform:none;font-size:11px;">({{ $likertCount }} pernyataan)</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Pilih tombol yang <strong>paling sesuai</strong> dengan pendapat Anda.</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Tidak ada jawaban benar atau salah.</span></div>
            <div class="likert-legend">
                <span class="likert-chip chip-sts">STS — Sangat Tidak Setuju</span>
                <span class="likert-chip chip-ts">TS — Tidak Setuju</span>
                <span class="likert-chip chip-r">R — Ragu-ragu</span>
                <span class="likert-chip chip-s">S — Setuju</span>
                <span class="likert-chip chip-ss">SS — Sangat Setuju</span>
            </div>
        </div>
        @endif
    </div>
</div>

@if($respondent->child)
<div class="child-summary">
    <div class="child-summary-icon">👶</div>
    <div>
        <div class="child-summary-name">{{ $respondent->child->name }}</div>
        <div class="child-summary-meta">
            Anak ke-{{ $respondent->child->child_order }} ·
            {{ $respondent->child->ageLabel() }} ·
            {{ ucfirst($respondent->child->gender) }} ·
            {{ $respondent->child->weight_kg + 0 }} kg ·
            {{ $respondent->child->height_cm + 0 }} cm
        </div>
    </div>
</div>
@endif

@if($respondent->pre_test_done)
    <div class="done-banner">
        <div class="done-icon">✅</div>
        <div class="done-title">Pre-Test Selesai!</div>
        <div class="done-sub">Jawaban Anda sudah tersimpan.</div>
    </div>
@endif

@if($errors->any())
<div class="error-banner">⚠️ {{ $errors->first() }}</div>
@endif

<div class="progress-wrap">
    <div class="progress-header">
        <span>Progres pengisian</span>
        <span id="progress-label">{{ $answeredCount }}/{{ $totalQuestions }}</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill" id="progress-fill"
             style="width: {{ $totalQuestions > 0 ? ($answeredCount / $totalQuestions * 100) : 0 }}%"></div>
    </div>
</div>

<form method="POST" action="{{ route('respondent.pretest.submit') }}" id="test-form">
    @csrf
    @foreach($questions as $index => $question)
    @php
        $isAnswered = isset($answers[$question->question_id]);
        $isChildQ   = $question->category === 'kondisi_anak';
    @endphp
    @if($question->category !== $prevCategory)
        <div class="section-heading">{{ $isChildQ ? '👶 Kondisi Anak' : '📝 Pengetahuan & Sikap' }}</div>
        @php $prevCategory = $question->category; @endphp
    @endif
    <div class="question-card {{ $isAnswered ? 'answered' : '' }}" id="qcard-{{ $question->question_id }}">

        <div class="question-top">
            <div class="question-meta">
                <span class="question-num">Soal {{ $index + 1 }}</span>
                @if($isChildQ)
                    <span class="question-format-badge badge-child">👶 Kondisi Anak</span>
                @elseif($hasMc && $hasLikert)
                    @if($question->question_format === 'likert')
                        <span class="question-format-badge badge-likert">📊 Sikap</span>
                    @else
                        <span class="question-format-badge badge-mc">🔤 Pengetahuan</span>
                    @endif
                @endif
            </div>
            <div class="question-text">{{ $question->question_text }}</div>
        </div>

        @if($question->question_format === 'likert')
            {{-- ── LIKERT HORIZONTAL ── --}}
            @php
                $sortedOptions = $question->options->sortBy(fn($o) => $likertOrder[$o->label] ?? 99);
                $selectedOption = $answers[$question->question_id] ?? null;
            @endphp

            <div class="likert-scale">
                {{-- Label teks penuh di atas (desktop only) --}}
                <div class="likert-labels">
                    @foreach($sortedOptions as $opt)
                    <div class="likert-label-text">{{ $opt->option_text }}</div>
                    @endforeach
                </div>

                {{-- 5 tombol sejajar --}}
                <div class="likert-grid" id="likert-grid-{{ $question->question_id }}">
                    @foreach($sortedOptions as $opt)
                    @php $isChecked = $selectedOption && $selectedOption->question_option_id == $opt->question_option_id; @endphp
                    <label class="likert-btn {{ $isChecked ? 'selected-'.$opt->label : '' }}"
                           id="likert-btn-{{ $question->question_id }}-{{ $opt->label }}">
                        <input type="radio"
                            name="answers[{{ $question->question_id }}]"
                            value="{{ $opt->question_option_id }}"
                            data-qid="{{ $question->question_id }}"
                            data-label="{{ $opt->label }}"
                            {{ $isChecked ? 'checked' : '' }}
                            {{ $respondent->pre_test_done ? 'disabled' : '' }}
                            onchange="onLikertChange(this)">
                        <div class="likert-btn-inner">
                            <div class="likert-dot-indicator"></div>
                            <span class="likert-abbr">{{ $opt->label }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Skala gradien bawah --}}
                <div class="likert-scale-bar">
                    <span class="likert-scale-left">← Tidak Setuju</span>
                    <div class="likert-scale-line"></div>
                    <span class="likert-scale-right">Setuju →</span>
                </div>
            </div>

        @else
            {{-- ── MC OPTIONS ── --}}
            <div class="options-list">
                @foreach($question->options as $option)
                <label class="option-label">
                    <input type="radio"
                        name="answers[{{ $question->question_id }}]"
                        value="{{ $option->question_option_id }}"
                        {{ isset($answers[$question->question_id]) && $answers[$question->question_id]->question_option_id == $option->question_option_id ? 'checked' : '' }}
                        {{ $respondent->pre_test_done ? 'disabled' : '' }}
                        onchange="autoSave({{ $question->question_id }}, {{ $option->question_option_id }}); markCardAnswered({{ $question->question_id }})">
                    <div class="option-radio"></div>
                    <span>{{ $option->label }}. {{ $option->option_text }}</span>
                </label>
                @endforeach
            </div>
        @endif
    </div>
    @endforeach

    @if(!$respondent->pre_test_done)
    <button type="submit" class="btn-submit">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Selesaikan Pre-Test
    </button>
    @endif
</form>

{{-- RESULT MODAL --}}
@if(session('show_result') && session('result'))
@php $r = session('result'); @endphp
<div class="result-overlay active" id="resultModal">
    <div class="result-box">
        <div class="result-header">
            <div class="result-emoji">🎉</div>
            <div class="result-title">Pre-Test Selesai!</div>
            <div class="result-sub">Berikut hasil jawaban Anda</div>
        </div>
        <div class="result-body">
            @if($r['has_mc'])
            <div class="result-section-title">📝 Pengetahuan (Pilihan Ganda)</div>
            <div class="result-row">
                <span class="result-row-label">✅ Jawaban Benar</span>
                <span class="result-row-value value-correct">{{ $r['mc_correct'] }} / {{ $r['mc_total'] }}</span>
            </div>
            <div class="result-row">
                <span class="result-row-label">❌ Jawaban Salah</span>
                <span class="result-row-value value-wrong">{{ $r['mc_wrong'] }} / {{ $r['mc_total'] }}</span>
            </div>
            <div class="result-row">
                <span class="result-row-label">🏆 Skor</span>
                <span class="result-row-value value-score">{{ $r['mc_score'] }} / {{ $r['mc_max'] }}</span>
            </div>
            @endif
            @if($r['has_likert'])
            <div class="result-section-title">📊 Sikap (Likert)</div>
            <div class="result-row">
                <span class="result-row-label">📋 Jumlah Pernyataan</span>
                <span class="result-row-value value-likert">{{ $r['likert_total'] }}</span>
            </div>
            <div class="result-row">
                <span class="result-row-label">🏆 Total Skor</span>
                <span class="result-row-value value-likert">{{ $r['likert_score'] }} / {{ $r['likert_max'] }}</span>
            </div>
            @endif
            <div class="result-score-big">
                <div class="score-num">{{ $r['mc_score'] + $r['likert_score'] }}</div>
                <div class="score-label">Total Skor dari {{ $r['mc_max'] + $r['likert_max'] }}</div>
            </div>
        </div>
        <div class="result-footer">
            <button class="btn-result-ok" onclick="closeResult()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Oke, Mengerti!
            </button>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const totalQuestions = {{ $totalQuestions }};

// ── Petunjuk toggle ──
function toggleInstruction() {
    const body = document.getElementById('instrBody');
    const btn  = document.getElementById('instrToggleBtn');
    const open = body.classList.contains('open');
    body.classList.toggle('open', !open);
    btn.classList.toggle('open', !open);
}

// ── Likert: saat tombol diklik ──
function onLikertChange(input) {
    const qid   = input.dataset.qid;
    const label = input.dataset.label;
    const optId = input.value;

    // Hapus semua class selected di grid ini
    const grid = document.getElementById('likert-grid-' + qid);
    grid.querySelectorAll('.likert-btn').forEach(function(btn) {
        btn.className = btn.className.replace(/selected-\S+/g, '').trim();
    });

    // Tambah class selected ke tombol yang dipilih
    const parentBtn = input.closest('.likert-btn');
    parentBtn.classList.add('selected-' + label);

    // Mark card answered
    markCardAnswered(qid);

    // Autosave
    autoSave(qid, optId);
}

// ── Mark card border hijau saat sudah dijawab ──
function markCardAnswered(qid) {
    const card = document.getElementById('qcard-' + qid);
    if (card) card.classList.add('answered');
    updateProgress();
}

// ── Progress ──
function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    document.getElementById('progress-fill').style.width = (answered / totalQuestions * 100) + '%';
    document.getElementById('progress-label').textContent = answered + '/' + totalQuestions;
}

// ── Autosave ──
function autoSave(questionId, optionId) {
    fetch('{{ route('respondent.autosave') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            question_id: questionId,
            question_option_id: optionId,
            test_type: 'pre'
        })
    }).then(function() { updateProgress(); });
}

// ── Result modal ──
function closeResult() {
    document.getElementById('resultModal').classList.remove('active');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeResult();
});
</script>
@endpush