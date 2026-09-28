@extends('layouts.app')

@section('title', 'Materi Edukasi')

@push('styles')
<style>
    .page-header { margin-bottom: 28px; }
    .page-title { font-size: 24px; font-weight: 800; }
    .page-sub { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

    .materials-list { display: flex; flex-direction: column; gap: 20px; }

    /* ── MATERIAL CARD ── */
    .material-card {
        background: white;
        border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: all 0.2s;
    }
    .material-card.read { border-color: #a7f3d0; }

    .material-card-top {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
    }

    .material-thumb {
        width: 52px; height: 52px;
        border-radius: 12px;
        background: var(--bg);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 22px;
    }
    .material-thumb.video   { background: #ede9fe; }
    .material-thumb.artikel { background: #e0f2fe; }
    .material-thumb.pdf     { background: #fef3c7; }
    .material-thumb.ppt     { background: #fff0e6; }

    .material-body { flex: 1; min-width: 0; }
    .material-meta {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 5px; font-size: 12px; font-weight: 600;
    }
    .type-badge { padding: 2px 9px; border-radius: 100px; }
    .type-badge.video   { background: #ede9fe; color: #7c3aed; }
    .type-badge.artikel { background: #e0f2fe; color: #0369a1; }
    .type-badge.pdf     { background: #fef3c7; color: #92400e; }
    .type-badge.ppt     { background: #fff0e6; color: #c2410c; }
    .material-duration  { color: var(--text-muted); }
    .material-name  { font-size: 16px; font-weight: 700; margin-bottom: 4px; color: var(--text-dark); }
    .material-desc  { font-size: 13px; color: var(--text-muted); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .material-action { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }

    .btn-open {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px;
        border: 1.5px solid var(--border);
        font-size: 14px; font-weight: 600;
        color: var(--text-dark); background: white;
        cursor: pointer; transition: all 0.15s; white-space: nowrap;
        font-family: inherit;
    }
    .btn-open:hover { border-color: var(--primary); color: var(--primary); }
    .btn-open svg { width: 14px; height: 14px; }

    .read-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; font-weight: 700;
        color: #065f46; background: #ecfdf5;
        padding: 3px 8px; border-radius: 100px;
    }

    /* ── COMMENT TOGGLE BUTTON ── */
    .comment-toggle {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 24px;
        border-top: 1px solid var(--border);
        background: #fafafa;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        transition: background 0.15s;
        user-select: none;
    }
    .comment-toggle:hover { background: #f3f4f6; }
    .comment-toggle-left { display: flex; align-items: center; gap: 8px; }
    .comment-toggle-left svg { width: 15px; height: 15px; }
    .comment-count-badge {
        background: var(--primary);
        color: #111827;
        font-size: 11px;
        font-weight: 800;
        padding: 1px 7px;
        border-radius: 100px;
    }
    .comment-toggle-arrow { transition: transform 0.2s; }
    .comment-toggle-arrow.open { transform: rotate(180deg); }

    /* ── COMMENT SECTION ── */
    .comment-section {
        display: none;
        border-top: 1px solid var(--border);
        padding: 20px 24px;
    }
    .comment-section.open { display: block; }

    /* Form kirim komentar */
    .comment-form-wrap { margin-bottom: 20px; }
    .comment-textarea {
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
        min-height: 80px;
    }
    .comment-textarea:focus { border-color: var(--primary); background: white; }
    .comment-textarea::placeholder { color: var(--text-light); }
    .comment-submit-row {
        display: flex; align-items: center; justify-content: flex-end;
        margin-top: 8px; gap: 8px;
    }
    .btn-comment-submit {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px;
        background: var(--primary); color: #111827;
        border: none; border-radius: 8px;
        font-family: inherit; font-size: 13px; font-weight: 700;
        cursor: pointer; transition: background 0.15s;
    }
    .btn-comment-submit:hover { background: var(--primary-dark); }

    /* Daftar komentar */
    .comments-list { display: flex; flex-direction: column; gap: 14px; }

    .comment-item {
        padding: 14px 16px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: white;
    }

    .comment-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 8px;
    }
    .comment-author {
        display: flex; align-items: center; gap: 8px;
    }
    .comment-avatar {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800; color: #111827;
        flex-shrink: 0;
    }
    .comment-avatar.is-me { background: #dbeafe; color: #1e40af; }
    .comment-name { font-size: 13px; font-weight: 700; color: var(--text-dark); }
    .comment-time { font-size: 12px; color: var(--text-light); margin-left: 4px; }
    .comment-text { font-size: 14px; color: var(--text-mid); line-height: 1.6; }

    /* Tombol hapus komentar sendiri */
    .btn-delete-comment {
        background: none; border: none;
        color: var(--text-light); font-size: 12px;
        cursor: pointer; padding: 2px 6px;
        border-radius: 5px; font-family: inherit;
        transition: all 0.15s;
    }
    .btn-delete-comment:hover { background: #fef2f2; color: var(--danger); }

    /* Balasan admin */
    .admin-reply {
        margin-top: 10px;
        padding: 10px 14px;
        background: #f0fdf4;
        border-left: 3px solid var(--primary);
        border-radius: 0 8px 8px 0;
    }
    .admin-reply-label {
        font-size: 11px; font-weight: 800;
        color: #065f46; margin-bottom: 4px;
        display: flex; align-items: center; gap: 5px;
    }
    .admin-reply-text { font-size: 13px; color: #065f46; line-height: 1.6; }

    /* Empty state */
    .no-comments {
        text-align: center; padding: 24px;
        color: var(--text-muted); font-size: 13px;
    }

    /* ── MODAL ── */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.7); z-index: 1000;
        align-items: center; justify-content: center; padding: 16px;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: white; border-radius: 16px;
        width: 100%; max-width: 900px;
        max-height: 90vh; display: flex;
        flex-direction: column; overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .modal-header {
        display: flex; align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border); flex-shrink: 0;
    }
    .modal-title { font-size: 16px; font-weight: 700; color: var(--text-dark); }
    .modal-close {
        width: 32px; height: 32px; border-radius: 8px;
        border: none; background: var(--bg);
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); transition: all 0.15s;
    }
    .modal-close:hover { background: #fee2e2; color: var(--danger); }
    .modal-close svg { width: 16px; height: 16px; }
    .modal-body { flex: 1; overflow: hidden; position: relative; }
    .modal-body iframe { width: 100%; height: 100%; min-height: 500px; border: none; display: block; }
    .modal-loading {
        position: absolute; inset: 0; display: flex;
        flex-direction: column; align-items: center;
        justify-content: center; gap: 12px;
        background: white; color: var(--text-muted); font-size: 14px;
    }
    .spinner {
        width: 36px; height: 36px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 768px) {
        .material-card-top { padding: 16px; }
        .comment-section { padding: 16px; }
        .comment-toggle { padding: 12px 16px; }
        .modal-box { max-height: 95vh; border-radius: 12px; }
        .modal-body iframe { min-height: 400px; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">Materi Edukasi</div>
    <div class="page-sub">Pelajari materi kesehatan berikut ini</div>
</div>

<div class="materials-list">
    @forelse($materials as $material)
    @php
        $isRead = in_array($material->material_id, $readMaterialIds);
        $emoji = match($material->type) {
            'video'   => '🎬',
            'pdf'     => '📄',
            'ppt'     => '📊',
            'artikel' => '📝',
            default   => '📁',
        };
        $commentCount = $material->comments->count();
        $myRespondentId = session('respondent_id');
    @endphp
    <div class="material-card {{ $isRead ? 'read' : '' }}">

        {{-- Bagian atas: info materi + tombol buka --}}
        <div class="material-card-top">
            <div class="material-thumb {{ $material->type }}">{{ $emoji }}</div>

            <div class="material-body">
                <div class="material-meta">
                    <span class="type-badge {{ $material->type }}">
                        {{ strtoupper($material->type) }}
                    </span>
                    @if($material->duration)
                    <span class="material-duration">
                        {{ $material->duration }} menit{{ $material->type === 'video' ? '' : ' baca' }}
                    </span>
                    @endif
                </div>
                <div class="material-name">{{ $material->title }}</div>
                @if($material->description)
                <div class="material-desc">{{ $material->description }}</div>
                @endif
            </div>

            <div class="material-action">
                <button
                    class="btn-open"
                    onclick="openModal('{{ $material->material_id }}', '{{ addslashes($material->title) }}', '{{ $material->type }}', '{{ $material->content }}'); markRead({{ $material->material_id }})">
                    Buka
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>

                @if($isRead)
                <div class="read-badge" id="read-badge-{{ $material->material_id }}">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Sudah dibaca
                </div>
                @else
                <div class="read-badge" id="read-badge-{{ $material->material_id }}" style="display:none;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Sudah dibaca
                </div>
                @endif
            </div>
        </div>

        {{-- Toggle komentar --}}
        <div class="comment-toggle" onclick="toggleComments({{ $material->material_id }})">
            <div class="comment-toggle-left">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Diskusi &amp; Komentar
                @if($commentCount > 0)
                <span class="comment-count-badge">{{ $commentCount }}</span>
                @endif
            </div>
            <svg class="comment-toggle-arrow" id="arrow-{{ $material->material_id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        {{-- Section komentar --}}
        <div class="comment-section" id="comments-{{ $material->material_id }}">

            {{-- Form kirim komentar --}}
            <div class="comment-form-wrap">
                <form method="POST" action="{{ route('respondent.comment.store', $material) }}">
                    @csrf
                    <textarea
                        name="comment_text"
                        class="comment-textarea"
                        placeholder="Tulis pertanyaan atau komentar tentang materi ini..."
                        rows="3"></textarea>
                    @error('comment_text')
                        <div class="form-error" style="margin-top:4px;">{{ $message }}</div>
                    @enderror
                    <div class="comment-submit-row">
                        <button type="submit" class="btn-comment-submit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Kirim Komentar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Daftar komentar --}}
            @if($material->comments->count() > 0)
            <div class="comments-list">
                @foreach($material->comments->sortByDesc('created_at') as $comment)
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="comment-author">
                            <div class="comment-avatar {{ $comment->respondent_id === $myRespondentId ? 'is-me' : '' }}">
                                {{ strtoupper(substr($comment->respondent->name ?? 'R', 0, 1)) }}
                            </div>
                            <div>
                                <span class="comment-name">
                                    {{ $comment->respondent->name ?? 'Responden' }}
                                    @if($comment->respondent_id === $myRespondentId)
                                        <span style="font-size:11px;color:var(--text-muted);font-weight:500;">(Saya)</span>
                                    @endif
                                </span>
                                <span class="comment-time">· {{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        {{-- Tombol hapus hanya untuk komentar milik sendiri --}}
                        @if($comment->respondent_id === $myRespondentId)
                        <form method="POST" action="{{ route('respondent.comment.destroy', $comment) }}"
                              onsubmit="return confirm('Hapus komentar ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-comment">Hapus</button>
                        </form>
                        @endif
                    </div>

                    <div class="comment-text">{{ $comment->comment_text }}</div>

                    {{-- Balasan admin --}}
                    @if($comment->hasReply())
                    <div class="admin-reply">
                        <div class="admin-reply-label">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Tim Dokter · {{ $comment->replied_at?->diffForHumans() }}
                        </div>
                        <div class="admin-reply-text">{{ $comment->admin_reply }}</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="no-comments">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px;margin:0 auto 8px;display:block;opacity:0.3;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Belum ada komentar. Jadilah yang pertama bertanya!
            </div>
            @endif
        </div>

    </div>
    @empty
    <div style="padding:48px 24px;text-align:center;color:var(--text-muted);background:white;border-radius:14px;border:1px solid var(--border);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:48px;height:48px;margin:0 auto 12px;opacity:0.3;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <div style="font-weight:700;font-size:15px;margin-bottom:4px;">Belum ada materi</div>
        <div style="font-size:13px;">Materi edukasi akan segera ditambahkan.</div>
    </div>
    @endforelse
</div>

{{-- MODAL --}}
<div class="modal-overlay" id="materialModal" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Memuat...</div>
            <button class="modal-close" onclick="closeModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-loading" id="modalLoading">
                <div class="spinner"></div>
                <span>Memuat konten...</span>
            </div>
            <iframe id="modalIframe" src="" onload="hideLoading()" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Comment Toggle ──
function toggleComments(materialId) {
    const section = document.getElementById('comments-' + materialId);
    const arrow   = document.getElementById('arrow-'    + materialId);
    const isOpen  = section.classList.contains('open');

    section.classList.toggle('open', !isOpen);
    arrow.classList.toggle('open', !isOpen);
}

// Auto-buka section komentar jika baru submit (agar flash success terlihat)
@if(session('success'))
document.addEventListener('DOMContentLoaded', function () {
    // Buka semua section agar user bisa lihat balasan terbaru
    // (opsional: bisa lebih spesifik jika ada query string)
});
@endif

// ── Modal ──
function getEmbedUrl(type, url) {
    if (type === 'video') {
        let videoId = '';
        if (url.includes('youtu.be/')) {
            videoId = url.split('youtu.be/')[1].split('?')[0];
        } else if (url.includes('youtube.com/watch')) {
            const params = new URLSearchParams(url.split('?')[1]);
            videoId = params.get('v');
        } else if (url.includes('youtube.com/embed/')) {
            return url;
        }
        return videoId ? 'https://www.youtube.com/embed/' + videoId + '?autoplay=0' : url;
    }
    if (url.includes('drive.google.com')) {
        const parts = url.split('/d/');
        if (parts.length > 1) {
            const fileId = parts[1].split('/')[0].split('?')[0];
            return 'https://drive.google.com/file/d/' + fileId + '/preview';
        }
    }
    return 'https://docs.google.com/viewer?url=' + encodeURIComponent(url) + '&embedded=true';
}

function openModal(id, title, type, url) {
    const modal   = document.getElementById('materialModal');
    const iframe  = document.getElementById('modalIframe');
    const titleEl = document.getElementById('modalTitle');
    const loading = document.getElementById('modalLoading');

    titleEl.textContent = title;
    loading.style.display = 'flex';
    iframe.src = '';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    setTimeout(() => { iframe.src = getEmbedUrl(type, url); }, 100);
}

function hideLoading() {
    document.getElementById('modalLoading').style.display = 'none';
}

function closeModal() {
    document.getElementById('materialModal').classList.remove('active');
    document.getElementById('modalIframe').src = '';
    document.body.style.overflow = '';
}

function closeModalOutside(event) {
    if (event.target === document.getElementById('materialModal')) closeModal();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

function markRead(materialId) {
    const badge = document.getElementById('read-badge-' + materialId);
    if (badge) badge.style.display = 'inline-flex';

    fetch('/materi/' + materialId + '/baca', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}
</script>
@endpush    