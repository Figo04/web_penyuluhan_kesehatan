@extends('layouts.admin')

@section('title', 'Komentar — ' . $material->title)

@push('styles')
<style>
    .comment-card {
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
        background: white;
    }
    .comment-card.has-reply { border-color: #a7f3d0; }

    .comment-top {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        background: #fafafa;
        display: flex; align-items: flex-start;
        justify-content: space-between; gap: 16px;
    }
    .comment-author-info { display: flex; align-items: center; gap: 10px; }
    .comment-avatar {
        width: 34px; height: 34px; border-radius: 9px;
        background: var(--primary); display: flex; align-items: center;
        justify-content: center; font-weight: 800; font-size: 14px;
        color: #111827; flex-shrink: 0;
    }
    .comment-author-name { font-weight: 700; font-size: 14px; color: var(--text-dark); }
    .comment-author-time { font-size: 12px; color: var(--text-muted); margin-top: 1px; }

    .comment-body { padding: 16px 20px; }
    .comment-text { font-size: 14px; color: var(--text-mid); line-height: 1.7; margin-bottom: 16px; }

    /* Reply section */
    .reply-section { }
    .reply-existing {
        padding: 12px 16px;
        background: #f0fdf4;
        border-left: 3px solid var(--primary);
        border-radius: 0 8px 8px 0;
        margin-bottom: 12px;
    }
    .reply-label {
        font-size: 11px; font-weight: 800; color: #065f46;
        margin-bottom: 5px; display: flex; align-items: center; gap: 5px;
    }
    .reply-text { font-size: 13px; color: #065f46; line-height: 1.6; }

    .reply-form-label {
        font-size: 12px; font-weight: 700; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;
    }
    .reply-textarea {
        width: 100%; padding: 10px 12px;
        border: 1.5px solid var(--border); border-radius: 8px;
        font-family: inherit; font-size: 14px; color: var(--text-dark);
        background: var(--bg); resize: vertical; outline: none;
        transition: border-color 0.15s; min-height: 80px;
    }
    .reply-textarea:focus { border-color: var(--primary); background: white; }
    .reply-actions { display: flex; gap: 8px; margin-top: 8px; justify-content: flex-end; }

    .pending-label {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; background: #fef3c7; color: #92400e;
        border-radius: 100px; font-size: 12px; font-weight: 700;
    }
    .answered-label {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; background: #ecfdf5; color: #065f46;
        border-radius: 100px; font-size: 12px; font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Komentar Materi</div>
        <div class="page-sub">{{ $material->title }}</div>
    </div>
    <a href="{{ route('admin.comments.index') }}" class="btn btn-outline">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>

@if($comments->count() === 0)
<div style="padding:48px;text-align:center;background:white;border-radius:12px;border:1px solid var(--border);color:var(--text-muted);">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.3;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
    <div style="font-weight:700;margin-bottom:4px;">Belum ada komentar</div>
    <div style="font-size:13px;">Responden belum memberikan komentar untuk materi ini.</div>
</div>
@else

<div style="margin-bottom:16px;color:var(--text-muted);font-size:14px;">
    Total <strong style="color:var(--text-dark);">{{ $comments->count() }}</strong> komentar ·
    <strong style="color:#92400e;">{{ $comments->whereNull('admin_reply')->count() }}</strong> belum dibalas
</div>

@foreach($comments as $comment)
<div class="comment-card {{ $comment->hasReply() ? 'has-reply' : '' }}">
    <div class="comment-top">
        <div class="comment-author-info">
            <div class="comment-avatar">
                {{ strtoupper(substr($comment->respondent->name ?? 'R', 0, 1)) }}
            </div>
            <div>
                <div class="comment-author-name">{{ $comment->respondent->name ?? 'Responden' }}</div>
                <div class="comment-author-time">{{ $comment->created_at->format('d M Y, H:i') }} · {{ $comment->created_at->diffForHumans() }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            @if($comment->hasReply())
                <span class="answered-label">✅ Sudah dibalas</span>
            @else
                <span class="pending-label">⏳ Belum dibalas</span>
            @endif
            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}"
                  onsubmit="return confirm('Hapus komentar ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </div>
    </div>

    <div class="comment-body">
        <div class="comment-text">{{ $comment->comment_text }}</div>

        {{-- Balasan yang sudah ada --}}
        @if($comment->hasReply())
        <div class="reply-existing">
            <div class="reply-label">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Tim Dokter · {{ $comment->replied_at?->format('d M Y, H:i') }}
            </div>
            <div class="reply-text">{{ $comment->admin_reply }}</div>
        </div>
        @endif

        {{-- Form balas / edit balasan --}}
        <div class="reply-section">
            <div class="reply-form-label">
                {{ $comment->hasReply() ? '✏️ Edit Balasan' : '💬 Tulis Balasan' }}
            </div>
            <form method="POST" action="{{ route('admin.comments.reply', $comment) }}">
                @csrf
                <textarea
                    name="admin_reply"
                    class="reply-textarea"
                    placeholder="Tulis balasan untuk komentar ini..."
                    rows="3">{{ $comment->admin_reply ?? '' }}</textarea>
                @error('admin_reply')
                    <div style="font-size:13px;color:var(--danger);margin-top:4px;">{{ $message }}</div>
                @enderror
                <div class="reply-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ $comment->hasReply() ? 'Perbarui Balasan' : 'Kirim Balasan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection