@extends('layouts.admin')

@section('title', 'Kelola Lokasi')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Lokasi</div>
        <div class="page-sub">{{ $locations->count() }} lokasi praktik bidan terdaftar</div>
    </div>
</div>

@if(session('success'))
    <div class="card" style="padding:14px 20px; margin-bottom:16px; background:#ecfdf5; color:#065f46; font-weight:600;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="card" style="padding:14px 20px; margin-bottom:16px; background:#fef2f2; color:#991b1b; font-weight:600;">
        {{ session('error') }}
    </div>
@endif

<div class="card" style="padding:20px 24px; margin-bottom:16px;">
    <form method="POST" action="{{ route('admin.locations.store') }}" style="display:flex; gap:10px; flex-wrap:wrap;">
        @csrf
        <input type="text" name="name" class="search-input" style="flex:1; min-width:220px;"
            placeholder="Nama praktik bidan / puskesmas" value="{{ old('name') }}" required>
        <button type="submit" class="btn btn-primary">Tambah Lokasi</button>
    </form>
    @error('name')
        <div style="color:#dc2626; font-size:13px; margin-top:8px;">{{ $message }}</div>
    @enderror
</div>

<div class="card" style="padding:20px 24px;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Lokasi</th>
                    <th>Jumlah Responden</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $lokasi)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('admin.locations.update', $lokasi) }}"
                                  style="display:flex; gap:8px; align-items:center;">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $lokasi->name }}"
                                       class="search-input" style="min-width:200px;" required>
                                <button type="submit" class="btn">Simpan</button>
                            </form>
                        </td>
                        <td>{{ $lokasi->respondents_count }}</td>
                        <td style="text-align:right;">
                            @if($lokasi->respondents_count === 0)
                                <form method="POST" action="{{ route('admin.locations.destroy', $lokasi) }}"
                                      onsubmit="return confirm('Hapus lokasi {{ $lokasi->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn">Hapus</button>
                                </form>
                            @else
                                <span style="color:#9ca3af; font-size:13px;">Sedang dipakai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center; color:#9ca3af; padding:24px;">
                            Belum ada lokasi. Tambahkan minimal satu agar responden bisa memilihnya saat mendaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
