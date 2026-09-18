@extends('layouts.admin')

@section('title', 'Kelola Komentar')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Komentar</div>
        <div class="page-sub">Lihat dan balas komentar responden per materi</div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul Materi</th>
                    <th>Tipe</th>
                    <th>Total Komentar</th>
                    <th>Belum Dibalas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $i => $material)
                <tr>
                    <td style="color:var(--text-muted);">{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:600;font-size:14px;">{{ $material->title }}</div>
                    </td>
                    <td>
                        <span class="badge" style="
                            background: {{ match($material->type) {
                                'video'   => '#ede9fe',
                                'pdf'     => '#fef3c7',
                                'ppt'     => '#fff0e6',
                                'artikel' => '#e0f2fe',
                                default   => '#f3f4f6',
                            } }};
                            color: {{ match($material->type) {
                                'video'   => '#7c3aed',
                                'pdf'     => '#92400e',
                                'ppt'     => '#c2410c',
                                'artikel' => '#0369a1',
                                default   => '#374151',
                            } }};">
                            {{ strtoupper($material->type) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700;">{{ $material->comments_count }}</span>
                        <span style="color:var(--text-muted);font-size:13px;"> komentar</span>
                    </td>
                    <td>
                        @if($material->pending_count > 0)
                            <span class="badge" style="background:#fef3c7;color:#92400e;">
                                {{ $material->pending_count }} belum dibalas
                            </span>
                        @else
                            <span style="color:var(--text-muted);font-size:13px;">Semua sudah dibalas</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.comments.show', $material) }}" class="btn btn-outline btn-sm">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat &amp; Balas
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                        Belum ada materi yang tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection