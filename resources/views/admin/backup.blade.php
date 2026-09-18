@extends('layouts.admin')

@section('title', 'Backup Database')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Backup Database</div>
        <div class="page-sub">Download salinan lengkap database dalam format SQL</div>
    </div>
    <a href="{{ route('admin.backup.download') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Download Backup (.sql)
    </a>
</div>

{{-- Info card --}}
<div class="card" style="padding: 24px; margin-bottom: 24px;">
    <div style="display: flex; gap: 16px; align-items: flex-start;">
        <div style="width: 44px; height: 44px; background: #edfdf9; border-radius: 10px;
                    display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" fill="none" stroke="#5EE9C7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 15px; margin-bottom: 6px;">
                Tentang Fitur Backup
            </div>
            <div style="font-size: 14px; color: var(--text-muted); line-height: 1.7;">
                Fitur ini menghasilkan file <strong>.sql</strong> berisi seluruh struktur tabel dan data
                database SehatEdukasi. File dapat digunakan untuk:
                <ul style="margin-top: 8px; padding-left: 20px;">
                    <li>Cadangan data sebelum perubahan besar</li>
                    <li>Migrasi ke server lain</li>
                    <li>Lampiran dokumentasi penelitian / skripsi</li>
                    <li>Restore data jika terjadi masalah</li>
                </ul>
            </div>
            <div style="margin-top: 12px; padding: 10px 14px; background: #fffbeb;
                        border: 1px solid #fde68a; border-radius: 8px;
                        font-size: 13px; color: #92400e;">
                ⚠️ Lakukan backup secara berkala — terutama sebelum mengubah data atau
                melakukan update aplikasi.
            </div>
        </div>
    </div>
</div>

{{-- Tabel info database --}}
<div class="card">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border);
                display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-weight: 700; font-size: 16px;">Isi Database</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                {{ count($tables) }} tabel ditemukan
            </div>
        </div>
        <div style="font-size: 13px; color: var(--text-muted);">
            Database: <strong>{{ config('database.connections.mysql.database') }}</strong>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Nama Tabel</th>
                    <th style="text-align: right;">Jumlah Baris</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $i => $table)
                <tr>
                    <td style="color: var(--text-muted); font-size: 13px;">{{ $i + 1 }}</td>
                    <td>
                        <code style="background: var(--bg); padding: 3px 8px; border-radius: 6px;
                                     font-size: 13px; color: var(--text-dark);">
                            {{ $table['name'] }}
                        </code>
                    </td>
                    <td style="text-align: right;">
                        @if($table['count'] > 0)
                            <span class="badge badge-success">
                                {{ number_format($table['count']) }} baris
                            </span>
                        @else
                            <span class="badge badge-muted">kosong</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="padding: 16px 24px; border-top: 1px solid var(--border);
                display: flex; align-items: center; justify-content: space-between;">
        <div style="font-size: 13px; color: var(--text-muted);">
            Terakhir dicek: {{ now()->format('d M Y, H:i') }} WIB
        </div>
        <a href="{{ route('admin.backup.download') }}" class="btn btn-primary btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Backup
        </a>
    </div>
</div>
@endsection