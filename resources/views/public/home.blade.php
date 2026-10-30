@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- HERO SECTION — BRUTALIST EDITORIAL -->
<section class="home-hero">
    <div class="hero-content">        
        <h1 class="hero-title">
            PENGADUAN LAYANAN PERPUSTAKAAN DAERAH BANGKALAN
        </h1>
        
        <p class="hero-lead">
            Saluran resmi pelaporan keluhan, aspirasi, dan saran fasilitas DISPUSIP Kabupaten Bangkalan. Terbuka, transparan, dan dipantau langsung hingga tuntas.
        </p>

        <div class="hero-cta-group">
            <a href="{{ route('complaint.create') }}" class="btn-hero-primary">
                <i class="bi bi-pencil-square"></i>
                <span>Tulis Aduan Baru</span>
            </a>
            <a href="{{ route('tracking.index') }}" class="btn-hero-secondary">
                <i class="bi bi-search"></i>
                <span>Lacak Status Tiket</span>
            </a>
        </div>

        <div class="hero-trust-list">
            <div class="trust-item"><i class="bi bi-shield-check"></i> Kerahasiaan Terjamin</div>
            <div class="trust-item"><i class="bi bi-stopwatch"></i> Respon Cepat</div>
            <div class="trust-item"><i class="bi bi-check2-circle"></i> Tanpa Biaya</div>
        </div>
    </div>

    <!-- TACTILE 3D RENDER CARD -->
    <div class="hero-visual">
        <div class="visual-tactile-card">
            <div class="visual-render-box">
                <img src="{{ asset('images/hero-render.jpg') }}" alt="DISPUSIP Bangkalan Tactile Material Cube">
            </div>
            <div class="visual-info-row">
                <div class="visual-stats-badge">
                    <span class="badge-tag" style="margin-bottom: 0;">DISPUSIP &bull; AKREDITASI A</span>
                </div>
                <div class="font-mono" style="font-size: 11px; color: var(--color-smoke); text-transform: uppercase;">
                    BANGKALAN &bull; 2026
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATISTIK SECTION — FLAT CARDS -->
<section class="section-block">
    <div class="section-header">
        <span class="section-tag">TRANSPARANSI PUBLIK</span>
        <h2>STATISTIK PENANGANAN ADUAN</h2>
        <p>Rekapitulasi data penanganan aspirasi dan keluhan masyarakat secara real-time di DISPUSIP Bangkalan.</p>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-header-micro">
                <span class="font-mono" style="font-size: 11px; text-transform: uppercase; color: var(--color-smoke);">TOTAL LAPORAN</span>
                <div class="stat-icon-brutalist"><i class="bi bi-folder-fill"></i></div>
            </div>
            <div class="stat-number">{{ number_format($stats['total'] ?? 0) }}</div>
            <div>
                <div class="stat-title">Aduan Masuk</div>
                <div class="stat-note">Semua laporan terarsip digital</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-header-micro">
                <span class="font-mono" style="font-size: 11px; text-transform: uppercase; color: var(--color-smoke);">MENUNGGU</span>
                <div class="stat-icon-brutalist" style="background: var(--color-voltage-yellow);"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <div class="stat-number">{{ number_format($stats['pending'] ?? 0) }}</div>
            <div>
                <div class="stat-title">Verifikasi Petugas</div>
                <div class="stat-note">Antrean validasi dokumen</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-header-micro">
                <span class="font-mono" style="font-size: 11px; text-transform: uppercase; color: var(--color-smoke);">DIPROSES</span>
                <div class="stat-icon-brutalist"><i class="bi bi-gear-fill"></i></div>
            </div>
            <div class="stat-number">{{ number_format($stats['in_progress'] ?? 0) }}</div>
            <div>
                <div class="stat-title">Tindak Lanjut</div>
                <div class="stat-note">Pemeriksaan di lapangan</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-header-micro">
                <span class="font-mono" style="font-size: 11px; text-transform: uppercase; color: var(--color-smoke);">SELESAI</span>
                <div class="stat-icon-brutalist" style="background: var(--color-mint-chip);"><i class="bi bi-check-lg"></i></div>
            </div>
            <div class="stat-number">{{ number_format($stats['resolved'] ?? 0) }}</div>
            <div>
                <div class="stat-title">Tuntas Selesai</div>
                <div class="stat-note">Respon resmi diterbitkan</div>
            </div>
        </div>
    </div>
</section>

