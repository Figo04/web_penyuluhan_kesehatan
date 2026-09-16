@extends('layouts.app')

@section('title', 'Pre-Test')

@push('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; }
    .page-sub { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

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
    .tab-link.active { background: var(--primary); color: white; }
    .tab-link svg { width: 16px; height: 16px; flex-shrink: 0; }

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
    .likert-legend { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .likert-chip {
        display: flex; align-items: center; gap: 5px;
        padding: 5px 10px; border-radius: 7px; font-size: 12px; font-weight: 700;
    }
    .chip-ss  { background: #d1fae5; color: #065f46; }
    .chip-s   { background: #dbeafe; color: #1e40af; }
    .chip-r   { background: #f3f4f6; color: #6b7280; }
    .chip-ts  { background: #fef3c7; color: #92400e; }
    .chip-sts { background: #fee2e2; color: #991b1b; }

    .progress-wrap { margin-bottom: 20px; }
    .progress-header { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600; }
    .progress-bar { height: 6px; background: var(--bg); border-radius: 100px; overflow: hidden; border: 1px solid var(--border); }
    .progress-fill { height: 100%; background: var(--primary); border-radius: 100px; transition: width 0.3s ease; }

    .question-card {
        background: white; border-radius: 12px; border: 1px solid var(--border);
        padding: 20px; margin-bottom: 16px; box-shadow: var(--shadow);
    }
    .question-num { font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 6px; }
    .question-text { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; line-height: 1.5; }
    .question-format-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; font-weight: 700; padding: 2px 8px;
        border-radius: 100px; margin-bottom: 10px;
    }
    .badge-mc     { background: #f3f4f6; color: #374151; }
    .badge-likert { background: #eff6ff; color: #1d4ed8; }

    .options-list { display: flex; flex-direction: column; gap: 8px; }
    .option-label {
        display: flex; align-items: center; gap: 12px; padding: 13px 16px;
        border-radius: 9px; border: 1.5px solid var(--border);
        cursor: pointer; transition: all 0.15s; font-size: 15px; color: var(--text-dark);
    }
    .option-label:hover { border-color: var(--primary); background: var(--primary-light); }
    .option-label input[type="radio"] { display: none; }
    .option-radio {
        width: 20px; height: 20px; border-radius: 50%; border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.15s;
    }
    .option-label:has(input[type="radio"]:checked) { border-color: var(--primary); background: var(--primary-light); }
    .option-label:has(input[type="radio"]:checked) .option-radio { border-color: var(--primary); background: var(--primary); }
    .option-label:has(input[type="radio"]:checked) .option-radio::after {
        content: ''; width: 8px; height: 8px; background: white; border-radius: 50%; display: block;
    }

    .likert-list { display: flex; flex-direction: column; gap: 8px; }
    .likert-item {
        display: flex; align-items: center; gap: 12px; padding: 14px 16px;
        border-radius: 10px; border: 1.5px solid var(--border);
        cursor: pointer; transition: all 0.15s; background: var(--bg);
    }
    .likert-item input[type="radio"] { display: none; }
    .likert-dot {
        width: 20px; height: 20px; border-radius: 50%; border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; transition: all 0.15s;
    }
    .likert-abbr-text { font-size: 13px; font-weight: 800; min-width: 32px; color: var(--text-muted); flex-shrink: 0; transition: color 0.15s; }
    .likert-full-text { font-size: 14px; color: var(--text-dark); font-weight: 500; transition: color 0.15s; }
    .likert-item:not([data-selected]):hover { border-color: var(--primary); background: var(--primary-light); }
    .likert-item[data-selected] .likert-dot::after { content: ''; width: 7px; height: 7px; background: white; border-radius: 50%; display: block; }
    .likert-item[data-selected="STS"] { background: #fef2f2; border-color: #fca5a5; }
    .likert-item[data-selected="STS"] .likert-dot { background: #ef4444; border-color: #ef4444; }
    .likert-item[data-selected="STS"] .likert-abbr-text { color: #7f1d1d; }
    .likert-item[data-selected="STS"] .likert-full-text { color: #991b1b; }
    .likert-item[data-selected="TS"] { background: #fffbeb; border-color: #fcd34d; }
    .likert-item[data-selected="TS"] .likert-dot { background: #f59e0b; border-color: #f59e0b; }
    .likert-item[data-selected="TS"] .likert-abbr-text { color: #451a03; }
    .likert-item[data-selected="TS"] .likert-full-text { color: #78350f; }
    .likert-item[data-selected="R"] { background: #f9fafb; border-color: #9ca3af; }
    .likert-item[data-selected="R"] .likert-dot { background: #6b7280; border-color: #6b7280; }
    .likert-item[data-selected="R"] .likert-abbr-text { color: #111827; }
    .likert-item[data-selected="R"] .likert-full-text { color: #374151; }
    .likert-item[data-selected="S"] { background: #eff6ff; border-color: #93c5fd; }
    .likert-item[data-selected="S"] .likert-dot { background: #3b82f6; border-color: #3b82f6; }
    .likert-item[data-selected="S"] .likert-abbr-text { color: #1e3a8a; }
    .likert-item[data-selected="S"] .likert-full-text { color: #1d4ed8; }
    .likert-item[data-selected="SS"] { background: #ecfdf5; border-color: #6ee7b7; }
    .likert-item[data-selected="SS"] .likert-dot { background: #10b981; border-color: #10b981; }
    .likert-item[data-selected="SS"] .likert-abbr-text { color: #064e3b; }
    .likert-item[data-selected="SS"] .likert-full-text { color: #047857; }

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

    .btn-submit {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 15px; background: var(--primary); color: white;
        border: none; border-radius: 10px; font-family: inherit; font-size: 16px;
        font-weight: 700; cursor: pointer; transition: all 0.15s; margin-top: 24px;
    }
    .btn-submit:hover { background: var(--primary-dark); }

    /* ── Result Modal ── */
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
    .result-row-label { font-size: 14px; color: var(--text-dark); font-weight: 500; display: flex; align-items: center; gap: 8px; }
    .result-row-value { font-size: 15px; font-weight: 800; }
    .value-correct { color: #059669; }
    .value-wrong   { color: #ef4444; }
    .value-score   { color: var(--primary); }
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
        width: 100%; padding: 13px; background: var(--primary); color: white;
        border: none; border-radius: 10px; font-family: inherit;
        font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.15s;
    }
    .btn-result-ok:hover { background: var(--primary-dark); }
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
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="color:rgba(255,255,255,0.8)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
    $hasMc       = $questions->where('question_format', 'multiple_choice')->count() > 0;
    $hasLikert   = $questions->where('question_format', 'likert')->count() > 0;
    $mcCount     = $questions->where('question_format', 'multiple_choice')->count();
    $likertCount = $questions->where('question_format', 'likert')->count();
    $likertOrder = ['STS' => 1, 'TS' => 2, 'R' => 3, 'S' => 4, 'SS' => 5];
    $answeredCount  = $answers->count();
    $totalQuestions = $questions->count();
@endphp

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
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Pilih yang <strong>paling sesuai</strong> dengan pendapat atau perasaan Anda.</span></div>
            <div class="instruction-item"><div class="instruction-bullet"></div><span>Tidak ada jawaban benar atau salah.</span></div>
            <div class="likert-legend">
                <div class="likert-chip chip-sts">STS = Sangat Tidak Setuju</div>
                <div class="likert-chip chip-ts">TS = Tidak Setuju</div>
                <div class="likert-chip chip-r">R = Ragu-ragu / Netral</div>
                <div class="likert-chip chip-s">S = Setuju</div>
                <div class="likert-chip chip-ss">SS = Sangat Setuju</div>
            </div>
        </div>
        @endif
    </div>
</div>

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
    <div class="question-card">
        @if($hasMc && $hasLikert)
            @if($question->question_format === 'likert')
                <span class="question-format-badge badge-likert">📊 Sikap</span>
            @else
                <span class="question-format-badge badge-mc">🔤 Pengetahuan</span>
            @endif
        @endif
        <div class="question-num">Soal {{ $index + 1 }}</div>
        <div class="question-text">{{ $question->question_text }}</div>

        @if($question->question_format === 'likert')
            @php $sortedOptions = $question->options->sortBy(fn($o) => $likertOrder[$o->label] ?? 99); @endphp
            <div class="likert-list">
                @foreach($sortedOptions as $option)
                @php $isChecked = isset($answers[$question->id]) && $answers[$question->id]->question_option_id == $option->id; @endphp
                <label class="likert-item" {{ $isChecked ? 'data-selected="'.$option->label.'"' : '' }}>
                    <input type="radio"
                        name="answers[{{ $question->id }}]"
                        value="{{ $option->id }}"
                        data-label="{{ $option->label }}"
                        {{ $isChecked ? 'checked' : '' }}
                        {{ $respondent->pre_test_done ? 'disabled' : '' }}
                        onchange="autoSave({{ $question->id }}, {{ $option->id }})">
                    <div class="likert-dot"></div>
                    <span class="likert-abbr-text">{{ $option->label }}</span>
                    <span class="likert-full-text">{{ $option->option_text }}</span>
                </label>
                @endforeach
            </div>
        @else
            <div class="options-list">
                @foreach($question->options as $option)
                <label class="option-label">
                    <input type="radio"
                        name="answers[{{ $question->id }}]"
                        value="{{ $option->id }}"
                        {{ isset($answers[$question->id]) && $answers[$question->id]->question_option_id == $option->id ? 'checked' : '' }}
                        {{ $respondent->pre_test_done ? 'disabled' : '' }}
                        onchange="autoSave({{ $question->id }}, {{ $option->id }})">
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

function toggleInstruction() {
    const body = document.getElementById('instrBody');
    const btn  = document.getElementById('instrToggleBtn');
    const open = body.classList.contains('open');
    body.classList.toggle('open', !open);
    btn.classList.toggle('open', !open);
}

document.querySelectorAll('.likert-item input[type="radio"]').forEach(function(input) {
    input.addEventListener('change', function() {
        const name  = this.getAttribute('name');
        const label = this.dataset.label;
        const item  = this.closest('.likert-item');
        document.querySelectorAll('input[name="' + name + '"]').forEach(function(i) {
            i.closest('.likert-item').removeAttribute('data-selected');
        });
        item.setAttribute('data-selected', label);
    });
});

function autoSave(questionId, optionId) {
    fetch('{{ route('respondent.autosave') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ question_id: questionId, question_option_id: optionId, test_type: 'pre' })
    }).then(function() { updateProgress(); });
}

function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    document.getElementById('progress-fill').style.width = (answered / totalQuestions * 100) + '%';
    document.getElementById('progress-label').textContent = answered + '/' + totalQuestions;
}

function closeResult() {
    document.getElementById('resultModal').classList.remove('active');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeResult();
});
</script>
@endpush