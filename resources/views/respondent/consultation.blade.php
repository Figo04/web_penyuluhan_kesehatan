@extends('layouts.app')

@section('title', 'Tanya Dokter')

@push('styles')
<style>
    .page-header { margin-bottom: 28px; }
    .page-title  { font-size: 24px; font-weight: 800; }
    .page-sub    { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

    .consult-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }

    /* ── FORM CARD ── */
    .form-card {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 24px;
        position: sticky;
        top: 80px;
    }
    .form-card-title {
        font-size: 16px; font-weight: 800;
        color: var(--text-dark); margin-bottom: 4px;
        display: flex; align-items: center; gap: 8px;
    }
    .form-card-sub { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

    .consult-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        font-family: inherit;
        font-size: 14px;
        color: var(--text-dark);
        background: var(--bg);
        resize: vertical;
        outline: none;
        transition: border-color 0.15s;
        min-height: 120px;
    }
    .consult-textarea:focus { border-color: var(--primary); background: white; }
    .consult-textarea::placeholder { color: var(--text-light); }

    .char-count { font-size: 12px; color: var(--text-light); margin-top: 6px; text-align: right; }

    .btn-submit-consult {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; margin-top: 16px;
        padding: 13px 20px;
        background: var(--primary); color: #111827;
        border: none; border-radius: 9px;
        font-family: inherit; font-size: 15px; font-weight: 700;
        cursor: pointer; transition: background 0.15s;
    }
    .btn-submit-consult:hover { background: var(--primary-dark); }

    /* Info box */
    .info-box {
        margin-top: 16px;
        padding: 12px 14px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 9px;
        font-size: 12px; color: #1e40af; line-height: 1.6;
    }
    .info-box strong { font-weight: 700; }

    /* ── RIWAYAT ── */
    .history-section { }

    .history-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px;
    }
    .history-title { font-size: 16px; font-weight: 800; color: var(--text-dark); }
    .history-count { font-size: 13px; color: var(--text-muted); }

    .consult-list { display: flex; flex-direction: column; gap: 14px; }

    .consult-item {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .consult-item.answered { border-color: #a7f3d0; }

    .consult-item-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px;
        background: #fafafa;
        border-bottom: 1px solid var(--border);
    }
    .consult-item-meta { display: flex; align-items: center; gap: 8px; }
    .consult-date { font-size: 12px; color: var(--text-muted); }

    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 100px;
        font-size: 11px; font-weight: 700;
    }
    .status-badge.pending  { background: #fef3c7; color: #92400e; }
    .status-badge.answered { background: #ecfdf5; color: #065f46; }

    .consult-item-body { padding: 14px 16px; }

    .consult-question-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 6px;
    }
    .consult-question-text {
        font-size: 14px; color: var(--text-dark); line-height: 1.6;
        background: var(--bg); padding: 10px 12px;
        border-radius: 8px; margin-bottom: 12px;
    }

    .consult-reply {
        padding: 12px 14px;
        background: #f0fdf4;
        border-left: 3px solid var(--primary);
        border-radius: 0 8px 8px 0;
    }
    .consult-reply-label {
        font-size: 11px; font-weight: 800; color: #065f46;
        margin-bottom: 5px;
        display: flex; align-items: center; gap: 5px;
    }
    .consult-reply-text { font-size: 14px; color: #065f46; line-height: 1.6; }

    .consult-pending-info {
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; color: #92400e;
        background: #fffbeb; padding: 10px 12px;
        border-radius: 8px;
    }

    /* Empty state */
    .empty-state {
        padding: 48px 24px; text-align: center;
        background: white; border-radius: 14px;
        border: 1px solid var(--border);
        color: var(--text-muted);
    }
    .empty-state svg { display: block; margin: 0 auto 12px; opacity: 0.3; }
    .empty-state-title { font-weight: 700; font-size: 15px; margin-bottom: 4px; }
    .empty-state-sub { font-size: 13px; }

    @media (max-width: 768px) {
        .consult-layout { grid-template-columns: 1fr; }
        .form-card { position: static; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">Tanya Dokter</div>
    <div class="page-sub">Kirim pertanyaan seputar kesehatan kepada tim dokter kami</div>
</div>

<div class="consult-layout">

    {{-- FORM KIRIM PERTANYAAN --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Kirim Pertanyaan
        </div>
        <div class="form-card-sub">Pertanyaan akan dijawab oleh tim dokter Poltekkes</div>

        <form method="POST" action="{{ route('respondent.consultation.store') }}">
            @csrf

            <div class="form-group" style="margin-bottom:0;">
                <textarea
                    name="question_text"
                    id="questionText"
                    class="consult-textarea"
                    placeholder="Tulis pertanyaan Anda di sini... Contoh: Apa saja makanan yang baik untuk mencegah stunting pada anak usia 6 bulan?"
                    maxlength="2000"
                    oninput="updateCharCount(this)">{{ old('question_text') }}</textarea>
                @error('question_text')
                    <div class="form-error" style="margin-top:4px;">{{ $message }}</div>
                @enderror
                <div class="char-count"><span id="charCount">0</span> / 2000 karakter</div>
            </div>

            <button type="submit" class="btn-submit-consult">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Kirim Pertanyaan
            </button>
        </form>

        <div class="info-box">
            💡 <strong>Info:</strong> Pertanyaan bersifat umum seputar kesehatan dan pencegahan stunting.
            Tim dokter akan berusaha menjawab secepatnya. Kembali ke halaman ini untuk melihat jawaban.
        </div>
    </div>

    {{-- RIWAYAT KONSULTASI --}}
    <div class="history-section">
        <div class="history-header">
            <div class="history-title">Riwayat Pertanyaan Saya</div>
            <div class="history-count">{{ $consultations->count() }} pertanyaan</div>
        </div>

        @if($consultations->count() > 0)
        <div class="consult-list">
            @foreach($consultations as $consult)
            <div class="consult-item {{ $consult->status }}">
                <div class="consult-item-header">
                    <div class="consult-item-meta">
                        <span class="status-badge {{ $consult->status }}">
                            @if($consult->isPending())
                                ⏳ Menunggu Jawaban
                            @else
                                ✅ Sudah Dijawab
                            @endif
                        </span>
                    </div>
                    <span class="consult-date">{{ $consult->created_at->format('d M Y, H:i') }}</span>
                </div>

                <div class="consult-item-body">
                    <div class="consult-question-label">Pertanyaan Anda</div>
                    <div class="consult-question-text">{{ $consult->question_text }}</div>

                    @if($consult->isAnswered())
                    <div class="consult-reply">
                        <div class="consult-reply-label">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Tim Dokter · {{ $consult->replied_at?->format('d M Y, H:i') }}
                        </div>
                        <div class="consult-reply-text">{{ $consult->admin_reply }}</div>
                    </div>
                    @else
                    <div class="consult-pending-info">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pertanyaan Anda sedang menunggu jawaban dari tim dokter.
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="empty-state-title">Belum ada pertanyaan</div>
            <div class="empty-state-sub">Kirim pertanyaan pertama Anda kepada tim dokter.</div>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function updateCharCount(el) {
    document.getElementById('charCount').textContent = el.value.length;
}
// Init saat load
document.addEventListener('DOMContentLoaded', () => {
    const ta = document.getElementById('questionText');
    if (ta) updateCharCount(ta);
});
</script>
@endpush