@extends('layouts.admin')

@section('title', 'Dashboard Petugas & Admin')
@section('page_title', 'Ringkasan Statistik & Operasional')

@section('content')
<!-- BANNER SAMBUTAN — INVERTED BRUTALIST CARD -->
<div class="card-inverted" style="padding: 32px 36px; margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; border-radius: 24px;">
    <div>
        <span class="section-tag" style="background-color: var(--color-mint-chip); color: var(--color-carbon-black); margin-bottom: 10px;">
            PETUGAS PIKET AKTIF
        </span>
        <h2 style="font-size: clamp(28px, 3.5vw, 42px); color: var(--color-paper-white); margin-bottom: 4px;">
            SELAMAT BERTUGAS, {{ Auth::user()->name }}!
        </h2>
        <p style="font-size: 14px; color: var(--color-smoke); margin: 0;">
            {{ now()->translatedFormat('l, d F Y') }} &bull; Portal Pengaduan DISPUSIP Bangkalan.
        </p>
    </div>
    <div>
        <a href="{{ route('admin.complaints.index') }}?status=pending" class="btn-mint" style="font-size: 13.5px; padding: 12px 20px;">
            <i class="bi bi-bell-fill"></i>
            <span>{{ $stats['pending'] ?? 0 }} Aduan Menunggu Verifikasi</span>
        </a>
    </div>
</div>

<!-- METRIC STATS ROW -->
<div class="admin-stats-row">
    <div class="metric-card">
        <div>
            <div class="metric-label">TOTAL LAPORAN</div>
            <div class="metric-value">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>
        <div class="metric-icon-box">
            <i class="bi bi-inbox-fill"></i>
        </div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">MENUNGGU VERIFIKASI</div>
            <div class="metric-value" style="color: var(--color-carbon-black);">{{ number_format($stats['pending'] ?? 0) }}</div>
        </div>
        <div class="metric-icon-box" style="background-color: var(--color-voltage-yellow); color: var(--color-carbon-black);">
            <i class="bi bi-hourglass-split"></i>
        </div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">SEDANG DIPROSES</div>
            <div class="metric-value">{{ number_format($stats['in_progress'] ?? 0) }}</div>
        </div>
        <div class="metric-icon-box">
            <i class="bi bi-gear-fill"></i>
        </div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">SELESAI DITANGANI</div>
            <div class="metric-value">{{ number_format($stats['resolved'] ?? 0) }}</div>
        </div>
        <div class="metric-icon-box" style="background-color: var(--color-mint-chip); color: var(--color-carbon-black);">
            <i class="bi bi-check-lg"></i>
        </div>
    </div>
</div>

<!-- GRAFIK & KATEGORI -->
<div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px;">
    <!-- Kolom Kiri: Tabel Laporan Terbaru -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 style="font-size: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-clock-history"></i>
                <span>Pengaduan Terbaru Masuk</span>
            </h3>
            <a href="{{ route('admin.complaints.index') }}" class="btn-admin-ghost" style="font-size: 12px; padding: 6px 14px;">
                <span>Lihat Semua</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @if($latestComplaints->count() > 0)
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>TIKET</th>
                            <th>JUDUL & PELAPOR</th>
                            <th>KATEGORI</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestComplaints as $complaint)
                            <tr>
                                <td>
                                    <span class="ticket-code-pill">{{ $complaint->tracking_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ Str::limit($complaint->subject, 32) }}</strong>
                                    <div class="font-mono" style="font-size: 11px; color: var(--color-smoke); text-transform: uppercase;">
                                        {{ $complaint->display_name }} &bull; {{ $complaint->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono" style="font-size: 11.5px; color: var(--color-slate);">{{ $complaint->category->name ?? 'Umum' }}</span>
                                </td>
                                <td>
                                    @if($complaint->status == 'resolved')
                                        <span class="badge-status badge-status-resolved">Selesai</span>
                                    @elseif($complaint->status == 'in_progress')
                                        <span class="badge-status badge-status-in_progress">Diproses</span>
                                    @elseif($complaint->status == 'rejected')
                                        <span class="badge-status badge-status-rejected">Ditolak</span>
                                    @else
                                        <span class="badge-status badge-status-pending">Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn-admin-dark" style="font-size: 12px; padding: 6px 12px;">
                                        <span>Proses</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 36px; text-align: center; color: var(--color-smoke);">
                <i class="bi bi-inbox" style="font-size: 32px; display:block; margin-bottom: 8px;"></i>
                Belum ada pengaduan yang diajukan.
            </div>
        @endif
    </div>

    <!-- Kolom Kanan: Rekap Kategori & Grafik -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Distribusi Kategori -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 style="font-size: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-pie-chart-fill"></i>
                    <span>Sebaran Kategori</span>
                </h3>
            </div>
            <div style="padding: 20px 24px;">
                @if(isset($categoryStats) && $categoryStats->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        @foreach($categoryStats as $cat)
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; margin-bottom: 6px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="bi {{ $cat->icon ?: 'bi-tag-fill' }}"></i>
                                        <span>{{ $cat->name }}</span>
                                    </div>
                                    <span class="font-mono" style="color: var(--color-smoke); font-size: 12px;">{{ $cat->total }} aduan</span>
                                </div>
                                <div style="width: 100%; height: 6px; background: var(--color-mist-gray); border-radius: 999px; overflow: hidden;">
                                    @php
                                        $percent = $stats['total'] > 0 ? round(($cat->total / $stats['total']) * 100) : 0;
                                    @endphp
                                    <div style="width: {{ $percent }}%; height: 100%; background: var(--color-carbon-black); border-radius: 999px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; color: var(--color-smoke); font-size: 13px;">Belum ada data kategori.</div>
                @endif
            </div>
        </div>

        <!-- Trend Bulanan -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 style="font-size: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-graph-up"></i>
                    <span>Grafik Laporan Bulanan</span>
                </h3>
                <span class="badge-tag" style="margin-bottom: 0;">
                    {{ $chartData['period'] ?? '2026' }}
                </span>
            </div>
            <div style="padding: 20px 24px;">
                <canvas id="monthlyTrendChart" height="175"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyTrendChart');
    if (!ctx) return;

    const labels = {!! json_encode($chartData['labels'] ?? (isset($monthlyStats) ? $monthlyStats->pluck('month_name') : ['Okt 2026'])) !!};
    const data   = {!! json_encode($chartData['totals'] ?? (isset($monthlyStats) ? $monthlyStats->pluck('total') : [0])) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Aduan Masuk',
                data: data,
                backgroundColor: '#000000',
                borderRadius: 6,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { family: 'JetBrains Mono', size: 11 } },
                    grid: { color: '#e5e5e5' }
                },
                x: {
                    ticks: { font: { family: 'JetBrains Mono', size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
