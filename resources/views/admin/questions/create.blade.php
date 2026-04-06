@extends('layouts.admin')

@section('title', 'Tambah Soal')

@push('styles')
<style>
    /* ── Format Toggle ── */
    .format-toggle {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 24px;
    }
    .format-card {
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.18s;
        background: var(--bg);
        text-align: center;
        user-select: none;
    }
    .format-card:hover { border-color: var(--primary); }
    .format-card.active {
        border-color: var(--primary);
        background: #e6faf5;
    }
    .format-card .format-icon {
        font-size: 26px;
        margin-bottom: 6px;
        display: block;
    }
    .format-card .format-title {
        font-weight: 700;
        font-size: 14px;
        color: var(--text-dark);
        margin-bottom: 2px;
    }
    .format-card .format-desc {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.4;
    }
    .format-card.active .format-title { color: #065f46; }

    /* ── MC Options ── */
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

    /* Add option button */
    .btn-add-option {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1.5px dashed var(--border);
        border-radius: 8px;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
        background: transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
        margin-top: 4px;
    }
    .btn-add-option:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    .btn-remove-option {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: 1px solid #fecaca;
        background: transparent;
        color: #ef4444;
        cursor: pointer;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s;
        font-family: inherit;
        line-height: 1;
    }
    .btn-remove-option:hover { background: #fef2f2; }

    /* ── Likert Preview ── */
    .likert-preview {
        border-radius: 10px;
        border: 1.5px solid var(--border);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .likert-preview-header {
        background: var(--bg);
        padding: 12px 16px;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        border-bottom: 1px solid var(--border);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .likert-option-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        border-bottom: 1px solid var(--border);
    }
    .likert-option-row:last-child { border-bottom: none; }
    .likert-badge {
        min-width: 42px;
        padding: 3px 8px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        flex-shrink: 0;
    }
    .likert-badge.ss  { background: #d1fae5; color: #065f46; }
    .likert-badge.s   { background: #dbeafe; color: #1e40af; }
    .likert-badge.r   { background: #f3f4f6; color: #6b7280; }
    .likert-badge.ts  { background: #fef3c7; color: #92400e; }
    .likert-badge.sts { background: #fee2e2; color: #991b1b; }
    .likert-score-badge {
        margin-left: auto;
        font-size: 12px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .score-high { background: #d1fae5; color: #065f46; }
    .score-mid  { background: #f3f4f6; color: #374151; }
    .score-low  { background: #fee2e2; color: #991b1b; }

    /* Favourable toggle */
    .fav-toggle-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 20px;
    }
    .fav-card {
        border: 2px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.15s;
        background: var(--bg);
    }
    .fav-card:hover { border-color: var(--primary); }
    .fav-card.active-fav  { border-color: #10b981; background: #ecfdf5; }
    .fav-card.active-unfav { border-color: #f59e0b; background: #fffbeb; }
    .fav-card .fav-title { font-weight: 700; font-size: 13px; margin-bottom: 2px; }
    .fav-card .fav-sub   { font-size: 12px; color: var(--text-muted); }
    .fav-card.active-fav  .fav-title { color: #065f46; }
    .fav-card.active-unfav .fav-title { color: #92400e; }

    /* Panel visibility */
    .panel-mc, .panel-likert { display: none; }
    .panel-mc.visible, .panel-likert.visible { display: block; }

    @media (max-width: 600px) {
        .format-toggle, .fav-toggle-group { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Tambah Soal</div>
        <div class="page-sub">Buat soal baru untuk pre-test atau post-test</div>
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

    <form method="POST" action="{{ route('admin.questions.store') }}" id="questionForm">
        @csrf

        {{-- Metadata --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Tipe Soal</label>
                <select name="type" class="form-input" required>
                    <option value="pre"  {{ old('type') === 'pre'  ? 'selected' : '' }}>Pre-Test</option>
                    <option value="post" {{ old('type') === 'post' ? 'selected' : '' }}>Post-Test</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Urutan Soal</label>
                <input type="number" name="order" class="form-input" value="{{ old('order', 1) }}" min="1" required>
                <div class="form-hint">Angka lebih kecil tampil lebih dulu</div>
            </div>
        </div>

        {{-- Teks Soal --}}
        <div class="form-group">
            <label class="form-label">Teks Soal / Pernyataan</label>
            <textarea name="question_text" class="form-input" rows="3"
                placeholder="Tulis pertanyaan atau pernyataan di sini..." required>{{ old('question_text') }}</textarea>
        </div>

        {{-- Format Toggle --}}
        <div style="margin-bottom:8px;">
            <label class="form-label">Format Soal</label>
        </div>
        <div class="format-toggle">
            <div class="format-card {{ old('question_format', 'multiple_choice') === 'multiple_choice' ? 'active' : '' }}"
                 onclick="setFormat('multiple_choice')">
                <span class="format-icon">🔤</span>
                <div class="format-title">Pilihan Ganda (A–E)</div>
                <div class="format-desc">Soal pengetahuan dengan satu jawaban benar</div>
            </div>
            <div class="format-card {{ old('question_format') === 'likert' ? 'active' : '' }}"
                 onclick="setFormat('likert')">
                <span class="format-icon">📊</span>
                <div class="format-title">Skala Likert (Sikap)</div>
                <div class="format-desc">SS / S / R / TS / STS — mengukur sikap responden</div>
            </div>
        </div>
        <input type="hidden" name="question_format" id="questionFormat"
               value="{{ old('question_format', 'multiple_choice') }}">

        {{-- ════ Panel: Multiple Choice ════ --}}
        <div class="panel-mc {{ old('question_format', 'multiple_choice') === 'multiple_choice' ? 'visible' : '' }}"
             id="panelMc">

            <div style="margin-bottom:12px;">
                <label class="form-label">Pilihan Jawaban</label>
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">
                    Isi pilihan jawaban (min. 2, maks. 5). Klik lingkaran untuk menandai jawaban <strong>benar</strong>.
                </div>

                <div id="optionsList">
                    @php
                        $oldOptions = old('options', [
                            ['text' => ''], ['text' => ''], ['text' => ''],
                            ['text' => ''], ['text' => ''],
                        ]);
                        $labels = ['A','B','C','D','E'];
                        $oldCorrect = old('correct_option', null);
                    @endphp

                    @foreach($oldOptions as $i => $opt)
                    <div class="option-row" id="optionRow{{ $i }}">
                        <div class="option-label-badge">{{ $labels[$i] }}</div>
                        <input type="text" name="options[{{ $i }}][text]"
                            class="option-text-input"
                            placeholder="Pilihan {{ $labels[$i] }}..."
                            value="{{ $opt['text'] ?? '' }}"
                            {{ $i < 2 ? 'required' : '' }}>
                        <input type="radio" name="correct_option" value="{{ $i }}"
                            id="correct_{{ $i }}"
                            class="correct-radio"
                            {{ $oldCorrect == $i ? 'checked' : '' }}>
                        <label for="correct_{{ $i }}" class="correct-check" title="Tandai jawaban benar"></label>
                        @if($i >= 2)
                        <button type="button" class="btn-remove-option" onclick="removeOption({{ $i }})" title="Hapus pilihan">×</button>
                        @endif
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn-add-option" id="btnAddOption" onclick="addOption()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pilihan
                </button>

                @error('correct_option')
                    <div style="font-size:13px;color:var(--danger);margin-top:8px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:14px 16px;margin-bottom:24px;font-size:13px;color:#166534;">
                💡 <strong>Tips:</strong> Klik pada baris pilihan untuk menandai sebagai jawaban benar. Setiap jawaban benar bernilai <strong>10 poin</strong>.
            </div>
        </div>

        {{-- ════ Panel: Likert ════ --}}
        <div class="panel-likert {{ old('question_format') === 'likert' ? 'visible' : '' }}"
             id="panelLikert">

            {{-- Favourable / Unfavourable --}}
            <div style="margin-bottom:8px;">
                <label class="form-label">Arah Pernyataan</label>
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">
                    Tentukan apakah pernyataan ini mendukung (favourable) atau menentang (unfavourable) pencegahan stunting.
                </div>
            </div>

            <div class="fav-toggle-group">
                <div class="fav-card {{ old('is_favourable', '1') == '1' ? 'active-fav' : '' }}"
                     onclick="setFavourable(true)" id="cardFav">
                    <div class="fav-title">✅ Favourable</div>
                    <div class="fav-sub">Mendukung pencegahan stunting<br>SS=5 · S=4 · R=3 · TS=2 · STS=1</div>
                </div>
                <div class="fav-card {{ old('is_favourable') == '0' ? 'active-unfav' : '' }}"
                     onclick="setFavourable(false)" id="cardUnfav">
                    <div class="fav-title">❌ Unfavourable</div>
                    <div class="fav-sub">Menentang pencegahan stunting<br>SS=1 · S=2 · R=3 · TS=4 · STS=5</div>
                </div>
            </div>
            <input type="hidden" name="is_favourable" id="isFavourable"
                   value="{{ old('is_favourable', '1') }}">

            {{-- Preview Skala --}}
            <div style="margin-bottom:8px;">
                <label class="form-label">Preview Skala Jawaban</label>
            </div>
            <div class="likert-preview" id="likertPreview">
                <div class="likert-preview-header">Pilihan jawaban yang akan tampil ke responden</div>
                <!-- dirender JS -->
            </div>

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:14px 16px;margin-bottom:24px;font-size:13px;color:#1e40af;">
                📊 <strong>Info:</strong> Pilihan jawaban Likert sudah otomatis diisi (STS/TS/R/S/SS). Skor dihitung otomatis sesuai arah pernyataan.
            </div>
        </div>

        {{-- Submit --}}
        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary" style="width:auto;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Simpan Soal
            </button>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// ── State ──
let currentFormat = '{{ old('question_format', 'multiple_choice') }}';
let isFavourable  = {{ old('is_favourable', '1') == '1' ? 'true' : 'false' }};
let optionCount   = 5; // total opsi MC yang ada
const MAX_OPTIONS = 5;
const labels      = ['A','B','C','D','E'];

// ── Format Toggle ──
function setFormat(format) {
    currentFormat = format;
    document.getElementById('questionFormat').value = format;

    document.querySelectorAll('.format-card').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.format-card')[format === 'multiple_choice' ? 0 : 1].classList.add('active');

    document.getElementById('panelMc').classList.toggle('visible', format === 'multiple_choice');
    document.getElementById('panelLikert').classList.toggle('visible', format === 'likert');

    // Disable/enable MC inputs saat tidak dipakai
    toggleMcInputs(format === 'multiple_choice');
}

function toggleMcInputs(enabled) {
    document.querySelectorAll('#optionsList input').forEach(el => {
        el.disabled = !enabled;
    });
}

// ── Favourable Toggle ──
function setFavourable(fav) {
    isFavourable = fav;
    document.getElementById('isFavourable').value = fav ? '1' : '0';

    document.getElementById('cardFav').className    = 'fav-card' + (fav ? ' active-fav' : '');
    document.getElementById('cardUnfav').className  = 'fav-card' + (!fav ? ' active-unfav' : '');

    renderLikertPreview();
}

// ── Likert Preview ──
const LIKERT = [
    { label:'STS', text:'Sangat Tidak Setuju', cls:'sts' },
    { label:'TS',  text:'Tidak Setuju',        cls:'ts'  },
    { label:'R',   text:'Ragu-ragu / Netral',  cls:'r'   },
    { label:'S',   text:'Setuju',              cls:'s'   },
    { label:'SS',  text:'Sangat Setuju',       cls:'ss'  },
];

function renderLikertPreview() {
    const favScores   = [1,2,3,4,5];
    const unfavScores = [5,4,3,2,1];
    const scores      = isFavourable ? favScores : unfavScores;

    let html = '';
    LIKERT.forEach((opt, i) => {
        const score = scores[i];
        const scoreCls = score >= 4 ? 'score-high' : score == 3 ? 'score-mid' : 'score-low';
        html += `
        <div class="likert-option-row">
            <span class="likert-badge ${opt.cls}">${opt.label}</span>
            <span style="font-size:14px;color:var(--text-dark);">${opt.text}</span>
            <span class="likert-score-badge ${scoreCls}">Skor ${score}</span>
        </div>`;
    });
    document.getElementById('likertPreview').innerHTML =
        '<div class="likert-preview-header">Pilihan jawaban yang akan tampil ke responden</div>' + html;
}

// ── MC: Add/Remove Option ──
function addOption() {
    if (optionCount >= MAX_OPTIONS) return;
    const idx  = optionCount;
    const lbl  = labels[idx];
    const list = document.getElementById('optionsList');

    const row = document.createElement('div');
    row.className = 'option-row';
    row.id = `optionRow${idx}`;
    row.innerHTML = `
        <div class="option-label-badge">${lbl}</div>
        <input type="text" name="options[${idx}][text]"
            class="option-text-input"
            placeholder="Pilihan ${lbl}...">
        <input type="radio" name="correct_option" value="${idx}"
            id="correct_${idx}" class="correct-radio">
        <label for="correct_${idx}" class="correct-check" title="Tandai jawaban benar"></label>
        <button type="button" class="btn-remove-option" onclick="removeOption(${idx})" title="Hapus pilihan">×</button>
    `;
    list.appendChild(row);
    optionCount++;

    if (optionCount >= MAX_OPTIONS) {
        document.getElementById('btnAddOption').style.display = 'none';
    }
}

function removeOption(idx) {
    const row = document.getElementById(`optionRow${idx}`);
    if (row) row.remove();
    optionCount--;
    document.getElementById('btnAddOption').style.display = '';
}

// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
    renderLikertPreview();
    setFormat(currentFormat);

    // Jaga required hanya untuk 2 opsi pertama saat format MC
    // Opsi 3-5 optional
});
</script>
@endpush
@endsection