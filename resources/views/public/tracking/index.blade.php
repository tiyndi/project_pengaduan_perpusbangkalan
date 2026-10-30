@extends('layouts.app')

@section('title', 'Lacak Status Tiket')

@section('content')
<div class="tracking-container">
    <!-- Header Pencarian Cepat — Flat Inverted Card -->
    <div class="card-inverted" style="margin-bottom: 40px; padding: 48px 36px; text-align: center;">
        <div style="max-width: 640px; margin: 0 auto;">
            <span class="section-tag" style="background-color: var(--color-mint-chip); color: var(--color-carbon-black); margin-bottom: 12px;">
                PELACAKAN TERBUKA
            </span>
            <h2 style="font-size: clamp(32px, 4.5vw, 52px); color: var(--color-paper-white); margin-bottom: 8px;">
                LACAK STATUS PENGADUAN
            </h2>
            <p style="font-size: 15px; color: var(--color-smoke); margin-bottom: 24px;">
                Masukkan nomor tiket unik Anda untuk melihat perkembangan proses dan tanggapan resmi petugas DISPUSIP Bangkalan.
            </p>

            <form action="{{ route('tracking.show') }}" method="POST" style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;">
                @csrf
                <div style="flex: 1; min-width: 260px; max-width: 440px;">
                    <input type="text" name="tracking_code" required
                           placeholder="CONTOH: ADU-2410-001"
                           value="{{ old('tracking_code') }}" class="gov-input" style="font-family: var(--font-mono); font-size: 15px; text-transform: uppercase; letter-spacing: 0.05em; background: var(--color-paper-white); color: var(--color-carbon-black);">
                </div>
                <button type="submit" class="btn-mint" style="padding: 12px 24px;">
                    <i class="bi bi-search"></i>
                    <span>Cari Tiket</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Riwayat Pengaduan Saya -->
    <div style="margin-bottom: 40px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <div>
                <span class="font-mono" style="font-size: 11px; text-transform: uppercase; color: var(--color-smoke); letter-spacing: 0.05em;">ARSIP PRIBADI</span>
                <h3 style="font-size: 28px; line-height: 1;">RIWAYAT PENGADUAN SAYA</h3>
            </div>
            <a href="{{ route('complaint.create') }}" class="btn-dark" style="font-size: 13.5px; padding: 10px 18px;">
                <i class="bi bi-plus-lg"></i>
                <span>Tulis Aduan Baru</span>
            </a>
        </div>

        @if($complaints->count() > 0)
            <div class="reports-table-card">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>NOMOR TIKET</th>
                            <th>JUDUL PENGADUAN</th>
                            <th>KATEGORI</th>
                            <th>TANGGAL LAPOR</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td>
                                    <span class="ticket-code-pill">{{ $complaint->tracking_code }}</span>
                                </td>
                                <td>
                                    <strong>{{ Str::limit($complaint->subject, 48) }}</strong>
                                </td>
                                <td>
                                    <span class="font-mono" style="font-size: 12px; color: var(--color-slate);">{{ $complaint->category->name ?? 'Umum' }}</span>
                                </td>
                                <td>
                                    <span class="font-mono" style="font-size: 12px; color: var(--color-smoke);">{{ $complaint->created_at->format('d/m/Y, H:i') }}</span>
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
                                    <a href="{{ route('tracking.show') }}?tracking_code={{ $complaint->tracking_code }}" class="btn-ghost" style="padding: 6px 14px; font-size: 12px;">
                                        <span>Detail</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 24px;">
                {{ $complaints->links() }}
            </div>
        @else
            <!-- State Kosong -->
            <div class="card-flat" style="padding: 48px 32px; text-align: center;">
                <div style="width: 48px; height: 48px; border-radius: 10px; background-color: var(--color-mist-gray); display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto 16px;">
                    <i class="bi bi-inbox"></i>
                </div>
                <h4 style="font-size: 22px; margin-bottom: 6px;">BELUM ADA RIWAYAT PENGADUAN</h4>
                <p style="font-size: 14px; color: var(--color-slate); max-width: 460px; margin: 0 auto 20px;">
                    Anda belum pernah mengajukan laporan pengaduan. Sampaikan aspirasi atau keluhan fasilitas perpustakaan sekarang.
                </p>
                <a href="{{ route('complaint.create') }}" class="btn-dark">
                    <i class="bi bi-pencil-square"></i>
                    <span>Mulai Buat Pengaduan</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
