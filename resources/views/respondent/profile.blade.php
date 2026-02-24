@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    .page-title { font-size: 24px; font-weight: 800; margin-bottom: 28px; }

    .profile-center { display: flex; flex-direction: column; align-items: center; margin-bottom: 32px; }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        font-size: 28px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .profile-name { font-size: 20px; font-weight: 800; color: var(--text-dark); }
    .profile-role { font-size: 14px; color: var(--text-muted); margin-top: 2px; }

    .info-list { display: flex; flex-direction: column; gap: 10px; }

    .info-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .info-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--bg);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--text-muted);
    }
    .info-icon svg { width: 18px; height: 18px; }

    .info-body { flex: 1; }
    .info-label { font-size: 12px; color: var(--text-muted); font-weight: 600; margin-bottom: 2px; }
    .info-value { font-size: 15px; font-weight: 600; color: var(--text-dark); }

    .progress-card {
        margin-top: 24px;
        background: white;
        border-radius: 14px;
        border: 1px solid var(--border);
        padding: 20px 24px;
        box-shadow: var(--shadow);
    }

    .progress-title { font-size: 15px; font-weight: 800; margin-bottom: 16px; }

    .progress-steps { display: flex; flex-direction: column; gap: 12px; }

    .progress-step { display: flex; align-items: center; gap: 12px; font-size: 14px; }

    .step-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .step-dot.done { background: var(--success); color: white; }
    .step-dot.todo { background: var(--bg); border: 2px solid var(--border); }
    .step-dot svg { width: 12px; height: 12px; }
    .step-dot-label { flex: 1; }
    .step-dot-label.done { color: var(--text-dark); font-weight: 600; }
    .step-dot-label.todo { color: var(--text-muted); }
</style>
@endpush

@section('content')
<div class="page-title">Profil Saya</div>

<div class="profile-center">
    <div class="profile-avatar">{{ strtoupper(substr($respondent->name, 0, 1)) }}</div>
    <div class="profile-name">{{ $respondent->name }}</div>
    <div class="profile-role">Responden</div>
</div>

<div class="info-list">
    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Nama Lengkap</div>
            <div class="info-value">{{ $respondent->name }}</div>
        </div>
    </div>

    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Kode Akses</div>
            <div class="info-value">{{ $respondent->access_code }}</div>
        </div>
    </div>

    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Umur</div>
            <div class="info-value">{{ $respondent->age }} tahun</div>
        </div>
    </div>

    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Jenis Kelamin</div>
            <div class="info-value">{{ ucfirst($respondent->gender) }}</div>
        </div>
    </div>

    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Status Perkawinan</div>
            <div class="info-value">{{ ucfirst($respondent->marital_status) }}</div>
        </div>
    </div>

    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Pekerjaan</div>
            <div class="info-value">{{ $respondent->occupation }}</div>
        </div>
    </div>

    @if($respondent->location)
    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Lokasi</div>
            <div class="info-value">{{ $respondent->location->name }}</div>
        </div>
    </div>
    @endif

    @if($respondent->medical_history && count($respondent->medical_history) > 0)
    <div class="info-item">
        <div class="info-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="info-body">
            <div class="info-label">Riwayat Penyakit</div>
            <div class="info-value">{{ implode(', ', $respondent->medical_history) }}</div>
        </div>
    </div>
    @endif
</div>

<div class="progress-card">
    <div class="progress-title">Progress Program</div>
    <div class="progress-steps">
        @php
            $steps = [
                ['label' => 'Pre-Test', 'done' => $respondent->pre_test_done],
                ['label' => 'Materi Edukasi', 'done' => $respondent->material_done],
                ['label' => 'Post-Test', 'done' => $respondent->post_test_done],
            ];
        @endphp
        @foreach($steps as $step)
        <div class="progress-step">
            <div class="step-dot {{ $step['done'] ? 'done' : 'todo' }}">
                @if($step['done'])
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @endif
            </div>
            <span class="step-dot-label {{ $step['done'] ? 'done' : 'todo' }}">{{ $step['label'] }}</span>
            @if($step['done'])
                <span style="font-size:12px;font-weight:700;color:#065f46;background:#ecfdf5;padding:2px 8px;border-radius:100px;">Selesai</span>
            @else
                <span style="font-size:12px;font-weight:600;color:var(--text-light);">Belum</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection