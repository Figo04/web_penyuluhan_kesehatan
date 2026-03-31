@extends('layouts.app')

@section('title', 'Materi Edukasi')

@push('styles')
<style>
    .page-header { margin-bottom: 28px; }
    .page-title { font-size: 24px; font-weight: 800; }
    .page-sub { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

    .materials-list { display: flex; flex-direction: column; gap: 14px; }

    .material-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
        background: white;
        border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        transition: all 0.2s;
    }
    .material-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .material-card.read { border-color: #a7f3d0; }

    .material-thumb {
        width: 52px; height: 52px;
        border-radius: 12px;
        background: var(--bg);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 22px;
    }
    .material-thumb.video { background: #ede9fe; }
    .material-thumb.artikel { background: #e0f2fe; }
    .material-thumb.pdf { background: #fef3c7; }
    .material-thumb.ppt { background: #fff0e6; }

    .material-body { flex: 1; min-width: 0; }

    .material-meta {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 5px; font-size: 12px; font-weight: 600;
    }

    .type-badge { padding: 2px 9px; border-radius: 100px; }
    .type-badge.video { background: #ede9fe; color: #7c3aed; }
    .type-badge.artikel { background: #e0f2fe; color: #0369a1; }
    .type-badge.pdf { background: #fef3c7; color: #92400e; }
    .type-badge.ppt { background: #fff0e6; color: #c2410c; }

    .material-duration { color: var(--text-muted); }
    .material-name { font-size: 16px; font-weight: 700; margin-bottom: 4px; color: var(--text-dark); }
    .material-desc { font-size: 13px; color: var(--text-muted); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .material-action { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }

    .btn-open {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px;
        border: 1.5px solid var(--border);
        font-size: 14px; font-weight: 600;
        color: var(--text-dark);
        background: white; cursor: pointer;
        transition: all 0.15s; white-space: nowrap;
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

    /* ── MODAL ─────────────────────────────── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.7);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-overlay.active { display: flex; }

    .modal-box {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .modal-title { font-size: 16px; font-weight: 700; color: var(--text-dark); }

    .modal-close {
        width: 32px; height: 32px;
        border-radius: 8px;
        border: none; background: var(--bg);
        cursor: pointer; display: flex;
        align-items: center; justify-content: center;
        color: var(--text-muted); transition: all 0.15s;
    }
    .modal-close:hover { background: #fee2e2; color: var(--danger); }
    .modal-close svg { width: 16px; height: 16px; }

    .modal-body {
        flex: 1;
        overflow: hidden;
        position: relative;
    }

    .modal-body iframe {
        width: 100%;
        height: 100%;
        min-height: 500px;
        border: none;
        display: block;
    }

    .modal-loading {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: white;
        color: var(--text-muted);
        font-size: 14px;
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
    @foreach($materials as $material)
    @php
        $isRead = in_array($material->id, $readMaterialIds);
        $emoji = match($material->type) {
            'video'   => '🎬',
            'pdf'     => '📄',
            'ppt'     => '📊',
            'artikel' => '📝',
            default   => '📁',
        };
        // Tentukan apakah pakai modal atau buka tab baru
        $useModal = in_array($material->type, ['pdf', 'ppt', 'video']);
    @endphp
    <div class="material-card {{ $isRead ? 'read' : '' }}">
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
            @if($useModal)
                {{-- PDF, PPT, Video → buka modal --}}
                <button
                    class="btn-open"
                    onclick="openModal('{{ $material->id }}', '{{ addslashes($material->title) }}', '{{ $material->type }}', '{{ $material->content }}'); markRead({{ $material->id }})">
                    Buka
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            @else
                {{-- Artikel → buka tab baru --}}
                <a href="{{ $material->content }}" target="_blank"
                   class="btn-open"
                   onclick="markRead({{ $material->id }})">
                    Buka
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            @endif

            @if($isRead)
                <div class="read-badge">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Sudah dibaca
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- MODAL --}}
<div class="modal-overlay" id="materialModal" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Memuat...</div>
            <button class="modal-close" onclick="closeModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-loading" id="modalLoading">
                <div class="spinner"></div>
                <span>Memuat konten...</span>
            </div>
            <iframe id="modalIframe" src="" onload="hideLoading()"></iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    if (type === 'pdf' || type === 'ppt') {
        if (url.includes('drive.google.com')) {
            // Ambil FILE_ID dari URL Google Drive
            const parts = url.split('/d/');
            if (parts.length > 1) {
                const fileId = parts[1].split('/')[0].split('?')[0];
                return 'https://drive.google.com/file/d/' + fileId + '/preview';
            }
        }
        return 'https://docs.google.com/viewer?url=' + encodeURIComponent(url) + '&embedded=true';
    }

    return url;
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

    setTimeout(() => {
        iframe.src = getEmbedUrl(type, url);
    }, 100);
}

function hideLoading() {
    document.getElementById('modalLoading').style.display = 'none';
}

function closeModal() {
    const modal  = document.getElementById('materialModal');
    const iframe = document.getElementById('modalIframe');
    modal.classList.remove('active');
    iframe.src = '';
    document.body.style.overflow = '';
}

function closeModalOutside(event) {
    if (event.target === document.getElementById('materialModal')) {
        closeModal();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

function markRead(materialId) {
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