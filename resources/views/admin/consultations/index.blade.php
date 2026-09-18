@extends('layouts.admin')

@section('title', 'Kelola Konsultasi')

@push('styles')
<style>
    .stats-mini {
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: 16px; margin-bottom: 24px;
    }
    .stat-mini-card {
        background: white; border-radius: 12px;
        border: 1px solid var(--border); padding: 18px 20px;
        display: flex; align-items: center; gap: 14px;
    }
    .stat-mini-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-mini-label { font-size: 13px; color: var(--text-muted); }
    .stat-mini-number { font-size: 26px; font-weight: 800; color: var(--text-dark); line-height: 1; }

    .consult-card {
        border: 1px solid var(--border); border-radius: 12px;
        overflow: hidden; margin-bottom: 16px; background: white;
    }
    .consult-card.answered { border-color: #a7f3d0; }

    .consult-header {
        padding: 14px 20px;
        background: #fafafa; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
    }
    .consult-author { display: flex; align-items: center; gap: 10px; }
    .consult-avatar {
        width: 32px; height: 32px; border-radius: 8px;
        background: var(--primary); display: flex; align-items: center;
        justify-content: center; font-weight: 800; font-size: 13px; color: #111827;
    }
    .consult-name { font-weight: 700; font-size: 14px; color: var(--text-dark); }
    .consult-time { font-size: 12px; color: var(--text-muted); }

    .status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; }
    .status-badge.pending  { background: #fef3c7; color: #92400e; }
    .status-badge.answered { background: #ecfdf5; color: #065f46; }

    .consult-body { padding: 16px 20px; }
    .question-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 6px; }
    .question-text {
        font-size: 14px; color: var(--text-mid); line-height: 1.7;
        background: var(--bg); padding: 10px 14px; border-radius: 8px; margin-bottom: 14px;
    }

    .reply-existing {
        padding: 12px 14px; background: #f0fdf4;
        border-left: 3px solid var(--primary);
        border-radius: 0 8px 8px 0; margin-bottom: 12px;
    }
    .reply-label { font-size: 11px; font-weight: 800; color: #065f46; margin-bottom: 4px; display: flex; align-items: center; gap: 5px; }
    .reply-text  { font-size: 13px; color: #065f46; line-height: 1.6; }

    .reply-form-label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .reply-textarea {
        width: 100%; padding: 10px 12px;
        border: 1.5px solid var(--border); border-radius: 8px;
        font-family: inherit; font-size: 14px; color: var(--text-dark);
        background: var(--bg); resize: vertical; outline: none;
        transition: border-color 0.15s; min-height: 80px;
    }
    .reply-textarea:focus { border-color: var(--primary); background: white; }
    .reply-actions { display: flex; gap: 8px; margin-top: 8px; justify-content: flex-end; }

    /* Filter tabs */
    .filter-tabs { display: flex; gap: 8px; margin-bottom: 20px; }
    .filter-tab {
        padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
        border: 1.5px solid var(--border); background: white;
        color: var(--text-muted); cursor: pointer; transition: all 0.15s;
        font-family: inherit;
    }
    .filter-tab.active { background: var(--primary); color: #111827; border-color: var(--primary); }
    .filter-tab:hover:not(.active) { border-color: var(--primary); color: var(--text-dark); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Konsultasi</div>
        <div class="page-sub">Jawab pertanyaan responden dari tim dokter</div>
    </div>
</div>

{{-- Stats mini --}}
<div class="stats-mini">
    <div class="stat-mini-card">
        <div class="stat-mini-icon" style="background:#fef3c7;">⏳</div>
        <div>
            <div class="stat-mini-label">Menunggu Jawaban</div>
            <div class="stat-mini-number">{{ $pendingCount }}</div>
        </div>
    </div>
    <div class="stat-mini-card">
        <div class="stat-mini-icon" style="background:#ecfdf5;">✅</div>
        <div>
            <div class="stat-mini-label">Sudah Dijawab</div>
            <div class="stat-mini-number">{{ $answeredCount }}</div>
        </div>
    </div>
</div>

{{-- Filter tabs --}}
<div class="filter-tabs">
    <button class="filter-tab active" onclick="filterConsult('all', this)">Semua</button>
    <button class="filter-tab" onclick="filterConsult('pending', this)">Belum Dijawab</button>
    <button class="filter-tab" onclick="filterConsult('answered', this)">Sudah Dijawab</button>
</div>

@if($consultations->count() === 0)
<div style="padding:48px;text-align:center;background:white;border-radius:12px;border:1px solid var(--border);color:var(--text-muted);">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.3;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div style="font-weight:700;margin-bottom:4px;">Belum ada konsultasi masuk</div>
    <div style="font-size:13px;">Pertanyaan dari responden akan muncul di sini.</div>
</div>
@else
<div id="consultList">
    @foreach($consultations as $consult)
    <div class="consult-card {{ $consult->status }}" data-status="{{ $consult->status }}">
        <div class="consult-header">
            <div class="consult-author">
                <div class="consult-avatar">
                    {{ strtoupper(substr($consult->respondent->name ?? 'R', 0, 1)) }}
                </div>
                <div>
                    <div class="consult-name">{{ $consult->respondent->name ?? 'Responden' }}</div>
                    <div class="consult-time">{{ $consult->created_at->format('d M Y, H:i') }} · {{ $consult->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="status-badge {{ $consult->status }}">
                    {{ $consult->isPending() ? '⏳ Menunggu' : '✅ Dijawab' }}
                </span>
                <form method="POST" action="{{ route('admin.consultations.destroy', $consult) }}"
                      onsubmit="return confirm('Hapus konsultasi ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </div>
        </div>

        <div class="consult-body">
            <div class="question-label">Pertanyaan Responden</div>
            <div class="question-text">{{ $consult->question_text }}</div>

            @if($consult->isAnswered())
            <div class="reply-existing">
                <div class="reply-label">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Tim Dokter · {{ $consult->replied_at?->format('d M Y, H:i') }}
                </div>
                <div class="reply-text">{{ $consult->admin_reply }}</div>
            </div>
            @endif

            <div class="reply-form-label">
                {{ $consult->isAnswered() ? '✏️ Edit Jawaban' : '💬 Tulis Jawaban' }}
            </div>
            <form method="POST" action="{{ route('admin.consultations.reply', $consult) }}">
                @csrf
                <textarea
                    name="admin_reply"
                    class="reply-textarea"
                    placeholder="Tulis jawaban untuk pertanyaan ini..."
                    rows="3">{{ $consult->admin_reply ?? '' }}</textarea>
                @error('admin_reply')
                    <div style="font-size:13px;color:var(--danger);margin-top:4px;">{{ $message }}</div>
                @enderror
                <div class="reply-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ $consult->isAnswered() ? 'Perbarui Jawaban' : 'Kirim Jawaban' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection

@push('scripts')
<script>
function filterConsult(status, btn) {
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    // Filter cards
    document.querySelectorAll('.consult-card').forEach(card => {
        if (status === 'all') {
            card.style.display = '';
        } else {
            card.style.display = card.dataset.status === status ? '' : 'none';
        }
    });
}
</script>
@endpush