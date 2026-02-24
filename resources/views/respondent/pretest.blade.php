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

    .progress-wrap { margin-bottom: 24px; }
    .progress-header { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600; }
    .progress-bar { height: 6px; background: var(--bg); border-radius: 100px; overflow: hidden; border: 1px solid var(--border); }
    .progress-fill { height: 100%; background: var(--primary); border-radius: 100px; transition: width 0.3s ease; }

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
    .done-icon { font-size: 40px; margin-bottom: 12px; }
    .done-title { font-size: 18px; font-weight: 800; color: #065f46; margin-bottom: 6px; }
    .done-sub { font-size: 14px; color: #047857; }
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

@if($respondent->pre_test_done)
    <div class="done-banner">
        <div class="done-icon">✅</div>
        <div class="done-title">Pre-Test Selesai!</div>
        <div class="done-sub">Anda telah menyelesaikan pre-test. Jawaban Anda sudah tersimpan.</div>
    </div>
@endif

@php
    $answeredCount = $answers->count();
    $totalQuestions = $questions->count();
@endphp

<div class="progress-wrap">
    <div class="progress-header">
        <span>Progres pengisian</span>
        <span id="progress-label">{{ $answeredCount }}/{{ $totalQuestions }}</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill" id="progress-fill" style="width: {{ $totalQuestions > 0 ? ($answeredCount / $totalQuestions * 100) : 0 }}%"></div>
    </div>
</div>

<form method="POST" action="{{ route('respondent.pretest.submit') }}" id="test-form">
    @csrf
    @foreach($questions as $index => $question)
    <div class="question-card">
        <div class="question-num">{{ $index + 1 }}</div>
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
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Selesaikan Pre-Test
    </button>
    @endif
</form>
@endsection

@push('scripts')
<script>
const totalQuestions = {{ $totalQuestions }};

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