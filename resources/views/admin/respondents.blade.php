@extends('layouts.admin')

@section('title', 'Data Responden')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Data Responden</div>
        <div class="page-sub">{{ $respondents->total() }} responden terdaftar (halaman {{ $respondents->currentPage() }} dari {{ $respondents->lastPage() }})</div>
    </div>
    <a href="{{ route('admin.export.csv') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
    </a>
</div>

<div class="card" style="padding:20px 24px;">
    <div class="search-wrap">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" class="search-input" placeholder="Cari responden..."
            value="{{ request('q') }}"
            onkeydown="if(event.key==='Enter'){window.location.href='?q='+this.value}">
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Umur</th>
                    <th>Lokasi</th>
                    <th style="text-align:center;">Pre</th>
                    <th style="text-align:center;">Post</th>
                </tr>
            </thead>
            <tbody>
                @forelse($respondents as $r)
                <tr>
                    <td><span style="font-size:12px;font-weight:600;color:var(--text-muted);font-family:monospace;">{{ $r->access_code }}</span></td>
                    <td style="font-weight:600;">{{ $r->name }}</td>
                    <td style="color:var(--text-muted);">{{ $r->age }}</td>
                    <td style="color:var(--text-muted);">{{ $r->location ? $r->location->name : '—' }}</td>
                    <td style="text-align:center;">
                        <span class="dot {{ $r->pre_test_done ? 'dot-green' : 'dot-gray' }}"></span>
                    </td>
                    <td style="text-align:center;">
                        <span class="dot {{ $r->post_test_done ? 'dot-green' : 'dot-gray' }}"></span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px;">Belum ada responden terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($respondents->hasPages())
    <div style="display:flex;justify-content:center;margin-top:20px;">
        {{ $respondents->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection