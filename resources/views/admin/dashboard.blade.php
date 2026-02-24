@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Dashboard</div>
        <div class="page-sub">Monitoring program penyuluhan kesehatan</div>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('admin.export.csv') }}" class="btn btn-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
        <a href="{{ route('admin.export.excel') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-teal">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div class="stat-number">{{ $totalRespondents }}</div>
        <div class="stat-label">Total Responden</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="stat-number">{{ $preTestDone }}</div>
        <div class="stat-label">Pre-Test Selesai</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div class="stat-number">{{ $postTestDone }}</div>
        <div class="stat-label">Post-Test Selesai</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div class="stat-number">{{ $completed }}</div>
        <div class="stat-label">Komplit (Pre+Post)</div>
    </div>
</div>

<div style="font-size:17px;font-weight:800;margin-bottom:16px;">Aktivitas Terbaru</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Lokasi</th>
                    <th>Pre-Test</th>
                    <th>Post-Test</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentRespondents as $r)
                <tr>
                    <td style="font-weight:600;">{{ $r->name }}</td>
                    <td style="color:var(--text-muted);">{{ $r->location ? $r->location->name : '—' }}</td>
                    <td>
                        @if($r->pre_test_done)
                            <span class="badge badge-success">✓ Selesai</span>
                        @else
                            <span class="badge badge-muted">○ Belum</span>
                        @endif
                    </td>
                    <td>
                        @if($r->post_test_done)
                            <span class="badge badge-success">✓ Selesai</span>
                        @else
                            <span class="badge badge-muted">○ Belum</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection