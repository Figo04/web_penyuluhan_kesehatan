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
                    @foreach($preQuestions as $q)
                        <th style="text-align:center;">Pre Q{{ $loop->iteration }}</th>
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
                    @foreach($preQuestions as $q)
                        <td style="text-align:center;color:var(--text-muted);">
                            @php $ans = $r->answers->where('question_id', $q->id)->where('test_type', 'pre')->first(); @endphp
                            {{ $ans && $ans->option ? $ans->option->label : '—' }}
                        </td>
                    @endforeach
                    @foreach($postQuestions as $q)
                        <td style="text-align:center;color:var(--text-muted);">
                            @php $ans = $r->answers->where('question_id', $q->id)->where('test_type', 'post')->first(); @endphp
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