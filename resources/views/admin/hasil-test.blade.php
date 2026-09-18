@extends('layouts.admin')

@section('title', 'Hasil Test')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Hasil Test</div>
        <div class="page-sub">Data jawaban Pre-Test dan Post-Test</div>
    </div>
    <a href="{{ route('admin.export.excel') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Data Penelitian
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Anak</th>
                    <th style="text-align:center;">Umur Anak</th>
                    <th style="text-align:center;">BB / TB</th>
                    @php $childNo = 0; $preNo = 0; @endphp
                    @foreach($preQuestions as $q)
                        @if($q->category === 'kondisi_anak')
                            <th style="text-align:center;background:#fdf2f8;" title="{{ $q->question_text }}">Anak Q{{ ++$childNo }}</th>
                        @else
                            <th style="text-align:center;" title="{{ $q->question_text }}">Pre Q{{ ++$preNo }}</th>
                        @endif
                    @endforeach
                    @foreach($postQuestions as $q)
                        <th style="text-align:center;">Post Q{{ $loop->iteration }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($respondents as $r)
                <tr>
                    <td style="font-weight:600;white-space:nowrap;">{{ $r->name }}</td>
                    <td style="white-space:nowrap;">{{ $r->child ? $r->child->name . ' (ke-' . $r->child->child_order . ')' : '—' }}</td>
                    <td style="text-align:center;white-space:nowrap;color:var(--text-muted);">{{ $r->child ? $r->child->ageLabel() : '—' }}</td>
                    <td style="text-align:center;white-space:nowrap;color:var(--text-muted);">{{ $r->child ? ($r->child->weight_kg + 0) . ' kg / ' . ($r->child->height_cm + 0) . ' cm' : '—' }}</td>
                    @foreach($preQuestions as $q)
                        @php $ans = $r->answers->where('question_id', $q->question_id)->where('test_type', 'pre')->first(); @endphp
                        <td style="text-align:center;color:var(--text-muted);" title="{{ $ans && $ans->option ? $ans->option->option_text : '' }}">
                            {{ $ans && $ans->option ? $ans->option->label : '—' }}
                        </td>
                    @endforeach
                    @foreach($postQuestions as $q)
                        <td style="text-align:center;color:var(--text-muted);">
                            @php $ans = $r->answers->where('question_id', $q->question_id)->where('test_type', 'post')->first(); @endphp
                            {{ $ans && $ans->option ? $ans->option->label : '—' }}
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection