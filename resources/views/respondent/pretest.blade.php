@extends('layouts.app')

@section('title', 'Pre-Test')

@push('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; }
    .page-sub { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

    .tab-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 6px;
        margin-bottom: 24px;
        gap: 4px;
        box-shadow: var(--shadow);
    }

    .tab-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        transition: all 0.15s;
    }
    .tab-link.active { background: var(--primary); color: white; }
    .tab-link svg { width: 16px; height: 16px; flex-shrink: 0; }

    /* ── Petunjuk Pengisian ── */
    .instruction-box {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    .instruction-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: #f0fdf9;
        border-bottom: 1px solid #d1fae5;
        cursor: pointer;
        user-select: none;
    }
    .instruction-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .instruction-header-left span {
        font-size: 14px;
        font-weight: 700;
        color: #065f46;
    }
    .instruction-icon {
        width: 32px;
        height: 32px;
        background: #d1fae5;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .instruction-toggle {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: #d1fae5;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .instruction-toggle svg {
        width: 14px;
        height: 14px;
        color: #065f46;
        transition: transform 0.25s;
    }
    .instruction-toggle.open svg { transform: rotate(180deg); }

    .instruction-body {
        padding: 18px;
        display: none;
    }
    .instruction-body.open { display: block; }

    .instruction-section {
        margin-bottom: 16px;
    }
    .instruction-section:last-child { margin-bottom: 0; }
    .instruction-section-title {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 8px;
    }
    .instruction-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 13.5px;
        color: var(--text-dark);
        line-height: 1.5;
        margin-bottom: 6px;
    }
    .instruction-item:last-child { margin-bottom: 0; }
    .instruction-bullet {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--primary);
        flex-shrink: 0;
        margin-top: 7px;
    }

    /* Likert scale legend */
    .likert-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }
    .likert-chip {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
    }
    .likert-chip .chip-abbr { font-weight: 800; }
    .likert-chip .chip-eq   { color: inherit; opacity: 0.5; font-weight: 400; }
    .chip-ss  { background: #d1fae5; color: #065f46; }
    .chip-s   { background: #dbeafe; color: #1e40af; }
    .chip-r   { background: #f3f4f6; color: #6b7280; }
    .chip-ts  { background: #fef3c7; color: #92400e; }
    .chip-sts { background: #fee2e2; color: #991b1b; }

    .score-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
        margin-top: 8px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border);
    }
    .score-table th {
        background: var(--bg);
        padding: 7px 10px;
        font-weight: 700;
        color: var(--text-muted);
        text-align: center;
        border-bottom: 1px solid var(--border);
    }
    .score-table td {
        padding: 6px 10px;
        text-align: center;
        border-bottom: 1px solid var(--border);
        color: var(--text-dark);
    }
    .score-table tr:last-child td { border-bottom: none; }
    .score-table td:first-child { font-weight: 700; }

    /* ── Progress ── */
    .progress-wrap { margin-bottom: 20px; }
    .progress-header { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600; }
    .progress-bar { height: 6px; background: var(--bg); border-radius: 100px; overflow: hidden; border: 1px solid var(--border); }
    .progress-fill { height: 100%; background: var(--primary); border-radius: 100px; transition: width 0.3s ease; }

    /* ── Question Cards ── */
    .question-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 24px;
        margin-bottom: 16px;
        box-shadow: var(--shadow);
    }

    .question-num { font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 10px; }
    .question-text { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; line-height: 1.5; }

    .options-list { display: flex; flex-direction: column; gap: 8px; }

    .option-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 16px;
        border-radius: 9px;
        border: 1.5px solid var(--border);
        cursor: pointer;
        transition: all 0.15s;
        font-size: 15px;
        color: var(--text-dark);
    }
    .option-label:hover { border-color: var(--primary); background: var(--primary-light); }
    .option-label input[type="radio"] { display: none; }

    .option-radio {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s;
    }

    .option-label:has(input[type="radio"]:checked) {
        border-color: var(--primary);
        background: var(--primary-light);
    }
    .option-label:has(input[type="radio"]:checked) .option-radio {
        border-color: var(--primary);
        background: var(--primary);
    }
    .option-label:has(input[type="radio"]:checked) .option-radio::after {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        display: block;
    }

    /* Badge format soal di dalam question-card */
    .question-format-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 100px;
        margin-bottom: 10px;
    }
    .badge-mc     { background: #f3f4f6; color: #374151; }
    .badge-likert { background: #eff6ff; color: #1d4ed8; }

    .btn-submit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 15px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-family: inherit;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
        margin-top: 24px;
    }
    .btn-submit:hover { background: var(--primary-dark); }

    .done-banner {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        margin-bottom: 24px;
    }
    .done-icon  { font-size: 40px; margin-bottom: 12px; }
    .done-title { font-size: 18px; font-weight: 800; color: #065f46; margin-bottom: 6px; }
    .done-sub   { font-size: 14px; color: #047857; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">Buku Kerja</div>
    <div class="page-sub">Kerjakan Pre-Test dan Post-Test di sini</div>
</div>

{{-- Tab Pre / Post --}}
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

{{-- ═══ Petunjuk Pengisian ═══ --}}
@php
    $hasMc     = $questions->where('question_format', 'multiple_choice')->count() > 0;
    $hasLikert = $questions->where('question_format', 'likert')->count() > 0;
    $mcCount     = $questions->where('question_format', 'multiple_choice')->count();
    $likertCount = $questions->where('question_format', 'likert')->count();
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

        {{-- Umum --}}
        <div class="instruction-section">
            <div class="instruction-section-title">Umum</div>
            <div class="instruction-item">
                <div class="instruction-bullet"></div>
                <span>Baca setiap soal dengan teliti sebelum menjawab.</span>
            </div>
            <div class="instruction-item">
                <div class="instruction-bullet"></div>
                <span>Jawaban tersimpan otomatis setiap kali Anda memilih pilihan.</span>
            </div>
            <div class="instruction-item">
                <div class="instruction-bullet"></div>
                <span>Pastikan semua soal telah dijawab sebelum menekan tombol <strong>Selesaikan Pre-Test</strong>.</span>
            </div>
        </div>

        {{-- Bagian A: Pilihan Ganda --}}
        @if($hasMc)
        <div class="instruction-section">
            <div class="instruction-section-title">
                Bagian A — Pengetahuan
                <span style="font-weight:500;text-transform:none;font-size:11px;">({{ $mcCount }} soal)</span>
            </div>
            <div class="instruction-item">
                <div class="instruction-bullet"></div>
                <span>Pilihlah <strong>satu jawaban yang paling benar</strong> dari pilihan A, B, C, D, atau E.</span>
            </div>
            <div class="instruction-item">
                <div class="instruction-bullet"></div>
                <span>Setiap jawaban benar bernilai <strong>10 poin</strong>.</span>
            </div>
        </div>
        @endif

        {{-- Bagian B: Likert --}}
        @if($hasLikert)
        <div class="instruction-section">
            <div class="instruction-section-title">
                Bagian B — Sikap
                <span style="font-weight:500;text-transform:none;font-size:11px;">({{ $likertCount }} pernyataan)</span>
            </div>
            <div class="instruction-item">
                <div class="instruction-bullet"></div>
                <span>Pilihlah jawaban yang <strong>paling sesuai</strong> dengan pendapat atau perasaan Anda.</span>
            </div>
            <div class="instruction-item">
                <div class="instruction-bullet"></div>
                <span>Tidak ada jawaban benar atau salah — jawab sesuai kondisi Anda yang sesungguhnya.</span>
            </div>

            {{-- Chip keterangan singkatan --}}
            <div class="likert-legend">
                <div class="likert-chip chip-ss">
                    <span class="chip-abbr">SS</span>
                    <span class="chip-eq">=</span>
                    <span>Sangat Setuju</span>
                </div>
                <div class="likert-chip chip-s">
                    <span class="chip-abbr">S</span>
                    <span class="chip-eq">=</span>
                    <span>Setuju</span>
                </div>
                <div class="likert-chip chip-r">
                    <span class="chip-abbr">R</span>
                    <span class="chip-eq">=</span>
                    <span>Ragu-ragu / Netral</span>
                </div>
                <div class="likert-chip chip-ts">
                    <span class="chip-abbr">TS</span>
                    <span class="chip-eq">=</span>
                    <span>Tidak Setuju</span>
                </div>
                <div class="likert-chip chip-sts">
                    <span class="chip-abbr">STS</span>
                    <span class="chip-eq">=</span>
                    <span>Sangat Tidak Setuju</span>
                </div>
            </div>

            {{-- Tabel skor --}}
            <table class="score-table" style="margin-top:12px;">
                <thead>
                    <tr>
                        <th>Pilihan</th>
                        <th>Pernyataan Positif (Favourable)</th>
                        <th>Pernyataan Negatif (Unfavourable)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>SS</td><td>5</td><td>1</td></tr>
                    <tr><td>S</td><td>4</td><td>2</td></tr>
                    <tr><td>R</td><td>3</td><td>3</td></tr>
                    <tr><td>TS</td><td>2</td><td>4</td></tr>
                    <tr><td>STS</td><td>1</td><td>5</td></tr>
                </tbody>
            </table>
        </div>
        @endif

    </div>{{-- /instruction-body --}}
</div>{{-- /instruction-box --}}

{{-- Done banner --}}
@if($respondent->pre_test_done)
    <div class="done-banner">
        <div class="done-icon">✅</div>
        <div class="done-title">Pre-Test Selesai!</div>
        <div class="done-sub">Anda telah menyelesaikan pre-test. Jawaban Anda sudah tersimpan.</div>
    </div>
@endif

{{-- Progress --}}
@php
    $answeredCount  = $answers->count();
    $totalQuestions = $questions->count();
@endphp

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

{{-- Soal --}}
<form method="POST" action="{{ route('respondent.pretest.submit') }}" id="test-form">
    @csrf
    @foreach($questions as $index => $question)
    <div class="question-card">

        {{-- Badge format --}}
        @if($hasMc && $hasLikert)
            @if($question->question_format === 'likert')
                <span class="question-format-badge badge-likert">📊 Sikap (Likert)</span>
            @else
                <span class="question-format-badge badge-mc">🔤 Pengetahuan</span>
            @endif
        @endif

        <div class="question-num">Soal {{ $index + 1 }}</div>
        <div class="question-text">{{ $question->question_text }}</div>
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
@endsection

@push('scripts')
<script>
const totalQuestions = {{ $totalQuestions }};

// ── Toggle petunjuk ──
function toggleInstruction() {
    const body   = document.getElementById('instrBody');
    const btn    = document.getElementById('instrToggleBtn');
    const isOpen = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    btn.classList.toggle('open', !isOpen);
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
    }).then(() => updateProgress());
}

function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    document.getElementById('progress-fill').style.width = (answered / totalQuestions * 100) + '%';
    document.getElementById('progress-label').textContent = answered + '/' + totalQuestions;
}
</script>
@endpush