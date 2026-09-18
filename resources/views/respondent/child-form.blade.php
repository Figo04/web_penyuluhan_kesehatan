@extends('layouts.app')

@section('title', 'Data Anak')

@push('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-title  { font-size: 24px; font-weight: 800; }
    .page-sub    { font-size: 14px; color: var(--text-muted); margin-top: 4px; }
    .child-card  { padding: 24px; max-width: 640px; }
    .child-intro {
        background: #f0fdf9; border: 1px solid #d1fae5; border-radius: 10px;
        padding: 14px 16px; margin-bottom: 20px; font-size: 14px; color: #065f46; line-height: 1.5;
    }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .error-banner {
        background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px;
        padding: 14px 18px; margin-bottom: 16px; color: #991b1b; font-size: 14px; font-weight: 600;
    }
    @media (max-width: 640px) {
        .child-card  { padding: 18px; }
        .form-row-2 { grid-template-columns: 1fr; gap: 0; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">Data Anak</div>
    <div class="page-sub">Langkah sebelum mengerjakan Pre-Test</div>
</div>

<div class="card child-card">
    <div class="child-intro">
        👶 Isi data anak Anda terlebih dahulu. Data ini dipakai untuk soal <strong>kondisi anak</strong>
        pada Pre-Test berikutnya.
    </div>

    @if($errors->any())
    <div class="error-banner">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('respondent.child.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Nama Anak</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="birth_date">Tanggal Lahir</label>
                <input type="date" id="birth_date" name="birth_date" class="form-input"
                       value="{{ old('birth_date') }}" max="{{ now()->toDateString() }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="gender">Jenis Kelamin</label>
                <select id="gender" name="gender" class="form-input" required>
                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih...</option>
                    <option value="laki-laki" {{ old('gender') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="perempuan" {{ old('gender') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="child_order">Anak ke-</label>
            <input type="number" id="child_order" name="child_order" class="form-input"
                   value="{{ old('child_order') }}" min="1" max="20" required>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="weight_kg">Berat Badan (kg)</label>
                <input type="number" id="weight_kg" name="weight_kg" class="form-input"
                       value="{{ old('weight_kg') }}" step="0.01" min="0.5" max="150" required>
                <div class="form-hint">Contoh: 10.5</div>
            </div>
            <div class="form-group">
                <label class="form-label" for="height_cm">Tinggi / Panjang Badan (cm)</label>
                <input type="number" id="height_cm" name="height_cm" class="form-input"
                       value="{{ old('height_cm') }}" step="0.1" min="20" max="200" required>
                <div class="form-hint">Contoh: 82.5</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan &amp; Lanjut ke Pre-Test</button>
    </form>
</div>
@endsection
