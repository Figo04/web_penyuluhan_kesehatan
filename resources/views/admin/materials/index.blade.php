@extends('layouts.admin')

@section('title', 'Kelola Materi')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Materi</div>
        <div class="page-sub">Upload dan kelola konten edukasi</div>
    </div>
    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Materi
    </a>
</div>

<div class="card" style="overflow:hidden;">
    @foreach($materials as $material)
    @php
        $icon = $material->type === 'video' ? '▶' : '📄';
        $typeColor = $material->type === 'video' ? '#7c3aed' : '#0369a1';
    @endphp
    <div style="display:flex;align-items:center;gap:14px;padding:18px 24px;border-bottom:1px solid var(--border);" class="material-row">
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $material->type === 'video' ? '#ede9fe' : '#e0f2fe' }};display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;color:{{ $typeColor }};">
            @if($material->type === 'video')
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            @endif
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;font-size:15px;margin-bottom:2px;">{{ $material->title }}</div>
            <div style="font-size:13px;color:var(--text-muted);">{{ ucfirst($material->type) }} • {{ $material->created_at->format('d M Y') }}</div>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
            <form method="POST" action="{{ route('admin.materials.destroy', $material) }}" onsubmit="return confirm('Hapus materi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    @endforeach

    @if($materials->isEmpty())
    <div style="padding:48px;text-align:center;color:var(--text-muted);">
        <div style="font-size:32px;margin-bottom:12px;">📚</div>
        <div style="font-weight:600;">Belum ada materi</div>
        <div style="font-size:13px;margin-top:4px;">Tambahkan materi edukasi pertama Anda</div>
    </div>
    @endif
</div>
@endsection