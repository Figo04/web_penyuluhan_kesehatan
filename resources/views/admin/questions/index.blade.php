@extends('layouts.admin')

@section('title', 'Kelola Soal')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Soal</div>
        <div class="page-sub">Manajemen soal pre-test dan post-test</div>
    </div>
    <a href="{{ route('admin.questions.create') }}" class="btn btn-primary" style="width:auto;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Soal
    </a>
</div>

@if(session('success'))
    <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;font-weight:600;">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- PRE-TEST --}}
<div style="margin-bottom:32px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="background:#dbeafe;color:#1e40af;font-size:13px;font-weight:700;padding:4px 12px;border-radius:100px;">Pre-Test</span>
            <span style="font-size:14px;color:var(--text-muted);">{{ $preQuestions->count() }} soal</span>
        </div>
    </div>

    <div class="card">
        @forelse($preQuestions as $index => $question)
        <div style="padding:20px 24px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
            <div style="display:flex;align-items:flex-start;gap:16px;">
                <div style="width:32px;height:32px;background:#dbeafe;color:#1e40af;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;">
                    {{ $index + 1 }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:15px;margin-bottom:12px;line-height:1.5;">{{ $question->question_text }}</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                        @foreach($question->options->sortBy('label') as $option)
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:7px;background:{{ $option->is_correct ? '#ecfdf5' : 'var(--bg)' }};border:1px solid {{ $option->is_correct ? '#a7f3d0' : 'var(--border)' }};">
                            <span style="font-weight:800;font-size:13px;color:{{ $option->is_correct ? '#065f46' : 'var(--text-muted)' }};width:18px;">{{ $option->label }}</span>
                            <span style="font-size:13px;color:{{ $option->is_correct ? '#065f46' : 'var(--text-dark)' }};">{{ $option->option_text }}</span>
                            @if($option->is_correct)
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;color:#10b981;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <a href="{{ route('admin.questions.edit', ['soal' => $question]) }}" style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:7px;border:1px solid var(--border);color:var(--text-muted);font-size:13px;font-weight:600;text-decoration:none;transition:all 0.15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.questions.destroy', ['soal' => $question]) }}" onsubmit="return confirm('Hapus soal ini? Jawaban responden terkait juga akan terhapus.')">
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
        <div style="padding:40px;text-align:center;color:var(--text-muted);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:40px;height:40px;margin:0 auto 12px;opacity:0.3;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Belum ada soal pre-test. <a href="{{ route('admin.questions.create') }}" style="color:var(--primary);font-weight:600;">Tambah sekarang</a>
        </div>
        @endforelse
    </div>
</div>

{{-- POST-TEST --}}
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="background:#fef3c7;color:#92400e;font-size:13px;font-weight:700;padding:4px 12px;border-radius:100px;">Post-Test</span>
            <span style="font-size:14px;color:var(--text-muted);">{{ $postQuestions->count() }} soal</span>
        </div>
    </div>

    <div class="card">
        @forelse($postQuestions as $index => $question)
        <div style="padding:20px 24px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
            <div style="display:flex;align-items:flex-start;gap:16px;">
                <div style="width:32px;height:32px;background:#fef3c7;color:#92400e;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;">
                    {{ $index + 1 }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:15px;margin-bottom:12px;line-height:1.5;">{{ $question->question_text }}</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                        @foreach($question->options->sortBy('label') as $option)
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:7px;background:{{ $option->is_correct ? '#ecfdf5' : 'var(--bg)' }};border:1px solid {{ $option->is_correct ? '#a7f3d0' : 'var(--border)' }};">
                            <span style="font-weight:800;font-size:13px;color:{{ $option->is_correct ? '#065f46' : 'var(--text-muted)' }};width:18px;">{{ $option->label }}</span>
                            <span style="font-size:13px;color:{{ $option->is_correct ? '#065f46' : 'var(--text-dark)' }};">{{ $option->option_text }}</span>
                            @if($option->is_correct)
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;color:#10b981;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <a href="{{ route('admin.questions.edit', ['soal' => $question]) }}" style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:7px;border:1px solid var(--border);color:var(--text-muted);font-size:13px;font-weight:600;text-decoration:none;transition:all 0.15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.questions.destroy', ['soal' => $question]) }}" onsubmit="return confirm('Hapus soal ini? Jawaban responden terkait juga akan terhapus.')">
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
        <div style="padding:40px;text-align:center;color:var(--text-muted);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:40px;height:40px;margin:0 auto 12px;opacity:0.3;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Belum ada soal post-test. <a href="{{ route('admin.questions.create') }}" style="color:var(--primary);font-weight:600;">Tambah sekarang</a>
        </div>
        @endforelse
    </div>
</div>
@endsection