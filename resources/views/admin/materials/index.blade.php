@extends('layouts.admin')

@section('title', 'Kelola Materi')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Materi</div>
        <div class="page-sub">{{ $materials->count() }} materi tersedia</div>
    </div>
    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary" style="width:auto;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
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
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $material->duration }} menit
                    </span>
                    @endif
                    @if($material->content)
                    <a href="{{ $material->content }}" target="_blank" style="font-size:12px;color:var(--primary);display:flex;align-items:center;gap:4px;text-decoration:none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Lihat konten
                    </a>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <a href="{{ route('admin.materials.edit', ['materi' => $material]) }}"
                   style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:7px;border:1px solid var(--border);color:var(--text-muted);font-size:13px;font-weight:600;text-decoration:none;"
                   onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.materials.destroy', ['materi' => $material]) }}"
                      onsubmit="return confirm('Hapus materi ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:7px;border:1px solid #fecaca;color:#ef4444;font-size:13px;font-weight:600;background:transparent;cursor:pointer;font-family:inherit;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
@endsection