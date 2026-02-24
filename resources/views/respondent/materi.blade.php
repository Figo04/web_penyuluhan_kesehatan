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
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: var(--bg);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 22px;
    }
    .material-thumb.video { background: #ede9fe; }
    .material-thumb.artikel { background: #e0f2fe; }
    .material-thumb.pdf { background: #fef3c7; }

    .material-body { flex: 1; min-width: 0; }

    .material-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
        font-size: 12px;
        font-weight: 600;
    }

    .type-badge { padding: 2px 9px; border-radius: 100px; }
    .type-badge.video { background: #ede9fe; color: #7c3aed; }
    .type-badge.artikel { background: #e0f2fe; color: #0369a1; }
    .type-badge.pdf { background: #fef3c7; color: #92400e; }

    .material-duration { color: var(--text-muted); }

    .material-name { font-size: 16px; font-weight: 700; margin-bottom: 4px; color: var(--text-dark); }
    .material-desc { font-size: 13px; color: var(--text-muted); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .material-action { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }

    .btn-open {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 9px;
        border: 1.5px solid var(--border);
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
        text-decoration: none;
        background: white;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .btn-open:hover { border-color: var(--primary); color: var(--primary); }
    .btn-open svg { width: 14px; height: 14px; }

    .read-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        color: #065f46;
        background: #ecfdf5;
        padding: 3px 8px;
        border-radius: 100px;
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
        $isRead = $readMaterialIds->contains($material->id);
        $emoji = $material->type === 'video' ? '🎬' : ($material->type === 'pdf' ? '📄' : '📝');
    @endphp
    <div class="material-card {{ $isRead ? 'read' : '' }}">
        <div class="material-thumb {{ $material->type }}">{{ $emoji }}</div>

        <div class="material-body">
            <div class="material-meta">
                <span class="type-badge {{ $material->type }}">
                    {{ ucfirst($material->type) }}
                </span>
                <span class="material-duration">
                    @if($material->type === 'video')
                        {{ $material->duration }} menit
                    @else
                        {{ $material->duration }} menit baca
                    @endif
                </span>
            </div>
            <div class="material-name">{{ $material->title }}</div>
            <div class="material-desc">{{ $material->description }}</div>
        </div>

        <div class="material-action">
            <a href="{{ $material->content }}" target="_blank"
               class="btn-open"
               onclick="markRead({{ $material->id }})">
                Buka
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
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
@endsection

@push('scripts')
<script>
function markRead(materialId) {
    fetch(`/materi/${materialId}/baca`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}
</script>
@endpush