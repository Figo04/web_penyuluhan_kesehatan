@extends('layouts.app')

@section('title', 'Post-Test')

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

    /* ── Petunjuk ── */
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

    /* ── Progress ── */
    .progress-wrap { margin-bottom: 20px; }
    .progress-header { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600; }
    .progress-bar { height: 6px; background: var(--bg); border-radius: 100px; overflow: hidden; border: 1px solid var(--border); }
    .progress-fill { height: 100%; background: var(--primary); border-radius: 100px; transition: width 0.3s ease; }

    /* ── Question Card ── */
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

    /* ── MC Options ── */
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

    /* ── Likert Options: vertikal berwarna ── */
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
    .likert-abbr-text {
        font-size: 13px; font-weight: 800; min-width: 32px;
        color: var(--text-muted); flex-shrink: 0; transition: color 0.15s;
    }
    .likert-full-text { font-size: 14px; color: var(--text-dark); font-weight: 500; transition: color 0.15s; }

    .likert-item:not(:has(input:checked)):hover { border-color: var(--primary); background: var(--primary-light); }
    .likert-item:has(input:checked) .likert-dot::after {
        content: ''; width: 7px; height: 7px; background: white; border-radius: 50%; display: block;
    }

    .likert-item:has(input[value="STS"]:checked) { background: #fef2f2; border-color: #fca5a5; }
    .likert-item:has(input[value="STS"]:checked) .likert-dot { background: #ef4444; border-color: #ef4444; }
    .likert-item:has(input[value="STS"]:checked) .likert-abbr-text { color: #7f1d1d; }
    .likert-item:has(input[value="STS"]:checked) .likert-full-text { color: #991b1b; }

    .likert-item:has(input[value="TS"]:checked) { background: #fffbeb; border-color: #fcd34d; }
    .likert-item:has(input[value="TS"]:checked) .likert-dot { background: #f59e0b; border-color: #f59e0b; }
    .likert-item:has(input[value="TS"]:checked) .likert-abbr-text { color: #451a03; }
    .likert-item:has(input[value="TS"]:checked) .likert-full-text { color: #78350f; }

    .likert-item:has(input[value="R"]:checked) { background: #f9fafb; border-color: #9ca3af; }
    .likert-item:has(input[value="R"]:checked) .likert-dot { background: #6b7280; border-color: #6b7280; }
    .likert-item:has(input[value="R"]:checked) .likert-abbr-text { color: #111827; }
    .likert-item:has(input[value="R"]:checked) .likert-full-text { color: #374151; }

    .likert-item:has(input[value="S"]:checked) { background: #eff6ff; border-color: #93c5fd; }
    .likert-item:has(input[value="S"]:checked) .likert-dot { background: #3b82f6; border-color: #3b82f6; }
    .likert-item:has(input[value="S"]:checked) .likert-abbr-text { color: #1e3a8a; }
    .likert-item:has(input[value="S"]:checked) .likert-full-text { color: #1d4ed8; }

    .likert-item:has(input[value="SS"]:checked) { background: #ecfdf5; border-color: #6ee7b7; }
    .likert-item:has(input[value="SS"]:checked) .likert-dot { background: #10b981; border-color: #10b981; }
    .likert-item:has(input[value="SS"]:checked) .likert-abbr-text { color: #064e3b; }
    .likert-item:has(input[value="SS"]:checked) .likert-full-text { color: #047857; }

    /* Lock & Done banners */
    .lock-banner {
        background: #fffbeb; border: 1px solid #fde68a;
        border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px;
    }
    .lock-icon  { font-size: 40px; margin-bottom: 12px; }
    .lock-title { font-size: 17px; font-weight: 800; color: #92400e; margin-bottom: 6px; }
    .lock-sub   { font-size: 14px; color: #b45309; margin-bottom: 16px; }
    .lock-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 20px; background: #f59e0b; color: white;
        border-radius: 8px; font-weight: 700; font-size: 14px;
        text-decoration: none; transition: background 0.15s;
    }
    .lock-btn:hover { background: #d97706; }

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
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">Buku Kerja</div>
    <div class="page-sub">Kerjakan Pre-Test dan Post-Test di sini</div>
</div>

<div class="tab-group">
    <a href="{{ route('respondent.pretest') }}" class="tab-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Pre-Test
        @if($respondent->pre_test_done)
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="color:var(--success)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
    </a>
    <a href="{{ route('respondent.posttest') }}" class="tab-link active">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Post-Test
        @if($respondent->post_test_done)
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="color:rgba(255,255,255,0.8)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
    </a>
</div>

@if(!$respondent->pre_test_done)
    <div class="lock-banner">
        <div class="lock-icon">🔒</div>
        <div class="lock-title">Post-Test Belum Bisa Diakses</div>
        <div class="lock-sub">Selesaikan Pre-Test terlebih dahulu sebelum mengerjakan Post-Test.</div>
        <a href="{{ route('respondent.pretest') }}" class="lock-btn">
            Kerjakan Pre-Test Dulu
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
@else

    @if($respondent->post_test_done)
        <div class="done-banner">
            <div class="done-icon">✅</div>
            <div class="done-title">Post-Test Selesai!</div>
            <div class="done-sub">Program penyuluhan telah selesai! 🎉</div>
        </div>
    @endif

    @php
        $hasMc       = $questions->where('question_format', 'multiple_choice')->count() > 0;
        $hasLikert   = $questions->where('question_format', 'likert')->count() > 0;
        $mcCount     = $questions->where('question_format', 'multiple_choice')->count();
        $likertCount = $questions->where('question_format', 'likert')->count();
        $likertOrder = ['STS' => 1, 'TS' => 2, 'R' => 3, 'S' => 4, 'SS' => 5];
        $answeredCount  = $answers->count();
        $totalQuestions = $questions->count();
    @endphp

    {{-- Petunjuk --}}
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
                <div class="instruction-item"><div class="instruction-bullet"></div><span>Pastikan semua soal dijawab sebelum menekan <strong>Selesaikan Post-Test</strong>.</span></div>
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

    <form method="POST" action="{{ route('respondent.posttest.submit') }}" id="test-form">
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
                    <label class="likert-item">
                        <input type="radio"
                            name="answers[{{ $question->id }}]"
                            value="{{ $option->label }}"
                            {{ isset($answers[$question->id]) && $answers[$question->id]->question_option_id == $option->id ? 'checked' : '' }}
                            {{ $respondent->post_test_done ? 'disabled' : '' }}
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
                            {{ $respondent->post_test_done ? 'disabled' : '' }}
                            onchange="autoSave({{ $question->id }}, {{ $option->id }})">
                        <div class="option-radio"></div>
                        <span>{{ $option->label }}. {{ $option->option_text }}</span>
                    </label>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach

        @if(!$respondent->post_test_done)
        <button type="submit" class="btn-submit">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Selesaikan Post-Test
        </button>
        @endif
    </form>

@endif
@endsection

@push('scripts')
<script>
const totalQuestions = {{ isset($totalQuestions) ? $totalQuestions : 0 }};

function toggleInstruction() {
    const body = document.getElementById('instrBody');
    const btn  = document.getElementById('instrToggleBtn');
    const open = body.classList.contains('open');
    body.classList.toggle('open', !open);
    btn.classList.toggle('open', !open);
}

function autoSave(questionId, optionId) {
    fetch('{{ route('respondent.autosave') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ question_id: questionId, question_option_id: optionId, test_type: 'post' })
    }).then(() => updateProgress());
}

function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    const fill  = document.getElementById('progress-fill');
    const label = document.getElementById('progress-label');
    if (fill)  fill.style.width = (answered / totalQuestions * 100) + '%';
    if (label) label.textContent = answered + '/' + totalQuestions;
}
</script>
@endpush