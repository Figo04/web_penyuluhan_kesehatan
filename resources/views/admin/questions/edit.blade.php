@extends('layouts.admin')

@section('title', 'Edit Soal')

@push('styles')
<style>
    .option-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 9px;
        border: 1.5px solid var(--border);
        margin-bottom: 10px;
        background: var(--bg);
        transition: all 0.15s;
    }
    .option-row:has(input[type="radio"]:checked) {
        border-color: var(--primary);
        background: #e8f5f0;
    }
    .option-label-badge {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: white;
        border: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .option-row:has(input[type="radio"]:checked) .option-label-badge {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    .option-text-input {
        flex: 1;
        border: none;
        background: transparent;
        font-family: inherit;
        font-size: 15px;
        color: var(--text-dark);
        outline: none;
    }
    .correct-radio { display: none; }
    .correct-check {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .option-row:has(input[type="radio"]:checked) .correct-check {
        background: var(--primary);
        border-color: var(--primary);
    }
    .option-row:has(input[type="radio"]:checked) .correct-check::after {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Edit Soal</div>
        <div class="page-sub">Perbarui soal yang sudah ada</div>
    </div>
    <a href="{{ route('admin.questions.index') }}" class="btn btn-outline">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="card" style="padding:32px;max-width:720px;">
    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;">
        <strong>Ada kesalahan:</strong>
        <ul style="margin-top:6px;padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.questions.update', $question) }}">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Tipe Soal</label>
                <select name="type" class="form-input" required>
                    <option value="pre"  {{ old('type', $question->type) === 'pre'  ? 'selected' : '' }}>Pre-Test</option>
                    <option value="post" {{ old('type', $question->type) === 'post' ? 'selected' : '' }}>Post-Test</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Urutan Soal</label>
                <input type="number" name="order" class="form-input"
                    value="{{ old('order', $question->order) }}" min="1" required>
                <div class="form-hint">Angka lebih kecil tampil lebih dulu</div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Teks Soal / Pertanyaan</label>
            <textarea name="question_text" class="form-input" rows="3"
                placeholder="Tulis pertanyaan di sini..." required>{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        <div style="margin-bottom:24px;">
            <label class="form-label">Pilihan Jawaban</label>
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">
                Edit pilihan jawaban, lalu klik lingkaran di kanan untuk menandai jawaban yang <strong>benar</strong>.
            </div>

            @php
                $labels  = ['A', 'B', 'C', 'D'];
                $options = $question->options->sortBy('label')->values();
            @endphp

            @foreach($labels as $i => $label)
            @php $opt = $options[$i] ?? null; @endphp
            <label class="option-row" for="correct_{{ $i }}">
                <div class="option-label-badge">{{ $label }}</div>
                <input type="text" name="options[{{ $i }}][text]"
                    class="option-text-input"
                    placeholder="Pilihan {{ $label }}..."
                    value="{{ old('options.' . $i . '.text', $opt ? $opt->option_text : '') }}"
                    required>
                <input type="radio" name="correct_option" value="{{ $i }}"
                    id="correct_{{ $i }}"
                    class="correct-radio"
                    {{ old('correct_option', $opt && $opt->is_correct ? $i : null) == $i ? 'checked' : '' }}>
                <div class="correct-check" title="Tandai sebagai jawaban benar"></div>
            </label>
            @endforeach

            @error('correct_option')
                <div style="font-size:13px;color:var(--danger);margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:14px 16px;margin-bottom:24px;font-size:13px;color:#166534;">
            💡 <strong>Tips:</strong> Klik pada baris pilihan untuk menandai sebagai jawaban benar. Pilihan yang benar akan berwarna hijau.
        </div>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary" style="width:auto;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection