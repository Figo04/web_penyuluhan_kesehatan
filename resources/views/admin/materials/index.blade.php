@extends('layouts.admin')

@section('title', 'Kelola Materi')

@push('styles')
<style>
    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.75);
        z-index: 1000;
        align-items: center; justify-content: center;
        padding: 16px;
    }
    .modal-overlay.active { display: flex; }

    .modal-box {
        background: white; border-radius: 16px;
        width: 100%; max-width: 900px; max-height: 92vh;
        display: flex; flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.35);
    }

    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; border-bottom: 1px solid var(--border); flex-shrink: 0;
    }
    .modal-title-wrap { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
    .modal-thumb {
        width: 36px; height: 36px; border-radius: 9px;
        background: #ede9fe; display: flex; align-items: center;
        justify-content: center; font-size: 18px; flex-shrink: 0;
    }
    .modal-title {
        font-size: 15px; font-weight: 700; color: var(--text-dark);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .modal-close {
        width: 32px; height: 32px; border-radius: 8px;
        border: none; background: var(--bg); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); transition: all 0.15s;
        flex-shrink: 0; margin-left: 10px;
    }
    .modal-close:hover { background: #fee2e2; color: #ef4444; }
    .modal-close svg { width: 16px; height: 16px; }

    .modal-body { flex: 1; overflow: hidden; position: relative; min-height: 480px; }
    .modal-body iframe { width: 100%; height: 100%; min-height: 480px; border: none; display: block; }

    .modal-loading {
        position: absolute; inset: 0;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 12px; background: white; color: var(--text-muted); font-size: 14px;
    }
    .spinner {
        width: 36px; height: 36px;
        border: 3px solid var(--border); border-top-color: var(--primary);
        border-radius: 50%; animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Materi</div>
        <div class="page-sub">{{ $materials->count() }} materi tersedia</div>
    </div>
    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary" style="width:auto;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Materi
    </a>
</div>

<div class="card">
    @forelse($materials as $material)
    <div style="padding:20px 24px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
        <div style="display:flex;align-items:center;gap:16px;">

            {{-- Urutan --}}
            <div style="width:32px;height:32px;background:var(--primary-light);color:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;">
                {{ $material->order }}
            </div>

            {{-- Info --}}
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <span style="font-weight:700;font-size:15px;">{{ $material->title }}</span>
                    @php
                        $typeColor = match($material->type) {
                            'video'   => 'background:#eff6ff;color:#1d4ed8;',
                            'artikel' => 'background:#f0fdf4;color:#166534;',
                            'pdf'     => 'background:#fef2f2;color:#991b1b;',
                            default   => 'background:var(--bg);color:var(--text-muted);',
                        };
                        $typeIcon = match($material->type) {
                            'video'   => '🎥',
                            'artikel' => '📄',
                            'pdf'     => '📋',
                            default   => '📁',
                        };
                    @endphp
                    <span style="{{ $typeColor }}font-size:12px;font-weight:700;padding:2px 10px;border-radius:100px;">
                        {{ $typeIcon }} {{ ucfirst($material->type) }}
                    </span>
                </div>

                @if($material->description)
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:6px;">{{ $material->description }}</div>
                @endif

                <div style="display:flex;align-items:center;gap:12px;">
                    @if($material->duration)
                    <span style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $material->duration }} menit
                    </span>
                    @endif

                    @if($material->content)
                    {{-- Tombol preview popup --}}
                    <button
                        onclick="openPreview('{{ addslashes($material->title) }}', '{{ $material->type }}', '{{ $material->content }}', '{{ $typeIcon }}')"
                        style="display:flex;align-items:center;gap:4px;font-size:12px;color:var(--primary);background:none;border:none;cursor:pointer;font-family:inherit;padding:0;font-weight:600;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview
                    </button>

                    {{-- Link buka tab baru (tetap ada sebagai alternatif) --}}
                    <a href="{{ $material->content }}" target="_blank"
                       style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;text-decoration:none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Buka link
                    </a>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <a href="{{ route('admin.materials.edit', ['materi' => $material]) }}"
                   style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:7px;border:1px solid var(--border);color:var(--text-muted);font-size:13px;font-weight:600;text-decoration:none;"
                   onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.materials.destroy', ['materi' => $material]) }}"
                      onsubmit="return confirm('Hapus materi ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:7px;border:1px solid #fecaca;color:#ef4444;font-size:13px;font-weight:600;background:transparent;cursor:pointer;font-family:inherit;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
    @empty
    <div style="padding:48px;text-align:center;color:var(--text-muted);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:48px;height:48px;margin:0 auto 12px;opacity:0.3;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <div style="font-weight:600;margin-bottom:8px;">Belum ada materi</div>
        <a href="{{ route('admin.materials.create') }}" style="color:var(--primary);font-weight:600;">Tambah materi pertama</a>
    </div>
    @endforelse
</div>

{{-- MODAL PREVIEW --}}
<div class="modal-overlay" id="previewModal" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <div class="modal-thumb" id="previewThumb">📄</div>
                <div class="modal-title" id="previewTitle">Memuat...</div>
            </div>
            <button class="modal-close" onclick="closePreview()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-loading" id="previewLoading">
                <div class="spinner"></div>
                <span>Memuat konten...</span>
            </div>
            <iframe id="previewIframe" src="" onload="hideLoading()" allowfullscreen></iframe>
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
        return videoId ? 'https://www.youtube.com/embed/' + videoId + '?rel=0' : url;
    }

    // PDF, artikel, PPT — semua pakai Google Drive preview atau Google Docs Viewer
    if (url.includes('drive.google.com')) {
        const parts = url.split('/d/');
        if (parts.length > 1) {
            const fileId = parts[1].split('/')[0].split('?')[0];
            return 'https://drive.google.com/file/d/' + fileId + '/preview';
        }
    }

    // URL biasa (artikel, ppt, dll) → Google Docs Viewer
    return 'https://docs.google.com/viewer?url=' + encodeURIComponent(url) + '&embedded=true';
}

function openPreview(title, type, url, emoji) {
    const modal   = document.getElementById('previewModal');
    const iframe  = document.getElementById('previewIframe');
    const titleEl = document.getElementById('previewTitle');
    const thumbEl = document.getElementById('previewThumb');
    const loading = document.getElementById('previewLoading');

    titleEl.textContent = title;
    thumbEl.textContent = emoji || '📄';
    loading.style.display = 'flex';
    iframe.src = '';

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    setTimeout(() => { iframe.src = getEmbedUrl(type, url); }, 150);
}

function hideLoading() {
    document.getElementById('previewLoading').style.display = 'none';
}

function closePreview() {
    document.getElementById('previewModal').classList.remove('active');
    document.getElementById('previewIframe').src = '';
    document.body.style.overflow = '';
}

function closeModalOutside(e) {
    if (e.target === document.getElementById('previewModal')) closePreview();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closePreview(); });
</script>
@endpush