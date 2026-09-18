@extends('layouts.admin')

@section('title', isset($material) ? 'Edit Materi' : 'Tambah Materi')

@push('styles')
<style>
    /* ===== RESPONSIVE FORM WRAPPER ===== */
    .form-page-wrapper {
        max-width: 760px;
        margin: 0 auto;
    }

    /* ===== PAGE HEADER ===== */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    /* ===== CARD ===== */
    .form-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 32px;
    }

    /* ===== FORM ELEMENTS ===== */
    .form-group { margin-bottom: 20px; }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* ===== ACTION BUTTONS ===== */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
        flex-wrap: wrap;
    }

    /* ===== MOBILE ===== */
    @media (max-width: 640px) {
        .form-card {
            padding: 20px 16px;
            border-radius: 12px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .page-header {
            flex-direction: column;
            gap: 12px;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="form-page-wrapper">
    <div class="page-header">
        <div>
            <div class="page-title">{{ isset($material) ? 'Edit Materi' : 'Tambah Materi' }}</div>
            <div class="page-sub">{{ isset($material) ? 'Perbarui konten edukasi' : 'Upload konten edukasi baru' }}</div>
        </div>
        <a href="{{ route('admin.materials.index') }}" class="btn btn-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ isset($material) ? route('admin.materials.update', $material) : route('admin.materials.store') }}">
            @csrf
            @if(isset($material)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Judul Materi</label>
                <input type="text" name="title" class="form-input"
                    placeholder="Masukkan judul materi"
                    value="{{ old('title', $material->title ?? '') }}" required>
                @error('title') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input" rows="3"
                    placeholder="Deskripsi singkat materi">{{ old('description', $material->description ?? '') }}</textarea>
                @error('description') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-input" required>
                        <option value="" disabled {{ old('type', $material->type ?? '') === '' ? 'selected' : '' }}>Pilih tipe</option>
                        <option value="video"   {{ old('type', $material->type ?? '') === 'video'   ? 'selected' : '' }}>Video</option>
                        <option value="artikel" {{ old('type', $material->type ?? '') === 'artikel' ? 'selected' : '' }}>Artikel</option>
                        <option value="pdf"     {{ old('type', $material->type ?? '') === 'pdf'     ? 'selected' : '' }}>PDF</option>
                    </select>
                    @error('type') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Durasi (menit)
                        <span style="font-weight:400;color:var(--text-muted);font-size:12px;">— opsional</span>
                    </label>
                    <input type="number" name="duration" class="form-input"
                        placeholder="Contoh: 10"
                        value="{{ old('duration', $material->duration ?? '') }}" min="1">
                    @error('duration') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">URL Konten</label>
                <input type="url" name="content" class="form-input"
                    placeholder="https://..."
                    value="{{ old('content', $material->content ?? '') }}" required>
                <div class="form-hint">Link video YouTube, artikel, atau file PDF</div>
                @error('content') <div style="font-size:13px;color:var(--danger);margin-top:5px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Urutan Tampil</label>
                <input type="number" name="order" class="form-input"
                    placeholder="1"
                    value="{{ old('order', $material->order ?? ($nextOrder ?? 1)) }}" min="1">
                <div class="form-hint">Angka lebih kecil tampil lebih dulu</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="width:auto;">
                    @if(isset($material))
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Materi
                    @endif
                </button>
                <a href="{{ route('admin.materials.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection