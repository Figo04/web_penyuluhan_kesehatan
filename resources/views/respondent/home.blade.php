@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    /* Palet khusus halaman beranda. Dibatasi di sini supaya halaman lain
       tetap memakai palet lama dan tidak ikut berubah. */
    .home {
        --h-cream:      #FBF6F1;
        --h-ink:        #14503E;
        --h-ink-soft:   #2C7A5F;
        --h-mint-1:     #E4F1E8;
        --h-mint-2:     #CFE7DA;
        --h-line:       #EFE6DD;
        --h-peach-bg:   #FDEDE3;
        --h-peach-ink:  #C4603B;
        --h-muted:      #8A9A93;
    }

    body { background: #FBF6F1; }

    /* ── SAPAAN ─────────────────────────────── */
    .greet {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 18px;
    }
    .greet-avatar {
        width: 44px; height: 44px; flex-shrink: 0;
        border-radius: 14px;
        background: var(--h-mint-1);
        display: flex; align-items: center; justify-content: center;
        color: var(--h-ink);
    }
    .greet-avatar svg { width: 22px; height: 22px; }
    .greet-text { flex: 1; min-width: 0; }
    .greet-hello { font-size: 13px; color: var(--h-muted); line-height: 1.3; }
    .greet-name {
        font-size: 19px; font-weight: 800; color: var(--h-ink);
        line-height: 1.3;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .greet-action {
        width: 38px; height: 38px; flex-shrink: 0;
        border-radius: 50%;
        border: 1px solid var(--h-line);
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        color: var(--h-ink-soft);
        text-decoration: none;
        transition: all 0.15s;
    }
    .greet-action:hover { background: var(--h-mint-1); }
    .greet-action svg { width: 18px; height: 18px; }

    /* ── HERO ───────────────────────────────── */
    .hero {
        position: relative; overflow: hidden;
        border-radius: 22px;
        background: linear-gradient(135deg, var(--h-mint-1) 0%, var(--h-mint-2) 100%);
        padding: 26px 24px 28px;
        margin-bottom: 16px;
    }
    .hero-eyebrow {
        font-size: 11px; font-weight: 800;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: var(--h-ink-soft); margin-bottom: 10px;
    }
    .hero-title {
        position: relative; z-index: 1;
        font-size: 28px; font-weight: 800; line-height: 1.22;
        color: var(--h-ink); margin-bottom: 12px;
        max-width: 15ch;
    }
    .hero-sub {
        position: relative; z-index: 1;
        font-size: 13.5px; line-height: 1.6;
        color: var(--h-ink-soft); max-width: 30ch;
    }
    /* Motif dekoratif — pengganti ilustrasi, tidak menutupi teks */
    .hero-motif {
        position: absolute; right: -30px; bottom: -40px;
        width: 210px; height: 210px; opacity: 0.55;
        pointer-events: none;
    }

    /* ── STRIP INFO ─────────────────────────── */
    .infostrip {
        display: flex; align-items: stretch;
        background: #fff; border: 1px solid var(--h-line);
        border-radius: 18px; padding: 14px 4px;
        margin-bottom: 26px;
    }
    .infostrip-cell {
        flex: 1; display: flex; align-items: center; gap: 10px;
        padding: 0 16px; min-width: 0;
    }
    .infostrip-cell + .infostrip-cell { border-left: 1px solid var(--h-line); }
    .infostrip-cell svg { width: 20px; height: 20px; flex-shrink: 0; color: var(--h-peach-ink); }
    .infostrip-label { font-size: 11.5px; color: var(--h-muted); line-height: 1.3; }
    .infostrip-value { font-size: 14px; font-weight: 800; color: var(--h-ink); line-height: 1.3; }

    /* ── JUDUL SEKSI ────────────────────────── */
    .sec-eyebrow {
        font-size: 11px; font-weight: 800;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: var(--h-muted); margin-bottom: 6px;
    }
    .sec-head {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 14px;
    }
    .sec-title { font-size: 20px; font-weight: 800; color: var(--h-ink); }
    .sec-pill {
        flex-shrink: 0;
        background: var(--h-peach-bg); color: var(--h-peach-ink);
        font-size: 12px; font-weight: 700;
        padding: 5px 12px; border-radius: 100px;
    }

    /* ── STEPPER ────────────────────────────── */
    .stepper {
        display: flex; align-items: flex-start;
        background: #fff; border: 1px solid var(--h-line);
        border-radius: 20px; padding: 24px 16px 20px;
        margin-bottom: 18px;
    }
    .step {
        flex: 1; position: relative;
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        text-decoration: none; color: inherit;
        padding: 0 4px;
    }
    /* Garis penghubung antar langkah */
    .step:not(:last-child)::after {
        content: ''; position: absolute;
        top: 19px; left: calc(50% + 24px); right: calc(-50% + 24px);
        height: 2px; background: var(--h-line); z-index: 0;
    }
    .step.done:not(:last-child)::after { background: var(--h-ink); }

    .step-dot {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        position: relative; z-index: 1;
        background: #fff; border: 2px solid var(--h-line);
        color: var(--h-muted);
        font-size: 13px; font-weight: 800;
    }
    .step.done .step-dot { background: var(--h-ink); border-color: var(--h-ink); color: #fff; }
    .step.done .step-dot svg { width: 17px; height: 17px; }
    .step.current .step-dot { border-color: var(--h-ink); color: var(--h-ink); }
    .step.current .step-dot::before {
        content: ''; width: 13px; height: 13px;
        border-radius: 50%; background: var(--h-ink);
    }
    .step.locked .step-dot { background: #F1EEEA; border-color: #F1EEEA; color: #B6ABA1; }
    .step.locked .step-dot svg { width: 15px; height: 15px; }
    .step.locked { cursor: not-allowed; }

    .step-name { font-size: 13px; font-weight: 800; color: var(--h-ink); text-align: center; }
    .step-state { font-size: 11px; text-align: center; line-height: 1.3; }
    .step.done    .step-state { color: var(--h-ink-soft); }
    .step.current .step-state { color: var(--h-peach-ink); font-weight: 700; }
    .step.locked  .step-name,
    .step.locked  .step-state { color: #B6ABA1; }

    /* ── TOMBOL UTAMA ───────────────────────── */
    .cta {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 17px;
        background: var(--h-ink); color: #fff;
        border-radius: 16px; font-size: 15.5px; font-weight: 700;
        text-decoration: none; transition: all 0.15s;
        margin-bottom: 32px;
    }
    .cta:hover { background: #0E3D2F; }
    .cta svg { width: 18px; height: 18px; }
    .cta.finished {
        background: var(--h-mint-1); color: var(--h-ink);
        cursor: default;
    }
    .cta.finished:hover { background: var(--h-mint-1); }

    /* ── KARTU INFO KESEHATAN ───────────────── */
    .tips { animation: tipsIn 0.5s ease both; }
    @keyframes tipsIn {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .tips-scroll {
        display: flex; gap: 12px;
        overflow-x: auto; padding-bottom: 10px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .tips-scroll::-webkit-scrollbar { display: none; }

    .tip-card {
        flex-shrink: 0; width: 230px;
        background: #fff; border: 1px solid var(--h-line);
        border-radius: 18px; padding: 18px;
        scroll-snap-align: start;
        text-decoration: none; display: block;
        transition: all 0.2s;
    }
    .tip-card:hover, .tip-card:active {
        border-color: var(--h-mint-2);
        box-shadow: 0 6px 20px rgba(20,80,62,0.08);
        transform: translateY(-2px);
    }
    .tip-title {
        font-size: 13.5px; font-weight: 800; color: var(--h-ink);
        margin-bottom: 9px; line-height: 1.4;
    }
    .tip-body { font-size: 12.5px; color: #5C6B64; line-height: 1.65; }
    .tip-body ul { padding-left: 15px; margin: 5px 0 0; }
    .tip-body li { margin-bottom: 3px; }

    .tips-dots { display: flex; justify-content: center; gap: 5px; margin-top: 12px; }
    .tips-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--h-line); transition: all 0.2s;
    }
    .tips-dot.active { background: var(--h-ink); width: 18px; border-radius: 3px; }

    .tips-link {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        margin-top: 18px; padding: 15px;
        background: #fff; border: 1.5px solid var(--h-ink);
        color: var(--h-ink); border-radius: 14px;
        font-weight: 700; font-size: 14.5px;
        text-decoration: none; transition: all 0.15s;
    }
    .tips-link:hover { background: var(--h-mint-1); }
    .tips-link svg { width: 17px; height: 17px; }

    @media (max-width: 380px) {
        .hero { padding: 22px 20px 24px; }
        .hero-title { font-size: 24px; }
        .step-name { font-size: 12px; }
        .step-state { font-size: 10px; }
    }
</style>
@endpush

@section('content')
<div class="home">

    {{-- SAPAAN --}}
    <div class="greet">
        <div class="greet-avatar">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3v6a6 6 0 0012 0V3"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2a4 4 0 008 0v-1"/>
                <circle cx="20" cy="13" r="2" stroke-width="2"/>
            </svg>
        </div>
        <div class="greet-text">
            <div class="greet-hello">Selamat datang kembali,</div>
            <div class="greet-name">{{ $sapaan }} {{ $respondent->name }}</div>
        </div>
        <a href="{{ route('respondent.profile') }}" class="greet-action" title="Lihat profil">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </a>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <svg class="hero-motif" viewBox="0 0 200 200" fill="none" aria-hidden="true">
            <circle cx="120" cy="90" r="70" fill="#fff" fill-opacity="0.35"/>
            <circle cx="60" cy="150" r="42" fill="#fff" fill-opacity="0.25"/>
            <path d="M150 140c0-24 18-42 42-42 0 24-18 42-42 42z" fill="#2C7A5F" fill-opacity="0.18"/>
            <path d="M150 140c-24 0-42-18-42-42 24 0 42 18 42 42z" fill="#2C7A5F" fill-opacity="0.12"/>
            <path d="M150 140v34" stroke="#2C7A5F" stroke-opacity="0.2" stroke-width="3" stroke-linecap="round"/>
        </svg>

        <div class="hero-eyebrow">Kenali Stunting</div>
        <div class="hero-title">Tumbuh sehat dimulai dari langkah kecil</div>
        <div class="hero-sub">Lanjutkan pembelajaran untuk mendampingi tumbuh kembang si kecil.</div>
    </div>

    {{-- STRIP INFO --}}
    <div class="infostrip">
        <div class="infostrip-cell">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <div class="infostrip-label">Estimasi waktu</div>
                <div class="infostrip-value">± 30 menit</div>
            </div>
        </div>
        <div class="infostrip-cell">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            <div>
                <div class="infostrip-label">Alur belajar</div>
                <div class="infostrip-value">3 tahap</div>
            </div>
        </div>
    </div>

    {{-- PROGRES --}}
    <div class="sec-eyebrow">Perjalanan Belajar</div>
    <div class="sec-head">
        <div class="sec-title">Progres Kamu</div>
        <span class="sec-pill">{{ $tahapSelesai }} dari 3 selesai</span>
    </div>

    @php
        $langkah = [
            [
                'nama'    => 'Pre-Test',
                'status'  => $statusPre,
                'url'     => route('respondent.pretest'),
                'terbuka' => true,
                'alasan'  => null,
            ],
            [
                'nama'    => 'Materi',
                'status'  => $statusMateri,
                'url'     => route('respondent.material'),
                'terbuka' => !$materiTerkunci,
                'alasan'  => 'Selesaikan Pre-Test terlebih dahulu',
            ],
            [
                'nama'    => 'Post-Test',
                'status'  => $statusPost,
                'url'     => route('respondent.posttest'),
                'terbuka' => !$postTestTerkunci,
                'alasan'  => 'Selesaikan Pre-Test dan baca semua materi terlebih dahulu',
            ],
        ];

        $teksStatus = [
            'done'    => 'Selesai',
            'current' => 'Sedang dipelajari',
            'locked'  => 'Terkunci',
        ];
    @endphp

    <div class="stepper">
        @foreach($langkah as $l)
            <{{ $l['terbuka'] ? 'a' : 'div' }}
                @if($l['terbuka']) href="{{ $l['url'] }}" @else title="{{ $l['alasan'] }}" @endif
                class="step {{ $l['status'] }}">
                <div class="step-dot">
                    @if($l['status'] === 'done')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @elseif($l['status'] === 'locked')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    @endif
                </div>
                <div>
                    <div class="step-name">{{ $l['nama'] }}</div>
                    <div class="step-state">{{ $teksStatus[$l['status']] }}</div>
                </div>
            </{{ $l['terbuka'] ? 'a' : 'div' }}>
        @endforeach
    </div>

    {{-- TOMBOL UTAMA --}}
    @if($cta)
        <a href="{{ $cta['url'] }}" class="cta">
            {{ $cta['label'] }}
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    @else
        <div class="cta finished">
            🎉 Program penyuluhan selesai. Terima kasih telah berpartisipasi!
        </div>
    @endif

    {{-- INFO KESEHATAN — muncul setelah pre-test selesai --}}
    @if($respondent->pre_test_done)
    <div class="tips" id="tipsSection">
        <div class="sec-eyebrow">Bacaan Singkat</div>
        <div class="sec-head">
            <div class="sec-title">Info Kesehatan Untukmu</div>
            <span class="sec-pill">✨ Baru</span>
        </div>

        <div class="tips-scroll" id="tipsScroll">
            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Tanda Anak Berisiko Stunting</div>
                <div class="tip-body">
                    Orang tua perlu waspada jika:
                    <ul>
                        <li>Anak jarang naik berat badan</li>
                        <li>Tinggi badan tidak sesuai usia</li>
                        <li>Anak sering sakit</li>
                    </ul>
                </div>
            </a>

            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Kesalahan yang Sering Dilakukan Orang Tua</div>
                <div class="tip-body">
                    <ul>
                        <li>Memberi makanan instan terlalu sering</li>
                        <li>MPASI tidak mengandung protein</li>
                        <li>Ayah tidak terlibat pengasuhan</li>
                    </ul>
                </div>
            </a>

            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Contoh Menu Sehari untuk Balita</div>
                <div class="tip-body">
                    🌅 Pagi: Nasi + telur + sayur<br>
                    ☀️ Siang: Nasi + ayam + tempe<br>
                    🌙 Malam: Nasi + ikan + sayur<br>
                    🍎 Snack: Buah atau susu
                </div>
            </a>

            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Peran Ayah yang Sering Diabaikan</div>
                <div class="tip-body">
                    Ayah bisa membantu dengan:
                    <ul>
                        <li>Mengingatkan jadwal posyandu</li>
                        <li>Membantu menyiapkan makanan</li>
                        <li>Mendukung ibu merawat anak</li>
                    </ul>
                </div>
            </a>

            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Pentingnya Posyandu</div>
                <div class="tip-body">
                    Manfaat posyandu:
                    <ul>
                        <li>Memantau pertumbuhan anak</li>
                        <li>Edukasi kesehatan gratis</li>
                        <li>Deteksi dini stunting</li>
                    </ul>
                </div>
            </a>

            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Lingkungan Rumah yang Sehat</div>
                <div class="tip-body">
                    Rumah sehat mencegah stunting:
                    <ul>
                        <li>Air bersih &amp; sanitasi baik</li>
                        <li>Anak aktif bermain</li>
                        <li>Kebersihan makanan terjaga</li>
                    </ul>
                </div>
            </a>

            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Dampak Stunting Saat Dewasa</div>
                <div class="tip-body">
                    Jika tidak dicegah:
                    <ul>
                        <li>Prestasi belajar menurun</li>
                        <li>Produktivitas rendah</li>
                        <li>Risiko penyakit meningkat</li>
                    </ul>
                </div>
            </a>

            <a href="{{ route('respondent.material') }}" class="tip-card">
                <div class="tip-title">Langkah Mudah Mulai Hari Ini</div>
                <div class="tip-body">
                    Mulai dari hal sederhana:
                    <ul>
                        <li>Sediakan makanan bergizi</li>
                        <li>Libatkan ayah dalam pengasuhan</li>
                        <li>Pantau tumbuh kembang anak</li>
                    </ul>
                </div>
            </a>
        </div>

        <div class="tips-dots" id="tipsDots">
            @for($i = 0; $i < 8; $i++)
                <div class="tips-dot {{ $i === 0 ? 'active' : '' }}"></div>
            @endfor
        </div>

        <a href="{{ route('respondent.material') }}" class="tips-link">
            Buka Materi Lengkap
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // Gulir otomatis ke bagian info kesehatan sesaat setelah pre-test selesai.
    @if($respondent->pre_test_done && session('show_highlight'))
        setTimeout(function () {
            var el = document.getElementById('tipsSection');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 500);
    @endif

    // Titik indikator mengikuti posisi geser kartu.
    (function () {
        var scroll = document.getElementById('tipsScroll');
        if (!scroll) return;

        var cards = scroll.querySelectorAll('.tip-card');
        var dots  = document.querySelectorAll('.tips-dot');
        if (!cards.length || !dots.length) return;

        scroll.addEventListener('scroll', function () {
            var index = Math.round(scroll.scrollLeft / (scroll.scrollWidth / cards.length));
            dots.forEach(function (d, i) { d.classList.toggle('active', i === index); });
        }, { passive: true });
    })();
</script>
@endpush