<!-- ALUR PENGADUAN (STEPS) -->
<section class="section-block">
    <div class="section-header">
        <span class="section-tag">ALUR LAYANAN</span>
        <h2>4 TAHAP PENANGANAN ADUAN</h2>
        <p>Prosedur transparan dan akuntabel dari pengajuan hingga respon resmi petugas.</p>
    </div>

    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">01</div>
            <h4 class="step-title">Tulis Laporan</h4>
            <p class="step-desc">Uraikan kronologi dan rincian masalah dengan jelas serta lampirkan bukti foto jika tersedia.</p>
        </div>
        <div class="step-card">
            <div class="step-number">02</div>
            <h4 class="step-title">Terima Tiket</h4>
            <p class="step-desc">Dapatkan kode tiket pelacakan unik (misal: <code>ADU-2410-001</code>) untuk memantau status secara langsung.</p>
        </div>
        <div class="step-card">
            <div class="step-number">03</div>
            <h4 class="step-title">Verifikasi Fisik</h4>
            <p class="step-desc">Petugas DISPUSIP memvalidasi laporan dan mengkoordinasikan perbaikan sarana di perpustakaan.</p>
        </div>
        <div class="step-card">
            <div class="step-number">04</div>
            <h4 class="step-title">Solusi & Jawaban</h4>
            <p class="step-desc">Hasil penyelesaian disampaikan secara terbuka dan terdokumentasi rapi pada sistem.</p>
        </div>
    </div>
</section>

<!-- KATEGORI LAYANAN -->
@if(isset($categories) && $categories->count() > 0)
<section class="section-block">
    <div class="section-header">
        <span class="section-tag">KLASIFIKASI BIDANG</span>
        <h2>KATEGORI PENGADUAN</h2>
        <p>Pilih kategori yang paling sesuai dengan kendala fasilitas atau layanan yang Anda alami.</p>
    </div>

    <div class="categories-grid">
        @foreach($categories as $category)
            <div class="cat-card">
                <div class="cat-card-icon">
                    <i class="bi {{ $category->icon ?: 'bi-tag' }}"></i>
                </div>
                <div>
                    <h3 class="cat-card-title">{{ $category->name }}</h3>
                    <p class="cat-card-desc">{{ Str::limit($category->description, 90) ?: 'Layanan pengaduan seputar bidang ini.' }}</p>
                </div>
                <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid var(--color-mist-gray);">
                    <span class="font-mono" style="font-size: 11px; text-transform: uppercase; color: var(--color-smoke);">
                        {{ $category->complaints_count ?? 0 }} LAPORAN TERCATAT
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

<!-- ADUAN TERAKHIR DISELESAIKAN -->
@if(isset($recentResolved) && $recentResolved->count() > 0)
<section class="section-block">
    <div class="section-header">
        <span class="section-tag">BUKTI TINDAK LANJUT</span>
        <h2>ADUAN TERAKHIR YANG SELESAI</h2>
        <p>Laporan masyarakat yang telah ditangani dan diberikan solusi konkret oleh DISPUSIP Bangkalan.</p>
    </div>

    <div class="stats-row">
        @foreach($recentResolved as $item)
            <div class="card-flat" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                        <span class="ticket-code-pill">{{ $item->tracking_code }}</span>
                        <span class="badge-status badge-status-resolved"><i class="bi bi-check2"></i> Selesai</span>
                    </div>
                    <h4 style="font-size: 20px; line-height: 1.1; margin-bottom: 8px;">{{ Str::limit($item->subject, 50) }}</h4>
                    <p style="font-size: 13.5px; color: var(--color-slate); line-height: 1.45;">{{ Str::limit($item->description, 100) }}</p>
                </div>
                <div style="margin-top: 18px; padding-top: 12px; border-top: 1px solid var(--color-mist-gray); display: flex; justify-content: space-between; font-size: 12px; color: var(--color-smoke); font-family: var(--font-mono); text-transform: uppercase;">
                    <span>{{ $item->category->name ?? 'Umum' }}</span>
                    <span>{{ $item->resolved_at ? $item->resolved_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

<!-- INVERTED DARK CALL TO ACTION CARD -->
<section class="section-block">
    <div class="card-inverted" style="display: flex; flex-direction: column; align-items: flex-start; gap: 20px;">
        <span class="section-tag" style="background-color: var(--color-voltage-yellow); color: var(--color-carbon-black); margin-bottom: 0;">
            SUARA MASYARAKAT
        </span>
        <h2 style="font-size: clamp(38px, 5vw, 68px); color: var(--color-paper-white);">
            SIAP MENYAMPAIKAN ASPIRASI ANDA?
        </h2>
        <p style="font-size: 16px; color: var(--color-smoke); max-width: 600px; line-height: 1.5;">
            Setiap laporan Anda adalah langkah penting untuk meningkatkan standar perpustakaan umum Kabupaten Bangkalan menjadi ruang literasi yang nyaman dan membanggakan.
        </p>
        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 10px;">
            <a href="{{ route('complaint.create') }}" class="btn-mint" style="font-size: 15px; padding: 14px 28px;">
                <i class="bi bi-pencil-square"></i>
                <span>Mulai Buat Pengaduan Sekarang</span>
            </a>
            <a href="{{ route('tracking.index') }}" class="btn-ghost" style="color: var(--color-paper-white); border-color: var(--color-smoke); font-size: 15px; padding: 14px 28px;">
                <i class="bi bi-search"></i>
                <span>Cari Status Pengaduan</span>
            </a>
        </div>
    </div>
</section>
@endsection
