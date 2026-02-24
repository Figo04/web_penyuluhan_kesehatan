@extends('layouts.admin')

@section('title', 'Edit Materi')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Edit Materi</div>
        <div class="page-sub">Perbarui konten edukasi</div>
    </div>
    <a href="{{ route('admin.materials.index') }}" class="btn btn-outline">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="card" style="padding:32px;max-width:680px;">
    <form method="POST" action="{{ route('admin.materials.update', $material) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Judul Materi</label>
            <input type="text" name="title" class="form-input"
                placeholder="Masukkan judul materi"
                value="{{ old('title', $material->title) }}" required>
            @error('title') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-input" rows="3"
                placeholder="Deskripsi singkat materi">{{ old('description', $material->description) }}</textarea>
            @error('description') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-input" required>
                    <option value="video"   {{ old('type', $material->type) === 'video'   ? 'selected' : '' }}>Video</option>
                    <option value="artikel" {{ old('type', $material->type) === 'artikel' ? 'selected' : '' }}>Artikel</option>
                    <option value="pdf"     {{ old('type', $material->type) === 'pdf'     ? 'selected' : '' }}>PDF</option>
                </select>
                @error('type') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Durasi (menit)</label>
                <input type="number" name="duration" class="form-input"
                    placeholder="Contoh: 10"
                    value="{{ old('duration', $material->duration) }}" min="1" required>
                @error('duration') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">URL Konten</label>
            <input type="url" name="content" class="form-input"
                placeholder="https://..."
                value="{{ old('content', $material->content) }}" required>
            <div class="form-hint">Link video YouTube, artikel, atau file PDF</div>
            @error('content') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="order" class="form-input"
                placeholder="1"
                value="{{ old('order', $material->order) }}" min="1">
            <div class="form-hint">Angka lebih kecil tampil lebih dulu</div>
        </div>

        <div style="display:flex;gap:12px;margin-top:28px;">
            <button type="submit" class="btn btn-primary" style="width:auto;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.materials.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection